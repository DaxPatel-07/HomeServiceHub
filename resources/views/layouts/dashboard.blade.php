<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - HomeServiceHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <aside class="app-sidebar" id="appSidebar">
        <div class="sidebar-brand">
            <a href="{{ route('home') }}" style="text-decoration:none;">
                <div class="brand-name"><i class="fas fa-tools me-2" style="color:var(--brand-blue)"></i>Home<span style="color:var(--brand-blue)">Service</span>Hub</div>
                <div class="brand-sub">@yield('role-label', 'Dashboard')</div>
            </a>
        </div>
        <div class="sidebar-user">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
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
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
            </form>
        </div>
    </aside>

    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()" aria-hidden="true"></div>

    <!-- Main Content -->
    <main class="app-content">
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
                <button type="button" class="btn btn-sm d-none d-md-inline-flex align-items-center" onclick="toggleFloatingChat()" style="background:rgba(37,99,235,.08);color:var(--brand-blue);border:1px solid rgba(37,99,235,.2);border-radius:20px;padding:.38rem .95rem;font-weight:600;font-size:.82rem;cursor:pointer;">
                    <i class="fas fa-robot me-1"></i>AI Assistant
                </button>
                <a href="{{ route('customer.reminders') }}" class="topbar-notif-btn" title="Maintenance Reminders">
                    <i class="fas fa-bell"></i>
                    <span class="topbar-notif-badge"></span>
                </a>
                @endif

                {{-- User Profile Menu Dropdown --}}
                <div class="dropdown">
                    <button class="btn d-flex align-items-center gap-2 dropdown-toggle" type="button" id="topbarUserDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background:var(--brand-cream);border:1px solid var(--card-border);border-radius:20px;padding:.32rem .85rem;font-size:.85rem;font-weight:700;color:var(--brand-deep-blue);box-shadow:none;">
                        <div class="avatar-sm" style="background:var(--brand-blue);color:#fff;width:28px;height:28px;font-size:.8rem;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="topbarUserDropdown" style="border-radius:12px;border:1px solid var(--card-border);min-width:210px;margin-top:.5rem;padding:.5rem 0;">
                        <li class="px-3 py-2 border-bottom" style="background:var(--bg-light);">
                            <div style="font-weight:700;font-size:.85rem;color:var(--brand-deep-blue);">{{ auth()->user()->name }}</div>
                            <div style="font-size:.72rem;color:var(--text-muted);word-break:break-all;">{{ auth()->user()->email }}</div>
                            <span class="badge mt-1" style="background:rgba(37,99,235,.1);color:var(--brand-blue);font-size:.65rem;font-weight:700;text-transform:uppercase;">
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
                        @endif
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
