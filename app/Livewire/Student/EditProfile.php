<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EditProfile extends Component
{
    use WithFileUploads;

    public $user;
    public $candidate;

    // Data Akun (Tabel Users)
    public $name;
    public $email;

    // Data Kandidat (Tabel Candidates)
    public $nrp;
    public $pangkat;
    public $korps;
    public $satuan;
    public $jabatan_terakhir;
    public $exam_number; // Nomor Ujian

    // Upload Foto
    public $photo; // File Temporary Livewire
    public $existingPhoto; // Path lama di Database

    public function mount()
    {
        $this->user = Auth::user();
        $this->candidate = $this->user->candidate;

        if (!$this->candidate) {
            return redirect()->route('student.dashboard');
        }

        // 1. Isi Data Akun
        $this->name = $this->user->name;
        $this->email = $this->user->email;

        // 2. Isi Data Militer & Ujian
        $this->nrp = $this->candidate->nrp;
        $this->pangkat = $this->candidate->pangkat;
        $this->korps = $this->candidate->korps;
        $this->satuan = $this->candidate->satuan;
        $this->jabatan_terakhir = $this->candidate->jabatan_terakhir;
        $this->exam_number = $this->candidate->exam_number;

        // 3. Foto Profil (photo_path)
        $this->existingPhoto = $this->candidate->photo_path;
    }

    public function save()
    {
        // Validasi
        $validated = $this->validate([
            // Validasi User
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->user->id)],

            // Validasi Kandidat (Unik kecuali punya sendiri)
            'nrp' => ['required', 'string', Rule::unique('candidates')->ignore($this->candidate->id)],
            'exam_number' => ['required', 'string', Rule::unique('candidates')->ignore($this->candidate->id)],

            'pangkat' => 'required|string|max:100',
            'korps' => 'required|string|max:100',
            'satuan' => 'required|string|max:100',
            'jabatan_terakhir' => 'nullable|string|max:255',

            // Validasi Foto (Maks 2MB)
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // 1. Handle Upload Foto
        $finalPhotoPath = $this->existingPhoto;

        if ($this->photo) {
            // Hapus foto lama jika ada
            if ($this->existingPhoto && Storage::disk('public')->exists($this->existingPhoto)) {
                Storage::disk('public')->delete($this->existingPhoto);
            }
            // Simpan foto baru ke folder 'candidates-photos'
            $finalPhotoPath = $this->photo->store('candidates-photos', 'public');
        }

        // 2. Update Tabel Users
        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        // 3. Update Tabel Candidates
        $this->candidate->update([
            'nrp' => $this->nrp,
            'pangkat' => $this->pangkat,
            'korps' => $this->korps,
            'satuan' => $this->satuan,
            'jabatan_terakhir' => $this->jabatan_terakhir,
            'exam_number' => $this->exam_number,
            'photo_path' => $finalPhotoPath, // Simpan path baru
        ]);

        // 4. Notifikasi
        session()->flash('success', 'Data profil berhasil diperbarui!');

        // Reset input file agar bersih
        $this->photo = null;
        $this->existingPhoto = $finalPhotoPath;
    }

    public function render()
    {
        return view('livewire.student.edit-profile');
    }
}
