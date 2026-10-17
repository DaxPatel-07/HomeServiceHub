<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Provider;
use App\Models\ServiceCategory;
use App\Models\Badge;
use Tests\TestCase;

class LocationBasedNeighbourProvidersTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceCategory $categoryElectrician;
    protected ServiceCategory $categoryPlumber;
    protected Badge $trustedBadge;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryElectrician = ServiceCategory::create([
            'name' => 'Electrician',
            'slug' => 'electrician',
            'description' => 'Certified electrical services',
            'icon' => 'fas fa-bolt',
            'color' => '#F59E0B',
            'is_active' => true,
        ]);

        $this->categoryPlumber = ServiceCategory::create([
            'name' => 'Plumber',
            'slug' => 'plumber',
            'description' => 'Pipe fitting & drainage',
            'icon' => 'fas fa-wrench',
            'color' => '#0EA5E9',
            'is_active' => true,
        ]);

        $this->trustedBadge = Badge::create([
            'name' => 'Neighbourhood Trusted',
            'slug' => 'neighbourhood-trusted',
            'description' => 'Voted most reliable pro by local neighbours',
            'icon' => 'fas fa-award',
            'color' => '#10B981',
        ]);
    }

    /**
     * Helper to create a provider with specified parameters
     */
    protected function createProvider(array $attributes = []): Provider
    {
        static $counter = 1;
        $counter++;

        $user = User::create([
            'name' => $attributes['name'] ?? "Provider User {$counter}",
            'email' => $attributes['email'] ?? "provider{$counter}@example.com",
            'password' => bcrypt('password123'),
            'role' => 'provider',
            'phone' => '98765432' . str_pad((string)$counter, 2, '0', STR_PAD_LEFT),
            'city' => $attributes['city'] ?? 'Ahmedabad',
        ]);

        $provider = Provider::create([
            'user_id' => $user->id,
            'category_id' => $attributes['category_id'] ?? $this->categoryElectrician->id,
            'business_name' => $attributes['business_name'] ?? "Business {$counter}",
            'bio' => $attributes['bio'] ?? 'Experienced professional.',
            'experience_years' => $attributes['experience_years'] ?? 5,
            'service_area' => $attributes['service_area'] ?? 'Satellite, Ahmedabad',
            'locality' => $attributes['locality'] ?? null,
            'building' => $attributes['building'] ?? null,
            'latitude' => $attributes['latitude'] ?? null,
            'longitude' => $attributes['longitude'] ?? null,
            'service_radius_km' => $attributes['service_radius_km'] ?? 10,
            'hourly_rate' => $attributes['hourly_rate'] ?? 400,
            'average_rating' => $attributes['average_rating'] ?? 4.8,
            'total_jobs' => $attributes['total_jobs'] ?? 25,
            'completed_jobs' => $attributes['completed_jobs'] ?? 24,
            'skill_score' => $attributes['skill_score'] ?? 85,
            'verification_status' => $attributes['verification_status'] ?? 'verified',
            'is_available' => $attributes['is_available'] ?? true,
            'is_active' => $attributes['is_active'] ?? true,
        ]);

        if (!empty($attributes['trusted'])) {
            $provider->badges()->attach($this->trustedBadge->id, ['awarded_at' => now()]);
        }

        return $provider;
    }

    /**
     * 1. A customer sees eligible providers in their own locality.
     */
    public function test_customer_sees_eligible_providers_in_their_own_locality(): void
    {
        $customer = User::create([
            'name' => 'Pooja Dave',
            'email' => 'pooja@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
            'locality' => 'Satellite',
            'address' => '102, Shivalik High Street, Satellite, Ahmedabad',
        ]);

        // Eligible provider in Satellite
        $localProvider = $this->createProvider([
            'business_name' => 'Satellite Spark Electricians',
            'locality' => 'Satellite',
            'service_area' => 'Satellite, Bodakdev',
            'latitude' => 23.0304,
            'longitude' => 72.5178,
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        // Provider in distant Gandhinagar Sector 21 with no Satellite coverage
        $distantProvider = $this->createProvider([
            'business_name' => 'Sector 21 Gandhinagar Power',
            'city' => 'Gandhinagar',
            'locality' => 'Sector 21',
            'service_area' => 'Sector 21, Sector 22, Gandhinagar',
            'latitude' => 23.2324,
            'longitude' => 72.6479,
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        $response = $this->actingAs($customer)->get(route('customer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Satellite Spark Electricians');
        $response->assertDontSee('Sector 21 Gandhinagar Power');
    }

    /**
     * 2. Providers in the same building or neighbourhood appear when location data supports it.
     */
    public function test_provider_in_same_building_matches_with_building_badge(): void
    {
        $customer = User::create([
            'name' => 'Rahul Verma',
            'email' => 'rahul@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
            'building' => 'Godrej Garden City',
            'locality' => 'Gota',
            'address' => 'Pine Block 402, Godrej Garden City, Gota, Ahmedabad',
        ]);

        // Same building provider
        $buildingProvider = $this->createProvider([
            'business_name' => 'Godrej Community Care Plumbing',
            'category_id' => $this->categoryPlumber->id,
            'building' => 'Godrej Garden City',
            'locality' => 'Gota',
            'service_area' => 'Godrej Garden City, Gota',
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        $response = $this->actingAs($customer)->get(route('customer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Godrej Community Care Plumbing');
        $response->assertSee('Same Building/Society');
    }

    /**
     * 3. Nearby providers are ranked by actual distance.
     */
    public function test_nearby_providers_are_ranked_by_actual_distance(): void
    {
        // Customer at Satellite coordinates (23.0304, 72.5178)
        $customer = User::create([
            'name' => 'Amit Shah',
            'email' => 'amit@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
            'locality' => 'Satellite',
            'latitude' => 23.0304,
            'longitude' => 72.5178,
            'address' => 'Satellite, Ahmedabad',
        ]);

        // Closer provider at Bodakdev (~0.9 km away)
        $closerProvider = $this->createProvider([
            'business_name' => 'Bodakdev Express Repair',
            'latitude' => 23.0373,
            'longitude' => 72.5120,
            'service_area' => 'Bodakdev, Satellite',
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        // Farther provider at Vastrapur (~1.3 km away)
        $fartherProvider = $this->createProvider([
            'business_name' => 'Vastrapur Maintenance Team',
            'latitude' => 23.0350,
            'longitude' => 72.5293,
            'service_area' => 'Vastrapur, Satellite',
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        // Even farther provider at Bopal (~5.4 km away - beyond 5km radius, not explicitly serving Satellite)
        $outsideRadiusProvider = $this->createProvider([
            'business_name' => 'South Bopal Hardware',
            'latitude' => 23.0345,
            'longitude' => 72.4646,
            'service_area' => 'South Bopal, Ghuma',
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        $response = $this->actingAs($customer)->get(route('customer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Bodakdev Express Repair');
        $response->assertSee('Vastrapur Maintenance Team');
        $response->assertDontSee('South Bopal Hardware');

        // Check relative ordering in response content: closer provider appears before farther provider
        $content = $response->getContent();
        $posCloser = strpos($content, 'Bodakdev Express Repair');
        $posFarther = strpos($content, 'Vastrapur Maintenance Team');
        $this->assertNotFalse($posCloser);
        $this->assertNotFalse($posFarther);
        $this->assertLessThan($posFarther, $posCloser, 'Closer provider should appear before farther provider.');
    }

    /**
     * 4. Providers outside the service radius are excluded unless their recorded service area explicitly covers customer area.
     */
    public function test_providers_outside_radius_are_excluded_unless_they_explicitly_serve_customer_area(): void
    {
        // Customer in Satellite (5 km default radius)
        $customer = User::create([
            'name' => 'Meera Joshi',
            'email' => 'meera@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
            'locality' => 'Satellite',
            'latitude' => 23.0304,
            'longitude' => 72.5178,
            'address' => 'Satellite, Ahmedabad',
        ]);

        // Provider at Navrangpura (~4.5 km away within 5km)
        $insideProvider = $this->createProvider([
            'business_name' => 'Navrangpura Quick Fix',
            'latitude' => 23.0365,
            'longitude' => 72.5611,
            'service_area' => 'Navrangpura, Ellisbridge',
        ]);

        // Provider at Chandkheda (~11.5 km away - outside 5 km radius, and DOES NOT cover Satellite)
        $outsideExcludedProvider = $this->createProvider([
            'business_name' => 'Chandkheda Central Services',
            'latitude' => 23.1118,
            'longitude' => 72.5852,
            'service_area' => 'Chandkheda, Motera, Sabarmati',
        ]);

        // Provider at Chandkheda (~11.5 km away - outside 5 km radius, BUT EXPLICITLY covers Satellite in service_area)
        $outsideIncludedProvider = $this->createProvider([
            'business_name' => 'Metro All-City Electricals',
            'latitude' => 23.1118,
            'longitude' => 72.5852,
            'service_area' => 'Chandkheda, Motera, Satellite, Bodakdev',
        ]);

        $response = $this->actingAs($customer)->get(route('customer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Navrangpura Quick Fix');
        $response->assertSee('Metro All-City Electricals');
        $response->assertDontSee('Chandkheda Central Services');
    }

    /**
     * 5. Inactive or unverified providers are excluded according to existing rules.
     */
    public function test_inactive_and_unverified_providers_are_excluded(): void
    {
        $customer = User::create([
            'name' => 'Sanjay Mehta',
            'email' => 'sanjay@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
            'locality' => 'Satellite',
            'latitude' => 23.0304,
            'longitude' => 72.5178,
            'address' => 'Satellite, Ahmedabad',
        ]);

        // Active and verified provider
        $validProvider = $this->createProvider([
            'business_name' => 'Verified Active Pro',
            'locality' => 'Satellite',
            'latitude' => 23.0304,
            'longitude' => 72.5178,
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        // Pending verification provider
        $pendingProvider = $this->createProvider([
            'business_name' => 'Pending Pro Services',
            'locality' => 'Satellite',
            'latitude' => 23.0304,
            'longitude' => 72.5178,
            'verification_status' => 'pending',
            'is_active' => true,
        ]);

        // Rejected verification provider
        $rejectedProvider = $this->createProvider([
            'business_name' => 'Rejected Pro Services',
            'locality' => 'Satellite',
            'latitude' => 23.0304,
            'longitude' => 72.5178,
            'verification_status' => 'rejected',
            'is_active' => true,
        ]);

        // Inactive provider
        $inactiveProvider = $this->createProvider([
            'business_name' => 'Inactive Local Technician',
            'locality' => 'Satellite',
            'latitude' => 23.0304,
            'longitude' => 72.5178,
            'verification_status' => 'verified',
            'is_active' => false,
        ]);

        $response = $this->actingAs($customer)->get(route('customer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Verified Active Pro');
        $response->assertDontSee('Pending Pro Services');
        $response->assertDontSee('Rejected Pro Services');
        $response->assertDontSee('Inactive Local Technician');
    }

    /**
     * 6. Customers with missing location information receive a helpful prompt.
     */
    public function test_customer_with_missing_location_receives_helpful_prompt(): void
    {
        $customerWithoutLocation = User::create([
            'name' => 'New User No Address',
            'email' => 'noaddress@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'address' => null,
            'locality' => null,
            'building' => null,
            'latitude' => null,
            'longitude' => null,
            'city' => null,
        ]);

        $response = $this->actingAs($customerWithoutLocation)->get(route('customer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Set Your Neighbourhood Location');
        $response->assertSee('Please enter your address, building, or allow browser location permission');
        $response->assertSee('Allow Location Access');
        $response->assertSee('Enter Address Manually');
    }

    /**
     * 7. No-match results display the appropriate message.
     */
    public function test_no_matches_displays_appropriate_friendly_message_and_radius_expansion(): void
    {
        // Customer in Kudasan, Gandhinagar
        $customer = User::create([
            'name' => 'Geeta Rana',
            'email' => 'geeta@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Gandhinagar',
            'locality' => 'Kudasan',
            'latitude' => 23.1850,
            'longitude' => 72.6280,
            'address' => 'Kudasan, Gandhinagar',
        ]);

        // Provider only in South Bopal (~23 km away, does NOT cover Kudasan)
        $distantProvider = $this->createProvider([
            'business_name' => 'Far Bopal Electrician',
            'city' => 'Ahmedabad',
            'locality' => 'Bopal',
            'latitude' => 23.0345,
            'longitude' => 72.4646,
            'service_area' => 'South Bopal, Ghuma',
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        $response = $this->actingAs($customer)->get(route('customer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('No trusted providers found near your location yet.');
        $response->assertSee('Expand Radius to 15 km');
        $response->assertSee('Expand Radius to 25 km');
        $response->assertDontSee('Far Bopal Electrician');
    }

    /**
     * 8. Customer location update endpoint saves location and updates results.
     */
    public function test_customer_can_update_location_and_persist_coordinates(): void
    {
        $customer = User::create([
            'name' => 'Vikram Trivedi',
            'email' => 'vikram@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
            'address' => null,
        ]);

        $updateResponse = $this->actingAs($customer)->post(route('customer.location.update'), [
            'building' => 'Iscon Platinum',
            'locality' => 'Bopal',
            'city' => 'Ahmedabad',
            'latitude' => 23.0345,
            'longitude' => 72.4646,
            'address' => 'A-402, Iscon Platinum, Bopal, Ahmedabad',
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success', 'Your neighbourhood location has been updated successfully!');

        $customer->refresh();
        $this->assertEquals('Iscon Platinum', $customer->building);
        $this->assertEquals('Bopal', $customer->locality);
        $this->assertEquals(23.0345, $customer->latitude);
        $this->assertEquals(72.4646, $customer->longitude);
    }

    /**
     * 9. Geolocation query parameters override and return proximity JSON when requested.
     */
    public function test_customer_dashboard_json_api_respects_browser_coordinates_and_returns_exact_distance(): void
    {
        $customer = User::create([
            'name' => 'Nidhi Shah',
            'email' => 'nidhi@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
        ]);

        $provider = $this->createProvider([
            'business_name' => 'Near Me Electricals',
            'latitude' => 23.0338,
            'longitude' => 72.5085,
            'service_area' => 'SG Highway',
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        // Customer passes browser coordinates matching SG Highway (approx 0 km)
        $response = $this->actingAs($customer)->getJson('/customer/dashboard?lat=23.0338&lng=72.5085&radius=5');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'customer_location',
            'radius',
            'total_matched',
            'providers' => [
                '*' => [
                    'id',
                    'name',
                    'category',
                    'service_area',
                    'distance_km',
                    'distance_text',
                    'match_type',
                    'match_badge',
                    'average_rating',
                    'completed_jobs',
                    'hourly_rate',
                ]
            ]
        ]);

        $data = $response->json();
        $this->assertEquals('found', $data['status']);
        $this->assertGreaterThanOrEqual(1, $data['total_matched']);
        $this->assertEquals('Near Me Electricals', $data['providers'][0]['name']);
        $this->assertNotNull($data['providers'][0]['distance_km']);
        $this->assertStringContainsString('m away', $data['providers'][0]['distance_text']);
    }
}
