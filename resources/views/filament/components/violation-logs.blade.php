<div class="space-y-4 py-4">
    @if($logs->isEmpty())
        <div class="text-center py-6">
            <p class="text-gray-500 italic">Tidak ada log pelanggaran untuk sesi ini.</p>
        </div>
    @else
        <div class="relative border-l-2 border-gray-200 ml-3 space-y-6">
            @foreach($logs->sortByDesc('detected_at') as $log)
                <div class="mb-5 ml-6">
                    <span class="absolute flex items-center justify-center w-6 h-6 bg-red-100 rounded-full -left-3 ring-8 ring-white">
                        <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-red-600"/>
                    </span>

                    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase">
                                {{ str_replace('_', ' ', $log->violation_type) }}
                            </h3>
                            <time class="text-xs font-mono text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                {{ $log->detected_at->format('H:i:s') }}
                            </time>
                        </div>
                        <p class="text-sm text-gray-600 italic">
                            "{{ $log->description }}"
                        </p>
                        <div class="mt-2 text-[10px] text-gray-400">
                            {{ $log->detected_at->format('d M Y') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
