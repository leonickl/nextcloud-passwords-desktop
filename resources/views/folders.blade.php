<x-app>

    <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6">
        @forelse($folders as $folder)
            <div class="border-b last:border-none py-4">
                <div class="text-lg font-semibold text-gray-800">{{ $folder['label'] }}</div>
                <div class="text-sm text-gray-600">{{ 0 }} subfolders, {{ 0 }} items</div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-4">No folders found.</div>
        @endforelse
    </div>

</x-app>
