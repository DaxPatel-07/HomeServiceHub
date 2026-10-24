<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Provider;
use App\Models\ServiceCategory;
use App\Models\Booking;
use App\Models\MaintenanceReminder;
use Tests\TestCase;

class AuthenticationAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceCategory $category;
    protected User $customerA;
    protected User $customerB;
    protected User $providerUserA;
    protected User $providerUserB;
    protected Provider $providerA;
    protected Provider $providerB;
    protected User $adminUser;
    protected User $masterAdminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = ServiceCategory::create([
            'name' => 'Electrician',
            'slug' => 'electrician',
            'description' => 'Certified electrical repairs',
            'icon' => 'fas fa-bolt',
            'color' => '#F59E0B',
            'is_active' => true,
        ]);

        $this->customerA = User::create([
            'name' => 'Customer Alice',
            'email' => 'alice@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'phone' => '9800000001',
            'city' => 'Ahmedabad',
            'is_active' => true,
        ]);

        $this->customerB = User::create([
            'name' => 'Customer Bob',
            'email' => 'bob@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'phone' => '9800000002',
            'city' => 'Ahmedabad',
            'is_active' => true,
        ]);

        $this->providerUserA = User::create([
            'name' => 'Provider Pro A',
            'email' => 'proa@example.com',
            'password' => bcrypt('password123'),
            'role' => 'provider',
            'phone' => '9800000003',
            'city' => 'Ahmedabad',
            'is_active' => true,
        ]);

        $this->providerA = Provider::create([
            'user_id' => $this->providerUserA->id,
            'category_id' => $this->category->id,
            'business_name' => 'Pro A Electric',
            'hourly_rate' => 500,
            'verification_status' => 'verified',
            'is_active' => true,
            'service_area' => 'Ahmedabad, Satellite',
        ]);

        $this->providerUserB = User::create([
            'name' => 'Provider Pro B',
            'email' => 'prob@example.com',
            'password' => bcrypt('password123'),
            'role' => 'provider',
            'phone' => '9800000004',
            'city' => 'Ahmedabad',
            'is_active' => true,
        ]);

        $this->providerB = Provider::create([
            'user_id' => $this->providerUserB->id,
            'category_id' => $this->category->id,
            'business_name' => 'Pro B Electric',
            'hourly_rate' => 450,
            'verification_status' => 'verified',
            'is_active' => true,
            'service_area' => 'Ahmedabad, Bopal',
        ]);

        $this->adminUser = User::create([
            'name' => 'Admin Operator',
            'email' => 'admin.sec@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'phone' => '9800000005',
            'city' => 'Ahmedabad',
            'is_active' => true,
        ]);

        $this->masterAdminUser = User::create([
            'name' => 'Master Admin Chief',
            'email' => 'master.sec@example.com',
            'password' => bcrypt('password123'),
            'role' => 'master_admin',
            'phone' => '9800000006',
            'city' => 'Ahmedabad',
            'is_active' => true,
        ]);
    }

    public function test_login_authenticates_valid_active_user(): void
    {
        $response = $this->post('/login', [
            'email' => 'alice@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticatedAs($this->customerA);
    }

    public function test_login_rejects_deactivated_account(): void
    {
        $this->customerA->update(['is_active' => false]);

        $response = $this->post('/login', [
            'email' => 'alice@example.com',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_deactivated_user_in_session_is_blocked_by_role_middleware(): void
    {
        $this->actingAs($this->customerA);

        // Deactivate customer during active session
        $this->customerA->update(['is_active' => false]);

        $response = $this->get('/customer/dashboard');

        // Should force logout and redirect to login with error
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_open_redirect_is_prevented_on_login(): void
    {
        $maliciousUrls = [
            'https://evil.com',
            'http://attacker.com/steal-creds',
            '//evil.com/phish',
            '/\\evil.com',
        ];

        foreach ($maliciousUrls as $evilUrl) {
            $response = $this->post('/login', [
                'email' => 'alice@example.com',
                'password' => 'password123',
                'redirect' => $evilUrl,
            ]);

            // Must NOT redirect to external malicious destination
            $response->assertRedirect(route('customer.dashboard'));
            $this->assertFalse(str_starts_with($response->headers->get('Location') ?? '', 'http://evil.com'));
            $this->assertFalse(str_starts_with($response->headers->get('Location') ?? '', 'https://evil.com'));
        }
    }

    public function test_safe_internal_redirect_is_preserved_on_login(): void
    {
        $response = $this->post('/login', [
            'email' => 'alice@example.com',
            'password' => 'password123',
            'redirect' => '/customer/bookings',
        ]);

        $response->assertRedirect('/customer/bookings');
    }

    public function test_customer_cannot_access_provider_routes(): void
    {
        $response = $this->actingAs($this->customerA)->get('/provider/dashboard');
        $response->assertStatus(403);

        $bookingsResponse = $this->actingAs($this->customerA)->get('/provider/bookings');
        $bookingsResponse->assertStatus(403);
    }

    public function test_provider_cannot_access_customer_routes(): void
    {
        $response = $this->actingAs($this->providerUserA)->get('/customer/dashboard');
        $response->assertStatus(403);

        $bookingsResponse = $this->actingAs($this->providerUserA)->get('/customer/bookings');
        $bookingsResponse->assertStatus(403);
    }

    public function test_customer_cannot_access_admin_or_master_admin_routes(): void
    {
        $response = $this->actingAs($this->customerA)->get('/admin/dashboard');
        $response->assertStatus(403);

        $masterResponse = $this->actingAs($this->customerA)->get('/master-admin/dashboard');
        $masterResponse->assertStatus(403);
    }

    public function test_admin_cannot_access_master_admin_routes(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/master-admin/dashboard');
        $response->assertStatus(403);

        $settingsResponse = $this->actingAs($this->adminUser)->get('/master-admin/settings');
        $settingsResponse->assertStatus(403);
    }

    public function test_customer_cannot_view_or_cancel_another_customers_booking(): void
    {
        $bookingA = Booking::create([
            'booking_code' => Booking::generateCode(),
            'customer_id' => $this->customerA->id,
            'provider_id' => $this->providerA->id,
            'category_id' => $this->category->id,
            'booking_date' => now()->addDays(2)->format('Y-m-d'),
            'booking_time' => '10:00',
            'address' => 'Alice House, Satellite',
            'city' => 'Ahmedabad',
            'service_charge' => 500,
            'total_amount' => 550,
            'status' => 'pending',
        ]);

        // Customer B attempts to view Customer A's booking
        $viewResponse = $this->actingAs($this->customerB)->get("/customer/bookings/{$bookingA->id}");
        $viewResponse->assertStatus(403);

        // Customer B attempts to cancel Customer A's booking
        $cancelResponse = $this->actingAs($this->customerB)->post("/customer/bookings/{$bookingA->id}/cancel", [
            'reason' => 'Malicious cancellation',
        ]);
        $cancelResponse->assertStatus(403);

        // Booking remains pending
        $this->assertEquals('pending', $bookingA->fresh()->status);
    }

    public function test_customer_cannot_review_another_customers_booking(): void
    {
        $bookingA = Booking::create([
            'booking_code' => Booking::generateCode(),
            'customer_id' => $this->customerA->id,
            'provider_id' => $this->providerA->id,
            'category_id' => $this->category->id,
            'booking_date' => now()->subDay()->format('Y-m-d'),
            'booking_time' => '10:00',
            'address' => 'Alice House, Satellite',
            'city' => 'Ahmedabad',
            'service_charge' => 500,
            'total_amount' => 550,
            'status' => 'completed',
        ]);

        // Customer B attempts to review Customer A's booking
        $response = $this->actingAs($this->customerB)->post("/customer/review/{$bookingA->id}", [
            'rating' => 1,
            'comment' => 'Fake review on another user booking',
        ]);
        $response->assertStatus(403);

        $this->assertDatabaseMissing('reviews', [
            'booking_id' => $bookingA->id,
            'customer_id' => $this->customerB->id,
        ]);
    }

    public function test_customer_cannot_file_complaint_on_another_customers_booking(): void
    {
        $bookingA = Booking::create([
            'booking_code' => Booking::generateCode(),
            'customer_id' => $this->customerA->id,
            'provider_id' => $this->providerA->id,
            'category_id' => $this->category->id,
            'booking_date' => now()->subDay()->format('Y-m-d'),
            'booking_time' => '10:00',
            'address' => 'Alice House, Satellite',
            'city' => 'Ahmedabad',
            'service_charge' => 500,
            'total_amount' => 550,
            'status' => 'completed',
        ]);

        // Customer B attempts to file complaint on Customer A's booking
        $response = $this->actingAs($this->customerB)->post("/customer/complaint/{$bookingA->id}", [
            'subject' => 'Unauthorized complaint',
            'description' => 'Detailed complaint description text',
        ]);
        $response->assertStatus(403);

        $this->assertDatabaseMissing('complaints', [
            'booking_id' => $bookingA->id,
            'customer_id' => $this->customerB->id,
        ]);
    }

    public function test_customer_cannot_delete_another_customers_reminder(): void
    {
        $reminderA = MaintenanceReminder::create([
            'customer_id' => $this->customerA->id,
            'category_id' => $this->category->id,
            'title' => 'Alice AC Service',
            'next_due_date' => now()->addMonth()->format('Y-m-d'),
            'repeat_months' => 6,
        ]);

        // Customer B attempts to delete Customer A's reminder
        $response = $this->actingAs($this->customerB)->delete("/customer/reminders/{$reminderA->id}");
        $response->assertStatus(403);

        $this->assertDatabaseHas('maintenance_reminders', ['id' => $reminderA->id]);
    }

    public function test_provider_cannot_accept_reject_or_complete_another_providers_booking(): void
    {
        $bookingA = Booking::create([
            'booking_code' => Booking::generateCode(),
            'customer_id' => $this->customerA->id,
            'provider_id' => $this->providerA->id,
            'category_id' => $this->category->id,
            'booking_date' => now()->addDays(2)->format('Y-m-d'),
            'booking_time' => '10:00',
            'address' => 'Alice House, Satellite',
            'city' => 'Ahmedabad',
            'service_charge' => 500,
            'total_amount' => 550,
            'status' => 'pending',
        ]);

        // Provider B attempts to accept Provider A's booking
        $acceptResponse = $this->actingAs($this->providerUserB)->post("/provider/bookings/{$bookingA->id}/accept");
        $acceptResponse->assertStatus(403);

        // Provider B attempts to reject Provider A's booking
        $rejectResponse = $this->actingAs($this->providerUserB)->post("/provider/bookings/{$bookingA->id}/reject", [
            'reason' => 'Unauthorized rejection',
        ]);
        $rejectResponse->assertStatus(403);

        // Provider B attempts to complete Provider A's booking
        $completeResponse = $this->actingAs($this->providerUserB)->post("/provider/bookings/{$bookingA->id}/complete");
        $completeResponse->assertStatus(403);

        $this->assertEquals('pending', $bookingA->fresh()->status);
    }

    public function test_split_booking_enforces_authoritative_provider_rates(): void
    {
        // Provider A hourly rate is 500, Provider B hourly rate is 450
        // Customer attempts to tamper with prices: amount = 1 and 2
        $response = $this->actingAs($this->customerA)->post('/customer/split-booking', [
            'booking_date' => now()->addDays(2)->format('Y-m-d'),
            'address' => '123 Satellite Road',
            'city' => 'Ahmedabad',
            'payment_method' => 'cash',
            'services' => [
                [
                    'provider_id' => $this->providerA->id,
                    'time' => '09:00',
                    'amount' => 1, // Tampered client value
                ],
                [
                    'provider_id' => $this->providerB->id,
                    'time' => '11:00',
                    'amount' => 2, // Tampered client value
                ],
            ]
        ]);

        $response->assertRedirect(route('customer.dashboard'));

        // Server must enforce authoritative provider rate (500 + 450 = 950)
        $this->assertDatabaseHas('split_bookings', [
            'customer_id' => $this->customerA->id,
            'total_amount' => 950,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('bookings', [
            'customer_id' => $this->customerA->id,
            'provider_id' => $this->providerA->id,
            'service_charge' => 500,
            'total_amount' => 500,
        ]);
    }

    public function test_inactive_provider_is_not_accessible_via_public_profile(): void
    {
        $this->providerA->update(['is_active' => false]);

        $response = $this->actingAs($this->customerA)->get(route('provider.show', $this->providerA));
        $response->assertStatus(404);
    }

    public function test_unauthenticated_api_request_to_role_protected_route_returns_401(): void
    {
        $response = $this->getJson('/customer/dashboard');
        $response->assertStatus(401);
        $response->assertJson([
            'error' => 'Unauthenticated',
        ]);
    }
}
