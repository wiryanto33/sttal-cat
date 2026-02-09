<div class="min-h-screen flex flex-row-reverse">

    <div class="w-full lg:w-1/2 flex items-center justify-center bg-white p-8">
        <div class="w-full max-w-md">
            <div class="mb-10 text-center">
                <a href="/" class="inline-flex items-center gap-2 mb-4 group">
                    <div>
                        <img src="{{ asset('storage/' . $siteSetting->logo_path) }}"
                            class="h-16 md:h-20 w-auto object-contain drop-shadow-lg" alt="Logo">
                    </div>
                    <span class="text-xl font-bold text-slate-900 tracking-wider">STTAL CAT</span>
                </a>
                <h2 class="text-3xl font-bold text-slate-900">Selamat Datang</h2>
                <p class="text-slate-500 mt-2">Silakan login untuk mengakses ujian.</p>
            </div>

            <form wire:submit.prevent="login" class="space-y-6">

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">NRP

                    </label>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input type="text" wire:model="nrp"
                            class="w-full pl-10 px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-900 outline-none uppercase"
                            placeholder="Contoh: 12345/P">
                    </div>
                    @error('nrp')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-medium text-slate-700">Password</label>
                    </div>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </div>
                        <input type="password" wire:model="password"
                            class="w-full pl-10 px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-900 outline-none"
                            placeholder="******">
                    </div>
                    @error('password')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" wire:model="remember" id="remember"
                        class="w-4 h-4 text-blue-900 border-gray-300 rounded focus:ring-blue-900">
                    <label for="remember" class="ml-2 block text-sm text-slate-600">Ingat Saya</label>
                </div>

                <button type="submit" wire:loading.attr="disabled"
                    class="w-full py-3 bg-blue-900 hover:bg-blue-800 text-white font-bold rounded-lg shadow-lg ...">

                    <span wire:loading.remove wire:target="login">MASUK</span>

                    <span wire:loading wire:target="login">Memproses...</span>
                </button>
            </form>

            <p class="mt-8 text-center text-sm text-slate-600">
                Belum memiliki akun?
                <a href="{{ route('register') }}" class="font-bold text-blue-900 hover:underline">Daftar Sekarang</a>
            </p>
        </div>
    </div>

    <div class="hidden lg:block w-1/2 bg-slate-800 relative overflow-hidden">
        <img src="{{ $siteSetting->login_image_path ? asset('storage/' . $siteSetting->login_image_path) : 'path/to/default-image.jpg' }}"
            class="absolute inset-0 w-full h-full object-cover opacity-60 mix-blend-overlay" alt="Navy Team">

        <div class="absolute bottom-0 left-0 p-12 text-white z-10">
            <h3 class="text-4xl font-bold mb-4">Integritas & Kualitas</h3>
            <p class="text-lg text-slate-300 max-w-md">
                Mengutamakan kejujuran dan kemampuan akademis dalam setiap tahapan seleksi.
            </p>
        </div>

        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-blue-500 rounded-full blur-3xl opacity-20"></div>
    </div>

</div>
