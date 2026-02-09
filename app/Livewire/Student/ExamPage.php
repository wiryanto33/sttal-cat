<?php

namespace App\Livewire\Student;

use Livewire\Component;
use App\Models\ExamPacket;
use App\Models\ExamSession;
use App\Models\ExamAnswer;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Carbon\Carbon;

class ExamPage extends Component
{
    public $packet;
    public $session;
    public $remainingTime;

    public $questionIds = [];
    public $currentQuestionIndex = 0;
    public $answers = [];

    protected $listeners = ['timerExpired' => 'finishExam'];

    public function mount($packetId)
    {
        $user = Auth::user();
        if (!$user || !$user->candidate) {
            return redirect()->route('student.dashboard')->with('error', 'Profil tidak ditemukan.');
        }

        $this->packet = ExamPacket::findOrFail($packetId);

        // Ambil ID soal urut dari ID terkecil
        $this->questionIds = $this->packet->questions()->orderBy('id')->pluck('id')->toArray();

        if (empty($this->questionIds)) {
            return redirect()->route('student.dashboard')->with('error', 'Paket soal kosong.');
        }

        // Buat atau Ambil Sesi Ujian
        $this->session = ExamSession::firstOrCreate(
            ['candidate_id' => $user->candidate->id, 'exam_packet_id' => $this->packet->id],
            [
                'status' => ExamSession::STATUS_ONGOING,
                'start_time' => now(),
                'total_score' => 0
            ]
        );

        // Jika status sudah selesai (2 atau 3), tendang keluar
        if ($this->session->status >= 2) {
            return redirect()->route('student.dashboard')->with('error', 'Anda sudah menyelesaikan ujian ini.');
        }

        // Load Jawaban Lokal
        $this->answers = ExamAnswer::where('exam_session_id', $this->session->id)
            ->pluck('answer', 'question_id')
            ->toArray();

        // 2. LOGIKA ANTI-RESET (PENTING!)
        // Hitung durasi berdasarkan Waktu Mulai di Database vs Waktu Sekarang

        $durasiMenit = $this->packet->duration_minutes ?? 90;
        $jatahDetik = $durasiMenit * 60;

        // Pastikan start_time dibaca sebagai Carbon Object
        $waktuMulai = Carbon::parse($this->session->start_time);

        // Sudah berjalan berapa detik sejak klik mulai pertama kali?
        $detikBerjalan = now()->diffInSeconds($waktuMulai);

        // Sisa Waktu = Jatah - Berjalan
        // (int) memaksa jadi angka bulat agar JS tidak bingung
        $this->remainingTime = (int) max(0, $jatahDetik - $detikBerjalan);
    }

    #[Computed]
    public function currentQuestion()
    {
        $currentId = $this->questionIds[$this->currentQuestionIndex] ?? null;
        return $currentId ? Question::find($currentId) : null;
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->questionIds) - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function prevQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function goToQuestion($index)
    {
        if (isset($this->questionIds[$index])) {
            $this->currentQuestionIndex = $index;
        }
    }

    public function saveAnswer($questionId, $answer)
    {
        $this->answers[$questionId] = $answer;

        $question = Question::find($questionId);
        if (!$question) return;

        // Auto-grade jika Pilihan Ganda
        $isCorrect = false;
        $score = 0;

        if ($question->type === 'multiple_choice') {
            $key = strtoupper(trim($question->correct_answer));
            $ans = strtoupper(trim($answer));
            $isCorrect = ($key === $ans);
            $score = $isCorrect ? ($question->weight ?? 1) : 0;
        }

        ExamAnswer::updateOrCreate(
            ['exam_session_id' => $this->session->id, 'question_id' => $questionId],
            ['answer' => $answer, 'is_correct' => $isCorrect, 'score' => $score]
        );
    }

    public function finishExam()
    {
        // 1. Cek Apakah ada soal ESSAY di paket ini?
        $hasEssay = $this->packet->questions()->where('type', 'essay')->exists();

        // 2. Hitung Nilai Pilihan Ganda (TPA) yang sudah pasti
        $nilaiMultipleChoice = ExamAnswer::where('exam_session_id', $this->session->id)
            ->whereHas('question', fn($q) => $q->where('type', 'multiple_choice'))
            ->sum('score');

        // 3. Tentukan Status & Nilai Akhir
        if ($hasEssay) {
            // KASUS CAMPURAN (PG + ESSAY)
            // Nilai Essay masih 0 (karena belum dikoreksi admin)
            // Status jadi "Menunggu Koreksi"

            $this->session->update([
                'status' => ExamSession::STATUS_WAITING_CORRECTION, // Status 2
                'end_time' => now(),
                'score_tpa_aggregate' => $nilaiMultipleChoice, // Simpan nilai PG
                'score_essay_aggregate' => 0, // Belum ada nilai
                'total_score' => $nilaiMultipleChoice // Total sementara (cuma PG)
            ]);

            $pesan = 'Ujian selesai. Jawaban Essay Anda akan diperiksa oleh penguji.';

        } else {
            // KASUS FULL PILIHAN GANDA
            // Nilai langsung FINAL

            $this->session->update([
                'status' => ExamSession::STATUS_FINISHED, // Status 3 (Final)
                'end_time' => now(),
                'score_tpa_aggregate' => $nilaiMultipleChoice,
                'score_essay_aggregate' => 0,
                'total_score' => $nilaiMultipleChoice
            ]);

            $pesan = 'Ujian selesai. Nilai Akhir Anda: ' . $nilaiMultipleChoice;
        }

        session()->flash('success', $pesan);
        return redirect()->route('student.dashboard');
    }

    public function render()
    {
        return view('livewire.student.exam-page');
    }
}
