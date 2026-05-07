@props(['name', 'label', 'rows' => 4])

<div class="form-field">
    @if ($label)
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" {{ $attributes }}>{{ old($name) }}</textarea>
    <x-error-message field="{{ $name }}" />
</div>
