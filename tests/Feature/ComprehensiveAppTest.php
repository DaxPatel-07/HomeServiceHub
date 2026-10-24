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

        // 1. Hero Section
        $response->assertSee('Everything Your Home Needs,');
        $response->assertSee('All in One Place.');
        $response->assertSee('Certified Home Care');
        $response->assertSee('What does your home need?');
        $response->assertSee('hero-pills-row');
        $response->assertSee('category-pill');

        // 2. Comprehensive Services Section
        $response->assertSee('Comprehensive Services');
        $response->assertSee('What Does Your Home Need');
        $response->assertSee('service-category-card');
        $response->assertSee('col-6 col-md-6 col-lg-3');

        // 3. Statistics Section
        $response->assertSee('Happy Customers');
        $response->assertSee('Trusted Providers');
        $response->assertSee('Jobs Completed');
        $response->assertSee('Average Rating');
        $response->assertSee('stats-banner');

        // 4. Become a Provider Section
        $response->assertSee('Are You a Home Service Professional?');
        $response->assertSee('Become a Provider Today');
        $response->assertSee('Earn With Your Skills');
        $response->assertSee(route('become-provider'));

        // 5. Split Booking Section
        $response->assertSee('Book Multiple Services');
        $response->assertSee('In One Go');
        $response->assertSee('Try Split Booking');
        $response->assertSee('split-booking-card');
        $response->assertSee('Total Amount');
        $response->assertSee('₹1,197');
        $response->assertDontSee('linear-gradient(135deg,#f0fdf4,#dcfce7)');
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

    public function test_customer_dashboard_has_logout_option_and_can_logout(): void
    {
        $customer = User::create([
            'name' => 'Logout Test User',
            'email' => 'logout.test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
        ]);

        $response = $this->actingAs($customer)->get(route('customer.dashboard'));
        $response->assertStatus(200);

        // Verify logout options are present in dashboard
        $response->assertSee('Logout');
        $response->assertSee(route('logout'));

        // Verify logging out
        $logoutResponse = $this->actingAs($customer)->post(route('logout'));
        $logoutResponse->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_customer_my_bookings_and_dashboard_have_no_stray_sidebar_logout_button(): void
    {
        $customer = User::create([
            'name' => 'Sidebar Check User',
            'email' => 'sidebar.check@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
        ]);

        $routesToTest = [
            route('customer.dashboard'),
            route('customer.bookings'),
            route('customer.reminders'),
            route('customer.split.booking'),
            route('customer.profile'),
        ];

        foreach ($routesToTest as $pageRoute) {
            $response = $this->actingAs($customer)->get($pageRoute);
            $response->assertStatus(200);

            $content = $response->getContent();

            // Extract sidebar content
            preg_match('/<aside class="app-sidebar" id="appSidebar">(.*?)<\/aside>/s', $content, $matches);
            $this->assertNotEmpty($matches, "Sidebar should exist on $pageRoute");
            $sidebarHtml = $matches[1];

            // Verify sidebar does not contain the stray/duplicate logout button
            $this->assertStringNotContainsString('sidebar-footer', $sidebarHtml, "Sidebar footer with duplicate logout should not be in sidebar on $pageRoute");
            $this->assertStringNotContainsString('<button type="submit"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>', $sidebarHtml, "Stray logout button should not be inside sidebar on $pageRoute");

            // Verify legitimate topbar logout button is present and accessible
            $this->assertStringContainsString('title="Logout"', $content, "Topbar legitimate logout button must exist on $pageRoute");
            $this->assertStringContainsString('topbarUserDropdown', $content, "User dropdown must exist on $pageRoute");
        }
    }

    public function test_dashboard_hamburger_menu_button_has_correct_attributes_and_markup(): void
    {
        $customer = User::create([
            'name' => 'Hamburger Test User',
            'email' => 'hamburger.test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
        ]);

        $response = $this->actingAs($customer)->get(route('customer.dashboard'));
        $response->assertStatus(200);

        // Verify hamburger button exists with proper ID, accessibility attributes, and click handler
        $response->assertSee('id="sidebarToggle"', false);
        $response->assertSee('aria-label="Toggle navigation menu"', false);
        $response->assertSee('aria-controls="appSidebar"', false);
        $response->assertSee('onclick="toggleSidebar(event)"', false);
        $response->assertSee('id="appSidebar"', false);
        $response->assertSee('id="sidebarBackdrop"', false);
    }

    public function test_all_authenticated_dashboards_have_home_button_and_can_navigate_home_without_logout(): void
    {
        $cat = ServiceCategory::first();

        // 1. Customer
        $customer = User::create([
            'name' => 'Home Nav Customer',
            'email' => 'home.customer@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
        ]);

        $resCustomer = $this->actingAs($customer)->get(route('customer.dashboard'));
        $resCustomer->assertStatus(200);
        $resCustomer->assertSee('class="topbar-home-btn"', false);
        $resCustomer->assertSee('href="' . route('home') . '"', false);
        $resCustomer->assertSee('fa-home');
        $resCustomer->assertSee('Home');

        // Customer navigates to homepage
        $homeRes = $this->actingAs($customer)->get(route('home'));
        $homeRes->assertStatus(200);
        $this->assertAuthenticatedAs($customer);

        // 2. Provider
        $providerUser = User::create([
            'name' => 'Home Nav Provider',
            'email' => 'home.provider@example.com',
            'password' => bcrypt('password123'),
            'role' => 'provider',
            'city' => 'Ahmedabad',
        ]);
        Provider::create([
            'user_id' => $providerUser->id,
            'category_id' => $cat->id,
            'business_name' => 'Home Pro',
            'hourly_rate' => 450,
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        $resProvider = $this->actingAs($providerUser)->get(route('provider.dashboard'));
        $resProvider->assertStatus(200);
        $resProvider->assertSee('class="topbar-home-btn"', false);
        $resProvider->assertSee('href="' . route('home') . '"', false);
        $resProvider->assertSee('fa-home');

        // Provider navigates to homepage
        $homeResProvider = $this->actingAs($providerUser)->get(route('home'));
        $homeResProvider->assertStatus(200);
        $this->assertAuthenticatedAs($providerUser);

        // 3. Admin
        $admin = User::create([
            'name' => 'Home Nav Admin',
            'email' => 'home.admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'city' => 'Ahmedabad',
        ]);

        $resAdmin = $this->actingAs($admin)->get(route('admin.dashboard'));
        $resAdmin->assertStatus(200);
        $resAdmin->assertSee('class="topbar-home-btn"', false);
        $resAdmin->assertSee('href="' . route('home') . '"', false);
        $resAdmin->assertSee('fa-home');

        // Admin navigates to homepage
        $homeResAdmin = $this->actingAs($admin)->get(route('home'));
        $homeResAdmin->assertStatus(200);
        $this->assertAuthenticatedAs($admin);

        // 4. Master Admin
        $masterAdmin = User::create([
            'name' => 'Home Nav Master Admin',
            'email' => 'home.master@example.com',
            'password' => bcrypt('password123'),
            'role' => 'master_admin',
            'city' => 'Ahmedabad',
        ]);

        $resMaster = $this->actingAs($masterAdmin)->get(route('master.dashboard'));
        $resMaster->assertStatus(200);
        $resMaster->assertSee('class="topbar-home-btn"', false);
        $resMaster->assertSee('href="' . route('home') . '"', false);
        $resMaster->assertSee('fa-home');

        // Master Admin navigates to homepage
        $homeResMaster = $this->actingAs($masterAdmin)->get(route('home'));
        $homeResMaster->assertStatus(200);
        $this->assertAuthenticatedAs($masterAdmin);

        // Verify Home button position is before logout button in HTML
        $content = $resCustomer->getContent();
        $userDropdownPos = strpos($content, 'id="topbarUserDropdown"');
        $homeBtnPos = strpos($content, 'class="topbar-home-btn"');
        $logoutPos = strpos($content, 'title="Logout"');

        $this->assertNotFalse($userDropdownPos);
        $this->assertNotFalse($homeBtnPos);
        $this->assertNotFalse($logoutPos);
        $this->assertTrue($userDropdownPos < $homeBtnPos, 'User dropdown must appear before Home button');
        $this->assertTrue($homeBtnPos < $logoutPos, 'Home button must appear before Logout button');
    }

    public function test_customer_dashboard_renders_dynamic_time_based_greeting_markup_and_script(): void
    {
        $customer = User::create([
            'name' => 'Dax',
            'email' => 'dax@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
        ]);

        $response = $this->actingAs($customer)->get(route('customer.dashboard'));
        $response->assertStatus(200);

        // Verify welcome banner container and styling are preserved
        $response->assertSee('welcome-banner', false);
        $response->assertSee("Welcome to HomeServiceHub — here's your home maintenance overview.", false);

        // Verify customer's actual name is present and wrapped with dynamic greeting spans
        $response->assertSee('id="greetingText"', false);
        $response->assertSee('id="greetingEmoji"', false);
        $response->assertSee(', Dax!', false);

        // Verify JavaScript time-based greeting functions exist in the script stack
        $response->assertSee('function getTimeBasedGreeting', false);
        $response->assertSee('Good morning', false);
        $response->assertSee('Good afternoon', false);
        $response->assertSee('Good evening', false);
        $response->assertSee('Good night', false);
        $response->assertSee('function getGreetingEmoji', false);
        $response->assertSee('function updateGreeting', false);
        $response->assertSee('visibilitychange', false);
        $response->assertSee('setInterval(updateGreeting, 60000)', false);
    }

    public function test_search_provider_listing_page_structure_and_responsive_elements(): void
    {
        $cat = ServiceCategory::first();
        $providerUser = User::create([
            'name' => 'Search Grid Provider',
            'email' => 'search.grid@example.com',
            'password' => bcrypt('password123'),
            'role' => 'provider',
            'phone' => '9876543210',
            'city' => 'Ahmedabad',
        ]);

        Provider::create([
            'user_id' => $providerUser->id,
            'category_id' => $cat->id,
            'business_name' => 'Grid Test Services',
            'hourly_rate' => 450,
            'experience_years' => 5,
            'service_area' => 'Ahmedabad, Navrangpura',
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        $response = $this->get(route('search'));
        $response->assertStatus(200);

        // Verify layout containers and headings
        $response->assertSee('search-page-container', false);
        $response->assertSee('Find Local Home Service Providers', false);
        $response->assertSee('Ahmedabad &amp; Gandhinagar Verified Network', false);

        // Verify filter controls
        $response->assertSee('search-filter-card', false);
        $response->assertSee('name="search"', false);
        $response->assertSee('name="category"', false);
        $response->assertSee('name="city"', false);
        $response->assertSee('name="min_rating"', false);
        $response->assertSee('name="neighborhood_trusted"', false);
        $response->assertSee('Apply Filters', false);
        $response->assertSee('Clear All', false);

        // Verify mobile filter collapse controls
        $response->assertSee('search-mobile-filter-btn', false);
        $response->assertSee('data-bs-target="#searchFiltersPanel"', false);
        $response->assertSee('id="searchFiltersPanel"', false);

        // Verify provider cards grid
        $response->assertSee('provider-cards-grid', false);
        $response->assertSee('search-provider-card', false);
        $response->assertSee('card-pricing-bar', false);
        $response->assertSee('Starting from', false);
        $response->assertSee('card-actions-footer', false);
        $response->assertSee('View Profile', false);
        $response->assertSee('Book Now', false);
    }
}


