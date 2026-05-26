<x-dashboard-layout>
    <x-page-header
        title="Edit Role User"
        subtitle="Ubah role akses user"
    >
        <a href="{{ route('users.index') }}"
            class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg font-medium">
            Kembali
        </a>
    </x-page-header>

    <div class="p-8">
        <div class="bg-white rounded-xl shadow p-6">
            @if ($errors->any())
                <div class="mb-6 bg-red-100 text-red-700 px-4 py-3 rounded-lg">
                    <p class="font-bold mb-2">Ada data yang belum benar:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" value="{{ $user->name }}" readonly
                            class="w-full rounded-lg border-gray-300 bg-gray-100 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" value="{{ $user->email }}" readonly
                            class="w-full rounded-lg border-gray-300 bg-gray-100 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <select name="role"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>
                                    {{ $role }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <x-form-input
                        label="Position / Jabatan"
                        name="position"
                        value="{{ $user->position }}"
                        placeholder="Section Head, Department Head, Division Head"
                    />
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <a href="{{ route('users.index') }}"
                        class="px-6 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium">
                        Batal
                    </a>

                    <button type="submit"
                        class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium">
                        Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-dashboard-layout>
