<div class="min-h-screen flex flex-col bg-slate-50">

    @if (session()->has('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 sticky top-0 z-[60]" role="alert">
            <div class="flex items-center justify-between max-w-6xl mx-auto">
                <div class="flex items-center">
                    <svg class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="font-bold">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()"
                    class="text-green-700 font-bold hover:text-green-900">X</button>
            </div>
        </div>
    @endif

    <nav
        class="absolute top-0 left-0 w-full z-50 px-6 py-4 flex justify-between items-center bg-gradient-to-b from-slate-900/80 to-transparent">
        <div class="flex items-center gap-4">
            @if ($siteSetting->logo_path)
                {{-- PERBAIKAN: Ubah h-10 menjadi h-16 (64px) atau h-20 (80px) --}}
                {{-- Tambahkan 'object-contain' agar rasio gambar tidak gepeng --}}
                <img src="{{ asset('storage/' . $siteSetting->logo_path) }}"
                    class="h-16 md:h-20 w-auto object-contain drop-shadow-lg" alt="Logo">
            @else
                <div
                    class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center font-bold text-blue-900 text-xl shadow-lg">
                    S
                </div>
            @endif

            {{-- PERBAIKAN: Tambahkan text-shadow agar terbaca di background terang/gelap --}}
            <div class="flex flex-col">
                <span class="font-bold tracking-wider text-white text-lg md:text-xl drop-shadow-md leading-tight">
                    {{ $siteSetting->site_name ?? 'STTAL CAT SYSTEM' }}
                </span>
                {{-- Opsional: Tambahkan sub-text kecil jika perlu --}}
                <span class="text-[10px] text-yellow-400 font-semibold tracking-widest uppercase opacity-90">
                    Official Assessment Platform
                </span>
            </div>
        </div>
    </nav>

    <section class="relative h-[90vh] flex items-center justify-center bg-slate-900 overflow-hidden">
        {{-- Background Image --}}
        <div class="absolute inset-0 z-0">
            <img src="{{ $hero ? asset('storage/' . $hero->image_path) : 'https://images.unsplash.com/photo-1519810755548-39211615a63e?q=80' }}"
                class="w-full h-full object-cover opacity-20" alt="Hero Background">
            {{-- Tambahan Gradient Overlay agar teks makin terbaca --}}
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-slate-900/50"></div>
        </div>

        {{-- Content --}}
        {{-- UPDATE: mt-16 jadi mt-24 agar turun sedikit dari navbar --}}
        <div class="relative z-10 text-center max-w-5xl px-4 mt-24 animate-fade-in-up">

            {{-- UPDATE: Tambah 'text-white' dan 'drop-shadow-lg' --}}
            <h1 class="text-4xl md:text-6xl font-black mb-6 leading-tight text-white drop-shadow-2xl">
                {{ $hero->title ?? 'Judul Default Belum Diatur' }}
            </h1>

            <p class="text-xl md:text-2xl text-slate-300 mb-10 max-w-3xl mx-auto drop-shadow-md">
                {{ $hero->caption ?? 'Silakan atur caption melalui admin panel.' }}
            </p>

            <div class="flex flex-col md:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('student.dashboard') }}"
                        class="px-8 py-4 bg-yellow-500 text-blue-900 font-bold rounded-lg shadow-lg hover:scale-105 transition transform border-2 border-yellow-400">
                        Lanjut ke Dashboard Ujian
                    </a>
                @else
                    <a href="{{ route('register') }}"
                        class="px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg shadow-lg shadow-blue-900/50 transition transform hover:-translate-y-1">
                        Daftar Sekarang
                    </a>
                    <a href="{{ route('login') }}"
                        class="px-8 py-4 bg-transparent border-2 border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition backdrop-blur-md">
                        Sudah Punya Akun? Masuk
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <section class="relative -mt-20 z-20 px-4 mb-20">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-20">
            @foreach ($stats as $stat)
                <div
                    class="bg-white p-8 rounded-2xl shadow-xl border-b-4 border-{{ $stat->color }}-500 transform hover:-translate-y-2 transition duration-300">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-{{ $stat->color }}-100 rounded-lg text-{{ $stat->color }}-600">
                            {{-- Tampilkan Icon dari Heroicons --}}
                            @svg($stat->icon, 'w-8 h-8')
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 font-bold uppercase">{{ $stat->label }}</p>
                            <h3 class="text-3xl font-black text-slate-800">{{ $stat->value }}</h3>
                        </div>
                    </div>
                    <p class="text-slate-600">{{ $stat->description }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="py-16 px-4">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900">Kegiatan Akademik</h2>
                    <p class="text-slate-500 mt-2">Gambaran aktivitas riset dan pendidikan di STTAL.</p>
                </div>
                <a href="#" class="text-blue-600 font-semibold hover:underline">Lihat Semua →</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach ($galleries as $item)
                    <div class="group relative overflow-hidden rounded-2xl shadow-lg cursor-pointer">
                        <div class="absolute inset-0 bg-slate-900/20 group-hover:bg-slate-900/10 transition z-10"></div>
                        <img src="{{ asset('storage/' . $item->image_path) }}"
                            class="w-full h-64 object-cover transform group-hover:scale-110 transition duration-700"
                            alt="{{ $item->title }}">
                        <div
                            class="absolute bottom-0 left-0 w-full p-6 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent z-20">
                            <span class="text-yellow-400 text-xs font-bold uppercase tracking-widest mb-2 block">
                                {{ $item->event_date ? $item->event_date->format('d M Y') : 'Kegiatan' }}
                            </span>
                            <h3 class="text-white font-bold text-lg leading-tight">{{ $item->title }}</h3>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-16 bg-white border-t">
        <div class="max-w-5xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-slate-900 mb-12">Alur Pendaftaran</h2>
            <div class="relative">
                <div class="hidden md:block absolute top-8 left-0 w-full h-1 bg-slate-200 -z-10"></div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    @foreach (['Registrasi Online', 'Verifikasi Berkas', 'Ujian CAT', 'Pantukhir'] as $index => $step)
                        <div class="relative bg-white md:bg-transparent p-4">
                            <div
                                class="w-16 h-16 mx-auto bg-blue-900 text-white rounded-full flex items-center justify-center text-xl font-bold border-4 border-white shadow-lg mb-4">
                                {{ $index + 1 }}
                            </div>
                            <h3 class="font-bold text-slate-800">{{ $step }}</h3>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-slate-900 text-slate-300 py-12 mt-auto">
        <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div>
                        <img src="{{ asset('storage/' . $siteSetting->logo_path) }}"
                            class="h-16 md:h-20 w-auto object-contain drop-shadow-lg" alt="Logo">
                    </div>
                    <span class="text-xl font-bold text-white">STTAL CAT System</span>
                </div>
                <p class="text-sm opacity-70 max-w-xs">
                    Jl. Bumimoro, Morokrembangan, Kec. Krembangan, Surabaya, Jawa Timur 60178.
                </p>
            </div>
            <div class="text-right flex flex-col justify-end">
                <p class="text-sm">&copy; {{ date('Y') }} Sekolah Tinggi Teknologi Angkatan Laut.</p>
                <div class="flex gap-4 justify-end mt-4">
                    <a href="#" class="hover:text-white">Instagram</a>
                    <a href="sttal.ac.id" target="_blank" class="hover:text-white">Website Utama</a>
                </div>
            </div>
        </div>
    </footer>
</div>
