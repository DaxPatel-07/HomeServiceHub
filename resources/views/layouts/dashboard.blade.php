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

    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    <!-- Main Content -->
    <main class="app-content">
        <div class="app-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm" id="sidebarToggle" onclick="toggleSidebar()" style="background:var(--bg-light);border:1px solid var(--card-border);border-radius:8px;padding:.4rem .7rem;">
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

            <div class="topbar-right">
                @if(auth()->check() && auth()->user()->role === 'customer')
                <button type="button" class="btn btn-sm" onclick="toggleFloatingChat()" style="background:rgba(37,99,235,.08);color:var(--brand-blue);border:1px solid rgba(37,99,235,.2);border-radius:20px;padding:.38rem .95rem;font-weight:600;font-size:.82rem;cursor:pointer;">
                    <i class="fas fa-robot me-1"></i>AI Assistant
                </button>
                <a href="{{ route('customer.reminders') }}" class="topbar-notif-btn" title="Maintenance Reminders">
                    <i class="fas fa-bell"></i>
                    <span class="topbar-notif-badge"></span>
                </a>
                @endif
                <a href="{{ auth()->user()->role === 'customer' ? route('customer.profile') : (auth()->user()->role === 'provider' ? route('provider.profile') : '#') }}" class="d-flex align-items-center gap-2" style="background:var(--brand-cream);border:1px solid var(--card-border);border-radius:20px;padding:.32rem .85rem;text-decoration:none;">
                    <div class="avatar-sm" style="background:var(--brand-blue);color:#fff;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <span style="font-size:.85rem;font-weight:700;color:var(--brand-deep-blue);">{{ auth()->user()->name }}</span>
                </a>
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
function toggleSidebar() {
    const sb = document.getElementById('appSidebar');
    const bd = document.getElementById('sidebarBackdrop');
    if (sb) sb.classList.toggle('open');
    if (bd) bd.classList.toggle('active');
}
</script>
@if(auth()->check() && auth()->user()->role === 'customer')
    @include('partials.ai-chatbox')
    <script src="{{ asset('js/ai-chat.js') }}"></script>
@endif
@stack('scripts')
</body>
</html>
