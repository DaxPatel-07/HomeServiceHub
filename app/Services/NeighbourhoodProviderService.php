<?php

namespace App\Services;

use App\Models\Provider;
use App\Models\User;
use Illuminate\Support\Collection;

class NeighbourhoodProviderService
{
    /**
     * Known geographical coordinates for prominent localities in Ahmedabad and Gandhinagar.
     */
    protected array $knownLocalities = [
        'satellite'     => ['lat' => 23.0304, 'lng' => 72.5178, 'city' => 'Ahmedabad', 'locality' => 'Satellite'],
        'sg highway'    => ['lat' => 23.0338, 'lng' => 72.5085, 'city' => 'Ahmedabad', 'locality' => 'SG Highway'],
        's.g. highway'  => ['lat' => 23.0338, 'lng' => 72.5085, 'city' => 'Ahmedabad', 'locality' => 'SG Highway'],
        'bodakdev'      => ['lat' => 23.0373, 'lng' => 72.5120, 'city' => 'Ahmedabad', 'locality' => 'Bodakdev'],
        'prahlad nagar' => ['lat' => 23.0120, 'lng' => 72.5108, 'city' => 'Ahmedabad', 'locality' => 'Prahlad Nagar'],
        'vastrapur'     => ['lat' => 23.0350, 'lng' => 72.5293, 'city' => 'Ahmedabad', 'locality' => 'Vastrapur'],
        'bopal'         => ['lat' => 23.0345, 'lng' => 72.4646, 'city' => 'Ahmedabad', 'locality' => 'Bopal'],
        'thaltej'       => ['lat' => 23.0550, 'lng' => 72.5080, 'city' => 'Ahmedabad', 'locality' => 'Thaltej'],
        'navrangpura'   => ['lat' => 23.0365, 'lng' => 72.5611, 'city' => 'Ahmedabad', 'locality' => 'Navrangpura'],
        'ashram road'   => ['lat' => 23.0330, 'lng' => 72.5700, 'city' => 'Ahmedabad', 'locality' => 'Ashram Road'],
        'maninagar'     => ['lat' => 22.9978, 'lng' => 72.6033, 'city' => 'Ahmedabad', 'locality' => 'Maninagar'],
        'chandkheda'    => ['lat' => 23.1118, 'lng' => 72.5852, 'city' => 'Ahmedabad', 'locality' => 'Chandkheda'],
        'motera'        => ['lat' => 23.0980, 'lng' => 72.5975, 'city' => 'Ahmedabad', 'locality' => 'Motera'],
        'gota'          => ['lat' => 23.0977, 'lng' => 72.5453, 'city' => 'Ahmedabad', 'locality' => 'Gota'],
        'chandlodia'    => ['lat' => 23.0760, 'lng' => 72.5400, 'city' => 'Ahmedabad', 'locality' => 'Chandlodia'],
        'naranpura'     => ['lat' => 23.0520, 'lng' => 72.5530, 'city' => 'Ahmedabad', 'locality' => 'Naranpura'],
        'paldi'         => ['lat' => 23.0130, 'lng' => 72.5620, 'city' => 'Ahmedabad', 'locality' => 'Paldi'],
        'nikol'         => ['lat' => 23.0480, 'lng' => 72.6720, 'city' => 'Ahmedabad', 'locality' => 'Nikol'],
        'sector 21'     => ['lat' => 23.2324, 'lng' => 72.6479, 'city' => 'Gandhinagar', 'locality' => 'Sector 21'],
        'sector 1-30'   => ['lat' => 23.2250, 'lng' => 72.6500, 'city' => 'Gandhinagar', 'locality' => 'Sector 1-30'],
        'infocity'      => ['lat' => 23.1932, 'lng' => 72.6300, 'city' => 'Gandhinagar', 'locality' => 'Infocity'],
        'kudasan'       => ['lat' => 23.1850, 'lng' => 72.6280, 'city' => 'Gandhinagar', 'locality' => 'Kudasan'],
        'gift city'     => ['lat' => 23.1610, 'lng' => 72.6840, 'city' => 'Gandhinagar', 'locality' => 'GIFT City'],
        'sargasan'      => ['lat' => 23.1900, 'lng' => 72.6050, 'city' => 'Gandhinagar', 'locality' => 'Sargasan'],
        'randesan'      => ['lat' => 23.1750, 'lng' => 72.6200, 'city' => 'Gandhinagar', 'locality' => 'Randesan'],
    ];

