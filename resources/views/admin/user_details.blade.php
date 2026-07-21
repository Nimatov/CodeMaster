<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Детали пользователя</title>
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
        .user-info {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 25px;
            color: white;
            margin-bottom: 25px;
        }
        .user-info .name { font-size: 1.8rem; font-weight: 700; color: #ffd700; }
        .user-info .email { color: rgba(255,255,255,0.5); font-size: 1rem; }
        .user-info .stat-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-right: 10px;
        }
        .stat-badge.blue { background: rgba(0,229,255,0.15); color: #00e5ff; }
        .stat-badge.green { background: rgba(0,230,118,0.15); color: #00e676; }
        .stat-badge.orange { background: rgba(255,145,0,0.15); color: #ff9100; }
        .stat-badge.pink { background: rgba(255,64,129,0.15); color: #ff4081; }
        
        .result-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 12px;
            padding: 15px 20px;
            color: white;
            margin-bottom: 12px;
            transition: all 0.3s;
        }
        .result-card:hover { background: rgba(255,255,255,0.05); }
        .result-card .subject { font-weight: 600; color: #ffd700; }
        .result-card .score { font-size: 1.2rem; font-weight: 700; }
        .result-card .score.pass { color: #00e676; }
        .result-card .score.fail { color: #ff6b6b; }
        .result-card .date { color: rgba(255,255,255,0.3); font-size: 0.8rem; }
        .result-card .time-spent {
            color: #00e5ff;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .badge-subject {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge-subject.html { background: rgba(227,79,38,0.2); color: #e34f26; }
        .badge-subject.css { background: rgba(21,114,182,0.2); color: #1572b6; }
        .badge-subject.sql { background: rgba(68,121,161,0.2); color: #4479a1; }
        .badge-subject.bootstrap { background: rgba(121,82,179,0.2); color: #7952b3; }
        .badge-subject.js { background: rgba(247,223,30,0.2); color: #f7df1e; }
        .badge-subject.laravel { background: rgba(255,45,32,0.2); color: #ff2d20; }
        
        .btn-secondary-glass {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.7);
            padding: 8px 20px;
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
        
        .time-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(0,229,255,0.08);
            color: #00e5ff;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="logo">
                    <span class="highlight">&lt;</span>Code<span style="color:#ffd700;">Master</span><span class="highlight">/&gt;</span>
                </div>
                <nav class="nav flex-column">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <i class="fas fa-chart-pie"></i> Статистика
                    </a>
                    <a href="{{ route('admin.questions') }}" class="nav-link">
                        <i class="fas fa-question-circle"></i> Вопросы
                    </a>
                    <a href="{{ route('admin.questions.create') }}" class="nav-link">
                        <i class="fas fa-plus-circle"></i> Добавить вопрос
                    </a>
                    <a href="{{ route('welcome') }}" class="nav-link">
                        <i class="fas fa-home"></i> На главную
                    </a>
                    <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Выйти
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 style="color:white;font-weight:700;">
                        <i class="fas fa-user" style="color:#ffd700;"></i> 
                        Детали пользователя
                    </h2>
                    <a href="{{ route('admin.dashboard') }}" class="btn-secondary-glass">
                        <i class="fas fa-arrow-left"></i> Назад
                    </a>
                </div>

                <!-- User Info -->
                <div class="user-info">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="name">{{ $user->full_name ?? $user->name }}</div>
                            <div class="email"><i class="fas fa-envelope"></i> {{ $user->email }}</div>
                            <div style="margin-top: 10px;">
                                <span class="stat-badge blue"><i class="fas fa-calendar"></i> {{ $user->created_at->format('d.m.Y') }}</span>
                                @if($user->is_blocked ?? false)
                                    <span class="stat-badge pink"><i class="fas fa-lock"></i> Заблокирован</span>
                                @else
                                    <span class="stat-badge green"><i class="fas fa-check-circle"></i> Активен</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <span class="stat-badge blue"><i class="fas fa-file-alt"></i> Тестов: {{ $results->count() }}</span>
                            <span class="stat-badge green"><i class="fas fa-check"></i> Правильно: {{ $results->sum('correct_answers') }}</span>
                            <span class="stat-badge orange"><i class="fas fa-percent"></i> Средний: {{ round($results->avg('score_percentage') ?? 0) }}%</span>
                            <span class="stat-badge" style="background:rgba(0,229,255,0.15); color:#00e5ff;">
                                <i class="fas fa-clock"></i> Сред. время: {{ $this->formatTime($results->avg('time_spent') ?? 0) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Results List -->
                <h5 style="color:rgba(255,255,255,0.5); margin-bottom: 15px;">
                    <i class="fas fa-list"></i> Результаты тестов
                </h5>
                @if($results->count() > 0)
    @foreach($results as $result)
        <div class="result-card">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <span class="badge-subject {{ strtolower($result->subject) }}">{{ $result->subject }}</span>
                </div>
                <div class="col-md-2">
                    <span class="score {{ $result->score_percentage >= 60 ? 'pass' : 'fail' }}">
                        {{ $result->score_percentage }}%
                    </span>
                </div>
                <div class="col-md-2">
                    <span style="color:rgba(255,255,255,0.5); font-size:0.9rem;">
                        ✅ {{ $result->correct_answers }} / ❌ {{ $result->wrong_answers }}
                    </span>
                </div>
                <div class="col-md-2">
                    <span class="time-badge">
                        <i class="far fa-clock"></i> {{ $result->time_formatted ?? '—' }}
                    </span>
                </div>
                <div class="col-md-3 text-md-end">
                    <span class="date">{{ $result->created_at->format('d.m.Y H:i') }}</span>
                    @if($result->certificate_level == 'excellent')
                        <span style="color:#ffd700; font-size:0.8rem;">🏆</span>
                    @elseif($result->certificate_level == 'good')
                        <span style="color:#00e676; font-size:0.8rem;">⭐</span>
                    @else
                        <span style="color:#ff6b6b; font-size:0.8rem;">📚</span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@else
    <div style="text-align:center; padding:40px 0; color:rgba(255,255,255,0.3);">
        <i class="fas fa-inbox" style="font-size:2rem; margin-bottom:10px;"></i>
        <p>Пользователь еще не проходил тесты</p>
    </div>
@endif 
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>