<datalist id="work-options">
    @foreach ($workOptions as $workOption)
        <option value="{{ $workOption }}"></option>
    @endforeach
</datalist>

<div class="mb-8 rounded-xl bg-white p-6 shadow">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Workshop People Today</h3>
            <p class="text-sm text-gray-500">
                Atur profil, pekerjaan hari ini, progress, dan catatan progress setiap orang.
            </p>
        </div>
        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
            PIC Workshop otomatis mengikuti nama di card
        </span>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-6">
        @foreach ($workshopPeople as $person)
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                <div class="mx-auto h-24 w-24 overflow-hidden rounded-full bg-gray-200 ring-4 ring-white">
                    @if ($person->photo)
                        <img src="{{ asset('storage/' . $person->photo) }}"
                            alt="{{ $person->name }}"
                            class="h-full w-full object-cover"
                            style="object-position: {{ $person->photo_position_x }}% {{ $person->photo_position_y }}%; transform: scale({{ $person->photo_zoom / 100 }});">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-2xl font-bold text-gray-500">
                            {{ strtoupper(substr($person->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="mt-3 text-center">
                    <h4 class="font-bold text-gray-900">{{ $person->name }}</h4>
                    <p class="mt-1 min-h-[40px] text-sm text-gray-600">
                        {{ $person->current_work ?: 'Belum ada pekerjaan' }}
                    </p>
                </div>

                <div class="mt-3">
                    <div class="mb-1 flex items-center justify-between text-xs font-semibold text-gray-500">
                        <span>Progress</span>
                        <span>{{ $person->progress_percentage }}%</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-gray-200">
                        <div class="h-full rounded-full bg-blue-600" style="width: {{ $person->progress_percentage }}%"></div>
                    </div>
                    <p class="mt-2 min-h-[36px] text-xs text-gray-500">
                        {{ $person->progress_note ?: 'Belum ada catatan progress.' }}
                    </p>
                </div>

                <details class="mt-4 rounded-lg border border-gray-200 bg-white p-3">
                    <summary class="cursor-pointer list-none text-center text-xs font-bold text-blue-700">
                        Edit Card
                    </summary>

                    <form action="{{ route('workshop-schedules.people.update', $person->id) }}" method="POST" enctype="multipart/form-data" class="mt-3 space-y-3">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-600">Nama</label>
                            <input type="text" name="name" value="{{ old('name', $person->name) }}"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-600">Foto Profil</label>
                            <input type="file" name="photo" class="w-full rounded-md border border-gray-200 bg-gray-50 p-1 text-xs">
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-gray-600">X</label>
                                <input type="number" name="photo_position_x" min="0" max="100" value="{{ old('photo_position_x', $person->photo_position_x) }}"
                                    class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-gray-600">Y</label>
                                <input type="number" name="photo_position_y" min="0" max="100" value="{{ old('photo_position_y', $person->photo_position_y) }}"
                                    class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-gray-600">Zoom</label>
                                <input type="number" name="photo_zoom" min="80" max="160" value="{{ old('photo_zoom', $person->photo_zoom) }}"
                                    class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-600">Pekerjaan</label>
                            <input type="text" name="current_work" list="work-options"
                                value="{{ old('current_work', $person->current_work) }}"
                                placeholder="Pilih dari schedule atau ketik manual"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-600">Progress (%)</label>
                            <input type="number" name="progress_percentage" min="0" max="100" value="{{ old('progress_percentage', $person->progress_percentage) }}"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-600">Catatan Progress</label>
                            <textarea name="progress_note" rows="2"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Catatan progress">{{ old('progress_note', $person->progress_note) }}</textarea>
                        </div>

                        <button type="submit"
                            class="w-full rounded-md bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                            Simpan Card
                        </button>
                    </form>
                </details>
            </div>
        @endforeach
    </div>
</div>
