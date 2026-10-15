<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - HomeServiceHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #FDF8F4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        /* Decorative background blobs */
        body::before {
            content: '';
            position: fixed;
            top: -120px;
            left: -120px;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: rgba(231, 206, 183, 0.40);
            z-index: 0;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -120px;
            right: -120px;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: rgba(238, 215, 193, 0.38);
            z-index: 0;
        }
        .deco-tr {
            position: fixed;
            top: -60px;
            right: -60px;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(231, 206, 183, 0.28);
            z-index: 0;
        }
        .deco-bl {
            position: fixed;
            bottom: -80px;
            left: -60px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(238, 215, 193, 0.30);
            z-index: 0;
        }

        /* Login Card */
        .login-card {
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 8px 48px rgba(50, 30, 10, 0.10);
            padding: 48px 52px;
            width: 100%;
            max-width: 460px;
            position: relative;
            z-index: 1;
        }

        /* Brand inside card */
        .card-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            margin-bottom: 26px;
        }
        .card-brand svg {
            width: 30px;
            height: 30px;
        }
        .card-brand .brand-text {
            font-size: 16px;
            font-weight: 700;
            color: #1A1A2E;
        }

        /* Card title */
        .card-title {
            text-align: center;
            margin-bottom: 30px;
        }
        .card-title h2 {
            font-size: 27px;
            font-weight: 800;
            color: #1C1A18;
            letter-spacing: -0.4px;
            margin-bottom: 7px;
        }
        .card-title p {
            font-size: 14px;
            color: #7B6F65;
            font-weight: 400;
        }

        /* Form fields */
        .field-group { margin-bottom: 16px; }
        .field-wrap {
            display: flex;
            align-items: center;
            background: #FFFFFF;
            border: 1.5px solid #D8D0C6;
            border-radius: 10px;
            overflow: hidden;
            transition: border-color 0.2s;
        }
        .field-wrap:focus-within { border-color: #1965DD; }
        .field-icon {
            padding: 0 14px;
            color: #9E9186;
            font-size: 15px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
        }
        .field-wrap input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            padding: 14px 0;
            font-size: 15px;
            color: #2A2218;
            font-family: 'Inter', sans-serif;
        }
        .field-wrap input::placeholder { color: #B0A498; }
        .field-toggle {
            padding: 0 14px;
            color: #9E9186;
            font-size: 15px;
            flex-shrink: 0;
            cursor: pointer;
            background: none;
            border: none;
            outline: none;
            display: flex;
            align-items: center;
        }
        .field-toggle:hover { color: #6B5D52; }

        /* Remember + Forgot row */
        .row-remember {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }
        .remember-check {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .remember-check input[type="checkbox"] {
            width: 17px;
            height: 17px;
            accent-color: #1965DD;
            cursor: pointer;
        }
        .remember-check label {
            font-size: 14px;
            color: #3A3028;
            font-weight: 500;
            cursor: pointer;
        }
        .forgot-link {
            font-size: 14px;
            font-weight: 600;
            color: #C0392B;
            text-decoration: none;
        }
        .forgot-link:hover { text-decoration: underline; }

        /* Login button */
        .btn-login {
            display: block;
            width: 100%;
            padding: 15px;
            background: #1965DD;
            color: #fff;
            font-size: 17px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            letter-spacing: 0.2px;
            transition: background 0.2s, transform 0.1s;
            margin-bottom: 18px;
        }
        .btn-login:hover { background: #1456C4; transform: translateY(-1px); }
        .btn-login:active { transform: translateY(0); }

        /* OR divider */
        .or-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        .or-divider::before,
        .or-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #E2D9D0;
        }
        .or-divider span {
            font-size: 13px;
            font-weight: 500;
            color: #9E9186;
        }

        /* Google button */
        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            padding: 13px;
            background: #FFFFFF;
            border: 1.5px solid #D8D0C6;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            color: #2A2218;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s;
            text-decoration: none;
            margin-bottom: 22px;
        }
        .btn-google:hover {
            background: #F7F0E8;
            border-color: #B8AFA7;
            color: #1A1208;
        }

        /* Signup link */
        .signup-text {
            text-align: center;
            font-size: 14px;
            color: #7B6F65;
        }
        .signup-text a {
            color: #1965DD;
            font-weight: 700;
            text-decoration: none;
        }
        .signup-text a:hover { text-decoration: underline; }

        /* Error */
        .alert-error {
            background: #FEF2F2;
            border: 1px solid #FCA5A5;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px;
            color: #DC2626;
            font-weight: 500;
        }

        /* Mobile */
        @media (max-width: 500px) {
            .login-card { padding: 36px 24px; }
            .card-title h2 { font-size: 23px; }
        }
    </style>
</head>
<body>

<div class="deco-tr"></div>
<div class="deco-bl"></div>

<div class="login-card">

    <!-- Brand Logo -->
    <div class="card-brand">
        <svg viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15 2.5L27.5 13V27H20V18H10V27H2.5V13L15 2.5Z" fill="#1965DD"/>
            <rect x="10" y="17" width="10" height="10" rx="0.5" fill="#1965DD"/>
            <rect x="11.5" y="12" width="7" height="6" rx="0.8" fill="white" opacity="0.8"/>
            <rect x="20" y="7" width="3.5" height="7" rx="0.4" fill="#1965DD"/>
        </svg>
        <span class="brand-text">HomeServiceHub</span>
    </div>

    <!-- Title -->
    <div class="card-title">
        <h2>Welcome Back</h2>
        <p>Log in to manage your home services.</p>
    </div>

    @if($errors->any())
    <div class="alert-error">
        <i class="fas fa-exclamation-circle"></i>
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <input type="hidden" name="login_role" id="loginRole" value="customer">

        <!-- Email -->
        <div class="field-group">
            <div class="field-wrap">
                <span class="field-icon"><i class="far fa-envelope"></i></span>
                <input
                    type="email"
                    name="email"
                    placeholder="Email or Mobile Number"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                >
            </div>
        </div>

        <!-- Password -->
        <div class="field-group">
            <div class="field-wrap">
                <span class="field-icon"><i class="fas fa-lock"></i></span>
                <input
                    type="password"
                    name="password"
                    id="loginPassword"
                    placeholder="Password"
                    required
                    autocomplete="current-password"
                >
                <button type="button" class="field-toggle" onclick="togglePwd()" aria-label="Toggle password">
                    <i class="far fa-eye" id="eyeIcon"></i>
                </button>
            </div>
        </div>

        <!-- Remember + Forgot -->
        <div class="row-remember">
            <div class="remember-check">
                <input type="checkbox" name="remember" id="rememberMe" value="1">
                <label for="rememberMe">Remember me</label>
            </div>
            <a href="#" class="forgot-link">Forgot Password?</a>
        </div>

        <!-- Login Button -->
        <button type="submit" class="btn-login">Login</button>
    </form>

    <!-- OR Divider -->
    <div class="or-divider"><span>OR</span></div>

    <!-- Google Sign In -->
    <a href="#" class="btn-google" onclick="return false;">
        <svg width="22" height="22" viewBox="0 0 22 22" xmlns="http://www.w3.org/2000/svg">
            <path d="M21.8 11.25c0-.75-.07-1.47-.2-2.16H11.2v4.09h5.95a5.09 5.09 0 01-2.21 3.34v2.77h3.57c2.09-1.92 3.29-4.76 3.29-8.04z" fill="#4285F4"/>
            <path d="M11.2 22c2.98 0 5.48-1 7.3-2.71l-3.57-2.77c-.99.66-2.26 1.06-3.73 1.06-2.87 0-5.3-1.94-6.16-4.54H1.35v2.86A11 11 0 0011.2 22z" fill="#34A853"/>
            <path d="M5.04 13.04A6.6 6.6 0 014.7 11c0-.7.12-1.38.34-2.04V6.1H1.35A11 11 0 00.2 11c0 1.77.43 3.44 1.15 4.9l3.69-2.86z" fill="#FBBC05"/>
            <path d="M11.2 4.42c1.62 0 3.07.56 4.21 1.65l3.16-3.16C16.67 1.1 14.17 0 11.2 0A11 11 0 001.35 6.1L5.04 8.96c.86-2.6 3.29-4.54 6.16-4.54z" fill="#EA4335"/>
        </svg>
        Sign in with Google
    </a>

    <!-- Sign Up Link -->
    <div class="signup-text">
        Don't have an account? <a href="{{ route('register') }}">Create Account</a>
    </div>

</div>

<script>
function togglePwd() {
    const input = document.getElementById('loginPassword');
    const icon  = document.getElementById('eyeIcon');
    if (!input) return;
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@stack('scripts')
</body>
</html>
