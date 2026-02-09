<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Candidate;
use App\Models\Strata;
use App\Models\Prodi;
use App\Models\ExamPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Register extends Component
{
    // Data Akun
    public $nrp;
    public $password;
    public $password_confirmation;
    public $name; // Nama Lengkap

    // Data Militer
    public $pangkat;
    public $korps;
    public $satuan;
    public $exam_number; // Dari Panitia

    // Data Akademik
    public $strata_id;
    public $prodi_1_id;
    public $prodi_2_id;

    // Untuk Dropdown
    public $prodiOptions = [];

    // Validasi Real-time
    protected function rules()
    {
        return [
            'nrp' => 'required|unique:users,username|min:4',
            'name' => 'required|string|max:255',
            'password' => 'required|min:6|confirmed',
            'pangkat' => 'required',
            'korps' => 'required',
            'satuan' => 'required',
            'exam_number' => 'required|unique:candidates,exam_number',
            'strata_id' => 'required|exists:stratas,id',
            'prodi_1_id' => 'required|exists:prodis,id',

            // Gunakan aturan nullable agar boleh kosong
            'prodi_2_id' => 'nullable|exists:prodis,id|different:prodi_1_id',
        ];
    }

    // Tambahkan pesan error kustom yang elegan
    protected $messages = [
        'prodi_2_id.different' => 'Mohon maaf, Pilihan Prodi ke-2 harus berbeda dengan Prodi Utama.',
        'nrp.unique' => 'NRP ini sudah terdaftar sebelumnya.',
        'exam_number.unique' => 'Nomor Ujian ini sudah digunakan peserta lain.',
        'password.confirmed' => 'Konfirmasi password tidak sesuai.',
    ];

    // Hook: Ketika Strata Dipilih, Cari Prodinya
    public function updatedStrataId($value)
    {
        $this->prodi_1_id = null;
        $this->prodi_2_id = null;
        if ($value) {
            $this->prodiOptions = Prodi::where('strata_id', $value)->get();
        } else {
            $this->prodiOptions = [];
        }
    }

    public function register()
    {
        $this->validate();

        // Perbaikan 2: Sanitasi NRP DULUAN sebelum validasi
        // Ini agar validasi 'unique' mengecek NRP yang sudah bersih (12345P), bukan yang kotor (12345/P)
        $this->nrp = preg_replace('/[^A-Za-z0-9]/', '', $this->nrp);

        // Perbaikan 3: Konversi Prodi 2 kosong jadi null SEBELUM validasi
        if (empty($this->prodi_2_id)) {
            $this->prodi_2_id = null;
        }

        // Gunakan Database Transaction agar aman (Simpan User & Candidate bersamaan)
        DB::transaction(function () {

            // A. Cari Periode Aktif
            $activePeriod = ExamPeriod::active()->first();
            if (!$activePeriod) {
                $this->addError('nrp', 'Tidak ada gelombang ujian yang aktif saat ini.');
                return;
            }

            // B. Buat User (Login Data)
            $user = User::create([
                'name' => $this->name,
                'username' => $this->nrp, // NRP sebagai Username login
                'email' => $this->nrp . '@sttal.ac.id', // Dummy email karena tabel users butuh email
                'password' => Hash::make($this->password),
            ]);

            // C. Buat Candidate (Profile Data)
            Candidate::create([
                'user_id' => $user->id,
                'exam_period_id' => $activePeriod->id,
                'nrp' => $this->nrp, // NRP Asli (dengan format /P dll jika ada)
                'pangkat' => $this->pangkat,
                'korps' => $this->korps,
                'satuan' => $this->satuan,
                'exam_number' => $this->exam_number,
                'strata_id' => $this->strata_id,
                'prodi_1_id' => $this->prodi_1_id,
                'prodi_2_id' => $this->prodi_2_id,
                'status' => 'pending', // Menunggu Verifikasi Admin
            ]);

            // Redirect ke Home dengan Pesan Sukses
            session()->flash('success', 'Registrasi Berhasil! Akun Anda sedang dalam proses verifikasi oleh Admin. Silakan menunggu persetujuan.');

            // D. Auto Login
            Auth::login($user);

            return redirect()->route('home');
        });

        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.auth.register', [
            'stratas' => Strata::all(),
        ]);
    }
}
