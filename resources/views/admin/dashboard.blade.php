<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@lang('messages.admin_title')</title>
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
        
        /* ===== ПЕРЕКЛЮЧАТЕЛЬ ЯЗЫКА В АДМИНКЕ ===== */
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
        .lang-switcher .divider {
            color: rgba(255,255,255,0.1);
            font-size: 1.2rem;
        }
        
        .stat-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 25px;
            color: white;
            transition: all 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card .number { font-size: 2.5rem; font-weight: 700; }
        .stat-card .label { color: rgba(255,255,255,0.5); font-size: 0.9rem; }
        .stat-card.blue { border-left: 4px solid #00e5ff; }
        .stat-card.green { border-left: 4px solid #00e676; }
        .stat-card.orange { border-left: 4px solid #ff9100; }
        .stat-card.pink { border-left: 4px solid #ff4081; }
        
        .progress-thin { height: 6px; border-radius: 10px; }
        .table-dark-custom {
            background: rgba(255,255,255,0.03);
            border-radius: 15px;
            overflow: hidden;
        }
        .table-dark-custom th {
            color: rgba(255,255,255,0.5);
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .table-dark-custom td {
            color: rgba(255,255,255,0.8);
            border-bottom: 1px solid rgba(255,255,255,0.03);
            vertical-align: middle;
        }
        .table-dark-custom tr.blocked td {
            opacity: 0.4;
            background: rgba(255,0,0,0.05);
        }
        
        .btn-primary-glass {
            background: linear-gradient(135deg, #00bcd4, #00e5ff);
            border: none;
            color: white;
            padding: 5px 15px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary-glass:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,229,255,0.3);
            color: white;
        }
        .btn-danger-glass {
            background: rgba(255,68,68,0.15);
            border: 1px solid rgba(255,68,68,0.2);
            color: #ff6b6b;
            border-radius: 8px;
            padding: 5px 15px;
            font-size: 0.8rem;
            transition: all 0.3s;
        }
        .btn-danger-glass:hover {
            background: rgba(255,68,68,0.25);
            color: #ff6b6b;
        }
        .btn-warning-glass {
            background: rgba(255,215,0,0.15);
            border: 1px solid rgba(255,215,0,0.2);
            color: #ffd700;
            border-radius: 8px;
            padding: 5px 15px;
            font-size: 0.8rem;
            transition: all 0.3s;
        }
        .btn-warning-glass:hover {
            background: rgba(255,215,0,0.25);
            color: #ffd700;
        }
        .btn-success-glass {
            background: rgba(0,230,118,0.15);
            border: 1px solid rgba(0,230,118,0.2);
            color: #00e676;
            border-radius: 8px;
            padding: 5px 15px;
            font-size: 0.8rem;
            transition: all 0.3s;
        }
        .btn-success-glass:hover {
            background: rgba(0,230,118,0.25);
            color: #00e676;
        }
        .badge-blocked {
            background: rgba(255,68,68,0.2);
            color: #ff6b6b;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge-active {
            background: rgba(0,230,118,0.2);
            color: #00e676;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        /* ===== СТИЛЬ ДЛЯ ВРЕМЕНИ ===== */
        .time-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(0,229,255,0.08);
            color: #00e5ff;
        }
        
        .modal-custom {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(10, 22, 40, 0.85);
            backdrop-filter: blur(20px);
            z-index: 99999;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }
        .modal-custom.active { display: flex; }

        .modal-custom .modal-box {
            background: linear-gradient(145deg, rgba(255,255,255,0.95), rgba(255,255,255,0.98));
            border-radius: 24px;
            padding: 40px 45px;
            max-width: 450px;
            width: 90%;
            text-align: center;
            box-shadow: 0 40px 100px rgba(0,0,0,0.4);
            animation: modalPop 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        .modal-custom .modal-box::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at center, rgba(0,229,255,0.03), transparent 70%);
            pointer-events: none;
        }
        
        .modal-custom .modal-box .icon {
            width: 75px;
            height: 75px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            position: relative;
            z-index: 1;
        }
        .modal-custom .modal-box .icon i { font-size: 2.2rem; color: white; }
        .modal-custom .modal-box .icon.danger {
            background: linear-gradient(135deg, #f72585, #7209b7);
            box-shadow: 0 10px 40px rgba(247,37,133,0.3);
        }
        .modal-custom .modal-box .icon.warning {
            background: linear-gradient(135deg, #ff9100, #ffd700);
            box-shadow: 0 10px 40px rgba(255,145,0,0.3);
        }
        
        .modal-custom .modal-box h3 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #0a1628;
            margin-bottom: 6px;
            position: relative;
            z-index: 1;
        }
        .modal-custom .modal-box .sub-text {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }
        .modal-custom .modal-box .user-name-highlight {
            color: #f72585;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .modal-custom .modal-box p {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 25px;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }
        .modal-custom .modal-box .btn-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }
        .modal-custom .modal-box .btn-modal {
            padding: 10px 35px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .modal-custom .modal-box .btn-modal:hover { transform: translateY(-2px); }
        .modal-custom .modal-box .btn-modal i { font-size: 0.9rem; }
        
        .modal-custom .modal-box .btn-danger {
            background: linear-gradient(135deg, #f72585, #7209b7);
            color: white;
            box-shadow: 0 8px 30px rgba(247,37,133,0.3);
        }
        .modal-custom .modal-box .btn-danger:hover { box-shadow: 0 12px 40px rgba(247,37,133,0.5); }
        .modal-custom .modal-box .btn-warning {
            background: linear-gradient(135deg, #ff9100, #ffd700);
            color: white;
            box-shadow: 0 8px 30px rgba(255,145,0,0.3);
        }
        .modal-custom .modal-box .btn-warning:hover { box-shadow: 0 12px 40px rgba(255,145,0,0.5); }
        .modal-custom .modal-box .btn-success {
            background: linear-gradient(135deg, #00e676, #00bcd4);
            color: white;
            box-shadow: 0 8px 30px rgba(0,230,118,0.3);
        }
        .modal-custom .modal-box .btn-success:hover { box-shadow: 0 12px 40px rgba(0,230,118,0.5); }
        .modal-custom .modal-box .btn-cancel {
            background: rgba(10,22,40,0.05);
            color: #0a1628;
            border: 1px solid rgba(10,22,40,0.08);
        }
        .modal-custom .modal-box .btn-cancel:hover { background: rgba(10,22,40,0.1); }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes modalPop {
            from { transform: scale(0.8) translateY(30px); opacity: 0; }
            to { transform: scale(1) translateY(0); opacity: 1; }
        }

        .alert {
            transition: opacity 0.5s ease;
        }
        /* ===== СТИЛИ ДЛЯ ЭКСПОРТА ===== */
.dropdown-item {
    transition: all 0.3s;
}
.dropdown-item:hover {
    background: rgba(0,229,255,0.1);
    color: white !important;
}
.dropdown-item i {
    margin-right: 8px;
}
.dropdown-divider {
    border-color: rgba(255,255,255,0.05);
}
    </style>
</head>
<body>

    <!-- ===== МОДАЛЬНОЕ ОКНО УДАЛЕНИЯ ===== -->
    <div class="modal-custom" id="deleteModal">
        <div class="modal-box">
            <div class="icon danger">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h3>@lang('messages.admin_delete_title')</h3>
            <p class="sub-text">@lang('messages.admin_delete_message')</p>
            <p><span class="user-name-highlight" id="deleteUserName">Имя</span></p>
            <p style="font-size:0.9rem; color:#ff6b6b; background:rgba(255,68,68,0.05); padding:10px; border-radius:8px;">
                <i class="fas fa-exclamation-circle"></i> @lang('messages.admin_delete_warning')
            </p>
            <div class="btn-group">
                <button class="btn-modal btn-cancel" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i> @lang('messages.admin_cancel')
                </button>
                <button class="btn-modal btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash"></i> @lang('messages.admin_delete_confirm')
                </button>
            </div>
        </div>
    </div>

    <!-- ===== МОДАЛЬНОЕ ОКНО БЛОКИРОВКИ ===== -->
    <div class="modal-custom" id="blockModal">
        <div class="modal-box">
            <div class="icon warning">
                <i class="fas fa-lock"></i>
            </div>
            <h3 id="blockModalTitle">@lang('messages.admin_block_title')</h3>
            <p class="sub-text">@lang('messages.admin_block_message')</p>
            <p><span class="user-name-highlight" id="blockUserName">Имя</span></p>
            <div class="btn-group">
                <button class="btn-modal btn-cancel" onclick="closeBlockModal()">
                    <i class="fas fa-times"></i> @lang('messages.admin_cancel')
                </button>
                <button class="btn-modal btn-warning" id="confirmBlockBtn">
                    <i class="fas fa-lock"></i> @lang('messages.admin_block_confirm')
                </button>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="logo">
                    <span class="highlight">&lt;</span>Code<span style="color:#ffd700;">Master</span><span class="highlight">/&gt;</span>
                </div>
                <nav class="nav flex-column">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link active">
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

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 style="color:white;font-weight:700;">
                        <i class="fas fa-chart-pie" style="color:#00e5ff;"></i> 
                        @lang('messages.admin_title')
                    </h2>
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2">
    <!-- Кнопки экспорта -->
    <div class="dropdown">
        <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" style="background: linear-gradient(135deg, #00b894, #00cec9); border: none; border-radius: 8px; padding: 5px 15px;">
            <i class="fas fa-file-excel"></i> Экспорт
        </button>
        <ul class="dropdown-menu dropdown-menu-end" style="background: rgba(10,22,40,0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.05);">
            <li>
                <a class="dropdown-item" href="{{ route('admin.export.users') }}" style="color: rgba(255,255,255,0.8);">
                    <i class="fas fa-file-excel" style="color: #00b894;"></i> Пользователи (Excel)
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('admin.export.users.csv') }}" style="color: rgba(255,255,255,0.8);">
                    <i class="fas fa-file-csv" style="color: #fdcb6e;"></i> Пользователи (CSV)
                </a>
            </li>
            <li><hr class="dropdown-divider" style="border-color: rgba(255,255,255,0.05);"></li>
            <li>
                <a class="dropdown-item" href="{{ route('admin.export.results') }}" style="color: rgba(255,255,255,0.8);">
                    <i class="fas fa-file-excel" style="color: #00b894;"></i> Результаты (Excel)
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('admin.export.results.csv') }}" style="color: rgba(255,255,255,0.8);">
                    <i class="fas fa-file-csv" style="color: #fdcb6e;"></i> Результаты (CSV)
                </a>
            </li>
        </ul>
    </div>
    
    <!-- Язык -->
    <div class="lang-switcher">...</div>
</div>
                        <!-- ===== ПЕРЕКЛЮЧАТЕЛЬ ЯЗЫКА ===== -->
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
                        <span style="color:rgba(255,255,255,0.4);font-size:0.9rem;">
                            <i class="fas fa-user"></i> {{ Auth::user()->full_name ?? Auth::user()->name }}
                        </span>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success" style="background:rgba(0,230,118,0.1); border:1px solid rgba(0,230,118,0.2); color:#00e676;">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger" style="background:rgba(255,68,68,0.1); border:1px solid rgba(255,68,68,0.2); color:#ff6b6b;">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Stats Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card blue">
                            <div class="number">{{ $users->count() }}</div>
                            <div class="label"><i class="fas fa-users"></i> @lang('messages.admin_total_students')</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card green">
                            <div class="number">{{ $results->count() }}</div>
                            <div class="label"><i class="fas fa-file-alt"></i> @lang('messages.admin_total_tests')</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card orange">
                            <div class="number">{{ round($results->avg('score_percentage') ?? 0) }}%</div>
                            <div class="label"><i class="fas fa-chart-line"></i> @lang('messages.admin_avg_score')</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card pink">
                            <div class="number">{{ $results->where('score_percentage','>=',60)->count() }}</div>
                            <div class="label"><i class="fas fa-trophy"></i> @lang('messages.admin_passed')</div>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="table-dark-custom">
                    <table class="table table-dark table-hover mb-0" style="background:transparent;">
                        <thead>
                            <tr>
                                <th>@lang('messages.admin_student')</th>
                                <th>@lang('messages.admin_email')</th>
                                <th>@lang('messages.admin_status')</th>
                                <th>@lang('messages.admin_tests')</th>
                                <th>@lang('messages.admin_correct')</th>
                                <th>@lang('messages.admin_avg_percent')</th>
                                <th>⏱️ Сред. время</th>
                                <th>@lang('messages.admin_subjects')</th>
                                <th>@lang('messages.admin_actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                @php 
                                    $s = $stats[$user->id] ?? ['total_tests'=>0, 'total_correct'=>0, 'avg_percentage'=>0, 'avg_time_formatted'=>'—', 'subjects'=>[]];
                                    $isBlocked = $user->is_blocked ?? false;
                                @endphp
                                <tr class="{{ $isBlocked ? 'blocked' : '' }}">
                                    <td>
                                        <span style="color:#ffd700;font-weight:600;">{{ $user->full_name ?? $user->name }}</span>
                                        @if($isBlocked)
                                            <span class="badge-blocked"><i class="fas fa-lock"></i> @lang('messages.admin_blocked')</span>
                                        @endif
                                    </td>
                                    <td style="color:rgba(255,255,255,0.5);">{{ $user->email }}</td>
                                    <td>
                                        @if($isBlocked)
                                            <span class="badge-blocked"><i class="fas fa-lock"></i> @lang('messages.admin_blocked')</span>
                                        @else
                                            <span class="badge-active"><i class="fas fa-check-circle"></i> @lang('messages.admin_active')</span>
                                        @endif
                                    </td>
                                    <td>{{ $s['total_tests'] }}</td>
                                    <td>{{ $s['total_correct'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress progress-thin flex-grow-1" style="width:60px;">
                                                <div class="progress-bar bg-{{ $s['avg_percentage'] >= 80 ? 'success' : ($s['avg_percentage'] >= 60 ? 'warning' : 'danger') }}" 
                                                     style="width:{{ $s['avg_percentage'] }}%;"></div>
                                            </div>
                                            <span>{{ round($s['avg_percentage']) }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($s['avg_time_formatted'] != '—')
                                            <span class="time-badge"><i class="far fa-clock"></i> {{ $s['avg_time_formatted'] }}</span>
                                        @else
                                            <span style="color:rgba(255,255,255,0.2);">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach(['Html','Css','Sql','Bootstrap','JavaScript','Laravel'] as $subj)
                                            @php $subjData = $s['subjects'][$subj] ?? ['total'=>0]; @endphp
                                            @if($subjData['total'] > 0)
                                                <span class="subject-badge {{ strtolower($subj) }}">{{ $subj }}</span>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <a href="{{ route('admin.user.details', $user->id) }}" class="btn-primary-glass" title="@lang('messages.admin_details')">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(!$isBlocked)
                                                <button type="button" class="btn-warning-glass" title="@lang('messages.admin_block')" 
                                                        onclick="showBlockModal('{{ $user->id }}', '{{ addslashes($user->full_name ?? $user->name) }}')">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                                <form id="block-form-{{ $user->id }}" action="{{ route('admin.user.block', $user->id) }}" method="POST" style="display:none;">
                                                    @csrf
                                                </form>
                                            @else
                                                <form action="{{ route('admin.user.block', $user->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn-success-glass" title="@lang('messages.admin_unblock')">
                                                        <i class="fas fa-unlock"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if($user->id != Auth::id())
                                                <button type="button" class="btn-danger-glass" title="@lang('messages.admin_delete')" 
                                                        onclick="showDeleteModal('{{ $user->id }}', '{{ addslashes($user->full_name ?? $user->name) }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <form id="delete-form-{{ $user->id }}" action="{{ route('admin.user.delete', $user->id) }}" method="POST" style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ============================================================
        // ===== УДАЛЕНИЕ =====
        // ============================================================
        let deleteUserId = null;
        
        function showDeleteModal(userId, userName) {
            deleteUserId = userId;
            document.getElementById('deleteUserName').textContent = userName;
            document.getElementById('deleteModal').classList.add('active');
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
            deleteUserId = null;
        }
        
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (deleteUserId) {
                var form = document.getElementById('delete-form-' + deleteUserId);
                if (form) form.submit();
            }
            closeDeleteModal();
        });

        // ============================================================
        // ===== БЛОКИРОВКА =====
        // ============================================================
        let blockUserId = null;
        
        function showBlockModal(userId, userName) {
            blockUserId = userId;
            document.getElementById('blockUserName').textContent = userName;
            document.getElementById('blockModalTitle').textContent = '@lang('messages.admin_block_title')';
            document.getElementById('blockModal').classList.add('active');
        }
        
        function closeBlockModal() {
            document.getElementById('blockModal').classList.remove('active');
            blockUserId = null;
        }
        
        document.getElementById('confirmBlockBtn').addEventListener('click', function() {
            if (blockUserId) {
                var form = document.getElementById('block-form-' + blockUserId);
                if (form) form.submit();
            }
            closeBlockModal();
        });

        // ============================================================
        // ===== ЗАКРЫТИЕ ПО КЛИКУ ВНЕ МОДАЛКИ =====
        // ============================================================
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });
        document.getElementById('blockModal').addEventListener('click', function(e) {
            if (e.target === this) closeBlockModal();
        });

        // ============================================================
        // ===== АВТОМАТИЧЕСКОЕ ИСЧЕЗНОВЕНИЕ УВЕДОМЛЕНИЯ =====
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>