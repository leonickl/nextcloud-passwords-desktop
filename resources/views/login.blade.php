<x-app>

    <form action="{{ route('login.action') }}" method="post" class="bg-white p-6 rounded-lg shadow-md w-80">
        @csrf
        <label for="master" class="block text-gray-700 font-semibold mb-2">Master Password</label>
        <input type="password" name="master" id="master"
               class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="submit" class="w-full mt-4 bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition">
            Login
        </button>
    </form>

</x-app>
