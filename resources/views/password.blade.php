<x-app>

    <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6 flex flex-col gap-5">

        <div class="font-bold text-2xl">{{ $password['label'] }}</div>

        <a class="text-xs font-medium text-white {{ $password['color'] }} px-2 py-1 rounded w-max"
           href="{{ route('folders', $password['folder_id']) }}">
            {{ $password['folder'] }}
        </a>

        @if($password['username'])
            <div>{{ $password['username'] }}</div>
        @endif

        @if($password['password'])
            <div id="password" class="flex flex-row gap-5 items-center max-w-full">

                <div class="font-mono flex-1 min-w-0 overflow-ellipsis overflow-x-clip" data-type="clear" style="display: none">{{ $password['password'] }}</div>
                <div class="font-mono flex-1 min-w-0 overflow-ellipsis overflow-x-clip" data-type="hidden">{{ str_repeat('•', strlen($password['password'])) }}</div>

                <div data-type="show" class="cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                         class="bi bi-eye-fill" viewBox="0 0 16 16">
                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                        <path
                            d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                    </svg>
                </div>

                <div data-type="hide" class="cursor-pointer" style="display: none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                         class="bi bi-eye-slash-fill" viewBox="0 0 16 16">
                        <path
                            d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7 7 0 0 0 2.79-.588M5.21 3.088A7 7 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474z"/>
                        <path
                            d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12z"/>
                    </svg>
                </div>

                <div data-type="copy" class="cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                         class="bi bi-copy" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                              d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z"/>
                    </svg>
                </div>
            </div>
        @endif

        @if($password['url'])
            <a href="{{ $password['url'] }}" class="hover:text-gray-400" target="_blank">{{ $password['url'] }}</a>
        @endif

        @if($password['notes'])
            <div class="border-y py-5">{{ $password['notes'] }}</div>
        @endif

        @if($password['customFields'] && count($password['customFields']))
            {{-- TODO: implement --}}
            @json($password['customFields'])
        @endif

    </div>

    <div id="toast"
         class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg fixed bottom-5 shadow-md"
         role="alert" style="display: none">
        <strong data-type="heading" class="font-bold">Toast!</strong>
        <span data-type="message" class="block sm:inline"></span>
    </div>

    <script>

        // Toast

        const toast = document.querySelector('#toast')
        const toastHeading = toast.querySelector('[data-type="heading"]')
        const toastMessage = toast.querySelector('[data-type="message"]')

        function notify(heading, message) {
            toastHeading.innerText = heading
            toastMessage.innerText = message

            toast.style.display = 'block'

            setTimeout(() => {
                toast.style.display = 'none'
            }, 5000)
        }

        // Password

        let shown = false

        const password = document.querySelector('#password')

        const clear = password.querySelector('[data-type="clear"]')
        const hidden = password.querySelector('[data-type="hidden"]')

        const show = password.querySelector('[data-type="show"]')
        const hide = password.querySelector('[data-type="hide"]')
        const copy = password.querySelector('[data-type="copy"]')

        show.addEventListener('click', function () {
            clear.style.display = 'block'
            hidden.style.display = 'none'

            show.style.display = 'none'
            hide.style.display = 'block'
        })

        hide.addEventListener('click', function () {
            clear.style.display = 'none'
            hidden.style.display = 'block'

            show.style.display = 'block'
            hide.style.display = 'none'
        })

        copy.addEventListener('click', function () {
            navigator.clipboard.writeText(clear.innerText)
            notify("Password Copied", "The password was copied to the clipboard.")
        })

    </script>

</x-app>
