<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ServiceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExactLoginRegisterUITest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_page_matches_reference_image_2_structure(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);

        // Branding & Background shapes
        $response->assertSee('bg-shape-top-right');
        $response->assertSee('bg-shape-bottom-left');
        $response->assertSee('images/logo-horizontal.svg');

        // Left Panel elements
        $response->assertSee('Your Home. Our Trusted Pros. Peace of Mind.');
        $response->assertSee('Book verified local professionals for repairs, cleaning, and everyday home care.');
        $response->assertSee('Verified Professionals');
        $response->assertSee('Upfront Pricing');
        $response->assertSee('Easy Booking');
        $response->assertSee('Verified service network in Ahmedabad &amp; Gandhinagar', false);

        // Right Panel elements
        $response->assertSee('Welcome Back');
        $response->assertSee('Log in to manage your bookings and home services.');
        $response->assertSee('Email Address');
        $response->assertSee('Password');
        $response->assertSee('Remember me');
        $response->assertSee('Forgot password?');
        $response->assertSee('Log In');
        $response->assertSee('Don\'t have an account?', false);
        $response->assertSee('Create Account');

        // Footer links
        $response->assertSee(route('home'));
        $response->assertSee('Privacy Policy');
        $response->assertSee('Terms of Service');
        $response->assertSee(route('become-provider'));
        $response->assertSee('Become a Pro');
    }

    public function test_registration_page_matches_reference_image_1_structure(): void
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);

        // Branding & Background shapes
        $response->assertSee('bg-shape-top-right');
        $response->assertSee('bg-shape-bottom-left');
        $response->assertSee('images/logo-horizontal.svg');

        // Header Title & Subtitle
        $response->assertSee('Create Your HomeServiceHub Account');
        $response->assertSee('Join in a minute and get trusted home services, or start growing your business.');

        // Account Type Selector
        $response->assertSee('I want to');
        $response->assertSee('Book services');
        $response->assertSee('Customer account');
        $response->assertSee('Offer services');
        $response->assertSee('Provider account');

        // Form fields
        $response->assertSee('Full Name');
        $response->assertSee('Phone Number');
        $response->assertSee('Email Address');
        $response->assertSee('Address');
        $response->assertSee('Enter your full address');
        $response->assertSee('name="address"', false);
        $response->assertSee('Password');
        $response->assertSee('Confirm Password');
        $response->assertSee('name="password_confirmation"', false);

        // Terms and conditions
        $response->assertSee('I agree to the');
        $response->assertSee('Terms of Service');
        $response->assertSee('Privacy Policy');

        // Primary Button & Switch Prompt
        $response->assertSee('Create Account');
        $response->assertSee('Already have an account?');
        $response->assertSee('Log In');

        // Footer links
        $response->assertSee(route('home'));
        $response->assertSee(route('become-provider'));
        $response->assertSee('Become a Pro');
    }

    public function test_customer_registration_via_new_form_submits_and_logs_in(): void
    {
        $response = $this->post(route('register.post'), [
            'name' => 'Aarav Patel',
            'phone' => '9876543210',
            'email' => 'aarav.patel@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'customer',
            'city' => 'Ahmedabad',
            'address' => 'Flat 402, Shivalik Heights, Judges Bungalow Road, Bodakdev',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'aarav.patel@example.com',
            'role' => 'customer',
            'name' => 'Aarav Patel',
            'address' => 'Flat 402, Shivalik Heights, Judges Bungalow Road, Bodakdev',
        ]);
    }

    public function test_provider_registration_via_new_form_submits_and_logs_in(): void
    {
        $category = ServiceCategory::first();
        $this->assertNotNull($category);

        $response = $this->post(route('register.post'), [
            'name' => 'Meera Electrician',
            'phone' => '9812345678',
            'email' => 'meera.electric@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'provider',
            'city' => 'Ahmedabad',
            'address' => 'Shop 12, Sunrise Complex, Near Vastrapur Lake',
            'category_id' => $category->id,
            'hourly_rate' => 450,
            'terms' => '1',
        ]);

        $response->assertRedirect(route('provider.dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'meera.electric@example.com',
            'role' => 'provider',
            'address' => 'Shop 12, Sunrise Complex, Near Vastrapur Lake',
        ]);

        $this->assertDatabaseHas('providers', [
            'category_id' => $category->id,
            'hourly_rate' => 450,
        ]);
    }

    public function test_registration_password_fields_layout_and_elements_integrity(): void
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);

        // Check password visibility toggles and attributes
        $response->assertSee('id="regPassword"', false);
        $response->assertSee('id="regPasswordConfirm"', false);
        $response->assertSee('id="pwdEye1"', false);
        $response->assertSee('id="pwdEye2"', false);
        $response->assertSee('togglePasswordVisibility', false);

        // Confirm CSS Grid minmax and layout classes prevent overflow
        $response->assertSee('repeat(2, minmax(0, 1fr))', false);
        $response->assertSee('reg-textarea-wrap', false);
        $response->assertSee('Enter your full address', false);
    }
}
