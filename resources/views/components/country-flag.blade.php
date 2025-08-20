{{-- À créer dans resources/views/components/country-flag.blade.php --}}
@php
    $flags = [
        'France' => '🇫🇷',
        'Allemagne' => '🇩🇪',
        'Italie' => '🇮🇹',
        'Espagne' => '🇪🇸',
        'Royaume-Uni' => '🇬🇧',
        'États-Unis' => '🇺🇸',
        'Japon' => '🇯🇵',
        'Corée du Sud' => '🇰🇷',
        'Chine' => '🇨🇳',
        'Suède' => '🇸🇪',
        'Norvège' => '🇳🇴',
        'Pays-Bas' => '🇳🇱',
        'Belgique' => '🇧🇪',
        'Suisse' => '🇨🇭',
        'Autriche' => '🇦🇹',
        'République tchèque' => '🇨🇿',
        'Pologne' => '🇵🇱',
        'Russie' => '🇷🇺',
        'Inde' => '🇮🇳',
        'Brésil' => '🇧🇷',
        'Canada' => '🇨🇦',
        'Australie' => '🇦🇺',
        'Roumanie' => '🇷🇴',
        'Malaisie' => '🇲🇾',
    ];
@endphp

{{ $flags[$country ?? ''] ?? '🌍' }}

{{-- Utilisation dans les vues :
<x-country-flag :country="$brand->country" />
--}}