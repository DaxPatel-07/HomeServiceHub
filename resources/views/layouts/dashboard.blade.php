<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - HomeServiceHub</title>
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
<div class="d-flex app-layout-wrapper">
    <!-- Sidebar -->
    <aside class="app-sidebar" id="appSidebar">
        <div class="sidebar-brand">
            <a href="{{ route('home') }}" class="d-flex align-items-center gap-2" style="text-decoration:none;" title="HomeServiceHub">
                <img src="{{ asset('images/logo-mark.svg') }}" alt="HomeServiceHub" width="34" height="34" class="brand-logo-img" style="flex-shrink:0;object-fit:contain;">
                <div>
                    <div class="brand-name" style="font-family:'Poppins',sans-serif;font-weight:800;line-height:1.1;color:var(--color-text-dark, #14213D);">HomeService<span style="color:var(--color-primary)">Hub</span></div>
                    <div class="brand-sub">@yield('role-label', 'Dashboard')</div>
                </div>
            </a>
        </div>
        <div class="sidebar-user">
            <div class="avatar" style="background:var(--color-primary);">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="user-info">
                <div class="name">{{ auth()->user()->name }}</div>
                <div class="role">{{ ucfirst(str_replace('_',' ', auth()->user()->role)) }}</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            @yield('sidebar-links')
        </nav>
        <div class="sidebar-decor">
            <div class="plant-icon">🪴</div>
            <div class="plant-quote">Better Services<br>Happier Homes ♡</div>
        </div>
        @hasSection('sidebar-footer')
        <div class="sidebar-footer">
            @yield('sidebar-footer')
        </div>
        @endif
    </aside>

    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()" aria-hidden="true"></div>

    <!-- Main Content -->
    <main class="app-content" id="mainContent">
        <div class="app-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm" id="sidebarToggle" type="button" aria-label="Toggle navigation menu" aria-controls="appSidebar" aria-expanded="false" onclick="toggleSidebar(event)" style="background:var(--bg-light);border:1px solid var(--card-border);border-radius:8px;padding:.4rem .7rem;">
                    <i class="fas fa-bars" style="color:var(--text-secondary);"></i>
                </button>
                <span class="page-title">@yield('page-title', 'Dashboard')</span>
            </div>
            
            <div class="topbar-search d-none d-lg-block">
                <i class="fas fa-search"></i>
                <form action="{{ route('search') }}" method="GET" class="m-0">
                    <input type="text" name="search" placeholder="Search for services, providers...">
                </form>
            </div>

            <div class="topbar-right d-flex align-items-center gap-2">
                @if(auth()->check() && auth()->user()->role === 'customer')
                <button type="button" class="btn btn-sm d-none d-md-inline-flex align-items-center" onclick="toggleFloatingChat()" style="background:var(--color-blue-tint);color:var(--color-primary);border:1px solid rgba(36,81,189,.2);border-radius:20px;padding:.38rem .95rem;font-weight:600;font-size:.82rem;cursor:pointer;">
                    <i class="fas fa-robot me-1"></i>AI Assistant
                </button>
                <a href="{{ route('customer.reminders') }}" class="topbar-notif-btn" title="Maintenance Reminders">
                    <i class="fas fa-bell"></i>
                    <span class="topbar-notif-badge"></span>
                </a>
                @elseif(auth()->check() && auth()->user()->role === 'provider')
                <a href="{{ route('provider.bookings') }}" class="topbar-notif-btn" title="Provider Bookings">
                    <i class="fas fa-bell"></i>
                    @php
                        $pendingCount = auth()->user()->provider ? auth()->user()->provider->bookings()->where('status', 'pending')->count() : 0;
                    @endphp
                    @if($pendingCount > 0)
                        <span class="topbar-notif-badge" style="background:#EF4444;"></span>
                    @endif
                </a>
                @endif

                {{-- User Profile Menu Dropdown --}}
                <div class="dropdown">
                    <button class="btn d-flex align-items-center gap-2 dropdown-toggle" type="button" id="topbarUserDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background:#FFFFFF;border:1px solid var(--color-border);border-radius:20px;padding:.32rem .85rem;font-size:.85rem;font-weight:700;color:var(--color-text);box-shadow:none;">
                        <div class="avatar-sm" style="background:var(--color-primary);color:#fff;width:28px;height:28px;font-size:.8rem;border-radius:50%;display:flex;align-items:center;justify-content:center;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="topbarUserDropdown" style="border-radius:12px;border:1px solid var(--color-border);min-width:210px;margin-top:.5rem;padding:.5rem 0;">
                        <li class="px-3 py-2 border-bottom" style="background:var(--color-background);">
                            <div style="font-weight:700;font-size:.85rem;color:var(--color-text);">{{ auth()->user()->name }}</div>
                            <div style="font-size:.72rem;color:var(--color-muted);word-break:break-all;">{{ auth()->user()->email }}</div>
                            <span class="badge mt-1" style="background:var(--color-blue-tint);color:var(--color-primary);font-size:.65rem;font-weight:700;text-transform:uppercase;">
                                {{ ucfirst(str_replace('_',' ', auth()->user()->role)) }}
                            </span>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ auth()->user()->role === 'customer' ? route('customer.profile') : (auth()->user()->role === 'provider' ? route('provider.profile') : '#') }}" style="font-size:.85rem;font-weight:600;color:var(--brand-deep-blue);">
                                <i class="fas fa-user-circle me-2 text-primary"></i>My Profile
                            </a>
                        </li>
                        @if(auth()->check() && auth()->user()->role === 'customer')
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('customer.bookings') }}" style="font-size:.85rem;font-weight:600;color:var(--brand-deep-blue);">
                                <i class="fas fa-calendar-check me-2 text-primary"></i>My Bookings
                            </a>
                        </li>
                        @elseif(auth()->check() && auth()->user()->role === 'provider')
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('provider.bookings') }}" style="font-size:.85rem;font-weight:600;color:var(--brand-deep-blue);">
                                <i class="fas fa-calendar-check me-2 text-primary"></i>Manage Bookings
                            </a>
                        </li>
                        @endif
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('home') }}" style="font-size:.85rem;font-weight:600;color:var(--brand-deep-blue);">
                                <i class="fas fa-home me-2 text-primary"></i>Home
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger" style="font-size:.85rem;font-weight:700;background:none;border:none;width:100%;text-align:left;cursor:pointer;">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

                {{-- Direct Topbar Home Button --}}
                <a href="{{ route('home') }}" class="topbar-home-btn" title="Go to HomeServiceHub Homepage" aria-label="Home">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>

                {{-- Direct Topbar Logout Button --}}
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm d-flex align-items-center gap-1" style="background:rgba(239,68,68,.08);color:#EF4444;border:1px solid rgba(239,68,68,.25);border-radius:20px;padding:.36rem .85rem;font-size:.82rem;font-weight:600;transition:var(--transition);" onmouseover="this.style.background='#EF4444';this.style.color='#fff';" onmouseout="this.style.background='rgba(239,68,68,.08)';this.style.color='#EF4444';" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="d-none d-md-inline">Logout</span>
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="mx-4 mt-4">
                <div class="alert-custom alert-success-custom"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="mx-4 mt-4">
                <div class="alert-custom alert-error-custom"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            </div>
        @endif
        @if(session('info'))
            <div class="mx-4 mt-4">
                <div class="alert-custom alert-info-custom" style="background:rgba(37,99,235,.08);color:var(--color-primary);border:1px solid rgba(37,99,235,.2);"><i class="fas fa-info-circle"></i> {{ session('info') }}</div>
            </div>
        @endif

        <div class="app-page">
            @yield('content')
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/utils.js') }}"></script>
<script src="{{ asset('js/main.js') }}"></script>
<script>
function isMobileView() {
    return window.innerWidth <= 991;
}

