<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $nrp; // Ganti $email jadi $nrp
    public $password;
    public $remember = false;

    protected $rules = [
        'nrp' => 'required',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();
        $cleanNRP = preg_replace('/[^A-Za-z0-9]/', '', $this->nrp);

        if (Auth::attempt(['username' => $cleanNRP, 'password' => $this->password], $this->remember)) {

            // AMBIL DATA USER & CANDIDATE
            $user = Auth::user();

            // Cek apakah user ini adalah peserta ujian (punya data candidate)
            if ($user->candidate) {

                // CEK STATUS
                if ($user->candidate->status === 'pending') {
                    Auth::logout(); // Tendang keluar
                    $this->addError('nrp', 'Akun Anda sedang diverifikasi oleh Admin. Harap tunggu.');
                    return;
                }

                if ($user->candidate->status === 'rejected') {
                    Auth::logout();
                    $this->addError('nrp', 'Maaf, Pendaftaran Anda ditolak. Hubungi panitia.');
                    return;
                }
            }

            session()->regenerate();
            return redirect()->route('student.dashboard');
        }

        $this->addError('nrp', 'NRP atau password salah.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
