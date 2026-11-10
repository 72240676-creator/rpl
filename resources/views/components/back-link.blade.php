@props([
    'href',
    'label' => 'Kembali ke Dashboard',
])

<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'app-back-link',
        'style' => 'display: inline-flex; align-items: center; gap: 8px; color: #64748b; font-size: 14px; font-weight: 500; line-height: 1.5; text-decoration: none;',
    ]) }}
>
    <span aria-hidden="true">←</span>
    <span>{{ $label }}</span>
</a>
