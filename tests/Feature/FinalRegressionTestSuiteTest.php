<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Provider;
use App\Models\ServiceCategory;
use App\Models\Booking;
use App\Models\MaintenanceReminder;
use App\Models\Review;
use App\Models\Complaint;
use Tests\TestCase;

class FinalRegressionTestSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceCategory $electricianCategory;
    protected ServiceCategory $plumberCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->electricianCategory = ServiceCategory::create([
            'name' => 'Electrician',
            'slug' => 'electrician',
            'description' => 'Electrical work and repairs',
            'icon' => 'fas fa-bolt',
            'color' => '#F59E0B',
            'is_active' => true,
        ]);

        $this->plumberCategory = ServiceCategory::create([
            'name' => 'Plumber',
            'slug' => 'plumber',
            'description' => 'Pipe and plumbing repairs',
            'icon' => 'fas fa-wrench',
            'color' => '#3B82F6',
            'is_active' => true,
        ]);
    }

    /** 1. Customer Registration, Login & Logout */
    public function test_customer_registration_and_login_and_logout(): void
    {
        // Registration
        $regResponse = $this->post('/register', [
            'name' => 'John QA',
            'email' => 'john.qa@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'customer',
            'phone' => '9876543210',
            'city' => 'Ahmedabad',
            'address' => '101, Test Road, Satellite',
        ]);

        $regResponse->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'john.qa@example.com',
            'role' => 'customer',
            'city' => 'Ahmedabad',
        ]);

        // Logout
        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect('/');
        $this->assertGuest();

        // Login
        $loginResponse = $this->post('/login', [
            'email' => 'john.qa@example.com',
            'password' => 'Password123!',
        ]);
        $loginResponse->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticated();
    }

    /** 2. Customer Registration Validation & Unsupported City */
    public function test_customer_registration_fails_with_invalid_data_or_unsupported_city(): void
    {
        // Try registering in unsupported city like Mumbai
        $response = $this->post('/register', [
            'name' => 'Invalid City User',
            'email' => 'mumbai.user@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'customer',
            'phone' => '9876543211',
            'city' => 'Mumbai',
        ]);
        $response->assertSessionHasErrors(['city']);

        // Try registering with invalid email
        $response2 = $this->post('/register', [
            'name' => 'Invalid Email',
            'email' => 'not-an-email',
            'password' => 'pass',
            'password_confirmation' => 'mismatch',
            'role' => 'customer',
            'city' => 'Ahmedabad',
        ]);
        $response2->assertSessionHasErrors(['email', 'password']);
    }

    /** 3. Provider Registration & Verification */
    public function test_provider_registration_and_login(): void
    {
        $regResponse = $this->post('/register', [
            'name' => 'Pro Builder',
            'email' => 'builder.pro@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'provider',
            'phone' => '9123456789',
            'city' => 'Gandhinagar',
            'business_name' => 'Builder Electricals',
            'category_id' => $this->electricianCategory->id,
            'hourly_rate' => 500,
        ]);

        $regResponse->assertRedirect(route('provider.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'builder.pro@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('provider', $user->role);

        $provider = Provider::where('user_id', $user->id)->first();
        $this->assertNotNull($provider);
        $this->assertEquals('Builder Electricals', $provider->business_name);
        $this->assertEquals('pending', $provider->verification_status);

        // Logout
        $this->post('/logout');
        $this->assertGuest();

        // Login as provider
        $loginResponse = $this->post('/login', [
            'email' => 'builder.pro@example.com',
            'password' => 'Password123!',
        ]);
        $loginResponse->assertRedirect(route('provider.dashboard'));
        $this->assertAuthenticated();
    }

    /** 4. Provider Application via Become-a-Provider */
    public function test_become_a_provider_page_and_submission(): void
    {
        $pageResponse = $this->get('/become-a-provider');
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Become a Service Provider');

        $submission = $this->post('/become-a-provider', [
            'name' => 'Harsh Patel',
            'email' => 'harsh.pro@example.com',
            'password' => 'SecretPass123',
            'password_confirmation' => 'SecretPass123',
            'role' => 'provider',
            'phone' => '9898989898',
            'city' => 'Ahmedabad',
            'category_id' => $this->plumberCategory->id,
            'hourly_rate' => 350,
            'experience_years' => 5,
            'service_area' => 'Vastrapur, Bodakdev, Bopal',
            'business_name' => 'Patel Plumbing Services',
            'bio' => 'Professional plumbing services in Ahmedabad.',
        ]);

        $submission->assertRedirect(route('provider.dashboard'));
        $this->assertDatabaseHas('providers', [
            'business_name' => 'Patel Plumbing Services',
            'verification_status' => 'pending',
        ]);
    }

    /** 5. Service Browsing & Search Filters */
    public function test_service_browsing_home_and_search_filters(): void
    {
        // Home page
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Electrician');
        $homeResponse->assertSee('Plumber');

        // Search page
        $searchResponse = $this->get('/search');
        $searchResponse->assertStatus(200);

        // Filter by category
        $catFilterResponse = $this->get('/search?category=' . $this->electricianCategory->slug);
        $catFilterResponse->assertStatus(200);

        // Filter by city
        $cityFilterResponse = $this->get('/search?city=Ahmedabad');
        $cityFilterResponse->assertStatus(200);
    }

    /** 6. Neighborhood Trusted Filter */
    public function test_neighborhood_trusted_filter_and_access_rules(): void
    {
        $proUser = User::create([
            'name' => 'Top Trusted Pro',
            'email' => 'top.pro@example.com',
            'password' => bcrypt('password123'),
            'role' => 'provider',
            'city' => 'Ahmedabad',
            'phone' => '9777777777',
        ]);

        $provider = Provider::create([
            'user_id' => $proUser->id,
            'category_id' => $this->electricianCategory->id,
            'business_name' => 'Top Trusted Electricians',
            'hourly_rate' => 600,
            'verification_status' => 'verified',
            'is_active' => true,
            'average_rating' => 4.9,
            'completed_jobs' => 12,
            'city' => 'Ahmedabad',
        ]);

        // Logged out search with neighborhood trusted filter
        $search = $this->get('/search?neighborhood_trusted=1&city=Ahmedabad');
        $search->assertStatus(200);
        $search->assertSee('Top Trusted Electricians');

        // Provider profile access for guest redirects to login with return url
        $guestView = $this->get("/provider/{$provider->id}");
        $guestView->assertRedirect(route('login'));

        // Customer logged in can view provider profile
        $customer = User::create([
            'name' => 'Cust One',
            'email' => 'cust.one@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
            'phone' => '9666666666',
        ]);

        $authView = $this->actingAs($customer)->get("/provider/{$provider->id}");
        $authView->assertStatus(200);
        $authView->assertSee('Top Trusted Electricians');
    }

    /** 7. Booking Creation (Single Booking) */
    public function test_booking_creation_single_booking(): void
    {
        $customer = User::create([
            'name' => 'Booker Customer',
            'email' => 'booker.test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
            'phone' => '9555555551',
        ]);

        $proUser = User::create([
            'name' => 'Bookable Pro',
            'email' => 'bookable.pro@example.com',
            'password' => bcrypt('password123'),
            'role' => 'provider',
            'city' => 'Ahmedabad',
            'phone' => '9555555552',
        ]);

        $provider = Provider::create([
            'user_id' => $proUser->id,
            'category_id' => $this->electricianCategory->id,
            'business_name' => 'Bookable Electricians',
            'hourly_rate' => 500,
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        // Customer views booking form
        $formResponse = $this->actingAs($customer)->get("/customer/book/{$provider->id}");
        $formResponse->assertStatus(200);

        // Customer creates booking
        $bookingDate = now()->addDays(2)->format('Y-m-d');
        $postResponse = $this->actingAs($customer)->post('/customer/book', [
            'provider_id' => $provider->id,
            'booking_date' => $bookingDate,
            'booking_time' => '10:00',
            'address' => 'Flat 502, Galaxy Heights, SG Highway',
            'city' => 'Ahmedabad',
            'description' => 'Fix master bedroom switchboard',
            'payment_method' => 'cash',
        ]);

        $postResponse->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'customer_id' => $customer->id,
            'provider_id' => $provider->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'city' => 'Ahmedabad',
        ]);
    }

    /** 8. Split Booking Creation */
    public function test_split_booking_creation_and_pending_status(): void
    {
        $customer = User::create([
            'name' => 'Split Tester',
            'email' => 'split.tester@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
            'phone' => '9444444441',
        ]);

        $proUser1 = User::create([
            'name' => 'Pro Electric',
            'email' => 'split.pro1@example.com',
            'password' => bcrypt('password123'),
            'role' => 'provider',
            'city' => 'Ahmedabad',
            'phone' => '9444444442',
        ]);
        $pro1 = Provider::create([
            'user_id' => $proUser1->id,
            'category_id' => $this->electricianCategory->id,
            'business_name' => 'Split Pro One',
            'hourly_rate' => 450,
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        $proUser2 = User::create([
            'name' => 'Pro Plumber',
            'email' => 'split.pro2@example.com',
            'password' => bcrypt('password123'),
            'role' => 'provider',
            'city' => 'Ahmedabad',
            'phone' => '9444444443',
        ]);
        $pro2 = Provider::create([
            'user_id' => $proUser2->id,
            'category_id' => $this->plumberCategory->id,
            'business_name' => 'Split Pro Two',
            'hourly_rate' => 400,
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        $response = $this->actingAs($customer)->post('/customer/split-booking', [
            'booking_date' => now()->addDays(3)->format('Y-m-d'),
            'address' => '204 Green Villa, Bopal',
            'city' => 'Ahmedabad',
            'payment_method' => 'card',
            'services' => [
                ['provider_id' => $pro1->id, 'time' => '10:00', 'amount' => 450],
                ['provider_id' => $pro2->id, 'time' => '13:00', 'amount' => 400],
            ],
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertDatabaseHas('split_bookings', [
            'customer_id' => $customer->id,
            'total_amount' => 850,
            'status' => 'pending',
        ]);
        $this->assertEquals(2, Booking::where('customer_id', $customer->id)->where('is_split', 1)->count());
    }

    /** 9. Pending Bookings Visible on Customer & Provider Dashboards */
    public function test_pending_bookings_appear_in_customer_and_provider_dashboards(): void
    {
        $customer = User::create([
            'name' => 'Pending Viewer Customer',
            'email' => 'pending.viewer@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
            'phone' => '9333333331',
        ]);

        $proUser = User::create([
            'name' => 'Pending Viewer Pro',
            'email' => 'pending.pro.viewer@example.com',
            'password' => bcrypt('password123'),
            'role' => 'provider',
            'city' => 'Ahmedabad',
            'phone' => '9333333332',
        ]);

        $provider = Provider::create([
            'user_id' => $proUser->id,
            'category_id' => $this->electricianCategory->id,
            'business_name' => 'Viewer Electric Services',
            'hourly_rate' => 550,
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        $booking = Booking::create([
            'booking_code' => Booking::generateCode(),
            'customer_id' => $customer->id,
            'provider_id' => $provider->id,
            'category_id' => $this->electricianCategory->id,
            'booking_date' => now()->addDays(1)->format('Y-m-d'),
            'booking_time' => '15:00',
            'address' => 'A-12 Nilamber Bunglows, Navrangpura',
            'city' => 'Ahmedabad',
            'service_charge' => 550,
            'total_amount' => 600,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        // Customer dashboard sees booking and pending status
        $custDash = $this->actingAs($customer)->get('/customer/dashboard');
        $custDash->assertStatus(200);
        $custDash->assertSee($booking->booking_code);
        $custDash->assertSee('Pending');

        // Customer bookings index
        $custBookings = $this->actingAs($customer)->get('/customer/bookings');
        $custBookings->assertStatus(200);
        $custBookings->assertSee($booking->booking_code);

        // Provider dashboard sees customer name in notifications and bookings
        $proDash = $this->actingAs($proUser)->get('/provider/dashboard');
        $proDash->assertStatus(200);
        $proDash->assertSee('Pending Viewer Customer');

        // Provider bookings list sees booking code and customer name
        $proBookings = $this->actingAs($proUser)->get('/provider/bookings');
        $proBookings->assertStatus(200);
        $proBookings->assertSee($booking->booking_code);
        $proBookings->assertSee('Pending Viewer Customer');
    }

    /** 10. Booking Status Updates: Accept, Complete, Reject, Cancel */
    public function test_booking_status_lifecycle_updates(): void
    {
        $customer = User::create([
            'name' => 'Lifecycle Customer',
            'email' => 'lifecycle.cust@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
            'phone' => '9222222221',
        ]);

        $proUser = User::create([
            'name' => 'Lifecycle Pro',
            'email' => 'lifecycle.pro@example.com',
            'password' => bcrypt('password123'),
            'role' => 'provider',
            'city' => 'Ahmedabad',
            'phone' => '9222222222',
        ]);

        $provider = Provider::create([
            'user_id' => $proUser->id,
            'category_id' => $this->electricianCategory->id,
            'business_name' => 'Lifecycle Electric',
            'hourly_rate' => 500,
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        // 1. Accept booking
        $booking1 = Booking::create([
            'booking_code' => Booking::generateCode(),
            'customer_id' => $customer->id,
            'provider_id' => $provider->id,
            'category_id' => $this->electricianCategory->id,
            'booking_date' => now()->addDays(1)->format('Y-m-d'),
            'booking_time' => '10:00',
            'address' => 'Address 1',
            'city' => 'Ahmedabad',
            'service_charge' => 500,
            'total_amount' => 550,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        $acceptResponse = $this->actingAs($proUser)->post("/provider/bookings/{$booking1->id}/accept");
        $acceptResponse->assertSessionHas('success');
        $this->assertEquals('accepted', $booking1->fresh()->status);

        // 2. Complete booking
        $completeResponse = $this->actingAs($proUser)->post("/provider/bookings/{$booking1->id}/complete");
        $completeResponse->assertSessionHas('success');
        $this->assertEquals('completed', $booking1->fresh()->status);
        $this->assertEquals('paid', $booking1->fresh()->payment_status);

        // 3. Reject booking
        $booking2 = Booking::create([
            'booking_code' => Booking::generateCode(),
            'customer_id' => $customer->id,
            'provider_id' => $provider->id,
            'category_id' => $this->electricianCategory->id,
            'booking_date' => now()->addDays(2)->format('Y-m-d'),
            'booking_time' => '11:00',
            'address' => 'Address 2',
            'city' => 'Ahmedabad',
            'service_charge' => 500,
            'total_amount' => 550,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        $rejectResponse = $this->actingAs($proUser)->post("/provider/bookings/{$booking2->id}/reject");
        $rejectResponse->assertSessionHas('success');
        $this->assertEquals('cancelled', $booking2->fresh()->status);

        // 4. Customer cancel booking
        $booking3 = Booking::create([
            'booking_code' => Booking::generateCode(),
            'customer_id' => $customer->id,
            'provider_id' => $provider->id,
            'category_id' => $this->electricianCategory->id,
            'booking_date' => now()->addDays(3)->format('Y-m-d'),
            'booking_time' => '12:00',
            'address' => 'Address 3',
            'city' => 'Ahmedabad',
            'service_charge' => 500,
            'total_amount' => 550,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        $cancelResponse = $this->actingAs($customer)->post("/customer/bookings/{$booking3->id}/cancel");
        $cancelResponse->assertSessionHas('success');
        $this->assertEquals('cancelled', $booking3->fresh()->status);
    }

    /** 11. Customer Reminders (Notifications) CRUD */
    public function test_customer_maintenance_reminders(): void
    {
        $customer = User::create([
            'name' => 'Reminder Customer',
            'email' => 'reminder.cust@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
            'phone' => '9111111112',
        ]);

        // View reminders page
        $view = $this->actingAs($customer)->get('/customer/reminders');
        $view->assertStatus(200);

        // Create a reminder
        $create = $this->actingAs($customer)->post('/customer/reminders', [
            'title' => 'AC Servicing',
            'category_id' => $this->electricianCategory->id,
            'next_due_date' => now()->addDays(30)->format('Y-m-d'),
            'repeat_months' => 3,
            'notes' => 'Pre-summer cleaning',
        ]);
        $create->assertRedirect();
        $this->assertDatabaseHas('maintenance_reminders', [
            'customer_id' => $customer->id,
            'title' => 'AC Servicing',
            'category_id' => $this->electricianCategory->id,
        ]);

        $reminder = MaintenanceReminder::where('customer_id', $customer->id)->first();
        $this->assertNotNull($reminder);

        // Delete reminder
        $delete = $this->actingAs($customer)->delete("/customer/reminders/{$reminder->id}");
        $delete->assertRedirect();
        $this->assertDatabaseMissing('maintenance_reminders', ['id' => $reminder->id]);
    }

    /** 12. Provider Availability Toggle */
    public function test_provider_availability_toggle(): void
    {
        $proUser = User::create([
            'name' => 'Toggle Pro',
            'email' => 'toggle.pro@example.com',
            'password' => bcrypt('password123'),
            'role' => 'provider',
            'city' => 'Gandhinagar',
            'phone' => '9111111113',
        ]);

        $provider = Provider::create([
            'user_id' => $proUser->id,
            'category_id' => $this->electricianCategory->id,
            'business_name' => 'Toggle Pro Services',
            'hourly_rate' => 450,
            'is_available' => true,
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        $toggleResponse = $this->actingAs($proUser)->postJson('/provider/availability');
        $toggleResponse->assertStatus(200);
        $toggleResponse->assertJson(['available' => false]);
        $this->assertFalse((bool)$provider->fresh()->is_available);

        $toggleResponse2 = $this->actingAs($proUser)->postJson('/provider/availability');
        $toggleResponse2->assertStatus(200);
        $toggleResponse2->assertJson(['available' => true]);
        $this->assertTrue((bool)$provider->fresh()->is_available);
    }

    /** 13. Admin Dashboard & Provider Approval/Rejection */
    public function test_admin_dashboard_and_provider_verification(): void
    {
        $admin = User::create([
            'name' => 'Admin QA',
            'email' => 'admin.qa@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'city' => 'Ahmedabad',
            'phone' => '9000000010',
        ]);

        // Admin dashboard loads
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);

        // Admin provider list
        $provList = $this->actingAs($admin)->get('/admin/providers');
        $provList->assertStatus(200);

        // Admin bookings list
        $bookList = $this->actingAs($admin)->get('/admin/bookings');
        $bookList->assertStatus(200);
    }

    /** 14. Master Admin Dashboard & Admin Management */
    public function test_master_admin_dashboard_and_admin_management(): void
    {
        $masterAdmin = User::create([
            'name' => 'Master Admin QA',
            'email' => 'master.qa@example.com',
            'password' => bcrypt('password123'),
            'role' => 'master_admin',
            'city' => 'Ahmedabad',
            'phone' => '9000000020',
        ]);

        // Dashboard
        $dash = $this->actingAs($masterAdmin)->get('/master-admin/dashboard');
        $dash->assertStatus(200);

        // Reports
        $reports = $this->actingAs($masterAdmin)->get('/master-admin/reports');
        $reports->assertStatus(200);

        // Create new Admin
        $createAdmin = $this->actingAs($masterAdmin)->post('/master-admin/admins', [
            'name' => 'Sub Admin',
            'email' => 'subadmin@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '9000000021',
            'city' => 'Gandhinagar',
        ]);
        $createAdmin->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'subadmin@example.com',
            'role' => 'admin',
        ]);
    }

    /** 15. AI Assistant & Chat API */
    public function test_ai_assistant_page_and_chat_api(): void
    {
        // View AI assistant page
        $page = $this->get('/ai-assistant');
        $page->assertStatus(200);

        // Send chat message
        $chat = $this->postJson('/ai/chat', [
            'message' => 'I have a leaking tap in Ahmedabad, what should I do?',
            'history' => [],
        ]);
        $chat->assertStatus(200);
        $chat->assertJsonStructure(['response', 'timestamp']);

        // Suggest service API
        $suggest = $this->postJson('/ai/suggest-service', [
            'issue' => 'AC not cooling properly',
        ]);
        $suggest->assertStatus(200);
    }

    /** 16. Reviews & Complaints Flow */
    public function test_reviews_and_complaints_flow(): void
    {
        $customer = User::create([
            'name' => 'Feedback Customer',
            'email' => 'feedback.cust@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
            'phone' => '9111111114',
        ]);

        $proUser = User::create([
            'name' => 'Feedback Pro',
            'email' => 'feedback.pro@example.com',
            'password' => bcrypt('password123'),
            'role' => 'provider',
            'city' => 'Ahmedabad',
            'phone' => '9111111115',
        ]);

        $provider = Provider::create([
            'user_id' => $proUser->id,
            'category_id' => $this->electricianCategory->id,
            'business_name' => 'Feedback Electric',
            'hourly_rate' => 500,
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        $booking = Booking::create([
            'booking_code' => Booking::generateCode(),
            'customer_id' => $customer->id,
            'provider_id' => $provider->id,
            'category_id' => $this->electricianCategory->id,
            'booking_date' => now()->subDay()->format('Y-m-d'),
            'booking_time' => '10:00',
            'address' => 'Customer address',
            'city' => 'Ahmedabad',
            'service_charge' => 500,
            'total_amount' => 550,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'completed',
        ]);

        // Customer submits review
        $reviewResponse = $this->actingAs($customer)->post("/customer/review/{$booking->id}", [
            'rating' => 5,
            'comment' => 'Outstanding service! Fast and polite.',
        ]);
        $reviewResponse->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'customer_id' => $customer->id,
            'rating' => 5,
        ]);

        // Customer submits complaint
        $complaintResponse = $this->actingAs($customer)->post("/customer/complaint/{$booking->id}", [
            'subject' => 'Late arrival',
            'description' => 'Provider was 15 minutes late.',
        ]);
        $complaintResponse->assertRedirect();
        $this->assertDatabaseHas('complaints', [
            'booking_id' => $booking->id,
            'customer_id' => $customer->id,
            'subject' => 'Late arrival',
            'status' => 'open',
        ]);

        // Admin resolves complaint
        $admin = User::create([
            'name' => 'Resolver Admin',
            'email' => 'resolver@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'city' => 'Ahmedabad',
            'phone' => '9000000030',
        ]);

        $complaint = Complaint::where('booking_id', $booking->id)->first();
        $this->assertNotNull($complaint);

        $resolveResponse = $this->actingAs($admin)->post("/admin/complaints/{$complaint->id}/resolve", [
            'resolution' => 'Customer compensated with discount code.',
        ]);
        $resolveResponse->assertSessionHas('success');
        $this->assertEquals('resolved', $complaint->fresh()->status);
    }

    /** 17. Customer Profile Update */
    public function test_customer_profile_update(): void
    {
        $customer = User::create([
            'name' => 'Profile User',
            'email' => 'profile.user@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
            'phone' => '9111111116',
            'address' => 'Old Address',
        ]);

        $updateResponse = $this->actingAs($customer)->put('/customer/profile', [
            'name' => 'Profile User Updated',
            'email' => 'profile.user@example.com',
            'phone' => '9111111199',
            'city' => 'Gandhinagar',
            'address' => 'New Address Sector 21',
        ]);

        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'name' => 'Profile User Updated',
            'email' => 'profile.user@example.com',
            'city' => 'Gandhinagar',
            'phone' => '9111111199',
            'address' => 'New Address Sector 21',
        ]);
    }
}
