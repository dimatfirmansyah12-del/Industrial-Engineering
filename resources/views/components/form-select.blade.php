@props([
    'label',
    'name',
    'options' => [],
    'value' => '',
    'placeholder' => 'Pilih Data',
])

<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }}
    </label>

    <select
        name="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'
        ]) }}
    >
        <option value="">{{ $placeholder }}</option>

        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
