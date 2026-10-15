<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Provider;
use App\Models\ServiceCategory;
use App\Models\Booking;
use Tests\TestCase;

class ComprehensiveAppTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        ServiceCategory::create([
            'name' => 'Electrician',
            'slug' => 'electrician',
            'description' => 'Electrical work',
            'icon' => 'fas fa-bolt',
            'color' => '#F59E0B',
            'is_active' => true,
        ]);
    }

    public function test_home_page_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Become a Provider');
    }

    public function test_become_a_provider_page_renders_successfully(): void
    {
        $response = $this->get('/become-a-provider');
        $response->assertStatus(200);
        $response->assertSee('Provider Application');
        $response->assertSee('Electrician');
    }

    public function test_become_a_provider_submission_sets_pending_status(): void
    {
        $cat = ServiceCategory::first();
        $response = $this->post('/become-a-provider', [
            'name' => 'Karan Dave',
            'email' => 'karan.dave@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => 'provider',
            'phone' => '9988776655',
            'city' => 'Ahmedabad',
            'category_id' => $cat->id,
            'hourly_rate' => 450,
            'experience_years' => 4,
            'service_area' => 'Satellite, Bopal, SG Highway',
            'business_name' => 'Dave Electrical Solutions',
            'bio' => 'Certified master electrician with 4+ years on-site experience.'
        ]);

        $response->assertRedirect(route('provider.dashboard'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['email' => 'karan.dave@example.com', 'role' => 'provider']);
        $this->assertDatabaseHas('providers', [
            'business_name' => 'Dave Electrical Solutions',
            'verification_status' => 'pending',
            'is_active' => 1
        ]);
    }

    public function test_admin_can_approve_provider(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'role' => 'admin',
            'phone' => '9999999999',
            'city' => 'Ahmedabad'
        ]);

        $cat = ServiceCategory::first();
        $providerUser = User::create([
            'name' => 'Pending Pro',
            'email' => 'pending@example.com',
            'password' => 'password123',
            'role' => 'provider',
            'phone' => '8888888888',
            'city' => 'Gandhinagar'
        ]);

        $provider = Provider::create([
            'user_id' => $providerUser->id,
            'category_id' => $cat->id,
            'business_name' => 'Pending Pro Services',
            'hourly_rate' => 400,
            'verification_status' => 'pending',
            'is_active' => true,
        ]);

        // Admin approves provider
        $response = $this->actingAs($admin)->post("/admin/providers/{$provider->id}/verify");
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('providers', [
            'id' => $provider->id,
            'verification_status' => 'verified',
            'is_active' => 1
        ]);
    }

    public function test_admin_can_reject_provider(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin2@example.com',
            'password' => 'password123',
            'role' => 'admin',
            'phone' => '9999999998',
            'city' => 'Ahmedabad'
        ]);

        $cat = ServiceCategory::first();
        $providerUser = User::create([
            'name' => 'Reject Pro',
            'email' => 'reject@example.com',
            'password' => 'password123',
            'role' => 'provider',
            'phone' => '7777777777',
            'city' => 'Ahmedabad'
        ]);

        $provider = Provider::create([
            'user_id' => $providerUser->id,
            'category_id' => $cat->id,
            'business_name' => 'Bad Pro',
            'hourly_rate' => 300,
            'verification_status' => 'pending',
            'is_active' => true,
        ]);

        // Admin rejects provider
        $response = $this->actingAs($admin)->post("/admin/providers/{$provider->id}/reject");
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('providers', [
            'id' => $provider->id,
            'verification_status' => 'rejected',
            'is_active' => 0
        ]);
    }

    public function test_unverified_provider_cannot_be_booked_by_customer(): void
    {
        $cat = ServiceCategory::first();
        $customer = User::create([
            'name' => 'Booking Customer',
            'email' => 'booker@example.com',
            'password' => 'password123',
            'role' => 'customer',
            'phone' => '9991112223',
            'city' => 'Ahmedabad'
        ]);

        $providerUser = User::create([
            'name' => 'Unverified Pro',
            'email' => 'unverified@example.com',
            'password' => 'password123',
            'role' => 'provider',
            'phone' => '8881112223',
            'city' => 'Ahmedabad'
        ]);

        $provider = Provider::create([
            'user_id' => $providerUser->id,
            'category_id' => $cat->id,
            'business_name' => 'Unverified Pro Services',
            'hourly_rate' => 400,
            'verification_status' => 'pending',
            'is_active' => true,
        ]);

        // Attempting to visit book page returns 404
        $response = $this->actingAs($customer)->get("/customer/book/{$provider->id}");
        $response->assertStatus(404);

        // Attempting to post booking returns 404
        $postResponse = $this->actingAs($customer)->post("/customer/book", [
            'provider_id' => $provider->id,
            'booking_date' => now()->addDays(2)->format('Y-m-d'),
            'booking_time' => '10:00',
            'address' => '123 Satellite Road',
            'city' => 'Ahmedabad',
        ]);
        $postResponse->assertStatus(404);
    }

    public function test_split_booking_creates_pending_appointments_for_providers_and_customer(): void
    {
        $cat = ServiceCategory::first();
        $cat2 = ServiceCategory::create([
            'name' => 'Plumber',
            'slug' => 'plumber',
            'description' => 'Plumbing work',
            'icon' => 'fas fa-wrench',
            'color' => '#3B82F6',
            'is_active' => true,
        ]);

        $customer = User::create([
            'name' => 'Split Booker',
            'email' => 'split.customer@example.com',
            'password' => 'password123',
            'role' => 'customer',
            'phone' => '9123456780',
            'city' => 'Ahmedabad'
        ]);

        $proUser1 = User::create([
            'name' => 'Pro One',
            'email' => 'pro1@example.com',
            'password' => 'password123',
            'role' => 'provider',
            'phone' => '9123456781',
            'city' => 'Ahmedabad'
        ]);
        $pro1 = Provider::create([
            'user_id' => $proUser1->id,
            'category_id' => $cat->id,
            'business_name' => 'Electrician One',
            'hourly_rate' => 500,
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        $proUser2 = User::create([
            'name' => 'Pro Two',
            'email' => 'pro2@example.com',
            'password' => 'password123',
            'role' => 'provider',
            'phone' => '9123456782',
            'city' => 'Ahmedabad'
        ]);
        $pro2 = Provider::create([
            'user_id' => $proUser2->id,
            'category_id' => $cat2->id,
            'business_name' => 'Plumber Two',
            'hourly_rate' => 400,
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        $response = $this->actingAs($customer)->post('/customer/split-booking', [
            'booking_date' => now()->addDays(2)->format('Y-m-d'),
            'address' => '456 SG Highway',
            'city' => 'Ahmedabad',
            'payment_method' => 'cash',
            'services' => [
                [
                    'provider_id' => $pro1->id,
                    'time' => '09:00',
                    'amount' => 500,
                ],
                [
                    'provider_id' => $pro2->id,
                    'time' => '11:00',
                    'amount' => 400,
                ],
            ]
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $response->assertSessionHas('success');

        // Check split booking parent record
        $this->assertDatabaseHas('split_bookings', [
            'customer_id' => $customer->id,
            'total_amount' => 900,
            'status' => 'pending',
        ]);

        // Check split items
        $this->assertDatabaseHas('split_booking_items', [
            'provider_id' => $pro1->id,
            'amount' => 500,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('split_booking_items', [
            'provider_id' => $pro2->id,
            'amount' => 400,
            'status' => 'pending',
        ]);

        // Check that actual Booking records with status 'pending' exist for both providers and customer
        $this->assertDatabaseHas('bookings', [
            'customer_id' => $customer->id,
            'provider_id' => $pro1->id,
            'status' => 'pending',
            'is_split' => 1,
        ]);
        $this->assertDatabaseHas('bookings', [
            'customer_id' => $customer->id,
            'provider_id' => $pro2->id,
            'status' => 'pending',
            'is_split' => 1,
        ]);

        // Verify customer dashboard displays pending status
        $dashboardResponse = $this->actingAs($customer)->get('/customer/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Pending');
    }

    public function test_provider_dashboard_receives_booking_notification_in_12_hour_format(): void
    {
        $cat = ServiceCategory::first();

        $customer = User::create([
            'name' => 'Booking Client',
            'email' => 'client@example.com',
            'password' => 'password123',
            'role' => 'customer',
            'phone' => '9988776611',
            'city' => 'Ahmedabad',
        ]);

        $proUser1 = User::create([
            'name' => 'Target Provider',
            'email' => 'target.pro@example.com',
            'password' => 'password123',
            'role' => 'provider',
            'phone' => '9988776622',
            'city' => 'Ahmedabad',
        ]);
        $targetPro = Provider::create([
            'user_id' => $proUser1->id,
            'category_id' => $cat->id,
            'business_name' => 'Target Electric',
            'hourly_rate' => 600,
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        $proUser2 = User::create([
            'name' => 'Other Provider',
            'email' => 'other.pro@example.com',
            'password' => 'password123',
            'role' => 'provider',
            'phone' => '9988776633',
            'city' => 'Ahmedabad',
        ]);
        $otherPro = Provider::create([
            'user_id' => $proUser2->id,
            'category_id' => $cat->id,
            'business_name' => 'Other Electric',
            'hourly_rate' => 600,
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        // Customer books Target Provider for 14:00 (02:00 PM)
        $booking = Booking::create([
            'booking_code' => Booking::generateCode(),
            'customer_id' => $customer->id,
            'provider_id' => $targetPro->id,
            'category_id' => $cat->id,
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'booking_time' => '14:00',
            'address' => '789 Satellite Rd',
            'city' => 'Ahmedabad',
            'service_charge' => 600,
            'total_amount' => 650,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        $this->assertEquals('02:00 PM', $booking->formatted_time);

        // Target Provider dashboard sees notification
        $targetResponse = $this->actingAs($proUser1)->get('/provider/dashboard');
        $targetResponse->assertStatus(200);
        $targetResponse->assertSee('Booking Notifications');
        $targetResponse->assertSee('Booking Client');
        $targetResponse->assertSee('02:00 PM');

        // Other Provider dashboard should NOT see this notification
        $otherResponse = $this->actingAs($proUser2)->get('/provider/dashboard');
        $otherResponse->assertStatus(200);
        $otherResponse->assertDontSee('Booking Client');
    }

    public function test_duplicate_provider_record_prevented_on_registration(): void
    {
        $cat = ServiceCategory::first();

        // First registration
        $response = $this->post('/register', [
            'name' => 'Unique Pro',
            'email' => 'unique.pro@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'provider',
            'phone' => '9988112233',
            'city' => 'Gandhinagar',
            'category_id' => $cat->id,
            'hourly_rate' => 450,
            'business_name' => 'Unique Services',
        ]);
        $response->assertRedirect(route('provider.dashboard'));

        $user = User::where('email', 'unique.pro@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(1, Provider::where('user_id', $user->id)->count());
    }
}
