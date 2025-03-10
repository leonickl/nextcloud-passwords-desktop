<x-app>

    <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6">

        <div class="last:border-none py-4 pb-5">
            <div class="flex flex-row gap-5 items-center">
                <div class="{{ $base_folder['color_fg'] }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                         class="bi bi-folder-fill" viewBox="0 0 16 16">
                        <path
                            d="M9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.825a2 2 0 0 1-1.991-1.819l-.637-7a2 2 0 0 1 .342-1.31L.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3m-8.322.12q.322-.119.684-.12h5.396l-.707-.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981z"/>
                    </svg>
                </div>

                <div class="text-lg font-semibold text-gray-800">{{ $base_folder['label'] }}</div>
            </div>

            <div class="text-sm text-gray-600">
                {{ $base_folder['items'] }} items

                @if($base_folder['children'])
                    , {{ $base_folder['children'] }} subfolders
                @endif
            </div>
        </div>

        <hr class="border-1 border-b-gray-600">

        @forelse($sub_folders as $folder)
            <div class="{{ $loop->last ?: 'border-b' }} border-b-gray-400 last:border-none py-4">
                <a class="flex flex-row gap-5 items-center" href="{{ route('folders', $folder['id']) }}">
                    <div class="{{ $folder['color_fg'] }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-folder-fill" viewBox="0 0 16 16">
                            <path
                                d="M9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.825a2 2 0 0 1-1.991-1.819l-.637-7a2 2 0 0 1 .342-1.31L.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3m-8.322.12q.322-.119.684-.12h5.396l-.707-.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981z"/>
                        </svg>
                    </div>

                    <div class="text-lg font-semibold text-gray-700">{{ $folder['label_short'] }}</div>
                </a>
            </div>
        @empty
            <div class="text-center text-gray-500 py-4">No subfolders found.</div>
        @endforelse

        <hr class="border-1 border-b-gray-600">

        @forelse($passwords as $password)
            <div class="{{ $loop->last ?: 'border-b' }} border-b-gray-400 last:border-none py-4 flex gap-5 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-key-fill" viewBox="0 0 16 16">
                    <path d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1-1-1 1H6.663a3.5 3.5 0 0 1-3.163 2M2.5 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                </svg>
                <div>
                    <a data-type="label"
                       class="text-lg font-semibold text-gray-700"
                       href="{{ route('password', $password['id']) }}">{{ $password['label'] }}</a>
                    <div data-type="username" class="text-sm text-gray-600">{{ $password['username'] }}</div>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-4">No passwords found.</div>
        @endforelse
    </div>

</x-app>
