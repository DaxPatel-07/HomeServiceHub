<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Home Service Hub') - Trusted Home Services</title>
    <meta name="description" content="@yield('meta_description', 'Find trusted local home service providers - electricians, plumbers, carpenters, painters, AC technicians and more.')">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/homeservicehub-icon.svg') }}">
    <link rel="alternate icon" type="image/png" href="{{ asset('images/homeservicehub-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Poppins:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body>
    <a href="#mainContent" class="skip-to-content">Skip to main content</a>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-main">
        <div class="container">
            <a class="navbar-brand d-inline-flex align-items-center" href="{{ route('home') }}" aria-label="HomeServiceHub Home">
                <img src="{{ asset('images/logo-horizontal.svg') }}" alt="HomeServiceHub" height="38" class="brand-logo-img">
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav me-auto ms-3 gap-1">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('search') }}">Services</a></li>
                    <li class="nav-item">
                        @if(request()->routeIs('home'))
                            <a class="nav-link" href="javascript:void(0)" onclick="toggleFloatingChat()"><i class="fas fa-robot me-1" style="color:var(--color-primary)"></i>AI Assistant</a>
                        @else
                            <a class="nav-link" href="{{ route('home') }}?open_ai=1"><i class="fas fa-robot me-1" style="color:var(--color-primary)"></i>AI Assistant</a>
                        @endif
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('search') }}">Find Providers</a></li>
                </ul>
                <div class="d-flex gap-2 align-items-center">
                    <div class="nav-location-pill d-none d-xl-inline-flex">
                        <i class="fas fa-map-marker-alt" style="color:var(--brand-blue)"></i>
                        <span>Ahmedabad, India</span>
                        <i class="fas fa-chevron-down ms-1" style="font-size:.65rem;color:var(--text-muted)"></i>
                    </div>
                    @auth
                        @if(auth()->user()->role === 'customer')
                            <a href="{{ route('customer.dashboard') }}" class="btn-primary-custom btn" style="border-radius:20px;padding:.42rem 1.25rem;">Dashboard</a>
                        @elseif(auth()->user()->role === 'provider')
                            <a href="{{ route('provider.dashboard') }}" class="btn-primary-custom btn" style="border-radius:20px;padding:.42rem 1.25rem;">Dashboard</a>
                        @elseif(auth()->user()->isMasterAdmin())
                            <a href="{{ route('master.dashboard') }}" class="btn-primary-custom btn" style="border-radius:20px;padding:.42rem 1.25rem;">Dashboard</a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="btn-primary-custom btn" style="border-radius:20px;padding:.42rem 1.25rem;">Dashboard</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-login nav-link">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('become-provider') }}" class="nav-link" style="color:var(--brand-deep-blue);font-weight:600;font-size:.88rem;padding:.4rem .75rem;white-space:nowrap;"><i class="fas fa-hard-hat me-1" style="color:var(--brand-blue);"></i>Become a Pro</a>
                        <a href="{{ route('login') }}" class="nav-link btn-login">Login</a>
                        <a href="{{ route('register') }}" class="btn-register nav-link">Book a Service</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="container mt-3">
            <div class="alert-custom alert-success-custom"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="container mt-3">
            <div class="alert-custom alert-error-custom"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        </div>
    @endif

    <main id="mainContent">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="footer-brand mb-2">
                        <a href="{{ route('home') }}" aria-label="HomeServiceHub Home" style="display:inline-block;text-decoration:none;">
                            <img src="{{ asset('images/logo.svg') }}" alt="HomeServiceHub" height="42" class="brand-logo-img">
                        </a>
                    </div>
                    <p class="footer-desc">Connecting you with trusted home service professionals in your neighborhood.</p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <div class="col-md-2">
                    <h6>Services</h6>
                    <ul style="list-style:none;padding:0;">
                        @foreach(['Electrician','Plumber','Carpenter','Painter','AC Technician','Cleaner'] as $s)
                            <li><a href="{{ route('search') }}?search={{ $s }}">{{ $s }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-2">
                    <h6>Company</h6>
                    <ul style="list-style:none;padding:0;">
                        @foreach(['About Us','Careers','Blog','Press','Contact'] as $l)
                            <li><a href="#">{{ $l }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6>Contact Us</h6>
                    <p style="font-size:.85rem;"><i class="fas fa-phone me-2" style="color:var(--color-primary);"></i> +91 1800-HSH-HELP</p>
                    <p style="font-size:.85rem;"><i class="fas fa-envelope me-2" style="color:var(--color-primary);"></i> support@hsh.com</p>
                    <p style="font-size:.85rem;"><i class="fas fa-map-marker-alt me-2" style="color:var(--color-primary);"></i> Ahmedabad &amp; Gandhinagar, Gujarat</p>
                </div>
            </div>
            <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <p>&copy; {{ date('Y') }} HomeServiceHub. All rights reserved.</p>
                <div class="d-flex gap-3" style="font-size:.82rem;">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Security</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/utils.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    @if(request()->routeIs('home'))
        @include('partials.ai-chatbox')
        <script src="{{ asset('js/ai-chat.js') }}"></script>
    @endif
    @stack('scripts')
</body>
</html>
