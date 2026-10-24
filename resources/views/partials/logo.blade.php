@php
    $variant = $variant ?? 'full';
    $dark = $dark ?? false;
    $height = $height ?? 38;
    $class = $class ?? '';
    $link = $link ?? true;
    $alt = $alt ?? 'HomeServiceHub';

    $src = match(true) {
        $variant === 'icon' && $dark => asset('images/homeservicehub-icon-dark.svg'),
        $variant === 'icon' => asset('images/homeservicehub-icon.svg'),
        $dark => asset('images/homeservicehub-logo-dark.svg'),
        default => asset('images/homeservicehub-logo.svg'),
    };
@endphp

@if($link)
<a href="{{ route('home') }}" class="d-inline-flex align-items-center text-decoration-none {{ $class }}" aria-label="{{ $alt }}">
    <img src="{{ $src }}" alt="{{ $alt }}" height="{{ $height }}" class="brand-logo-img" style="object-fit:contain;">
</a>
@else
<img src="{{ $src }}" alt="{{ $alt }}" height="{{ $height }}" class="brand-logo-img {{ $class }}" style="object-fit:contain;">
@endif