    /**
     * Calculate geographical distance in kilometers between two points using the Haversine formula.
     */
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusKm = 6371;

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadiusKm * $c, 2);
    }

    /**
     * Extract structured location metadata from user record and optional overrides.
     */
    public function resolveCustomerLocation(User $customer, array $options = []): array
    {
        $hasBrowserLat = isset($options['lat']) && is_numeric($options['lat']);
        $hasBrowserLng = isset($options['lng']) && is_numeric($options['lng']);

        $lat = $hasBrowserLat ? (float) $options['lat'] : ($customer->latitude ? (float) $customer->latitude : null);
        $lng = $hasBrowserLng ? (float) $options['lng'] : ($customer->longitude ? (float) $customer->longitude : null);

        $building = !empty($options['building']) ? trim($options['building']) : ($customer->building ? trim($customer->building) : null);
        $locality = !empty($options['locality']) ? trim($options['locality']) : ($customer->locality ? trim($customer->locality) : null);
        $city = !empty($options['city']) ? trim($options['city']) : ($customer->city ? trim($customer->city) : null);
        $address = trim((string) $customer->address);

        // Intelligently parse building and locality from address if not explicitly set
        if ($address !== '') {
            $parsed = $this->parseAddressString($address);
            if (!$building && !empty($parsed['building'])) {
                $building = $parsed['building'];
            }
            if (!$locality && !empty($parsed['locality'])) {
                $locality = $parsed['locality'];
            }
            if (!$city && !empty($parsed['city'])) {
                $city = $parsed['city'];
            }
        }

        // If coordinates are missing, attempt lookup from resolved locality
        if (($lat === null || $lng === null) && $locality) {
            $matchedCoord = $this->lookupLocalityCoordinates($locality);
            if ($matchedCoord) {
                $lat = $matchedCoord['lat'];
                $lng = $matchedCoord['lng'];
                if (!$city) {
                    $city = $matchedCoord['city'];
                }
            }
        }

        $source = $hasBrowserLat && $hasBrowserLng ? 'browser' : (($customer->latitude && $customer->longitude) ? 'profile_coords' : 'address');

        // Human-readable summary label
        $labelParts = array_filter([$building, $locality, $city]);
        $label = !empty($labelParts) ? implode(', ', $labelParts) : ($address ?: ($city ?: 'Location not set'));

        $isMissing = ($lat === null || $lng === null) && empty($locality) && empty($building) && empty($address);

        return [
            'is_missing' => $isMissing,
            'building'   => $building,
            'locality'   => $locality,
            'city'       => $city,
            'lat'        => $lat,
            'lng'        => $lng,
            'address'    => $address,
            'label'      => $label,
            'source'     => $source,
        ];
    }

    /**
     * Resolve provider coordinates and locality.
     */
    public function resolveProviderLocation(Provider $provider): array
    {
        $lat = $provider->latitude ? (float) $provider->latitude : null;
        $lng = $provider->longitude ? (float) $provider->longitude : null;
        $building = $provider->building ? trim($provider->building) : null;
        $locality = $provider->locality ? trim($provider->locality) : null;
        $serviceArea = trim((string) $provider->service_area);
        $userCity = $provider->user->city ?? null;

        // If no coordinates, try to lookup from provider locality or service_area
        if ($lat === null || $lng === null) {
            if ($locality) {
                $coords = $this->lookupLocalityCoordinates($locality);
                if ($coords) {
                    $lat = $coords['lat'];
                    $lng = $coords['lng'];
                }
            }
            if (($lat === null || $lng === null) && $serviceArea !== '') {
                // Check if any known locality is in service area
                foreach ($this->knownLocalities as $key => $locData) {
                    if (stripos($serviceArea, $key) !== false) {
                        $lat = $locData['lat'];
                        $lng = $locData['lng'];
                        if (!$locality) {
                            $locality = $locData['locality'];
                        }
                        break;
                    }
                }
            }
        }

        return [
            'lat'          => $lat,
            'lng'          => $lng,
            'building'     => $building,
            'locality'     => $locality,
            'service_area' => $serviceArea,
            'city'         => $userCity,
        ];
    }

    /**
     * Retrieve eligible nearby trusted providers for the customer.
     */
    public function getNearbyTrustedProviders(User $customer, array $options = []): array
    {
        $radiusKm = isset($options['radius']) && is_numeric($options['radius'])
            ? (float) $options['radius']
            : (float) config('neighbourhood.default_radius_km', 5.0);

        $customerLoc = $this->resolveCustomerLocation($customer, $options);

        // If customer has no location information at all
        if ($customerLoc['is_missing']) {
            return [
                'status'            => 'missing_location',
                'providers'         => collect([]),
                'customer_location' => $customerLoc,
                'radius'            => $radiusKm,
                'radius_options'    => config('neighbourhood.radius_options', [2, 5, 10, 15, 25]),
                'total_matched'     => 0,
            ];
        }

        // Fetch verified, active providers (prefer neighborhood trusted)
        $candidates = Provider::with(['user', 'category', 'badges'])
            ->where('verification_status', 'verified')
            ->where('is_active', true)
            ->get();

        $matched = [];

        foreach ($candidates as $provider) {
            $providerLoc = $this->resolveProviderLocation($provider);

            $distanceKm = null;
            $distanceText = null;
            $matchType = null;
            $matchBadge = null;
            $sortPriority = 999;

            // 1. Check Same Building / Society match
            if (!empty($customerLoc['building'])) {
                $cBuilding = mb_strtolower($customerLoc['building']);
                $pBuilding = mb_strtolower((string) $providerLoc['building']);
                $pServiceArea = mb_strtolower((string) $providerLoc['service_area']);

                if (($pBuilding !== '' && (str_contains($pBuilding, $cBuilding) || str_contains($cBuilding, $pBuilding)))
                    || str_contains($pServiceArea, $cBuilding)) {
                    $matchType = 'same_building';
                    $matchBadge = 'Same Building/Society';
                    $sortPriority = 1;
                }
            }

            // 2. Check Geographical Distance (if both coordinates exist)
            if ($customerLoc['lat'] !== null && $customerLoc['lng'] !== null &&
                $providerLoc['lat'] !== null && $providerLoc['lng'] !== null) {
                
                $d = $this->calculateDistance($customerLoc['lat'], $customerLoc['lng'], $providerLoc['lat'], $providerLoc['lng']);
                $distanceKm = $d;
                $distanceText = $d < 1 ? round($d * 1000) . ' m away' : sprintf('%.1f km away', $d);

                if ($d <= $radiusKm) {
                    if ($matchType === null) {
                        $matchType = 'nearby_radius';
                        $matchBadge = $distanceText;
                        $sortPriority = 2;
                    }
                } else {
                    // Outside radius: check if provider's recorded service area explicitly covers customer area
                    $explicitCoverage = $this->checkServiceAreaCoverage($providerLoc['service_area'], $customerLoc);
                    if ($explicitCoverage) {
                        if ($matchType === null) {
                            $matchType = 'service_coverage';
                            $matchBadge = 'Serves your area';
                            $sortPriority = 3;
                        }
                    } else {
                        // Beyond radius and does not explicitly serve the customer's area -> EXCLUDE
                        continue;
                    }
                }
            }

            // 3. Fallback when coordinates are unavailable: Check Locality / Service Coverage Match
            if ($matchType === null) {
                $explicitCoverage = $this->checkServiceAreaCoverage($providerLoc['service_area'], $customerLoc);
                if ($explicitCoverage) {
                    $matchType = 'same_neighbourhood';
                    $matchBadge = 'Serves your neighbourhood';
                    $sortPriority = 4;
                }
            }

            // If no match criteria met, provider is excluded
            if ($matchType === null) {
                continue;
            }

            // Set dynamic attributes on provider model for presentation
            $provider->distance_km = $distanceKm;
            $provider->distance_text = $distanceText;
            $provider->match_type = $matchType;
            $provider->match_badge = $matchBadge;
            $provider->sort_priority = $sortPriority;

            $matched[] = $provider;
        }

        // Sort results:
        // Prioritize by actual distance first (when available), followed by match tier and skill score
        usort($matched, function ($a, $b) {
            // If both have calculated distance, sort strictly by distance
            if ($a->distance_km !== null && $b->distance_km !== null) {
                if (abs($a->distance_km - $b->distance_km) > 0.05) {
                    return $a->distance_km <=> $b->distance_km;
                }
            } elseif ($a->distance_km !== null) {
                return -1;
            } elseif ($b->distance_km !== null) {
                return 1;
            }

            // Match priority tier (building = 1, nearby_radius = 2, service_coverage = 3, neighbourhood = 4)
            if ($a->sort_priority !== $b->sort_priority) {
                return $a->sort_priority <=> $b->sort_priority;
            }

            // Secondary: neighborhood trusted badge
            $aTrusted = $a->isNeighborhoodTrusted() ? 1 : 0;
            $bTrusted = $b->isNeighborhoodTrusted() ? 1 : 0;
            if ($aTrusted !== $bTrusted) {
                return $bTrusted <=> $aTrusted;
            }

            // Tertiary: skill score descending
            return $b->skill_score <=> $a->skill_score;
        });

        $matchedCollection = collect($matched);

        return [
            'status'            => $matchedCollection->isEmpty() ? 'no_matches' : 'found',
            'providers'         => $matchedCollection,
            'customer_location' => $customerLoc,
            'radius'            => $radiusKm,
            'radius_options'    => config('neighbourhood.radius_options', [2, 5, 10, 15, 25]),
            'total_matched'     => $matchedCollection->count(),
        ];
    }

    /**
     * Check if provider's recorded service area explicitly covers the customer's locality or building.
     */
    protected function checkServiceAreaCoverage(?string $serviceArea, array $customerLoc): bool
    {
        if (empty($serviceArea)) {
            return false;
        }

        $serviceAreaLower = mb_strtolower($serviceArea);

        // Check customer building
        if (!empty($customerLoc['building'])) {
            $buildingLower = mb_strtolower($customerLoc['building']);
            if (str_contains($serviceAreaLower, $buildingLower)) {
                return true;
            }
        }

        // Check customer locality
        if (!empty($customerLoc['locality'])) {
            $localityLower = mb_strtolower($customerLoc['locality']);
            if (str_contains($serviceAreaLower, $localityLower)) {
                return true;
            }
        }

        // Check tokens from customer address (excluding generic state/country/city names)
        if (!empty($customerLoc['address'])) {
            $tokens = array_map('trim', explode(',', $customerLoc['address']));
            foreach ($tokens as $token) {
                $tLower = mb_strtolower($token);
                if (strlen($tLower) > 3 && !in_array($tLower, ['india', 'gujarat', 'ahmedabad', 'gandhinagar', 'flat', 'house', 'plot', 'road'])) {
                    if (str_contains($serviceAreaLower, $tLower)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Parse raw address string to infer building, locality, and city.
     */
    protected function parseAddressString(string $address): array
    {
        $parts = array_map('trim', explode(',', $address));
        $count = count($parts);

        $building = null;
        $locality = null;
        $city = null;

        // Check city
        foreach ($parts as $p) {
            $pLower = mb_strtolower($p);
            if (str_contains($pLower, 'ahmedabad')) {
                $city = 'Ahmedabad';
            } elseif (str_contains($pLower, 'gandhinagar')) {
                $city = 'Gandhinagar';
            }
        }

        // Detect known localities
        $addressLower = mb_strtolower($address);
        foreach ($this->knownLocalities as $key => $locData) {
            if (str_contains($addressLower, $key)) {
                $locality = $locData['locality'];
                if (!$city) {
                    $city = $locData['city'];
                }
                break;
            }
        }

        // Building / Society inference (usually first or second token if it contains society, heights, tower, etc.)
        if ($count >= 2) {
            foreach ($parts as $part) {
                if (preg_match('/(heights|platinum|green|bunglows|residency|apartments|enclave|society|park|villas|palace|arcade|complex|flat|house|plot)/i', $part)) {
                    $building = $part;
                    break;
                }
            }
        }

        return [
            'building' => $building,
            'locality' => $locality,
            'city'     => $city,
        ];
    }

    /**
     * Lookup known locality coordinates.
     */
    protected function lookupLocalityCoordinates(string $locality): ?array
    {
        $clean = mb_strtolower(trim($locality));
        return $this->knownLocalities[$clean] ?? null;
    }
}
