@props(['name', 'label', 'options' => []])

<div class="form-field">
    @if ($label)
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    <select id="{{ $name }}" name="{{ $name }}" {{ $attributes }}>
        <option value="">Selecione</option>
        @foreach ($options as $value => $text)
            <option value="{{ $value }}" @selected(old($name) === $value)>
                {{ $text }}
            </option>
        @endforeach
    </select>
    <x-error-message field="{{ $name }}" />
</div>
