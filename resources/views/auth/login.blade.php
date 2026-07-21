<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeMaster - @lang('messages.login')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #00b4d8 0%, #7209b7 40%, #f72585 70%, #00b4d8 100%);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .code-bg {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .code-bg .light-streak {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.4;
            pointer-events: none;
        }

        .code-bg .light-streak:nth-child(1) {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(0, 180, 216, 0.4), transparent);
            top: -150px;
            right: -150px;
            animation: floatGlow 20s ease-in-out infinite;
        }

        .code-bg .light-streak:nth-child(2) {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(247, 37, 133, 0.35), transparent);
            bottom: -100px;
            left: -100px;
            animation: floatGlow 25s ease-in-out infinite reverse;
        }

        @keyframes floatGlow {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(40px, -30px) scale(1.2); }
            66% { transform: translate(-30px, 40px) scale(0.8); }
            100% { transform: translate(0, 0) scale(1); }
        }

        .code-bg .code-line {
            position: absolute;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            white-space: nowrap;
            animation: scrollCode 60s linear infinite;
            pointer-events: none;
            opacity: 0.12;
            color: #00e5ff;
            text-shadow: 0 0 20px rgba(0, 229, 255, 0.1);
        }

        .code-bg .code-line:nth-child(3) { top: 10%; left: 5%; animation-delay: 0s; }
        .code-bg .code-line:nth-child(4) { top: 30%; right: 8%; animation-delay: -15s; color: #ff00ff; }
        .code-bg .code-line:nth-child(5) { top: 50%; left: 3%; animation-delay: -30s; }
        .code-bg .code-line:nth-child(6) { top: 70%; right: 5%; animation-delay: -45s; color: #ff00ff; }
        .code-bg .code-line:nth-child(7) { top: 90%; left: 10%; animation-delay: -10s; }
        .code-bg .code-line:nth-child(8) { top: 20%; left: 50%; animation-delay: -25s; color: #ff00ff; }
        .code-bg .code-line:nth-child(9) { top: 60%; left: 60%; animation-delay: -35s; }
        .code-bg .code-line:nth-child(10) { top: 80%; left: 80%; animation-delay: -5s; color: #ff00ff; }

        @keyframes scrollCode {
            0% { transform: translateX(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateX(-400px); opacity: 0; }
        }

        .btn-home {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 100;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.7);
            padding: 8px 16px;
            font-size: 0.8rem;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-home:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 0 30px rgba(0, 229, 255, 0.05);
        }
        .btn-home i { font-size: 0.9rem; }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 35px 30px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.2), 0 0 60px rgba(0, 229, 255, 0.05);
        }

        .login-card .logo {
            text-align: center;
            margin-bottom: 25px;
        }
        .login-card .logo a {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0a1628;
            text-decoration: none;
            letter-spacing: -0.5px;
        }
        .login-card .logo .highlight { color: #ffd700; }
        .login-card .logo .accent { color: #ffd700; }
        .login-card .logo .subtitle {
            font-size: 0.75rem;
            color: rgba(10, 22, 40, 0.4);
            margin-top: 3px;
            font-weight: 400;
        }

        .login-card .title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #0a1628;
            margin-bottom: 3px;
        }
        .login-card .description {
            font-size: 0.8rem;
            color: rgba(10, 22, 40, 0.5);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 14px;
        }
        .form-group label {
            font-size: 0.65rem;
            font-weight: 600;
            color: rgba(10, 22, 40, 0.5);
            margin-bottom: 3px;
            display: block;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .input-group-custom {
            position: relative;
        }
        .input-group-custom .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(10, 22, 40, 0.2);
            font-size: 0.8rem;
        }
        .input-group-custom .form-control {
            background: #ffffff;
            border: 1px solid rgba(10, 22, 40, 0.08);
            border-radius: 10px;
            padding: 9px 14px 9px 38px;
            color: #0a1628;
            font-size: 0.85rem;
            transition: all 0.3s;
            width: 100%;
            height: 40px;
        }
        .input-group-custom .form-control:focus {
            border-color: #00e5ff;
            box-shadow: 0 0 0 3px rgba(0, 229, 255, 0.15);
            outline: none;
        }
        .input-group-custom .form-control::placeholder {
            color: rgba(10, 22, 40, 0.2);
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
        }
        .form-check input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #00e5ff;
            cursor: pointer;
        }
        .form-check label {
            color: rgba(10, 22, 40, 0.5);
            font-size: 0.8rem;
            cursor: pointer;
        }
        .forgot-link {
            color: #9600ff;
            font-size: 0.8rem;
            text-decoration: none;
            transition: all 0.3s;
            margin-left: auto;
            font-weight: 500;
        }
        .forgot-link:hover {
            color: #00e5ff;
        }

        .btn-login {
            width: 100%;
            padding: 11px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            color: #fff;
            background: linear-gradient(135deg, #00bcd4, #00e5ff);
            transition: all 0.3s;
            cursor: pointer;
            box-shadow: 0 0 30px rgba(0, 229, 255, 0.2);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 50px rgba(0, 229, 255, 0.4);
        }

        .register-link {
            text-align: center;
            margin-top: 14px;
            color: rgba(10, 22, 40, 0.4);
            font-size: 0.8rem;
        }
        .register-link a {
            color: #9600ff;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .register-link a:hover {
            color: #00e5ff;
        }

        .lang-selector {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
        }
        .lang-selector .lang-btn {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            color: rgba(255, 255, 255, 0.7);
            padding: 5px 12px;
            font-size: 0.75rem;
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
        }
        .lang-selector .lang-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
        }
        .lang-selector .dropdown-menu {
            background: rgba(10, 22, 40, 0.85);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 6px;
            min-width: 140px;
        }
        .lang-selector .dropdown-item {
            color: rgba(255, 255, 255, 0.7);
            border-radius: 6px;
            padding: 6px 14px;
            font-size: 0.8rem;
        }
        .lang-selector .dropdown-item:hover {
            background: rgba(0, 229, 255, 0.2);
            color: #fff;
        }
        .lang-selector .dropdown-item.active {
            background: rgba(0, 229, 255, 0.3);
            color: #00e5ff;
        }

        @media (max-width: 576px) {
            .login-card { padding: 25px 18px; }
            .login-card .logo a { font-size: 1.3rem; }
            .login-card .title { font-size: 1.1rem; }
            .btn-home { top: 10px; left: 10px; padding: 5px 12px; font-size: 0.7rem; }
            .lang-selector { top: 10px; right: 10px; }
            .lang-selector .lang-btn { padding: 4px 10px; font-size: 0.65rem; }
        }
    </style>
</head>
<body>

    <!-- ===== СВЕТЯЩИЙСЯ ФОН С КОДОМ ===== -->
    <div class="code-bg">
        <div class="light-streak"></div>
        <div class="light-streak"></div>
        <div class="code-line">&lt;?php namespace App; class User { public $name; } ?&gt;</div>
        <div class="code-line">function authenticate() { return auth()->user(); }</div>
        <div class="code-line">SELECT * FROM users WHERE email = ?;</div>
        <div class="code-line">.login-form { display: flex; flex-direction: column; }</div>
        <div class="code-line">&lt;input type="email" placeholder="Email" /&gt;</div>
        <div class="code-line">const user = await auth.login(credentials);</div>
        <div class="code-line">Route::post('/login', [AuthController::class, 'login']);</div>
        <div class="code-line">if (Auth::attempt($credentials)) { return redirect('/'); }</div>
    </div>

    <a href="{{ url('/') }}" class="btn-home">
        <i class="fas fa-home"></i> Home
    </a>

    <div class="lang-selector">
        <div class="dropdown">
            <button class="lang-btn dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown">
                @php
                    $currentLang = App::getLocale();
                    $languages = [
                        'ru' => ['flag' => '🇷🇺', 'name' => 'Рус'],
                        'uz' => ['flag' => '🇺🇿', 'name' => 'O\'zb'],
                        'en' => ['flag' => '🇬🇧', 'name' => 'Eng'],
                    ];
                    $current = $languages[$currentLang] ?? ['flag' => '🌐', 'name' => 'Яз'];
                @endphp
                <span class="flag">{{ $current['flag'] }}</span> {{ $current['name'] }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <form action="{{ route('change.language') }}" method="POST">
                        @csrf
                        <input type="hidden" name="language" value="ru">
                        <button class="dropdown-item {{ App::getLocale() == 'ru' ? 'active' : '' }}" type="submit">🇷🇺 Русский</button>
                    </form>
                </li>
                <li>
                    <form action="{{ route('change.language') }}" method="POST">
                        @csrf
                        <input type="hidden" name="language" value="uz">
                        <button class="dropdown-item {{ App::getLocale() == 'uz' ? 'active' : '' }}" type="submit">🇺🇿 O'zbekcha</button>
                    </form>
                </li>
                <li>
                    <form action="{{ route('change.language') }}" method="POST">
                        @csrf
                        <input type="hidden" name="language" value="en">
                        <button class="dropdown-item {{ App::getLocale() == 'en' ? 'active' : '' }}" type="submit">🇬🇧 English</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="logo">
                <a href="{{ url('/') }}">
                    <span class="highlight">&lt;</span>Code<span class="accent">Master</span><span class="highlight">/&gt;</span>
                </a>
                <div class="subtitle">@lang('messages.learn_programming')</div>
            </div>

            <div class="title">@lang('messages.welcome_back')</div>
            <div class="description">@lang('messages.login_description')</div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">@lang('messages.email')</label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" 
                               placeholder="you@example.com" required autocomplete="email" autofocus>
                        @error('email')
                            <div class="invalid-feedback" style="color: #ff6b6b; font-size: 0.7rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">@lang('messages.password')</label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" placeholder="••••••••" required autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback" style="color: #ff6b6b; font-size: 0.7rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">@lang('messages.remember_me')</label>
                    @if (Route::has('password.request'))
                        <a class="forgot-link" href="{{ route('password.request') }}">@lang('messages.forgot_password')</a>
                    @endif
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> @lang('messages.login')
                </button>

                <div class="register-link">
                    @lang('messages.no_account') <a href="{{ route('register') }}">@lang('messages.register')</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>