<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Provider;
use App\Models\ServiceCategory;
use App\Models\Review;
use App\Models\Badge;
use Tests\TestCase;

class NeighbourTrustedProvidersTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $providerUser;
    protected Provider $provider;
    protected ServiceCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = ServiceCategory::create([
            'name' => 'Electrician',
            'slug' => 'electrician',
            'description' => 'Certified electrical services',
            'icon' => 'fas fa-bolt',
            'color' => '#F59E0B',
            'is_active' => true,
        ]);

        $this->customer = User::create([
            'name' => 'Kavita Patel',
            'email' => 'kavita@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'phone' => '9876543210',
            'city' => 'Ahmedabad',
            'address' => 'Flat 501, Iscon Heights, Satellite, Ahmedabad',
        ]);

        $this->providerUser = User::create([
            'name' => 'Ramesh Sharma',
            'email' => 'ramesh@provider.com',
            'password' => bcrypt('password123'),
            'role' => 'provider',
            'phone' => '9876543211',
            'city' => 'Ahmedabad',
        ]);

        $this->provider = Provider::create([
            'user_id' => $this->providerUser->id,
            'category_id' => $this->category->id,
            'business_name' => 'Ramesh Electrical Works',
            'bio' => 'Master electrician with over 8 years experience in residential wiring and installations.',
            'experience_years' => 8,
            'service_area' => 'Satellite, Bodakdev, SG Highway',
            'hourly_rate' => 450,
            'average_rating' => 4.9,
            'total_jobs' => 40,
            'completed_jobs' => 38,
            'skill_score' => 92,
            'verification_status' => 'verified',
            'is_available' => true,
            'is_active' => true,
        ]);

        // Create a completed booking
        $booking = \App\Models\Booking::create([
            'booking_code' => \App\Models\Booking::generateCode(),
            'customer_id' => $this->customer->id,
            'provider_id' => $this->provider->id,
            'category_id' => $this->category->id,
            'booking_date' => now()->subDays(2)->format('Y-m-d'),
            'booking_time' => '10:00',
            'address' => 'Flat 501, Iscon Heights, Satellite, Ahmedabad',
            'city' => 'Ahmedabad',
            'service_charge' => 450,
            'total_amount' => 495,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'completed',
        ]);

        // Add a review
        Review::create([
            'booking_id' => $booking->id,
            'customer_id' => $this->customer->id,
            'provider_id' => $this->provider->id,
            'rating' => 5,
            'comment' => 'Outstanding service! Fixed all switchboards very quickly and cleanly.',
            'is_visible' => true,
        ]);
    }

    public function test_homepage_as_logged_out_visitor_shows_only_neighbour_trusted_providers_with_preview_cards(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // Section title must say Neighbour Trusted Providers
        $response->assertSee('Neighbour');
        $response->assertSee('Trusted Providers');

        // Must show brief introduction
        $response->assertSee('Discover community-endorsed, background-checked professionals serving your local neighbourhood');

        // Must show preview card details (Category, community badges, lock prompt, Protected Rate)
        $response->assertSee('Verified Electrician Pro');
        $response->assertSee('Neighbour Choice');
        $response->assertSee('Protected Rate');
        $response->assertSee('View Details');
        $response->assertSee('Sign in to unlock full name, rates &amp; reviews', false);

        // Must NOT expose full provider business name or bio to logged-out visitors
        $response->assertDontSee('Ramesh Electrical Works');
        $response->assertDontSee('Master electrician with over 8 years experience');

        // Must NOT show the Popular Providers section to logged-out visitors
        $response->assertDontSee('Popular <span>Providers</span>', false);
    }

    public function test_logged_out_visitor_direct_url_access_to_provider_details_redirects_to_login(): void
    {
        $response = $this->get(route('provider.show', $this->provider));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('info', 'Please log in to view full provider details.');
    }

    public function test_logged_out_visitor_api_request_returns_401_unauthenticated(): void
    {
        // JSON request to /provider/{provider}
        $jsonResponse = $this->getJson(route('provider.show', $this->provider));
        $jsonResponse->assertStatus(401);
        $jsonResponse->assertJson([
            'error' => 'Unauthenticated',
            'message' => 'Please log in to view full provider details.'
        ]);

        // Dedicated API route /api/providers/{provider}
        $apiResponse = $this->getJson(route('api.provider.show', $this->provider));
        $apiResponse->assertStatus(401);
        $apiResponse->assertJson([
            'error' => 'Unauthenticated',
            'message' => 'Please log in to view full provider details.'
        ]);
    }

    public function test_customer_after_login_can_view_full_details_of_neighbour_trusted_provider(): void
    {
        $response = $this->actingAs($this->customer)->get(route('provider.show', $this->provider));

        $response->assertStatus(200);

        // Full provider details are visible
        $response->assertSee('Ramesh Electrical Works');
        $response->assertSee('Master electrician with over 8 years experience');
        $response->assertSee('8 Years');
        $response->assertSee('38'); // completed jobs
        $response->assertSee('4.9'); // rating
        $response->assertSee('₹450'); // hourly rate
        $response->assertSee('Satellite, Bodakdev, SG Highway');
        $response->assertSee('Outstanding service! Fixed all switchboards very quickly and cleanly.');
        $response->assertSee('Kavita Patel');
        $response->assertSee('Book Now');
    }

    public function test_logged_out_visitor_clicking_view_details_is_redirected_to_provider_details_after_login(): void
    {
        // 1. Logged-out visitor accesses provider details directly (simulating clicking "View Details")
        $initialResponse = $this->get(route('provider.show', $this->provider));
        $initialResponse->assertRedirect(route('login'));

        // 2. Visitor submits valid customer login
        $loginResponse = $this->post(route('login.post'), [
            'email' => $this->customer->email,
            'password' => 'password123',
        ]);

        // 3. User is redirected to the intended provider profile
        $loginResponse->assertRedirect(route('provider.show', $this->provider));

        // 4. Following the redirect displays the provider full profile
        $profileResponse = $this->actingAs($this->customer)->get(route('provider.show', $this->provider));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('Ramesh Electrical Works');
    }

    public function test_homepage_after_customer_login_shows_full_provider_details_and_popular_providers(): void
    {
        $response = $this->actingAs($this->customer)->get('/');
        $response->assertStatus(200);

        // Full details on the Neighbour Trusted Providers cards
        $response->assertSee('Ramesh Electrical Works');
        $response->assertSee('₹450/hr');

        // Popular Providers section is visible when authenticated
        $response->assertSee('Popular <span>Providers</span>', false);
    }

    public function test_authenticated_api_request_returns_full_provider_details(): void
    {
        $response = $this->actingAs($this->customer)->getJson(route('api.provider.show', $this->provider));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'provider' => [
                'id' => $this->provider->id,
                'name' => 'Ramesh Electrical Works',
                'bio' => 'Master electrician with over 8 years experience in residential wiring and installations.',
                'category' => 'Electrician',
                'experience_years' => 8,
                'hourly_rate' => 450,
                'average_rating' => 4.9,
            ]
        ]);
    }
}
