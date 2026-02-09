<div class="min-h-screen bg-slate-100 p-6">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-4 gap-6">

        <div class="lg:col-span-3">
            <div
                class="bg-white shadow-sm rounded-xl p-5 mb-4 flex justify-between items-center border-l-8 border-blue-600 sticky top-4 z-20">
                <div>
                    <h2 class="text-sm font-bold text-slate-400 uppercase tracking-widest">Soal Nomor</h2>
                    <div class="text-2xl font-black text-slate-800">
                        {{ $currentQuestionIndex + 1 }} <span class="text-slate-300">/ {{ count($questionIds) }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Sisa Waktu</p>

                    <div wire:ignore>
                        <div id="timer" class="text-3xl font-mono font-bold text-slate-600">
                            Loading...
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm min-h-[500px] flex flex-col relative">

                <div wire:loading wire:target="nextQuestion, prevQuestion, goToQuestion"
                    class="absolute inset-0 bg-white/80 z-10 flex items-center justify-center backdrop-blur-sm rounded-2xl">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                </div>

                @if ($this->currentQuestion)
                    <div class="prose max-w-none mb-8 text-slate-800 text-lg">
                        {!! $this->currentQuestion->content !!}
                    </div>

                    @if ($this->currentQuestion->type === 'multiple_choice' && !empty($this->currentQuestion->options))
                        <div class="space-y-3">
                            @foreach ($this->currentQuestion->options as $key => $option)
                                @php
                                    $isSelected =
                                        isset($answers[$this->currentQuestion->id]) &&
                                        $answers[$this->currentQuestion->id] == $key;
                                @endphp

                                <div wire:click="saveAnswer({{ $this->currentQuestion->id }}, '{{ $key }}')"
                                    wire:loading.class.remove="hover:bg-slate-50"
                                    wire:loading.class="opacity-50 cursor-wait"
                                    class="p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 flex items-center group
                                     {{ $isSelected ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-blue-300 hover:bg-slate-50' }}">

                                    <div
                                        class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm mr-4 border-2 transition-colors
                                        {{ $isSelected
                                            ? 'bg-blue-500 border-blue-500 text-white'
                                            : 'bg-white border-slate-300 text-slate-500 group-hover:border-blue-400 group-hover:text-blue-500' }}">
                                        {{ $key }}
                                    </div>

                                    <div class="text-slate-700 font-medium text-lg flex-1">
                                        {{ $option }}
                                    </div>

                                    <div wire:loading
                                        wire:target="saveAnswer({{ $this->currentQuestion->id }}, '{{ $key }}')">
                                        <svg class="animate-spin h-5 w-5 text-blue-600"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($this->currentQuestion->type === 'essay')
                        <div class="mt-4">
                            <label class="block mb-2 font-bold text-slate-700">Jawaban Essay:</label>
                            <textarea wire:model.blur="answers.{{ $this->currentQuestion->id }}"
                                wire:change="saveAnswer({{ $this->currentQuestion->id }}, $event.target.value)" rows="6"
                                class="w-full p-4 border-2 border-slate-300 rounded-xl focus:border-blue-500 focus:ring-0 transition"
                                placeholder="Tulis jawaban Anda di sini..."></textarea>
                        </div>
                    @endif
                @else
                    <div class="text-center py-20 text-slate-400">
                        Soal tidak ditemukan.
                    </div>
                @endif

                <div class="mt-auto pt-8 flex justify-between items-center border-t border-slate-100 mt-8">
                    <button wire:click="prevQuestion" @if ($currentQuestionIndex === 0) disabled @endif
                        class="px-6 py-3 rounded-lg font-bold flex items-center gap-2 transition
                            {{ $currentQuestionIndex === 0 ? 'bg-slate-100 text-slate-300 cursor-not-allowed' : 'bg-white border-2 border-slate-200 text-slate-600 hover:border-blue-500 hover:text-blue-600' }}">
                        ← Sebelumnya
                    </button>

                    @if ($currentQuestionIndex === count($questionIds) - 1)
                        <button onclick="confirmFinish()"
                            class="px-8 py-3 bg-green-600 text-white rounded-lg font-bold hover:bg-green-500 shadow-lg hover:shadow-green-500/30 transition transform hover:-translate-y-1">
                            Selesai Ujian
                        </button>
                    @else
                        <button wire:click="nextQuestion"
                            class="px-8 py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-500 shadow-lg hover:shadow-blue-500/30 transition transform hover:-translate-y-1">
                            Selanjutnya →
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white shadow-sm rounded-xl p-5 sticky top-4">
                <h3 class="font-bold text-slate-700 mb-4 border-b pb-2">Navigasi Soal</h3>

                <div class="grid grid-cols-5 gap-2">
                    @foreach ($questionIds as $index => $qId)
                        @php
                            $isAnswered = isset($answers[$qId]) && $answers[$qId] !== null && $answers[$qId] !== '';
                            $isActive = $index === $currentQuestionIndex;
                        @endphp

                        <button wire:click="goToQuestion({{ $index }})"
                            class="aspect-square rounded-lg font-bold text-sm flex items-center justify-center transition border-2
                            {{ $isActive
                                ? 'bg-blue-600 text-white border-blue-600 scale-110 shadow-md ring-2 ring-blue-200'
                                : ($isAnswered
                                    ? 'bg-green-100 text-green-700 border-green-200 hover:border-green-400'
                                    : 'bg-slate-50 text-slate-500 border-slate-200 hover:border-blue-300 hover:text-blue-600') }}">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>

                <div class="mt-6 space-y-2 text-xs text-slate-500 font-medium">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-blue-600 rounded"></div> Sedang Dikerjakan
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-green-100 border border-green-200 rounded"></div> Sudah Dijawab
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-slate-50 border border-slate-200 rounded"></div> Belum Dijawab
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- 1. GLOBAL VARIABLES ---
        let examFinished = false;

        // --- 2. KONFIRMASI SELESAI ---
        function confirmFinish() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Ujian akan diakhiri dan jawaban disimpan.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Selesaikan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    examFinished = true;
                    @this.call('finishExam');
                }
            })
        }

        // --- 3. LOGIKA TIMER (Anti-Refresh & Anti-Sleep) ---
        (function() {
            // Ambil waktu dari server via Blade
            const startTime = new Date("{{ $session->start_time }}").getTime();
            const durationSeconds = {{ $packet->duration_minutes * 60 }};
            const timerDisplay = document.getElementById('timer');

            function formatTime(seconds) {
                if (seconds < 0) seconds = 0;
                const h = Math.floor(seconds / 3600);
                const m = Math.floor((seconds % 3600) / 60);
                const s = seconds % 60;
                return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
            }

            function getRemainingTime() {
                const now = new Date().getTime();
                const elapsed = Math.floor((now - startTime) / 1000); // Selisih waktu sekarang vs waktu mulai db
                return Math.max(0, durationSeconds - elapsed);
            }

            // Update timer setiap detik
            const timerInterval = setInterval(() => {
                // Jika ujian sudah ditandai selesai, hentikan timer (cegah alert ganda)
                if (examFinished) {
                    clearInterval(timerInterval);
                    return;
                }

                const remainingTime = getRemainingTime();

                if (remainingTime <= 0) {
                    // WAKTU HABIS
                    clearInterval(timerInterval);
                    timerDisplay.innerText = "00:00:00";
                    timerDisplay.classList.add('text-red-600');
                    examFinished = true; // Kunci

                    Swal.fire({
                        title: 'Waktu Habis!',
                        text: 'Ujian otomatis dikumpulkan.',
                        icon: 'warning',
                        timer: 2000,
                        showConfirmButton: false,
                        allowOutsideClick: false
                    }).then(() => {
                        @this.call('finishExam');
                    });

                } else {
                    // UPDATE TAMPILAN
                    timerDisplay.innerText = formatTime(remainingTime);
                    if (remainingTime <= 300) { // 5 menit terakhir
                        timerDisplay.classList.add('text-red-500', 'animate-pulse');
                    }
                }
            }, 1000);

            // Set tampilan awal langsung (biar gak nunggu 1 detik)
            timerDisplay.innerText = formatTime(getRemainingTime());

            // Prevent Close Tab (Hanya jika belum selesai)
            window.addEventListener('beforeunload', function(e) {
                if (!examFinished && getRemainingTime() > 0) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        })();

        // --- 4. INTEGRASI MATHJAX (Disatukan agar Rapi) ---
        document.addEventListener('DOMContentLoaded', () => {
            // Fungsi helper untuk re-render
            const reRenderMath = () => {
                if (window.MathJax) {
                    window.MathJax.typesetPromise().catch((err) => console.log(err));
                }
            };

            // A. Render saat pertama kali load
            reRenderMath();

            // B. Render saat Livewire selesai update DOM (Ganti Soal)
            // Gunakan hook ini untuk Livewire 3
            if (typeof Livewire !== 'undefined') {
                Livewire.hook('morph.updated', ({
                    el,
                    component
                }) => {
                    reRenderMath();
                });
            }

            // C. Render saat navigasi SPA (jika pakai wire:navigate)
            document.addEventListener('livewire:navigated', () => {
                reRenderMath();
            });
        });
    </script>
</div>
