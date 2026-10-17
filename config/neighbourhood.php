<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Nearby Search Radius (in Kilometers)
    |--------------------------------------------------------------------------
    |
    | Default radius used for locating nearby trusted providers.
    | Can be overridden via NEIGHBOURHOOD_SEARCH_RADIUS_KM or request input.
    |
    */
    'default_radius_km' => (float) env('NEIGHBOURHOOD_SEARCH_RADIUS_KM', 5.0),

    /*
    |--------------------------------------------------------------------------
    | Available Search Radii (in Kilometers)
    |--------------------------------------------------------------------------
    */
    'radius_options' => [2, 5, 10, 15, 25],
];
