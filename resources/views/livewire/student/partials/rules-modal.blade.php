{{-- FILE: resources/views/livewire/student/partials/rules-modal.blade.php --}}

<div x-show="showRules"
     style="display: none;"
     class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[85vh] flex flex-col overflow-hidden"
         @click.away="showRules = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4">

        {{-- HEADER MODAL --}}
        <div class="bg-blue-600 px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                TATA TERTIB UJIAN
            </h2>
            <button @click="showRules = false" class="text-white hover:bg-blue-700 p-1 rounded-full transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- ISI MODAL (SCROLLABLE) --}}
        <div class="p-6 overflow-y-auto custom-scrollbar">
            <div class="prose prose-slate max-w-none text-slate-700">
                <p class="mb-4 text-sm text-slate-500 italic">Harap membaca aturan berikut dengan seksama sebelum mengerjakan ujian.</p>

                <h3 class="font-bold text-lg mb-2 text-slate-800">1. Persiapan Umum</h3>
                <ul class="list-disc list-outside ml-5 space-y-1 mb-4">
                    <li>Peserta wajib login menggunakan akun yang telah diverifikasi.</li>
                    <li>Pastikan koneksi internet stabil selama pengerjaan ujian.</li>
                    <li>Dilarang membuka tab browser lain atau aplikasi kalkulator/bantuan lainnya (sistem akan mendeteksi aktivitas mencurigakan).</li>
                </ul>

                <h3 class="font-bold text-lg mb-2 text-slate-800">2. Pengerjaan Soal</h3>
                <ul class="list-disc list-outside ml-5 space-y-1 mb-4">
                    <li>Waktu ujian berjalan mundur otomatis (Countdown). Waktu tetap berjalan meskipun browser ditutup.</li>
                    <li>Jawaban tersimpan otomatis setiap kali Anda memilih opsi atau berpindah soal.</li>
                    <li>Soal <strong>Essay</strong> harus diisi dengan jawaban yang jelas dan relevan.</li>
                    <li>Tombol "Selesai Ujian" hanya muncul di nomor soal terakhir.</li>
                </ul>

                <h3 class="font-bold text-lg mb-2 text-slate-800">3. Pelanggaran & Sanksi</h3>
                <ul class="list-disc list-outside ml-5 space-y-1 mb-4">
                    <li>Segala bentuk kecurangan (joki, contekan, penggunaan AI) akan berakibat diskualifikasi.</li>
                    <li>Peserta yang terdeteksi melakukan aktivitas mencurigakan akan dicatat dalam log sistem.</li>
                </ul>
            </div>
        </div>

        {{-- FOOTER MODAL --}}
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex justify-end">
            <button @click="showRules = false" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow transition transform hover:-translate-y-0.5">
                Saya Mengerti
            </button>
        </div>
    </div>
</div>
