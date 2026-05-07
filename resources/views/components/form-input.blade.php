@props(['name', 'label', 'type' => 'text', 'value' => '', 'placeholder' => ''])

<div class="form-field">
    @if ($label)
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" 
           value="{{ $value ?: old($name) }}" 
           placeholder="{{ $placeholder }}"
           {{ $attributes }}>
    <x-error-message field="{{ $name }}" />
</div>
