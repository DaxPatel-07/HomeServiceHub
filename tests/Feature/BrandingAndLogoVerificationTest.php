<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandingAndLogoVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_logo_and_icon_assets_exist_and_are_valid(): void
    {
        $requiredAssets = [
            'images/homeservicehub-logo.svg',
            'images/homeservicehub-logo.png',
            'images/homeservicehub-logo-dark.svg',
            'images/homeservicehub-logo-dark.png',
            'images/homeservicehub-icon.svg',
            'images/homeservicehub-icon.png',
            'images/homeservicehub-icon-dark.svg',
            'images/homeservicehub-icon-dark.png',
            'images/logo.svg',
            'images/logo-horizontal.svg',
            'images/logo-mark.svg',
            'images/logo-dark.svg',
            'images/logo-horizontal-dark.svg',
            'images/logo.png',
            'images/login-brand.png',
            'favicon.ico',
        ];

        foreach ($requiredAssets as $asset) {
            $path = public_path($asset);
            $this->assertFileExists($path, "Asset {$asset} does not exist in public directory.");
            $this->assertGreaterThan(0, filesize($path), "Asset {$asset} is empty (0 bytes).");
        }
    }

    public function test_login_page_renders_logo_and_favicon(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('images/logo-horizontal.svg');
        $response->assertSee('images/homeservicehub-icon.svg');
        $response->assertSee('HomeServiceHub');
        $response->assertSee('Welcome Back');
    }

    public function test_registration_page_renders_logo_and_favicon(): void
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
        $response->assertSee('images/logo-horizontal.svg');
        $response->assertSee('images/homeservicehub-icon.svg');
    }

    public function test_homepage_renders_navbar_and_footer_logos(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('images/logo-horizontal.svg');
        $response->assertSee('images/logo.svg');
        $response->assertSee('images/homeservicehub-icon.svg');
    }

    public function test_customer_dashboard_renders_readable_book_service_cta_button(): void
    {
        $customer = User::create([
            'name' => 'Branding Customer',
            'email' => 'branding.cust@example.com',
            'password' => bcrypt('Password@123'),
            'role' => 'customer',
            'city' => 'Ahmedabad',
            'area' => 'Vastrapur',
            'status' => 'active',
        ]);

        $response = $this->actingAs($customer)->get(route('customer.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('btn-banner-cta');
        $response->assertSee('Book a Service');
        $response->assertDontSee('btn-gold-custom');
        $response->assertSee('images/logo-mark.svg');
        $response->assertSee('images/homeservicehub-icon.svg');
    }

    public function test_reusable_app_logo_component_and_partial_render(): void
    {
        $renderedComponent = (string) $this->blade('<x-app-logo />');
        $this->assertStringContainsString('images/homeservicehub-logo.svg', $renderedComponent);
        $this->assertStringContainsString('HomeServiceHub', $renderedComponent);

        $renderedIcon = (string) $this->blade('<x-app-logo variant="icon" />');
        $this->assertStringContainsString('images/homeservicehub-icon.svg', $renderedIcon);

        $renderedDark = (string) $this->blade('<x-app-logo :dark="true" />');
        $this->assertStringContainsString('images/homeservicehub-logo-dark.svg', $renderedDark);

        $renderedPartial = (string) $this->blade("@include('partials.logo')");
        $this->assertStringContainsString('images/homeservicehub-logo.svg', $renderedPartial);
    }
}
