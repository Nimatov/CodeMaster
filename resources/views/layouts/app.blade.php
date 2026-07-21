<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="google" content="notranslate">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* ===== ЗАПРЕТ ВЫДЕЛЕНИЯ ===== */
        * {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
            -webkit-touch-callout: none !important;
        }
        input, textarea {
            -webkit-user-select: text !important;
            -moz-user-select: text !important;
            -ms-user-select: text !important;
            user-select: text !important;
        }
        img, a {
            -webkit-user-drag: none !important;
            pointer-events: none !important;
        }
        p, h1, h2, h3, h4, h5, h6, span, div, label {
            cursor: default !important;
        }

        /* ============================================================
        ===== СТИЛИ ДЛЯ МОДАЛЬНОГО ОКНА ВЫХОДА =====
        ============================================================ */
        .exit-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(10, 22, 40, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }
        .exit-overlay.active {
            display: flex;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .exit-modal {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.98));
            border-radius: 24px;
            padding: 45px 50px;
            max-width: 480px;
            width: 90%;
            text-align: center;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.4), 0 0 60px rgba(0, 229, 255, 0.05);
            animation: modalPop 0.4s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        @keyframes modalPop {
            from { transform: scale(0.8) translateY(30px); opacity: 0; }
            to { transform: scale(1) translateY(0); opacity: 1; }
        }

        .exit-modal .icon-wrapper {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f72585, #7209b7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 40px rgba(247, 37, 133, 0.3);
        }

        .exit-modal .icon-wrapper i {
            font-size: 2.5rem;
            color: white;
        }

        .exit-modal h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0a1628;
            margin-bottom: 8px;
        }

        .exit-modal .sub-text {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .exit-modal .sub-text .highlight {
            color: #f72585;
            font-weight: 600;
        }

        .exit-modal .btn-group-exit {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .exit-modal .btn-exit {
            padding: 12px 35px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            cursor: pointer;
            min-width: 120px;
        }

        .exit-modal .btn-exit:hover {
            transform: translateY(-2px);
        }

        .exit-modal .btn-exit-leave {
            background: linear-gradient(135deg, #f72585, #7209b7);
            color: white;
            box-shadow: 0 8px 30px rgba(247, 37, 133, 0.3);
        }

        .exit-modal .btn-exit-leave:hover {
            box-shadow: 0 12px 40px rgba(247, 37, 133, 0.5);
        }

        .exit-modal .btn-exit-cancel {
            background: rgba(10, 22, 40, 0.05);
            color: #0a1628;
            border: 1px solid rgba(10, 22, 40, 0.08);
        }

        .exit-modal .btn-exit-cancel:hover {
            background: rgba(10, 22, 40, 0.08);
        }

        .exit-modal .btn-exit-cancel i {
            margin-right: 8px;
        }
        /* ============================================================
        ===== ЗАЩИТА ОТ СКРИНШОТОВ =====
        ============================================================ */
        body.blurred .test-content {
            filter: blur(20px) !important;
            opacity: 0.1 !important;
            transition: all 0.3s;
            pointer-events: none !important;
        }

        body.blurred::after {
            content: "🔒 СКРИНШОТ ЗАПРЕЩЕН! 🔒";
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 3rem;
            color: red;
            background: rgba(0,0,0,0.9);
            padding: 40px 60px;
            border-radius: 20px;
            z-index: 99999;
            font-weight: bold;
            text-align: center;
            animation: blink 0.5s ease infinite alternate;
        }

        @keyframes blink {
            from { opacity: 1; }
            to { opacity: 0.5; }
        }

        /* Водяной знак */
        body::before {
            content: "🔒 ID: {{ auth()->id() ?? 'Гость' }}";
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 100px;
            color: rgba(255, 0, 0, 0.05);
            pointer-events: none;
            z-index: 9998;
            white-space: nowrap;
            font-weight: bold;
            font-family: Arial, sans-serif;
            user-select: none !important;
        }

        /* ============================================================
        ===== УВЕДОМЛЕНИЯ =====
        ============================================================ */
        .notification {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: #ff4444;
            color: white;
            padding: 14px 30px;
            border-radius: 12px;
            font-weight: 600;
            z-index: 99999;
            box-shadow: 0 10px 40px rgba(255, 0, 0, 0.3);
            max-width: 90%;
            text-align: center;
            animation: slideUp 0.4s ease;
        }

        .notification.success {
            background: #28a745;
        }

        .notification.warning {
            background: #ffc107;
            color: #333;
        }

        @keyframes slideUp {
            from {
                transform: translateX(-50%) translateY(100px);
                opacity: 0;
            }
            to {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
        }

        /* ============================================================
        ===== АДАПТИВНОСТЬ =====
        ============================================================ */
        @media (max-width: 576px) {
            .exit-modal {
                padding: 30px 20px;
            }
            .exit-modal .btn-exit {
                padding: 10px 20px;
                font-size: 0.85rem;
                min-width: 80px;
            }
            .exit-modal h3 {
                font-size: 1.2rem;
            }
            .exit-modal .sub-text {
                font-size: 0.85rem;
            }
        }

        /* ============================================================
        ===== АКТИВНАЯ КНОПКА ЯЗЫКА =====
        ============================================================ */
        .dropdown-item.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .dropdown-item.active:hover {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        /* ============================================================
        ===== PRINT SCREEN ЗАЩИТА =====
        ============================================================ */
        @media print {
            body * {
                visibility: hidden !important;
                display: none !important;
            }
            body::after {
                content: "🔒 Скриншот запрещен! 🔒";
                visibility: visible !important;
                display: block !important;
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                font-size: 2rem;
                color: red;
                background: white;
                padding: 40px;
                border-radius: 20px;
                border: 3px solid red;
                z-index: 99999;
                text-align: center;
            }
        }

        /* ============================================================
        ===== ВОДЯНОЙ ЗНАК =====
        ============================================================ */
        body::before {
            content: "ID: {{ auth()->id() ?? 'Гость' }}";
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 80px;
            color: rgba(200, 200, 200, 0.08);
            pointer-events: none;
            z-index: 9999;
            white-space: nowrap;
            font-weight: bold;
            font-family: Arial, sans-serif;
        }

        /* Тестовый контент */
        .test-content {
            transition: filter 0.3s;
        }

        /* Защита при потере фокуса */
        body.blurred .test-content {
            filter: blur(5px);
            opacity: 0.3;
            transition: all 0.3s;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <!-- ===== ЯЗЫК ===== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                @php
                                    $currentLang = App::getLocale();
                                    $flags = [
                                        'ru' => '🇷🇺',
                                        'uz' => '🇺🇿',
                                        'en' => '🇬🇧',
                                    ];
                                    $langNames = [
                                        'ru' => 'Русский',
                                        'uz' => "O'zbekcha",
                                        'en' => 'English',
                                    ];
                                @endphp
                                {{ $flags[$currentLang] ?? '🌐' }} {{ $langNames[$currentLang] ?? 'Язык' }}
                            </a>
                            <div class="dropdown-menu" aria-labelledby="languageDropdown">
                                <form action="{{ route('change.language') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="language" value="ru">
                                    <button class="dropdown-item {{ App::getLocale() == 'ru' ? 'active' : '' }}" type="submit">🇷🇺 Русский</button>
                                </form>
                                <form action="{{ route('change.language') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="language" value="uz">
                                    <button class="dropdown-item {{ App::getLocale() == 'uz' ? 'active' : '' }}" type="submit">🇺🇿 O'zbekcha</button>
                                </form>
                                <form action="{{ route('change.language') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="language" value="en">
                                    <button class="dropdown-item {{ App::getLocale() == 'en' ? 'active' : '' }}" type="submit">🇬🇧 English</button>
                                </form>
                            </div>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('messages.login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('messages.register') }}</a>
                                </li>
                            @endif
                        @else
                            <!-- ===== ССЫЛКА НА АДМИН-ПАНЕЛЬ ===== -->
                            @if(Auth::user()->is_admin == 1 || Auth::user()->email == 'admin@example.com')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.dashboard') }}" style="color: #ffd700; font-weight: 600;">
                                        <i class="fas fa-chart-bar"></i> Админ
                                    </a>
                                </li>
                            @endif

                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->full_name ?? Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('messages.logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <!-- ============================================================
    ===== МОДАЛЬНОЕ ОКНО ВЫХОДА =====
    ============================================================ -->
    <div class="exit-overlay" id="exitModal">
        <div class="exit-modal">
            <div class="icon-wrapper">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h3>@lang('messages.exit_title')</h3>
            <p class="sub-text">
                @lang('messages.exit_message')
                <br>
                <span class="highlight">@lang('messages.exit_warning')</span>
            </p>
            <div class="btn-group-exit">
                <button class="btn-exit btn-exit-cancel" onclick="closeExitModal()">
                    <i class="fas fa-times"></i> @lang('messages.exit_cancel')
                </button>
                <button class="btn-exit btn-exit-leave" onclick="confirmExit()">
                    <i class="fas fa-check"></i> @lang('messages.exit_confirm')
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================================
    ===== ГЛОБАЛЬНАЯ ЗАЩИТА =====
    ============================================================ -->
    <script src="{{ asset('js/protection.js') }}"></script>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>