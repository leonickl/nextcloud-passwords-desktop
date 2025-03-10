<x-app>

    <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6">
        <div class="flex flex.row gap-10 mb-5">
            <input type="search" placeholder="Search" id="search"
                   class="w-full border px-5 py-2 rounded shadow bg-gray-50">

            <div class="flex flex-row text-nowrap gap-0 items-center">
                <span id="count-shown">{{ count($passwords) }}</span>
                <span>&nbsp;/&nbsp;{{ count($passwords) }}</span>
            </div>
        </div>

        <div id="passwords">
            @forelse($passwords as $password)
                <div class="{{ $loop->last ?: 'border-b' }} last:border-none py-4 flex justify-between items-center">
                    <div>
                        <a data-type="label"
                           class="text-lg font-semibold text-gray-700"
                           href="{{ route('password', $password['id']) }}">{{ $password['label'] }}</a>
                        <div data-type="username" class="text-sm text-gray-600">{{ $password['username'] }}</div>
                    </div>

                    <a data-type="folder"
                       class="text-xs font-medium text-white {{ $password['color'] }} px-2 py-1 rounded"
                       href="{{ route('folders', $password['folder_id']) }}">
                        {{ $password['folder'] }}
                    </a>
                </div>
            @empty
                <div class="text-center text-gray-500 py-4">No passwords found.</div>
            @endforelse
        </div>
    </div>

    <script>
        const search = document.querySelector('#search')
        const passwords = document.querySelector('#passwords')
        const countShown = document.querySelector('#count-shown')

        search.addEventListener('keyup', function () {
            const term = search.value?.toLowerCase()

            let shown = 0

            for (const password of passwords.children) {
                const label = password.querySelector('[data-type="label"]').innerText
                const username = password.querySelector('[data-type="username"]').innerText
                const folder = password.querySelector('[data-type="folder"]').innerText

                const found = label.toLowerCase().includes(term)
                    || username.toLowerCase().includes(term)
                    || folder.toLowerCase().includes(term);

                if (found) {
                    shown++
                }

                password.style.display = found ? 'flex' : 'none'
            }

            countShown.innerHTML = shown
        })
    </script>

</x-app>
