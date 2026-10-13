@props(['name', 'size' => 20])

<svg aria-hidden="true" width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" {{ $attributes->merge(['class' => 'shrink-0']) }}>{!! config('icons.'.$name, '') !!}</svg>
