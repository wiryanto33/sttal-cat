{{-- Inisialisasi Alpine Data dengan nilai dari PHP Livewire --}}
<div class="min-h-screen bg-slate-50 flex flex-col" x-data="{ showRules: @json($shouldShowRules) }">

    <nav class="bg-slate-900 text-white shadow-lg sticky top-0 z-40 py-2">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between min-h-[4rem]">
                <div class="flex items-center gap-4">
                    {{-- LOGO DINAMIS --}}
                    <div class="flex-shrink-0">
                        <img src="{{ $siteSetting->logo_path ? asset('storage/' . $siteSetting->logo_path) : 'https://via.placeholder.com/150' }}"
                            class="h-14 md:h-16 w-auto object-contain drop-shadow-md transition hover:scale-105"
                            alt="Logo">
                    </div>

                    {{-- JUDUL APLIKASI --}}
                    <div class="flex flex-col">
                        <span class="font-bold tracking-wider text-lg leading-tight">CAT DASHBOARD</span>
                        <span
                            class="text-[10px] text-yellow-500 font-semibold tracking-widest uppercase opacity-80 hidden md:block">
                            Sekolah Tinggi Teknologi Angkatan Laut
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-4">

                    {{-- TOMBOL TATA TERTIB --}}
                    <button @click="showRules = true"
                        class="text-slate-300 hover:text-white text-sm font-medium flex items-center gap-1 transition px-2 py-1 rounded hover:bg-slate-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="hidden md:inline">Tata Tertib</span>
                    </button>

                    {{-- PEMBATAS VERTIKAL --}}
                    <div class="h-8 w-px bg-slate-700 mx-1 hidden md:block"></div>

                    {{-- PROFIL USER --}}
                    <div class="text-right hidden md:block group relative">
                        <a href="{{ route('student.profile') }}"
                            class="block hover:text-blue-300 transition cursor-pointer" title="Klik untuk edit profil">
                            <p class="text-sm font-bold flex items-center justify-end gap-1">
                                {{ $user->name }}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-slate-400"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </p>
                            <p class="text-xs text-slate-300">
                                {{ $user->candidate->pangkat ?? '-' }} {{ $user->candidate->korps ?? '' }}
                            </p>
                        </a>
                    </div>

                    {{-- TOMBOL KELUAR --}}
                    <button wire:click="logout"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md transition transform hover:-translate-y-0.5 border border-red-500">
                        Keluar
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

        {{-- JIKA BELUM DI-APPROVE --}}
        @if (!$candidate || $candidate->status !== 'approved')
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Akun Anda sedang dalam proses verifikasi. Silakan hubungi panitia jika belum aktif dalam 24
                            jam.
                        </p>
                    </div>
                </div>
            </div>

            {{-- JIKA SUDAH APPROVED --}}
        @else
            <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Paket Ujian Tersedia</h1>
                    <p class="text-slate-500">Pilih paket ujian di bawah ini untuk mulai mengerjakan.</p>
                </div>
                {{-- Tombol Mobile Only --}}
                <button @click="showRules = true"
                    class="md:hidden w-full bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-bold text-sm">
                    Baca Tata Tertib Ujian
                </button>
            </div>

            @if (count($packets) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($packets as $packet)
                        <div
                            class="bg-white rounded-xl shadow-sm hover:shadow-md transition duration-300 border border-slate-200 overflow-hidden relative group">

                            {{-- BADGE STATUS --}}
                            @if ($packet['is_finished'])
                                <div
                                    class="absolute top-0 right-0 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-bl-xl shadow-sm z-10">
                                    SELESAI
                                </div>
                            @elseif ($packet['is_ongoing'])
                                <div
                                    class="absolute top-0 right-0 bg-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-bl-xl shadow-sm z-10 animate-pulse">
                                    SEDANG MENGERJAKAN
                                </div>
                            @endif

                            <div class="p-6">
                                {{-- KATEGORI --}}
                                <div class="flex items-center gap-2 mb-3">
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $packet['category'] }}
                                    </span>
                                    <span class="text-xs text-slate-400 flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $packet['duration'] ?? 90 }} Menit
                                    </span>
                                </div>

                                {{-- JUDUL --}}
                                <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-blue-600 transition">
                                    {{ $packet['title'] }}
                                </h3>

                                {{-- JADWAL --}}
                                <div class="text-sm text-slate-500 mb-6 flex items-start gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 mt-0.5"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Dijadwalkan: <br> <span
                                            class="font-semibold text-slate-700">{{ $packet['date'] }}</span></span>
                                </div>

                                {{-- TOMBOL AKSI --}}
                                <div class="mt-auto">
                                    @if ($packet['is_finished'])
                                        <button disabled
                                            class="w-full block py-3 bg-slate-100 text-slate-400 text-center font-bold rounded-lg cursor-not-allowed border border-slate-200">
                                            SUDAH DIKERJAKAN
                                        </button>
                                    @elseif ($packet['is_ongoing'])
                                        <a href="{{ route('exam.show', $packet['id']) }}" wire:navigate
                                            class="w-full block py-3 bg-yellow-500 hover:bg-yellow-400 text-white text-center font-bold rounded-lg shadow-lg shadow-yellow-500/30 transition transform hover:-translate-y-0.5">
                                            LANJUTKAN UJIAN
                                        </a>
                                    @elseif (!$packet['is_open'])
                                        <button disabled
                                            class="w-full block py-3 bg-slate-200 text-slate-500 text-center font-bold rounded-lg cursor-not-allowed">
                                            <div class="flex items-center justify-center gap-2">
                                                BELUM DIBUKA
                                            </div>
                                        </button>
                                    @else
                                        <a href="{{ route('exam.show', $packet['id']) }}" wire:navigate
                                            class="w-full block py-3 bg-blue-600 hover:bg-blue-500 text-white text-center font-bold rounded-lg shadow-lg shadow-blue-600/30 transition transform hover:-translate-y-0.5">
                                            KERJAKAN SEKARANG
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-xl border-2 border-dashed border-slate-300">
                    <p class="mt-4 text-slate-500 text-lg">Belum ada paket ujian yang tersedia.</p>
                </div>
            @endif
        @endif
    </main>

    <footer class="bg-white border-t py-6 text-center text-slate-400 text-sm">
        &copy; 2026 STTAL CAT System.
    </footer>

    {{-- ========================================== --}}
    {{-- PANGGIL MODAL DARI FILE TERPISAH DI SINI   --}}
    {{-- ========================================== --}}

    @include('livewire.student.partials.rules-modal')

</div>