function updateAriaState(isOpen) {
    const toggleBtn = document.getElementById('sidebarToggle');
    if (toggleBtn) {
        toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }
}

function openSidebar() {
    const sb = document.getElementById('appSidebar');
    const bd = document.getElementById('sidebarBackdrop');
    const appContent = document.querySelector('.app-content');
    
    if (isMobileView()) {
        if (sb) sb.classList.add('open');
        if (bd) bd.classList.add('active');
        document.body.classList.add('sidebar-mobile-open');
    } else {
        if (sb) sb.classList.remove('collapsed');
        if (appContent) appContent.classList.remove('sidebar-collapsed');
        document.body.classList.remove('sidebar-collapsed');
    }
    updateAriaState(true);
}

function closeSidebar() {
    const sb = document.getElementById('appSidebar');
    const bd = document.getElementById('sidebarBackdrop');
    const appContent = document.querySelector('.app-content');

    if (isMobileView()) {
        if (sb) sb.classList.remove('open');
        if (bd) bd.classList.remove('active');
        document.body.classList.remove('sidebar-mobile-open');
    } else {
        if (sb) sb.classList.add('collapsed');
        if (appContent) appContent.classList.add('sidebar-collapsed');
        document.body.classList.add('sidebar-collapsed');
    }
    updateAriaState(false);
}

function toggleSidebar(e) {
    if (e && e.preventDefault) {
        e.preventDefault();
    }
    const sb = document.getElementById('appSidebar');
    if (!sb) return;

    if (isMobileView()) {
        const isOpen = sb.classList.contains('open');
        if (isOpen) {
            closeSidebar();
        } else {
            openSidebar();
        }
    } else {
        const isCollapsed = sb.classList.contains('collapsed') || document.body.classList.contains('sidebar-collapsed');
        if (isCollapsed) {
            openSidebar();
        } else {
            closeSidebar();
        }
    }
}

// Support Escape key to close mobile drawer
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' || e.key === 'Esc' || e.keyCode === 27) {
        const sb = document.getElementById('appSidebar');
        if (sb && sb.classList.contains('open')) {
            closeSidebar();
            const toggleBtn = document.getElementById('sidebarToggle');
            if (toggleBtn) toggleBtn.focus();
        }
    }
});

// Close mobile drawer on navigation link clicks or click outside
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('appSidebar');
    if (sidebar) {
        sidebar.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && isMobileView()) {
                closeSidebar();
            }
        });
    }

    // Set initial aria-expanded based on screen width
    updateAriaState(!isMobileView());
});

// Responsive resize listener to synchronize classes and attributes
window.addEventListener('resize', function() {
    const sb = document.getElementById('appSidebar');
    const bd = document.getElementById('sidebarBackdrop');
    if (!isMobileView()) {
        if (sb) sb.classList.remove('open');
        if (bd) bd.classList.remove('active');
        document.body.classList.remove('sidebar-mobile-open');
        const isCollapsed = sb && sb.classList.contains('collapsed');
        updateAriaState(!isCollapsed);
    } else {
        const isOpen = sb && sb.classList.contains('open');
        updateAriaState(isOpen);
    }
});
</script>
@if(auth()->check() && auth()->user()->role === 'customer')
    @include('partials.ai-chatbox')
    <script src="{{ asset('js/ai-chat.js') }}"></script>
@endif
@stack('scripts')
</body>
</html>
