<div class="min-h-screen bg-slate-50 flex flex-col">

    <nav class="bg-slate-900 text-white shadow-lg sticky top-0 z-50 py-2">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between min-h-[4rem]">

                <div class="flex items-center gap-4 cursor-pointer group"
                    onclick="window.location='{{ route('student.dashboard') }}'" title="Kembali ke Dashboard">

                    {{-- LOGO DINAMIS --}}
                    <div class="flex-shrink-0 transition transform group-hover:scale-105">
                        <img src="{{ $siteSetting->logo_path ? asset('storage/' . $siteSetting->logo_path) : 'https://via.placeholder.com/150' }}"
                            class="h-12 md:h-14 w-auto object-contain drop-shadow-md" alt="Logo">
                    </div>

                    {{-- JUDUL HALAMAN --}}
                    <div class="flex flex-col">
                        <span
                            class="font-bold tracking-wider text-lg leading-tight group-hover:text-blue-300 transition">
                            EDIT PROFIL
                        </span>
                        <span
                            class="text-[10px] text-yellow-500 font-semibold tracking-widest uppercase opacity-80 hidden md:block">
                            {{ $siteSetting->site_name ?? 'CAT System' }}
                        </span>
                    </div>
                </div>

                <div>
                    <a href="{{ route('student.dashboard') }}"
                        class="flex items-center gap-2 text-slate-300 hover:text-white text-sm font-medium transition bg-slate-800 hover:bg-slate-700 px-4 py-2 rounded-lg border border-slate-700 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span class="hidden md:inline">Kembali ke Dashboard</span>
                        <span class="md:hidden">Kembali</span>
                    </a>
                </div>

            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Edit Profil Peserta</h1>
            <p class="text-slate-500">Pastikan data militer dan nomor ujian Anda valid.</p>
        </div>

        @if (session()->has('success'))
            <div
                class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex justify-between items-center">
                <div>
                    <span class="font-bold">Berhasil!</span> {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <form wire:submit="save" class="p-6 md:p-8">

                <div class="grid grid-cols-1 md:grid-cols-12 gap-8">

                    <div
                        class="md:col-span-4 flex flex-col items-center text-center border-r-0 md:border-r border-slate-100 pr-0 md:pr-8">
                        <div class="relative group">
                            {{-- Container Foto --}}
                            <div
                                class="w-40 h-52 bg-slate-200 rounded-lg overflow-hidden shadow-md mb-4 flex items-center justify-center border-2 border-slate-300">
                                @if ($photo)
                                    {{-- Preview Foto Baru --}}
                                    <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                @elseif ($existingPhoto)
                                    {{-- Foto Lama dari Storage --}}
                                    <img src="{{ asset('storage/' . $existingPhoto) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    {{-- Placeholder Kosong --}}
                                    <div class="text-slate-400 flex flex-col items-center">
                                        <svg class="w-12 h-12 mb-2" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-xs">Belum ada foto</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Input File --}}
                            <label for="photo-upload"
                                class="cursor-pointer bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-lg text-sm font-bold transition inline-block shadow-lg shadow-blue-500/30">
                                Upload Foto Resmi
                                <input type="file" id="photo-upload" wire:model="photo" class="hidden"
                                    accept="image/png, image/jpeg, image/jpg">
                            </label>
                        </div>

                        <div wire:loading wire:target="photo"
                            class="text-xs text-blue-500 mt-2 font-bold animate-pulse">
                            Sedang mengupload...
                        </div>
                        @error('photo')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror

                        <div class="bg-blue-50 text-blue-800 text-xs p-3 rounded mt-4 text-left w-full">
                            <strong>Ketentuan Foto:</strong>
                            <ul class="list-disc ml-4 mt-1 space-y-1">
                                <li>Pakaian Dinas Harian (PDH)</li>
                                <li>Latar belakang warna (Merah/Biru)</li>
                                <li>Format JPG/PNG, Maks 2MB</li>
                            </ul>
                        </div>
                    </div>

                    <div class="md:col-span-8 space-y-6">

                        <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4">Data Identitas & Akun</h3>

                        {{-- Nama & Email --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Nama Lengkap</label>
                                <input type="text" wire:model="name"
                                    class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                @error('name')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">NRP</label>
                                <input type="text" wire:model="nrp"
                                    class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 font-mono">
                                @error('nrp')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            {{-- <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Email</label>
                                <input type="email" wire:model="email"
                                    class="w-full rounded-lg border-slate-300 bg-slate-100 text-slate-500 cursor-not-allowed"
                                    readonly>
                            </div> --}}
                        </div>

                        {{-- No Ujian & NRP --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Nomor Ujian (Manual)</label>
                                <input type="text" wire:model="exam_number" placeholder="Contoh: 123-ABC-2026"
                                    class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 font-mono font-bold text-slate-800">
                                @error('exam_number')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4 mt-6">Data Militer</h3>

                        {{-- Pangkat & Korps --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Pangkat</label>
                                <input type="text" wire:model="pangkat" placeholder="Contoh: Lettu"
                                    class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                @error('pangkat')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Korps</label>
                                <input type="text" wire:model="korps" placeholder="Contoh: Pelaut (P)"
                                    class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                @error('korps')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Satuan & Jabatan --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Satuan</label>
                                <input type="text" wire:model="satuan"
                                    class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                @error('satuan')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Jabatan Terakhir</label>
                                <input type="text" wire:model="jabatan_terakhir"
                                    class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                @error('jabatan_terakhir')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="pt-6 border-t border-slate-100 flex justify-end gap-3 mt-4">
                            <a href="{{ route('student.dashboard') }}"
                                class="px-5 py-2.5 bg-white border border-slate-300 text-slate-600 font-bold rounded-lg hover:bg-slate-50 transition">
                                Batal
                            </a>
                            <button type="submit"
                                class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-500 shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                                <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                                <span wire:loading wire:target="save">Menyimpan...</span>
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-white border-t py-6 text-center text-slate-400 text-sm">
        &copy; 2026 STTAL CAT System.
    </footer>
</div>
