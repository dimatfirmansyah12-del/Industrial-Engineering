<x-dashboard-layout>
    <x-page-header
        title="Edit Request"
        subtitle="Perbarui data request Industrial Engineering"
    >
        <a href="{{ route('ie-requests.index') }}"
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

            <form action="{{ route('ie-requests.update', $ieRequest->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('ie-requests._form', [
                    'isEdit' => true,
                    'submitLabel' => 'Update Request',
                ])
            </form>
        </div>
    </div>
</x-dashboard-layout>
