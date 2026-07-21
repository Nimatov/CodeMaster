<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeMaster</title>
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
            overflow-x: hidden;
            color: #fff;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* ===== ФОН С КОДОМ (СВЕТЯЩИЙСЯ) ===== */
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
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(0, 180, 216, 0.4), transparent);
            top: -200px;
            right: -200px;
            animation: floatGlow 20s ease-in-out infinite;
        }

        .code-bg .light-streak:nth-child(2) {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(247, 37, 133, 0.35), transparent);
            bottom: -150px;
            left: -150px;
            animation: floatGlow 25s ease-in-out infinite reverse;
        }

        .code-bg .light-streak:nth-child(3) {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(114, 9, 183, 0.3), transparent);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: floatGlow 30s ease-in-out infinite;
        }

        @keyframes floatGlow {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(60px, -40px) scale(1.3); }
            66% { transform: translate(-40px, 60px) scale(0.7); }
            100% { transform: translate(0, 0) scale(1); }
        }

        .code-bg .code-line {
            position: absolute;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            white-space: nowrap;
            animation: scrollCode 70s linear infinite;
            pointer-events: none;
            opacity: 0.12;
            color: #00e5ff;
            text-shadow: 0 0 20px rgba(0, 229, 255, 0.1);
        }

        .code-bg .code-line:nth-child(4) { top: 5%; left: 2%; animation-delay: 0s; }
        .code-bg .code-line:nth-child(5) { top: 12%; right: 5%; animation-delay: -12s; color: #ff00ff; }
        .code-bg .code-line:nth-child(6) { top: 20%; left: 8%; animation-delay: -24s; }
        .code-bg .code-line:nth-child(7) { top: 28%; right: 3%; animation-delay: -36s; color: #ff00ff; }
        .code-bg .code-line:nth-child(8) { top: 36%; left: 5%; animation-delay: -48s; }
        .code-bg .code-line:nth-child(9) { top: 44%; right: 8%; animation-delay: -60s; color: #ff00ff; }
        .code-bg .code-line:nth-child(10) { top: 52%; left: 10%; animation-delay: -10s; }
        .code-bg .code-line:nth-child(11) { top: 60%; right: 2%; animation-delay: -22s; color: #ff00ff; }
        .code-bg .code-line:nth-child(12) { top: 68%; left: 4%; animation-delay: -34s; }
        .code-bg .code-line:nth-child(13) { top: 76%; right: 10%; animation-delay: -46s; color: #ff00ff; }
        .code-bg .code-line:nth-child(14) { top: 84%; left: 6%; animation-delay: -58s; }
        .code-bg .code-line:nth-child(15) { top: 92%; right: 4%; animation-delay: -8s; color: #ff00ff; }

        @keyframes scrollCode {
            0% { transform: translateX(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateX(-600px); opacity: 0; }
        }

        /* ===== НАВИГАЦИЯ ===== */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding: 10px 0;
            position: relative;
            z-index: 100;
        }

        .navbar-custom .navbar-brand {
            color: #ffd700 !important;
            font-weight: 800;
            font-size: 1.3rem;
            letter-spacing: -0.5px;
            text-shadow: 0 0 30px rgba(255, 215, 0, 0.2);
        }
        .navbar-custom .navbar-brand .highlight { color: #ffd700; }
        .navbar-custom .navbar-brand .accent { color: #ffd700; }

        .btn-glass {
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 6px 18px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s;
            font-size: 0.8rem;
            text-decoration: none;
        }
        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 0 30px rgba(0, 229, 255, 0.1);
        }

        .btn-primary-glass {
            background: linear-gradient(135deg, #00bcd4, #00e5ff);
            color: #fff;
            border: none;
            padding: 6px 18px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 0.8rem;
            text-decoration: none;
            box-shadow: 0 0 30px rgba(0, 229, 255, 0.2);
        }
        .btn-primary-glass:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 50px rgba(0, 229, 255, 0.4);
            color: #fff;
        }

        /* ===== ЯЗЫК ===== */
        .lang-link {
            text-decoration: none !important;
            border: none !important;
            color: rgba(255, 255, 255, 0.7) !important;
            font-weight: 500;
            padding: 4px 10px;
            border-radius: 8px;
            transition: all 0.3s;
            font-size: 0.8rem;
        }
        .lang-link:hover {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.05);
        }
        .lang-link::after { display: none !important; }

        .lang-display {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .lang-display .flag { font-size: 1rem; }
        .lang-display .arrow { font-size: 0.5rem; margin-left: 2px; opacity: 0.5; }

        .dropdown-menu {
            background: rgba(10, 22, 40, 0.85);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 6px;
            min-width: 150px;
        }
        .dropdown-item {
            color: rgba(255, 255, 255, 0.7);
            border-radius: 6px;
            padding: 6px 14px;
            font-size: 0.8rem;
            transition: all 0.3s;
        }
        .dropdown-item:hover {
            background: rgba(0, 229, 255, 0.2);
            color: #fff;
        }
        .dropdown-item.active {
            background: rgba(0, 229, 255, 0.3);
            color: #00e5ff;
        }

        /* ===== HERO ===== */
        .hero-section {
            position: relative;
            z-index: 1;
            padding: 50px 0 30px;
            text-align: center;
        }
        .hero-section .badge {
            display: inline-block;
            background: rgba(0, 229, 255, 0.15);
            border: 1px solid rgba(0, 229, 255, 0.2);
            color: #00e5ff;
            padding: 4px 16px;
            border-radius: 50px;
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 15px;
            text-shadow: 0 0 20px rgba(0, 229, 255, 0.2);
        }
        .hero-section h1 {
            font-size: 3rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 15px;
            letter-spacing: -1px;
            color: #fff;
            text-shadow: 0 0 40px rgba(0, 0, 0, 0.1);
        }
        .hero-section h1 .gradient-text {
            background: linear-gradient(135deg, #00e5ff, #ff00ff, #ffd700);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 300% 300%;
            animation: gradientMove 4s ease infinite;
        }
        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .hero-section p {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.6);
            max-width: 500px;
            margin: 0 auto 30px;
            line-height: 1.6;
        }

        .btn-hero {
            padding: 10px 32px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s;
            margin: 0 6px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-hero:hover { transform: translateY(-3px); }
        .btn-hero-outline {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #fff;
        }
        .btn-hero-outline:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.05);
        }
        .btn-hero-primary {
            background: linear-gradient(135deg, #00bcd4, #00e5ff);
            border: none;
            color: #fff;
            box-shadow: 0 8px 30px rgba(0, 229, 255, 0.3);
        }
        .btn-hero-primary:hover {
            box-shadow: 0 12px 50px rgba(0, 229, 255, 0.5);
            color: #fff;
        }

        /* ===== WELCOME CARD ===== */
        .welcome-card {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 30px;
            margin-top: 20px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.2), 0 0 60px rgba(0, 229, 255, 0.05);
        }

        .welcome-card .greeting {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 3px;
            color: #0a1628;
        }
        .welcome-card .greeting .wave {
            display: inline-block;
            animation: wave 2s infinite;
        }
        @keyframes wave {
            0%, 100% { transform: rotate(0deg); }
            20% { transform: rotate(15deg); }
            40% { transform: rotate(-10deg); }
            60% { transform: rotate(10deg); }
            80% { transform: rotate(-5deg); }
        }
        .welcome-card .subtitle {
            color: rgba(10, 22, 40, 0.5);
            font-size: 0.9rem;
        }

        /* ===== SUBJECT CARDS ===== */
        .subject-card {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.04);
            border-radius: 14px;
            padding: 20px 15px;
            text-align: center;
            transition: all 0.4s;
            height: 100%;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }
        .subject-card:hover {
            transform: translateY(-6px);
            border-color: rgba(0, 229, 255, 0.2);
            box-shadow: 0 15px 50px rgba(0, 229, 255, 0.1), 0 0 40px rgba(0, 229, 255, 0.02);
        }
        .subject-card .icon-wrapper {
            width: 55px;
            height: 55px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 1.5rem;
            position: relative;
            z-index: 1;
        }
        .subject-card .icon-wrapper.html { background: rgba(255, 107, 53, 0.12); color: #ff6b35; }
        .subject-card .icon-wrapper.css { background: rgba(0, 150, 255, 0.12); color: #0096ff; }
        .subject-card .icon-wrapper.sql { background: rgba(150, 0, 255, 0.12); color: #9600ff; }
        .subject-card .icon-wrapper.bootstrap { background: rgba(120, 80, 200, 0.12); color: #7850c8; }
        .subject-card .icon-wrapper.js { background: rgba(255, 215, 0, 0.12); color: #ffd700; }
        .subject-card .icon-wrapper.laravel { background: rgba(255, 45, 32, 0.12); color: #ff2d20; }

        .subject-card h5 {
            font-weight: 700;
            color: #0a1628;
            margin-bottom: 3px;
            position: relative;
            z-index: 1;
            font-size: 1rem;
        }
        .subject-card .meta {
            color: rgba(10, 22, 40, 0.4);
            font-size: 0.7rem;
            position: relative;
            z-index: 1;
        }
        .subject-card .btn-start-subject {
            background: linear-gradient(135deg, #00bcd4, #00e5ff);
            color: #fff;
            border: none;
            padding: 5px 18px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none !important;
            display: inline-block;
            position: relative;
            z-index: 1;
            font-size: 0.75rem;
            margin-top: 8px;
            box-shadow: 0 0 25px rgba(0, 229, 255, 0.15);
        }
        .subject-card .btn-start-subject:hover {
            transform: scale(1.05);
            box-shadow: 0 0 45px rgba(0, 229, 255, 0.3);
            color: #fff;
        }

        .subject-card {
            opacity: 0;
            animation: fadeUp 0.6s ease forwards;
        }
        .subject-card:nth-child(1) { animation-delay: 0.1s; }
        .subject-card:nth-child(2) { animation-delay: 0.2s; }
        .subject-card:nth-child(3) { animation-delay: 0.3s; }
        .subject-card:nth-child(4) { animation-delay: 0.4s; }
        .subject-card:nth-child(5) { animation-delay: 0.5s; }
        .subject-card:nth-child(6) { animation-delay: 0.6s; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== УВЕДОМЛЕНИЯ ===== */
        .alert-glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 229, 255, 0.2);
            border-radius: 12px;
            color: #00e5ff;
            padding: 14px 20px;
            font-weight: 500;
            position: relative;
            z-index: 1;
            font-size: 0.9rem;
            animation: slideDown 0.5s ease forwards;
            transition: all 0.5s ease;
            text-shadow: 0 0 20px rgba(0, 229, 255, 0.1);
        }

        .alert-glass.hide {
            animation: slideUp 0.5s ease forwards;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideUp {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-30px); }
        }

        .alert-glass-danger {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 0, 0, 0.15);
            border-radius: 12px;
            color: #ff6b6b;
            padding: 14px 20px;
            font-weight: 500;
            position: relative;
            z-index: 1;
            font-size: 0.9rem;
        }

        /* ===== АДАПТИВ ===== */
        @media (max-width: 768px) {
            .hero-section h1 { font-size: 2rem; }
            .hero-section p { font-size: 0.9rem; padding: 0 15px; }
            .welcome-card { padding: 20px 15px; }
            .welcome-card .greeting { font-size: 1.2rem; }
            .btn-hero { padding: 8px 20px; font-size: 0.8rem; margin: 4px; }
            .navbar-custom .navbar-brand { font-size: 1.1rem; }
            .subject-card .icon-wrapper { width: 45px; height: 45px; font-size: 1.2rem; }
        }
        @media (max-width: 576px) {
            .hero-section h1 { font-size: 1.6rem; }
            .subject-card { padding: 15px 10px; }
            .subject-card h5 { font-size: 0.85rem; }
        }
    </style>
</head>
<body>

    <!-- ===== СВЕТЯЩИЙСЯ ФОН С КОДОМ ===== -->
    <div class="code-bg">
        <div class="light-streak"></div>
        <div class="light-streak"></div>
        <div class="light-streak"></div>
        <div class="code-line">&lt;?php namespace App; class User { public $name; } ?&gt;</div>
        <div class="code-line">function greet() { console.log("Hello World!"); }</div>
        <div class="code-line">SELECT * FROM users WHERE active = 1;</div>
        <div class="code-line">.container { display: flex; justify-content: center; }</div>
        <div class="code-line">&lt;div class="card"&gt;&lt;h1&gt;Title&lt;/h1&gt;&lt;/div&gt;</div>
        <div class="code-line">const app = new Vue({ el: '#app' });</div>
        <div class="code-line">npm install && npm run dev</div>
        <div class="code-line">Route::get('/', function() { return view('welcome'); });</div>
        <div class="code-line">import React from 'react'; export default App;</div>
        <div class="code-line">public function store(Request $request) { ... }</div>
        <div class="code-line">&lt;script&gt; alert("Hello"); &lt;/script&gt;</div>
        <div class="code-line">docker-compose up -d</div>
        <div class="code-line">composer require laravel/ui</div>
        <div class="code-line">git push origin main</div>
        <div class="code-line">php artisan migrate</div>
        <div class="code-line">return view('welcome', compact('data'));</div>
    </div>

    <!-- ===== HEADER ===== -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="#">
                <span class="highlight">&lt;</span>Code<span class="accent">Master</span><span class="highlight">/&gt;</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="border-color: rgba(255,255,255,0.1);">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    @guest
                        <li class="nav-item dropdown me-2">
                            <a class="nav-link lang-link" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown">
                                @php
                                    $currentLang = App::getLocale();
                                    $languages = [
                                        'ru' => ['flag' => '🇷🇺', 'name' => 'Рус'],
                                        'uz' => ['flag' => '🇺🇿', 'name' => 'O\'zb'],
                                        'en' => ['flag' => '🇬🇧', 'name' => 'Eng'],
                                    ];
                                    $current = $languages[$currentLang] ?? ['flag' => '🌐', 'name' => 'Яз'];
                                @endphp
                                <span class="lang-display">
                                    <span class="flag">{{ $current['flag'] }}</span>
                                    <span>{{ $current['name'] }}</span>
                                    <span class="arrow">▼</span>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form action="{{ route('change.language') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="language" value="ru">
                                        <button class="dropdown-item {{ App::getLocale() == 'ru' ? 'active' : '' }}" type="submit">🇷🇺 Русский ✅</button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ route('change.language') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="language" value="uz">
                                        <button class="dropdown-item {{ App::getLocale() == 'uz' ? 'active' : '' }}" type="submit">🇺🇿 O'zbekcha ✅</button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ route('change.language') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="language" value="en">
                                        <button class="dropdown-item {{ App::getLocale() == 'en' ? 'active' : '' }}" type="submit">🇬🇧 English ✅</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item me-2">
                            <a href="{{ route('login') }}" class="btn-glass">
                                <i class="fas fa-sign-in-alt"></i> @lang('messages.login')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="btn-primary-glass">
                                <i class="fas fa-user-plus"></i> @lang('messages.register')
                            </a>
                        </li>
                    @else
                        @if(Auth::user()->email == 'admin@example.com')
                            <li class="nav-item me-2">
                                <a href="{{ route('admin.dashboard') }}" class="btn-primary-glass">
                                    <i class="fas fa-chart-bar"></i> @lang('messages.admin_dashboard')
                                </a>
                            </li>
                        @endif

                        <li class="nav-item dropdown me-2">
                            <a class="nav-link lang-link" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown">
                                @php
                                    $currentLang = App::getLocale();
                                    $languages = [
                                        'ru' => ['flag' => '🇷🇺', 'name' => 'Рус'],
                                        'uz' => ['flag' => '🇺🇿', 'name' => 'O\'zb'],
                                        'en' => ['flag' => '🇬🇧', 'name' => 'Eng'],
                                    ];
                                    $current = $languages[$currentLang] ?? ['flag' => '🌐', 'name' => 'Яз'];
                                @endphp
                                <span class="lang-display">
                                    <span class="flag">{{ $current['flag'] }}</span>
                                    <span>{{ $current['name'] }}</span>
                                    <span class="arrow">▼</span>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form action="{{ route('change.language') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="language" value="ru">
                                        <button class="dropdown-item {{ App::getLocale() == 'ru' ? 'active' : '' }}" type="submit">🇷🇺 Русский ✅</button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ route('change.language') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="language" value="uz">
                                        <button class="dropdown-item {{ App::getLocale() == 'uz' ? 'active' : '' }}" type="submit">🇺🇿 O'zbekcha ✅</button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ route('change.language') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="language" value="en">
                                        <button class="dropdown-item {{ App::getLocale() == 'en' ? 'active' : '' }}" type="submit">🇬🇧 English ✅</button>
                                    </form>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" style="color: rgba(255,255,255,0.8); text-decoration: none; font-size: 0.85rem;">
                                <i class="fas fa-user-circle" style="font-size: 1.1rem; color: #ffd700;"></i>
                                {{ Auth::user()->full_name ?? Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt"></i> @lang('messages.logout')
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- ===== ОСНОВНОЕ СОДЕРЖИМОЕ ===== -->
    <div class="container" style="position: relative; z-index: 1;">
        @guest
            <div class="hero-section">
                <div class="badge">🚀 @lang('messages.interactive_learning')</div>
                <h1>
                    @lang('messages.test_your_skills')<br>
                    <span class="gradient-text">@lang('messages.programming_knowledge')</span>
                </h1>
                <p>@lang('messages.hero_description')</p>
                <div>
                    <a href="{{ route('login') }}" class="btn-hero btn-hero-outline">
                        <i class="fas fa-sign-in-alt"></i> @lang('messages.login')
                    </a>
                    <a href="{{ route('register') }}" class="btn-hero btn-hero-primary">
                        <i class="fas fa-rocket"></i> @lang('messages.start_learning')
                    </a>
                </div>
            </div>
        @else
            @if(session('success'))
                <div class="alert-glass mt-3" id="successAlert">
                    <i class="fas fa-check-circle"></i>
                    <span id="successMessage">{{ session('success') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: invert(1); opacity: 0.5;"></button>
                </div>
            @endif

            <div class="welcome-card">
                <div class="greeting">
                    <span class="wave">👋</span>
                    @lang('messages.welcome'), {{ Auth::user()->full_name ?? Auth::user()->name }}!
                </div>
                <p class="subtitle">@lang('messages.choose_subject') 💪</p>

                <div class="row mt-3">
                    @php
                        $subjects = [
                            'Html' => ['icon' => 'fa-html5', 'class' => 'html'],
                            'Css' => ['icon' => 'fa-css3-alt', 'class' => 'css'],
                            'Sql' => ['icon' => 'fa-database', 'class' => 'sql'],
                            'Bootstrap' => ['icon' => 'fa-bootstrap', 'class' => 'bootstrap'],
                            'JavaScript' => ['icon' => 'fa-js-square', 'class' => 'js'],
                            'Laravel' => ['icon' => 'fa-laravel', 'class' => 'laravel']
                        ];
                    @endphp

                    @foreach($subjects as $subject => $data)
                        <div class="col-6 col-md-4 col-lg-4 mb-3">
                            <div class="subject-card">
                                <div class="icon-wrapper {{ $data['class'] }}">
                                    <i class="fab {{ $data['icon'] }}"></i>
                                </div>
                                <h5>{{ $subject }}</h5>
                                <div class="meta">@lang('messages.questions')</div>
                                <a href="{{ route('test.start', $subject) }}" class="btn-start-subject">
                                    @lang('messages.start_test') →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endguest
    </div>

    @if(session('error'))
        <div class="container" style="position:relative; z-index:1;">
            <div class="alert-glass-danger mt-3">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: invert(1);"></button>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('successAlert');
            if (alert) {
                setTimeout(function() {
                    alert.classList.add('hide');
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                }, 5000);
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>