<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@lang('messages.admin_questions_edit')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #0a1628; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            min-height: 100vh;
            padding: 20px;
            border-right: 1px solid rgba(255,255,255,0.05);
        }
        .sidebar .logo { color: #ffd700; font-size: 1.5rem; font-weight: 800; margin-bottom: 30px; }
        .sidebar .logo .highlight { color: #00e5ff; }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.6);
            padding: 12px 20px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .sidebar .nav-link.active { background: rgba(0,229,255,0.1); color: #00e5ff; }
        .sidebar .nav-link i { margin-right: 12px; }
        
        .main-content { padding: 30px; }
        .form-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 30px;
            color: white;
            max-width: 800px;
        }
        .form-card label { font-weight: 600; color: rgba(255,255,255,0.7); font-size: 0.9rem; margin-bottom: 5px; }
        .form-card .form-control {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            color: white;
            padding: 10px 15px;
            transition: all 0.3s;
        }
        .form-card .form-control:focus {
            background: rgba(255,255,255,0.08);
            border-color: #00e5ff;
            box-shadow: 0 0 0 3px rgba(0,229,255,0.1);
        }
        .form-card .form-control::placeholder { color: rgba(255,255,255,0.2); }
        .form-card .form-select {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            color: white;
            padding: 10px 15px;
        }
        .form-card .form-select option { background: #0a1628; color: white; }
        .form-card .form-select:focus {
            border-color: #00e5ff;
            box-shadow: 0 0 0 3px rgba(0,229,255,0.1);
        }
        
        .btn-primary-glass {
            background: linear-gradient(135deg, #00bcd4, #00e5ff);
            border: none;
            color: white;
            padding: 10px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
        }
        .btn-primary-glass:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,229,255,0.3);
            color: white;
        }
        .btn-secondary-glass {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.7);
            padding: 10px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-secondary-glass:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .option-row { display: flex; gap: 10px; align-items: center; margin-bottom: 10px; }
        .option-row .letter { 
            width: 30px; 
            height: 30px; 
            background: rgba(255,255,255,0.05); 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-weight: 700; 
            color: rgba(255,255,255,0.5);
            flex-shrink: 0;
        }

        .lang-switcher {
            display: flex;
            gap: 5px;
            align-items: center;
            background: rgba(255,255,255,0.05);
            padding: 5px 10px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .lang-switcher .lang-btn {
            background: transparent;
            border: none;
            color: rgba(255,255,255,0.4);
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
        }
        .lang-switcher .lang-btn:hover {
            color: rgba(255,255,255,0.8);
            background: rgba(255,255,255,0.05);
        }
        .lang-switcher .lang-btn.active {
            color: #00e5ff;
            background: rgba(0,229,255,0.1);
        }
        .lang-switcher .lang-btn .flag { font-size: 1rem; margin-right: 4px; }
        .lang-switcher .divider { color: rgba(255,255,255,0.1); font-size: 1.2rem; }

        .alert { transition: opacity 0.5s ease; padding: 12px 20px; border-radius: 10px; }
        .alert-success { background: rgba(0,230,118,0.1); border: 1px solid rgba(0,230,118,0.2); color: #00e676; }
        .alert-danger { background: rgba(255,68,68,0.1); border: 1px solid rgba(255,68,68,0.2); color: #ff6b6b; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <div class="logo">
                    <span class="highlight">&lt;</span>Code<span style="color:#ffd700;">Master</span><span class="highlight">/&gt;</span>
                </div>
                <nav class="nav flex-column">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <i class="fas fa-chart-pie"></i> @lang('messages.admin_statistics')
                    </a>
                    <a href="{{ route('admin.questions') }}" class="nav-link">
                        <i class="fas fa-question-circle"></i> @lang('messages.admin_questions')
                    </a>
                    <a href="{{ route('admin.questions.create') }}" class="nav-link">
                        <i class="fas fa-plus-circle"></i> @lang('messages.admin_add_question')
                    </a>
                    <a href="{{ route('welcome') }}" class="nav-link">
                        <i class="fas fa-home"></i> @lang('messages.admin_back_home')
                    </a>
                    <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> @lang('messages.admin_logout')
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </nav>
            </div>

            <div class="col-md-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 style="color:white;font-weight:700;">
                        <i class="fas fa-edit" style="color:#ffd700;"></i> 
                        @lang('messages.admin_questions_edit')
                    </h2>
                    <div class="d-flex align-items-center gap-3">
                        <div class="lang-switcher">
                            <form action="{{ route('change.language') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="language" value="ru">
                                <button type="submit" class="lang-btn {{ App::getLocale() == 'ru' ? 'active' : '' }}">
                                    <span class="flag">🇷🇺</span> RU
                                </button>
                            </form>
                            <span class="divider">|</span>
                            <form action="{{ route('change.language') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="language" value="uz">
                                <button type="submit" class="lang-btn {{ App::getLocale() == 'uz' ? 'active' : '' }}">
                                    <span class="flag">🇺🇿</span> UZ
                                </button>
                            </form>
                            <span class="divider">|</span>
                            <form action="{{ route('change.language') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="language" value="en">
                                <button type="submit" class="lang-btn {{ App::getLocale() == 'en' ? 'active' : '' }}">
                                    <span class="flag">🇬🇧</span> EN
                                </button>
                            </form>
                        </div>
                        <a href="{{ route('admin.questions') }}" class="btn-secondary-glass">
                            <i class="fas fa-arrow-left"></i> @lang('messages.admin_questions_back')
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="form-card">
                    <form method="POST" action="{{ route('admin.questions.update', $question->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="subject">@lang('messages.admin_questions_subject')</label>
                            <select name="subject" id="subject" class="form-select" required>
                                <option value="Html" {{ $question->subject == 'Html' ? 'selected' : '' }}>HTML</option>
                                <option value="Css" {{ $question->subject == 'Css' ? 'selected' : '' }}>CSS</option>
                                <option value="Sql" {{ $question->subject == 'Sql' ? 'selected' : '' }}>SQL</option>
                                <option value="Bootstrap" {{ $question->subject == 'Bootstrap' ? 'selected' : '' }}>Bootstrap</option>
                                <option value="JavaScript" {{ $question->subject == 'JavaScript' ? 'selected' : '' }}>JavaScript</option>
                                <option value="Laravel" {{ $question->subject == 'Laravel' ? 'selected' : '' }}>Laravel</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="question">@lang('messages.admin_questions_question')</label>
                            <textarea name="question" id="question" class="form-control" rows="2" required>{{ $question->question }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label>@lang('messages.admin_questions_options')</label>
                            @php 
                                $options = is_array($question->options) ? $question->options : json_decode($question->options, true); 
                                if (!is_array($options)) $options = ['', '', '', ''];
                            @endphp
                            <div class="option-row">
                                <span class="letter">A</span>
                                <input type="text" name="option_a" class="form-control" value="{{ $options[0] ?? '' }}" required>
                            </div>
                            <div class="option-row">
                                <span class="letter">B</span>
                                <input type="text" name="option_b" class="form-control" value="{{ $options[1] ?? '' }}" required>
                            </div>
                            <div class="option-row">
                                <span class="letter">C</span>
                                <input type="text" name="option_c" class="form-control" value="{{ $options[2] ?? '' }}" required>
                            </div>
                            <div class="option-row">
                                <span class="letter">D</span>
                                <input type="text" name="option_d" class="form-control" value="{{ $options[3] ?? '' }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="correct">@lang('messages.admin_questions_correct')</label>
                            <select name="correct" id="correct" class="form-select" required>
                                <option value="0" {{ $question->correct == 0 ? 'selected' : '' }}>A</option>
                                <option value="1" {{ $question->correct == 1 ? 'selected' : '' }}>B</option>
                                <option value="2" {{ $question->correct == 2 ? 'selected' : '' }}>C</option>
                                <option value="3" {{ $question->correct == 3 ? 'selected' : '' }}>D</option>
                            </select>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn-primary-glass">
                                <i class="fas fa-save"></i> @lang('messages.admin_questions_save')
                            </button>
                            <a href="{{ route('admin.questions') }}" class="btn-secondary-glass">
                                @lang('messages.admin_questions_cancel')
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>