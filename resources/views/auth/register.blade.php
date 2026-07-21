<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeMaster - @lang('messages.register')</title>
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

        .code-bg .code-line:nth-child(3) { top: 5%; left: 2%; animation-delay: 0s; }
        .code-bg .code-line:nth-child(4) { top: 15%; right: 5%; animation-delay: -10s; color: #ff00ff; }
        .code-bg .code-line:nth-child(5) { top: 25%; left: 8%; animation-delay: -20s; }
        .code-bg .code-line:nth-child(6) { top: 35%; right: 3%; animation-delay: -30s; color: #ff00ff; }
        .code-bg .code-line:nth-child(7) { top: 45%; left: 5%; animation-delay: -40s; }
        .code-bg .code-line:nth-child(8) { top: 55%; right: 8%; animation-delay: -50s; color: #ff00ff; }
        .code-bg .code-line:nth-child(9) { top: 65%; left: 10%; animation-delay: -15s; }
        .code-bg .code-line:nth-child(10) { top: 75%; right: 2%; animation-delay: -25s; color: #ff00ff; }
        .code-bg .code-line:nth-child(11) { top: 85%; left: 4%; animation-delay: -35s; }
        .code-bg .code-line:nth-child(12) { top: 92%; right: 10%; animation-delay: -45s; color: #ff00ff; }

        @keyframes scrollCode {
            0% { transform: translateX(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateX(-500px); opacity: 0; }
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

        .register-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 35px 30px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.2), 0 0 60px rgba(0, 229, 255, 0.05);
        }

        .register-card .logo {
            text-align: center;
            margin-bottom: 25px;
        }
        .register-card .logo a {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0a1628;
            text-decoration: none;
            letter-spacing: -0.5px;
        }
        .register-card .logo .highlight { color: #ffd700; }
        .register-card .logo .accent { color: #ffd700; }
        .register-card .logo .subtitle {
            font-size: 0.75rem;
            color: rgba(10, 22, 40, 0.4);
            margin-top: 3px;
            font-weight: 400;
        }

        .register-card .title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #0a1628;
            margin-bottom: 3px;
        }
        .register-card .description {
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

        .btn-register {
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
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 50px rgba(0, 229, 255, 0.4);
        }

        .login-link {
            text-align: center;
            margin-top: 14px;
            color: rgba(10, 22, 40, 0.4);
            font-size: 0.8rem;
        }
        .login-link a {
            color: #9600ff;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .login-link a:hover {
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
            .register-card { padding: 25px 18px; }
            .register-card .logo a { font-size: 1.3rem; }
            .register-card .title { font-size: 1.1rem; }
            .btn-home { top: 10px; left: 10px; padding: 5px 12px; font-size: 0.7rem; }
            .lang-selector { top: 10px; right: 10px; }
            .lang-selector .lang-btn { padding: 4px 10px; font-size: 0.65rem; }
            .input-group-custom .form-control { height: 36px; font-size: 0.8rem; padding: 7px 12px 7px 34px; }
            .btn-register { padding: 9px; font-size: 0.8rem; }
        }
    </style>
</head>
<body>

    <!-- ===== СВЕТЯЩИЙСЯ ФОН С КОДОМ ===== -->
    <div class="code-bg">
        <div class="light-streak"></div>
        <div class="light-streak"></div>
        <div class="code-line">&lt;?php namespace App; class User { public $name; } ?&gt;</div>
        <div class="code-line">function register() { return User::create($data); }</div>
        <div class="code-line">INSERT INTO users (name, email, password) VALUES (?, ?, ?);</div>
        <div class="code-line">.register-form { display: flex; flex-direction: column; }</div>
        <div class="code-line">&lt;input type="text" placeholder="Full Name" /&gt;</div>
        <div class="code-line">const user = await auth.register(credentials);</div>
        <div class="code-line">Route::post('/register', [AuthController::class, 'register']);</div>
        <div class="code-line">if (Auth::attempt($credentials)) { return redirect('/'); }</div>
    </div>

    <!-- ===== КНОПКА HOME ===== -->
    <a href="{{ url('/') }}" class="btn-home">
        <i class="fas fa-home"></i> Home
    </a>

    <!-- ===== ЯЗЫК ===== -->
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

    <!-- ===== КАРТОЧКА РЕГИСТРАЦИИ ===== -->
    <div class="register-wrapper">
        <div class="register-card">
            <div class="logo">
                <a href="{{ url('/') }}">
                    <span class="highlight">&lt;</span>Code<span class="accent">Master</span><span class="highlight">/&gt;</span>
                </a>
                <div class="subtitle">@lang('messages.learn_programming')</div>
            </div>

            <div class="title">@lang('messages.create_account')</div>
            <div class="description">@lang('messages.register_description')</div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- ===== ИМЯ ПОЛЬЗОВАТЕЛЯ ===== -->
                <div class="form-group">
                    <label for="name">@lang('messages.username')</label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fas fa-user"></i></span>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name') }}" 
                               placeholder="@lang('messages.username_placeholder')" required autocomplete="name" autofocus>
                        @error('name')
                            <div class="invalid-feedback" style="color: #ff6b6b; font-size: 0.7rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- ===== ПОЛНОЕ ИМЯ (ОБЯЗАТЕЛЬНО) ===== -->
                <div class="form-group">
                    <label for="full_name">@lang('messages.full_name')</label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fas fa-id-card"></i></span>
                        <input id="full_name" type="text" class="form-control @error('full_name') is-invalid @enderror" 
                               name="full_name" value="{{ old('full_name') }}" 
                               placeholder="@lang('messages.full_name_placeholder')" required autocomplete="full_name">
                        @error('full_name')
                            <div class="invalid-feedback" style="color: #ff6b6b; font-size: 0.7rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- ===== EMAIL ===== -->
                <div class="form-group">
                    <label for="email">@lang('messages.email')</label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" 
                               placeholder="you@example.com" required autocomplete="email">
                        @error('email')
                            <div class="invalid-feedback" style="color: #ff6b6b; font-size: 0.7rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- ===== ПАРОЛЬ ===== -->
                <div class="form-group">
                    <label for="password">@lang('messages.password')</label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" placeholder="••••••••" required autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback" style="color: #ff6b6b; font-size: 0.7rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- ===== ПОДТВЕРЖДЕНИЕ ПАРОЛЯ ===== -->
                <div class="form-group">
                    <label for="password-confirm">@lang('messages.confirm_password')</label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fas fa-check-circle"></i></span>
                        <input id="password-confirm" type="password" class="form-control" 
                               name="password_confirmation" placeholder="••••••••" required autocomplete="new-password">
                    </div>
                </div>

                <!-- ===== КНОПКА РЕГИСТРАЦИИ ===== -->
                <button type="submit" class="btn-register">
                    <i class="fas fa-user-plus"></i> @lang('messages.register')
                </button>

                <!-- ===== ССЫЛКА НА ЛОГИН ===== -->
                <div class="login-link">
                    @lang('messages.have_account') <a href="{{ route('login') }}">@lang('messages.login')</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>