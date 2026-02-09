<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\ExamPacket;
use App\Models\ExamSession;

class Dashboard extends Component
{

    // Properti untuk memberitahu view apakah harus auto-open modal
    public $shouldShowRules = false;

    public function mount()
    {
        // Cek apakah user sudah melihat aturan di sesi login ini?
        if (!session()->has('has_seen_rules')) {
            $this->shouldShowRules = true;
            // Tandai sudah melihat (agar kalau refresh halaman tidak muncul lagi)
            session()->put('has_seen_rules', true);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home');
    }

    public function render()
    {
        $user = Auth::user();
        $candidate = $user->candidate;
        $examPackets = [];

        if ($candidate && $candidate->status === 'approved') {

            // Ambil Prodi ID dari Kandidat
            $selectedProdiIds = [
                $candidate->prodi_1_id,
                $candidate->prodi_2_id
            ];

            $examPackets = ExamPacket::query()
                ->where('strata_id', $candidate->strata_id)
                ->where('is_active', true)
                ->where(function ($query) use ($selectedProdiIds) {
                    $query->whereNull('prodi_id') // Paket Umum
                        ->orWhereIn('prodi_id', array_filter($selectedProdiIds)); // Paket Prodi
                })
                ->with(['examCategory'])
                ->get()
                ->map(function ($packet) use ($candidate) {
                    $now = now();

                    // Cek apakah user sudah pernah mengerjakan paket ini?
                    $session = ExamSession::where('candidate_id', $candidate->id)
                        ->where('exam_packet_id', $packet->id)
                        ->first();

                    // Tentukan Status
                    // 0 = Belum, 1 = OnGoing, 2 = Menunggu Koreksi, 3 = Selesai Final
                    $statusCode = $session ? $session->status : 0;

                    // Logic Boolean untuk View
                    $isFinished = $statusCode >= 2; // Status 2 dan 3 dianggap selesai bagi siswa
                    $isOngoing = $statusCode == 1;  // Sedang mengerjakan (bisa lanjut)

                    return [
                        'id' => $packet->id,
                        'title' => $packet->title,
                        'category' => $packet->examCategory->name ?? 'Umum',
                        'duration' => $packet->duration, // Pastikan nama kolom di DB 'duration' atau 'duration_minutes'
                        'start_time' => $packet->start_time,
                        'date' => $packet->start_time ? $packet->start_time->format('d M Y, H:i') . ' WIB' : 'Kapan saja',

                        // Data untuk UI
                        'status_code' => $statusCode,
                        'is_finished' => $isFinished,
                        'is_ongoing' => $isOngoing,
                        'is_open' => $packet->start_time ? $now->gte($packet->start_time) : true,
                    ];
                });
        }

        return view('livewire.student.dashboard', [
            'user' => $user,
            'candidate' => $candidate,
            'packets' => $examPackets
        ]);
    }
}
