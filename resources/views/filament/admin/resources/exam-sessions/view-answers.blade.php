<div class="space-y-6 py-4">
    @forelse($session->answers as $index => $answer)
        <div
            class="fi-ta-ctn rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900 p-6">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-white/5 pb-4 mb-4">
                <div class="flex items-center gap-3">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-sm font-bold">
                        {{ $index + 1 }}
                    </span>
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ $answer->question->type === 'essay' ? 'Soal Essay' : 'Pilihan Ganda' }}
                        </span>
                    </div>
                </div>

                @if ($answer->question->type === 'multiple_choice')
                    <div @class([
                        'flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold uppercase',
                        'bg-green-100 text-green-700' => $answer->is_correct,
                        'bg-red-100 text-red-700' => !$answer->is_correct,
                    ])>
                        {{ $answer->is_correct ? 'Benar' : 'Salah' }}
                    </div>
                @endif
            </div>

            <div class="prose dark:prose-invert max-w-none mb-6 text-gray-800 dark:text-gray-200">
                {!! $answer->question->content !!}
            </div>

            <div class="space-y-4">
                <div class="rounded-lg bg-gray-50 dark:bg-white/5 p-4 border border-gray-100 dark:border-white/10">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Jawaban Peserta:</p>
                    <div class="text-base text-gray-700 dark:text-gray-300">
                        @php
                            $jawaban = $answer->answer ?? $answer->chosen_answer;
                        @endphp

                        @if ($jawaban)
                            <span class="font-medium">{{ $jawaban }}</span>
                        @else
                            <span class="italic text-gray-400">Tidak mengisi jawaban</span>
                        @endif
                    </div>
                </div>

                @if ($answer->question->type === 'multiple_choice')
                    <div class="mt-4 flex items-center gap-2 text-xs text-gray-500">
                        <span class="font-semibold">Kunci:</span>
                        <span class="text-green-600 font-bold uppercase">{{ $answer->question->correct_answer }}</span>
                    </div>
                @elseif($answer->question->type === 'essay')
                    <div
                        class="mt-5 p-4 rounded-lg border border-green-100 bg-green-50/30 dark:border-green-500/20 dark:bg-green-500/5">
                        <p class="text-[10px] font-bold text-green-600 dark:text-green-400 uppercase mb-1">Kunci Jawaban
                            / Referensi:</p>
                        <div class="mt-2 text-sm text-gray-700 dark:text-gray-300 italic leading-relaxed">
                            {{-- Sesuaikan 'essay_answer' dengan nama kolom di database Anda --}}
                            {{ $answer->question->essay_answer ?? ($answer->question->correct_answer ?? 'Referensi jawaban tidak tersedia') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="flex flex-col items-center justify-center py-12 text-gray-500">
            <x-heroicon-o-document-magnifying-glass class="w-12 h-12 mb-4 opacity-20" />
            <p class="italic">Belum ada jawaban yang direkam untuk sesi ini.</p>
        </div>
    @endforelse
</div>
