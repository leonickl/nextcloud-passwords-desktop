<x-app>

    <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6">
        @forelse($passwords as $password)
            <div class="border-b last:border-none py-4">
                <div class="text-lg font-semibold text-gray-800">{{ $password['label'] }}</div>
                <div class="text-sm text-gray-600">{{ $password['username'] }}</div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-4">No passwords found.</div>
        @endforelse
    </div>

</x-app>
