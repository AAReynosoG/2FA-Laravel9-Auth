@props(['field'])

@error($field)
    <p class="text-red-500 text-xs italic">{{ $message }}</p>
@enderror
