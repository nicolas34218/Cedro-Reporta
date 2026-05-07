@props(['field'])

@error($field)
    <small class="form-error">{{ $message }}</small>
@enderror
