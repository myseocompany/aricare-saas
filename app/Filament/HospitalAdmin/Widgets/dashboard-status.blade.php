<div class="space-y-4">
    @foreach ($stateCounts as $state => $count)
        <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="text-lg font-semibold">{{ $state }}</h3>
            <p class="text-gray-600">Total: {{ $count }}</p>
        </div>
    @endforeach
</div>
