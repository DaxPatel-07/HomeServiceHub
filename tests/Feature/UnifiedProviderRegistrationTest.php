<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Provider;
use App\Models\ServiceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnifiedProviderRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** 1. "Become a Pro" opens the unified provider registration page */
    public function test_become_a_pro_route_opens_unified_provider_registration_page(): void
    {
        $response = $this->get(route('become-provider'));
        $response->assertStatus(200);

        // Uses the unified registration card structure
        $response->assertSee('Become a Service Provider');
        $response->assertSee('Provider Application &amp; Onboarding', false);
        $response->assertSee('roleCardProvider');
        $response->assertSee('Personal &amp; Account Details', false);
        $response->assertSee('Professional &amp; Service Profile', false);
        $response->assertSee('Create Provider Account');
    }

    /** 2. Selecting "Offer services" uses the same provider registration flow */
    public function test_register_page_with_provider_query_param_opens_unified_provider_flow(): void
    {
        $response = $this->get(route('register', ['role' => 'provider']));
        $response->assertStatus(200);

        $response->assertSee('Become a Service Provider');
        $response->assertSee('Professional &amp; Service Profile', false);
        $response->assertSee('Create Provider Account');
        $response->assertSee('Service Category / Trade');
        $response->assertSee('Standard Hourly Rate (₹)', false);
    }

    /** 3. All personal and professional fields appear on the same page */
    public function test_all_personal_and_professional_fields_exist_on_same_page(): void
    {
        $response = $this->get(route('become-provider'));
        $response->assertStatus(200);

        // Section 1: Personal & Account Details
        $response->assertSee('name="name"', false);
        $response->assertSee('name="phone"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="city"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('name="password_confirmation"', false);

        // Section 2: Professional & Service Profile
        $response->assertSee('name="category_id"', false);
        $response->assertSee('name="business_name"', false);
        $response->assertSee('name="experience_years"', false);
        $response->assertSee('name="hourly_rate"', false);
        $response->assertSee('name="service_area"', false);
        $response->assertSee('name="bio"', false);

        // Section 3: Agreement & Submit
        $response->assertSee('name="terms"', false);
        $response->assertSee('Create Provider Account');
    }

    /** 4 & 6 & 7 & 8: Provider completes full registration on single page, saves data, creates single profile with status pending */
    public function test_provider_registration_saves_all_personal_and_professional_details_without_duplicate(): void
    {
        $category = ServiceCategory::where('is_active', true)->first();
        $this->assertNotNull($category);

        $response = $this->post(route('register.post'), [
            'name'                  => 'Vikram Mistry',
            'email'                 => 'vikram.mistry@example.com',
            'phone'                 => '9822334455',
            'city'                  => 'Ahmedabad',
            'password'              => 'VikramSecure@123',
            'password_confirmation' => 'VikramSecure@123',
            'role'                  => 'provider',
            'category_id'           => $category->id,
            'business_name'         => 'Mistry Electrical Solutions',
            'experience_years'      => 7,
            'hourly_rate'           => 450,
            'service_area'          => 'Satellite, Vastrapur, Bodakdev',
            'bio'                   => 'Licensed electrician with 7 years of industrial and residential maintenance experience.',
            'terms'                 => '1',
        ]);

        $response->assertRedirect(route('provider.dashboard'));
        $this->assertAuthenticated();

        // Single user record created with role provider
        $this->assertEquals(1, User::where('email', 'vikram.mistry@example.com')->count());
        $user = User::where('email', 'vikram.mistry@example.com')->first();
        $this->assertEquals('provider', $user->role);
        $this->assertEquals('Ahmedabad', $user->city);

        // Single provider profile created and correctly associated
        $this->assertEquals(1, Provider::where('user_id', $user->id)->count());
        $provider = Provider::where('user_id', $user->id)->first();
        $this->assertEquals($category->id, $provider->category_id);
        $this->assertEquals('Mistry Electrical Solutions', $provider->business_name);
        $this->assertEquals(7, $provider->experience_years);
        $this->assertEquals(450, $provider->hourly_rate);
        $this->assertEquals('Satellite, Vastrapur, Bodakdev', $provider->service_area);
        $this->assertEquals('pending', $provider->verification_status);
        $this->assertTrue((bool)$provider->is_active);
    }

    /** 5. Required-field validation and password confirmation work */
    public function test_provider_registration_validation_enforces_required_fields_and_password_confirmation(): void
    {
        $response = $this->post(route('register.post'), [
            'role'                  => 'provider',
            'name'                  => '',
            'email'                 => 'invalid-email',
            'phone'                 => '',
            'city'                  => 'Delhi', // Unsupported city
            'password'              => 'short',
            'password_confirmation' => 'mismatch',
            'category_id'           => 99999, // Non-existent category
            'hourly_rate'           => 'not-a-number',
        ]);

        $response->assertSessionHasErrors([
            'name',
            'email',
            'phone',
            'city',
            'password',
            'category_id',
            'hourly_rate',
        ]);

        $this->assertGuest();
    }

    /** 9. Customer registration and login still work */
    public function test_customer_registration_and_login_still_function_seamlessly(): void
    {
        $regResponse = $this->post(route('register.post'), [
            'name'                  => 'Sneha Shah',
            'email'                 => 'sneha.shah@example.com',
            'phone'                 => '9876501234',
            'city'                  => 'Ahmedabad',
            'password'              => 'SnehaPass@123',
            'password_confirmation' => 'SnehaPass@123',
            'role'                  => 'customer',
            'terms'                 => '1',
        ]);

        $regResponse->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'sneha.shah@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('customer', $user->role);
        $this->assertNull(Provider::where('user_id', $user->id)->first());

        // Logout
        $this->post(route('logout'));
        $this->assertGuest();

        // Customer Login
        $loginResponse = $this->post(route('login.post'), [
            'email'    => 'sneha.shah@example.com',
            'password' => 'SnehaPass@123',
        ]);
        $loginResponse->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticated();
    }
}
