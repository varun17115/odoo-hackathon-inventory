<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Invento Market</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: 'Inter', sans-serif; background: #0f172a; }

        .login-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #0f172a;
            position: relative;
            overflow: hidden;
        }

        /* subtle grid bg */
        .login-wrap::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(79,70,229,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(79,70,229,0.06) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* glow blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            pointer-events: none;
        }
        .blob-1 { width: 400px; height: 400px; background: #4f46e5; top: -100px; left: -100px; }
        .blob-2 { width: 300px; height: 300px; background: #7c3aed; bottom: -80px; right: -80px; }

        .login-card {
            position: relative;
            z-index: 1;
            background: #1e293b;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            padding: 40px 36px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.5);
        }

        .login-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 32px;
        }
        .logo-icon {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.1rem;
            box-shadow: 0 4px 16px rgba(79,70,229,0.4);
        }
        .logo-text { font-size: 1.3rem; font-weight: 800; color: #fff; letter-spacing: -0.02em; }
        .logo-text span { color: #818cf8; }

        .login-heading {
            text-align: center;
            margin-bottom: 28px;
        }
        .login-heading h2 {
            font-size: 1.4rem; font-weight: 700; color: #f1f5f9;
            margin-bottom: 6px;
        }
        .login-heading p {
            font-size: 0.82rem; color: #64748b;
        }

        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block; font-size: 0.78rem; font-weight: 600;
            color: #94a3b8; margin-bottom: 6px; letter-spacing: 0.02em;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: #475569; font-size: 0.82rem; pointer-events: none;
        }
        .form-input {
            width: 100%; padding: 10px 12px 10px 36px;
            background: #0f172a; border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px; color: #f1f5f9; font-size: 0.875rem;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }
        .form-input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79,70,229,0.2);
        }
        .form-input::placeholder { color: #334155; }

        .form-row {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px;
        }
        .remember-wrap { display: flex; align-items: center; gap: 7px; }
        .remember-wrap input[type="checkbox"] {
            width: 15px; height: 15px; accent-color: #4f46e5; cursor: pointer;
        }
        .remember-wrap label { font-size: 0.78rem; color: #64748b; cursor: pointer; }
        .forgot-link {
            font-size: 0.78rem; font-weight: 600; color: #818cf8;
            text-decoration: none;
        }
        .forgot-link:hover { color: #a5b4fc; text-decoration: underline; }

        .btn-login {
            width: 100%; padding: 11px;
            background: linear-gradient(135deg, #4f46e5, #6d28d9);
            color: #fff; border: none; border-radius: 10px;
            font-size: 0.9rem; font-weight: 700; cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: opacity 0.15s, transform 0.1s;
            box-shadow: 0 4px 16px rgba(79,70,229,0.35);
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-login:hover { opacity: 0.92; transform: translateY(-1px); }
        .btn-login:active { transform: translateY(0); }

        .divider {
            display: flex; align-items: center; gap: 12px;
            margin: 20px 0; color: #334155; font-size: 0.75rem;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: rgba(255,255,255,0.07);
        }

        .register-link {
            text-align: center; font-size: 0.8rem; color: #64748b;
        }
        .register-link a {
            color: #818cf8; font-weight: 600; text-decoration: none;
        }
        .register-link a:hover { color: #a5b4fc; text-decoration: underline; }

        .error-alert {
            background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3);
            border-radius: 8px; padding: 10px 14px; margin-bottom: 18px;
            font-size: 0.8rem; color: #fca5a5;
        }
        .error-alert i { margin-right: 6px; }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="login-card">

        <div class="login-logo">
            <div class="logo-icon"><i class="fas fa-cubes"></i></div>
            <div class="logo-text">Invento<span>Market</span></div>
        </div>

        <div class="login-heading">
            <h2>Welcome back</h2>
            <p>Sign in to your account to continue</p>
        </div>

        @if($errors->any())
        <div class="error-alert">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" class="form-input"
                        placeholder="you@company.com"
                        value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrap">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" class="form-input"
                        placeholder="••••••••" required>
                </div>
            </div>

            <div class="form-row">
                <div class="remember-wrap">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                <a href="{{ route('password.forgot') }}" class="forgot-link">Forgot password?</a>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>

        <div class="divider">or</div>

        <div class="register-link">
            Don't have an account? <a href="{{ route('register') }}">Create one</a>
        </div>

    </div>
</div>

<script>
@if(session('error'))
Swal.fire({ icon:'error', title:'Login Failed', text:@json(session('error')), background:'#1e293b', color:'#f1f5f9', confirmButtonColor:'#4f46e5' });
@endif
</script>
</body>
</html>
