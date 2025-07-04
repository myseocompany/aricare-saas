<x-filament-widgets::widget>
    <div class="flex gap-4">
        @foreach ($statusCounts as $status => $count)
            <div class="fi-wi-stats-overview-stat relative items-center rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 flex justify-between">
                <div class="grid gap-y-2 w-full">
                    <div class="flex items-center gap-x-2">
                        <span class="fi-wi-stats-overview-stat-label text-sm font-bold text-gray-500 dark:text-gray-400">
                            {{ $status }}
                        </span>
                    </div>
                    <div class="text-right text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">
                        {{ $count }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
