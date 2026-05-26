<x-dashboard-layout>
            <x-page-header
                title="Edit Line / Area"
                subtitle="Perbarui data line produksi atau area kerja"
            >
                <a href="{{ route('line-areas.index') }}"
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

                    <form action="{{ route('line-areas.update', $lineArea->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Line / Area</label>
                                <input type="text" name="name" value="{{ old('name', $lineArea->name) }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Code</label>
                                <input type="text" name="code" value="{{ old('code', $lineArea->code) }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                <select name="department"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->name }}" {{ old('department', $lineArea->department) == $department->name ? 'selected' : '' }}>
                                            {{ $department->name }}
                                            @if ($department->code)
                                                - {{ $department->code }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="Active" {{ old('status', $lineArea->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ old('status', $lineArea->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="3"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $lineArea->description) }}</textarea>
                        </div>

                        <div class="mt-8 flex justify-end gap-3">
                            <a href="{{ route('line-areas.index') }}"
                                class="px-6 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium">
                                Batal
                            </a>

                            <button type="submit"
                                class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium">
                                Update Line / Area
                            </button>
                        </div>
                    </form>

                </div>
            </div>
</x-dashboard-layout>
