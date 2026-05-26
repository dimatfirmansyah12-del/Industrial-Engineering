<x-dashboard-layout>
            <x-page-header
                title="Master Data Department"
                subtitle="Kelola daftar department untuk request Industrial Engineering"
            >
                <a href="{{ route('departments.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium">
                    + Tambah Department
                </a>
            </x-page-header>

            <div class="p-8">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 text-green-700 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="bg-white rounded-xl shadow p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left text-gray-500">
                                    <th class="py-3 px-3">No</th>
                                    <th class="py-3 px-3">Nama Department</th>
                                    <th class="py-3 px-3">Code</th>
                                    <th class="py-3 px-3">Status</th>
                                    <th class="py-3 px-3">Description</th>
                                    <th class="py-3 px-3">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($departments as $department)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-3">
                                            {{ $departments->firstItem() + $loop->index }}
                                        </td>

                                        <td class="py-3 px-3 font-semibold">
                                            {{ $department->name }}
                                        </td>

                                        <td class="py-3 px-3">
                                            {{ $department->code ?? '-' }}
                                        </td>

                                        <td class="py-3 px-3">
                                            @if ($department->status === 'Active')
                                                <span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                                    Active
                                                </span>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-700">
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>

                                        <td class="py-3 px-3">
                                            {{ $department->description ?? '-' }}
                                        </td>

                                        <td class="py-3 px-3">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('departments.edit', $department->id) }}"
                                                    class="text-yellow-600 hover:underline">
                                                    Edit
                                                </a>

                                                <form action="{{ route('departments.destroy', $department->id) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus department ini?')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="text-red-600 hover:underline">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-gray-400">
                                            Belum ada data department.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $departments->links() }}
                    </div>
                </div>
            </div>
</x-dashboard-layout>
