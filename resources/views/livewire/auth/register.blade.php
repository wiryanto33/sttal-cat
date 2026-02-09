<div class="min-h-screen flex bg-slate-50">

    <div class="w-full lg:w-3/5 flex items-center justify-center p-6 lg:p-12 overflow-y-auto">
        <div class="w-full max-w-2xl">
            <div class="mb-8">
                <a href="/" class="inline-flex items-center gap-2 mb-4">
                    <div>
                        <img src="{{ asset('storage/' . $siteSetting->logo_path) }}"
                            class="h-16 md:h-20 w-auto object-contain drop-shadow-lg" alt="Logo">
                    </div>
                    <span class="text-lg font-bold text-slate-900 tracking-wider">KAMPUS STTAL</span>
                </a>
                <h2 class="text-3xl font-bold text-slate-900">Registrasi Peserta</h2>
                <p class="text-slate-500 mt-2 text-sm">Isi data sesuai dengan identitas militer dan nomor ujian dari
                    panitia.</p>
            </div>

            <form wire:submit.prevent="register" class="space-y-6">

                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="text-sm font-bold text-blue-900 uppercase tracking-wider mb-4 border-b pb-2">Data
                        Personel</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                            <input type="text" wire:model="name"
                                class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-900 outline-none"
                                placeholder="Contoh: Budi Santoso">
                            @error('name')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">NRP / NIP</label>
                            <input type="text" wire:model="nrp"
                                class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-900 outline-none"
                                placeholder="12345/P">
                            @error('nrp')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nomor Ujian (Dari
                                Panitia)</label>
                            <input type="text" wire:model="exam_number"
                                class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-900 outline-none"
                                placeholder="U-2026-XXXX">
                            @error('exam_number')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Pangkat</label>
                            <input type="text" wire:model="pangkat"
                                class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-900 outline-none"
                                placeholder="Lettu">
                            @error('pangkat')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Korps</label>
                            <input type="text" wire:model="korps"
                                class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-900 outline-none"
                                placeholder="Pelaut (P)">
                            @error('korps')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Satuan Asal</label>
                            <input type="text" wire:model="satuan"
                                class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-900 outline-none"
                                placeholder="KRI ... / Lantamal ...">
                            @error('satuan')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="text-sm font-bold text-blue-900 uppercase tracking-wider mb-4 border-b pb-2">Pilihan
                        Program Studi</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Jenjang Pendidikan
                                (Strata)</label>
                            <select wire:model.live="strata_id"
                                class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-900 outline-none bg-white">
                                <option value="">-- Pilih Jenjang --</option>
                                @foreach ($stratas as $strata)
                                    <option value="{{ $strata->id }}">{{ $strata->name }}</option>
                                @endforeach
                            </select>
                            @error('strata_id')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Pilihan Prodi 1
                                    (Utama)</label>
                                <select wire:model="prodi_1_id"
                                    class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-900 outline-none bg-white"
                                    @if (empty($prodiOptions)) disabled @endif>
                                    <option value="">-- Pilih Prodi --</option>
                                    @foreach ($prodiOptions as $prodi)
                                        <option value="{{ $prodi->id }}">{{ $prodi->name }}</option>
                                    @endforeach
                                </select>
                                @error('prodi_1_id')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                                    Pilihan Prodi 2 (Opsional)
                                </label>

                                <div class="relative">
                                    <select wire:model="prodi_2_id"
                                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 outline-none bg-white transition
                @error('prodi_2_id') border-red-500 focus:ring-red-200 @else border-slate-300 focus:ring-blue-900 @enderror"
                                        @if (empty($prodiOptions)) disabled @endif>

                                        <option value="">-- Pilih Prodi Alternatif --</option>

                                        @foreach ($prodiOptions as $prodi)
                                            <option value="{{ $prodi->id }}"
                                                @if ($prodi->id == $prodi_1_id) disabled class="bg-gray-200 text-gray-400 italic" @endif>
                                                {{ $prodi->name }}
                                                {{ $prodi->id == $prodi_1_id ? '(Sudah dipilih di Utama)' : '' }}
                                            </option>
                                        @endforeach

                                    </select>

                                    @error('prodi_2_id')
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    @enderror
                                </div>

                                @error('prodi_2_id')
                                    <div
                                        class="flex items-center gap-2 mt-2 text-red-600 bg-red-50 px-3 py-2 rounded-md border border-red-100 animate-pulse">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                            </path>
                                        </svg>
                                        <span class="text-xs font-medium">{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                        <input type="password" wire:model="password"
                            class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-900 outline-none"
                            placeholder="******">
                        @error('password')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                        <input type="password" wire:model="password_confirmation"
                            class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-900 outline-none"
                            placeholder="******">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full py-3 bg-blue-900 hover:bg-blue-800 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
                        <span wire:loading.remove>DAFTAR SEKARANG</span>
                        <span wire:loading>Memproses Data...</span>
                    </button>
                    <p class="mt-4 text-center text-sm text-slate-600">
                        Sudah punya akun? <a href="{{ route('login') }}"
                            class="font-bold text-blue-900 hover:underline">Login NRP</a>
                    </p>
                </div>

            </form>
        </div>
    </div>

    <div class="hidden lg:block w-2/5 bg-slate-900 relative">
        <img src="https://images.unsplash.com/photo-1599368146755-d6e0db659564?q=80&w=2000&auto=format&fit=crop"
            class="absolute inset-0 w-full h-full object-cover opacity-50 mix-blend-overlay" alt="Navy Ship">
        <div class="absolute inset-0 bg-gradient-to-t from-blue-900/80 to-transparent"></div>
        <div class="absolute bottom-0 left-0 p-12 text-white">
            <h3 class="text-3xl font-bold mb-2">Persiapkan Masa Depan</h3>
            <p class="text-slate-300">Pastikan data yang Anda masukkan benar dan sesuai dengan dokumen administrasi
                militer.</p>
        </div>
    </div>
</div>
