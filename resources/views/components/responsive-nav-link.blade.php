@props(['active'])

@php
$classes = ($active ?? false)
            ? 'nav-item is-active'
            : 'nav-item';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
