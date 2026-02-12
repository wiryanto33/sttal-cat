<?php

namespace App\Livewire\Student;

use App\Models\ExamAnswer;
use App\Models\ExamPacket;
use App\Models\ExamSession;
use App\Models\ExamViolation;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ExamPage extends Component
{
    public $packet;
    public $session;
    public $remainingTime;
    public $questionIds = [];
    public $currentQuestionIndex = 0;
    public $answers = [];

    public function mount($packetId)
    {
        $user = Auth::user();
        if (!$user || !$user->candidate) {
            return redirect()->route('student.dashboard');
        }

        $this->packet = ExamPacket::findOrFail($packetId);
        $this->questionIds = $this->packet->questions()->orderBy('id')->pluck('id')->toArray();

        $this->session = ExamSession::firstOrCreate(
            ['candidate_id' => $user->candidate->id, 'exam_packet_id' => $this->packet->id],
            ['status' => ExamSession::STATUS_ONGOING, 'start_time' => now()]
        );

        // Keamanan: Jika sudah diskualifikasi atau selesai, tendang keluar
        if ($this->session->is_disqualified || $this->session->status >= 2) {
            return redirect()->route('student.dashboard')->with('error', 'Akses ujian ditutup.');
        }

        $this->answers = ExamAnswer::where('exam_session_id', $this->session->id)->pluck('answer', 'question_id')->toArray();

        $durasiDetik = ($this->packet->duration_minutes ?? 90) * 60;
        $detikBerjalan = now()->diffInSeconds(Carbon::parse($this->session->start_time));
        $this->remainingTime = (int) max(0, $durasiDetik - $detikBerjalan);
    }

    // --- LOGIKA PELANGGARAN (PROCTORING) ---
    public function handleViolation($type, $description)
    {
        // Jika sudah diskualifikasi sebelumnya, langsung lempar ke dashboard
        if ($this->session->is_disqualified) {
            return $this->autoSubmitViolation();
        }

        if ($this->session->status !== 1) {
            return;
        }

        // 1. Catat Log ke Database
        ExamViolation::create([
            'exam_session_id' => $this->session->id,
            'violation_type' => $type,
            'description' => $description,
            'detected_at' => now(),
        ]);

        // 2. Hitung jumlah pelanggaran REAL-TIME
        $count = ExamViolation::where('exam_session_id', $this->session->id)->count();

        // 3. Logika Diskualifikasi (Pelanggaran ke-4)
        if ($count >= 4) {
            $this->session->update([
                'is_disqualified' => true,
                'disqualification_reason' => "Otomatis: Melakukan $count jenis pelanggaran.",
                'status' => ExamSession::STATUS_WAITING_CORRECTION, // Kunci status
                'end_time' => now()
            ]);
            return $this->autoSubmitViolation();
        }

        // 4. Kirim Peringatan ke Browser (Peringatan 1, 2, atau 3)
        $this->dispatch('show-proctor-warning', [
            'count' => $count,
            'message' => "Sistem mendeteksi tindakan: " . str_replace('_', ' ', strtoupper($type)),
            'is_critical' => ($count == 3)
        ]);
    }

    public function autoSubmitViolation()
    {
        // Hitung skor sementara yang sudah masuk (agar tidak 0 jika sudah dikerjakan sebagian)
        $nilaiPG = ExamAnswer::where('exam_session_id', $this->session->id)
            ->whereHas('question', fn($q) => $q->where('type', 'multiple_choice'))
            ->sum('score');

        $this->session->update(['total_score' => $nilaiPG]);

        $this->dispatch('force-redirect', url: route('student.dashboard'));
    }

    // --- NAVIGASI DENGAN GUARD ---
    public function nextQuestion()
    {
        if ($this->session->is_disqualified) return $this->autoSubmitViolation();
        if ($this->currentQuestionIndex < count($this->questionIds) - 1) $this->currentQuestionIndex++;
    }

    public function goToQuestion($index)
    {
        // Cek apakah index valid dan session masih aktif
        if (isset($this->questionIds[$index]) && !$this->session->is_disqualified) {
            $this->currentQuestionIndex = $index;

            // Opsional: Paksa render ulang untuk memastikan Computed Property terpanggil
            $this->dispatch('question-changed');
        }
    }

    public function saveAnswer($questionId, $answer)
    {
        if ($this->session->is_disqualified || $this->session->status >= 2) return;

        $this->answers[$questionId] = $answer;
        $question = Question::find($questionId);

        $isCorrect = false;
        $score = 0;
        if ($question && $question->type === 'multiple_choice') {
            $isCorrect = (strtoupper(trim($question->correct_answer)) === strtoupper(trim($answer)));
            $score = $isCorrect ? ($question->weight ?? 1) : 0;
        }

        ExamAnswer::updateOrCreate(
            ['exam_session_id' => $this->session->id, 'question_id' => $questionId],
            ['answer' => $answer, 'is_correct' => $isCorrect, 'score' => $score]
        );
    }

    #[Computed]
    public function currentQuestion()
    {
        // Ambil ID soal berdasarkan index saat ini
        $currentId = $this->questionIds[$this->currentQuestionIndex] ?? null;

        if (! $currentId) {
            return null;
        }

        return Question::find($currentId);
    }

    public function finishExam()
    {
        // Jika sudah pernah diproses, jangan jalankan lagi (mencegah error ganda)
        if ($this->session->status >= 2 && !$this->session->is_disqualified) {
            return redirect()->route('student.dashboard');
        }

        try {
            // 1. Cek Apakah ada soal ESSAY di paket ini?
            $hasEssay = $this->packet->questions()->where('type', 'essay')->exists();

            // 2. Hitung Nilai Pilihan Ganda (TPA) yang sudah pasti
            $nilaiMultipleChoice = ExamAnswer::where('exam_session_id', $this->session->id)
                ->whereHas('question', fn($q) => $q->where('type', 'multiple_choice'))
                ->sum('score');

            // 3. Update Sesi Ujian (Gunakan update tunggal agar atomik)
            $newStatus = $hasEssay ? ExamSession::STATUS_WAITING_CORRECTION : ExamSession::STATUS_FINISHED;

            $this->session->update([
                'status' => $newStatus,
                'end_time' => now(),
                'score_tpa_aggregate' => $nilaiMultipleChoice,
                'total_score' => $nilaiPG ?? $nilaiMultipleChoice, // Gunakan nilai yang ada
            ]);

            $pesan = $hasEssay
                ? 'Ujian selesai. Jawaban Essay Anda akan diperiksa oleh penguji.'
                : 'Ujian selesai. Nilai Akhir Anda: ' . $nilaiMultipleChoice;

            session()->flash('success', $pesan);
            return redirect()->route('student.dashboard');
        } catch (\Exception $e) {
            // Jika ada error saat simpan, tetap lempar ke dashboard agar user tidak stuck
            return redirect()->route('student.dashboard')->with('error', 'Terjadi masalah saat menyimpan, namun jawaban Anda sudah aman.');
        }
    }

    public function render()
    {
        return view('livewire.student.exam-page');
    }
}
