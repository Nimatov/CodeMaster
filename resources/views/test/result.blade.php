<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@lang('messages.result')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        
        .result-wrapper {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .result-card {
            background: white;
            border-radius: 25px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            text-align: center;
            animation: slideUp 0.6s ease;
        }
        
        @keyframes slideUp {
            0% { transform: translateY(50px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        
        .result-icon {
            font-size: 5rem;
            margin-bottom: 15px;
        }
        
        .result-title {
            font-weight: 800;
            font-size: 2.2rem;
            color: #333;
            margin-bottom: 5px;
        }
        
        .result-subject {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 30px;
            border-radius: 50px;
            display: inline-block;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 25px 0;
        }
        
        .stat-item {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        .stat-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }
        
        .stat-item .number {
            font-size: 2.5rem;
            font-weight: 800;
        }
        
        .stat-item .label {
            color: #888;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        .stat-item.green {
            background: #d4edda;
        }
        .stat-item.green .number { color: #28a745; }
        
        .stat-item.red {
            background: #f8d7da;
        }
        .stat-item.red .number { color: #dc3545; }
        
        .stat-item.blue {
            background: #d1ecf1;
        }
        .stat-item.blue .number { color: #17a2b8; }
        
        .progress-result {
            height: 30px;
            border-radius: 50px;
            margin: 25px 0;
            background: #eef2f7;
            overflow: hidden;
        }
        
        .progress-result .bar {
            height: 100%;
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
            transition: width 1.5s ease;
        }
        
        .bar.excellent { background: linear-gradient(90deg, #28a745, #20c997); }
        .bar.good { background: linear-gradient(90deg, #ffc107, #fd7e14); }
        .bar.bad { background: linear-gradient(90deg, #dc3545, #e83e8c); }
        
        .level-badge {
            display: inline-block;
            padding: 15px 45px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.5rem;
            margin: 15px 0 25px;
        }
        
        .level-excellent {
            background: #d4edda;
            color: #28a745;
            border: 2px solid #28a745;
        }
        
        .level-good {
            background: #fff3cd;
            color: #856404;
            border: 2px solid #ffc107;
        }
        
        .level-bad {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #dc3545;
        }
        
        .user-info {
            color: #888;
            font-size: 0.95rem;
            margin: 10px 0 25px;
        }
        
        .user-info i {
            margin: 0 5px;
        }
        
        .btn-group-result {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 25px;
        }
        
        .btn-result {
            padding: 14px 35px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }
        
        .btn-result:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .btn-certificate {
            background: linear-gradient(135deg, #ffd700, #ffed4a);
            color: #333;
        }
        
        .btn-retry {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .btn-home {
            background: #6c757d;
            color: white;
        }
        
        /* Конфетти эффект для отличного результата */
        .confetti-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: 999;
            overflow: hidden;
        }
        
        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            top: -10px;
            animation: confettiFall linear forwards;
        }
        
        @keyframes confettiFall {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; }
            100% { transform: translateY(110vh) rotate(720deg); opacity: 0; }
        }
        
        @media (max-width: 768px) {
            .result-card {
                padding: 30px 20px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .result-title {
                font-size: 1.8rem;
            }
            
            .btn-group-result {
                flex-direction: column;
            }
            
            .btn-result {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="result-wrapper">
        <div class="result-card">
            @php
                $levelClass = $result->certificate_level == 'excellent' ? 'level-excellent' : ($result->certificate_level == 'good' ? 'level-good' : 'level-bad');
                $icon = $result->certificate_level == 'excellent' ? '🎉' : ($result->certificate_level == 'good' ? '👍' : '📚');
                $levelText = $result->certificate_level == 'excellent' ? __('messages.excellent') : ($result->certificate_level == 'good' ? __('messages.good') : __('messages.bad'));
                $barClass = $result->certificate_level == 'excellent' ? 'excellent' : ($result->certificate_level == 'good' ? 'good' : 'bad');
            @endphp

            <div class="result-icon">{{ $icon }}</div>
            <h1 class="result-title">@lang('messages.result')</h1>
            <div class="result-subject">
                <i class="fas fa-book"></i> {{ $result->subject }}
            </div>

            <div class="stats-grid">
                <div class="stat-item green">
                    <div class="number">{{ $result->correct_answers }}</div>
                    <div class="label">✅ @lang('messages.correct_answers')</div>
                </div>
                <div class="stat-item red">
                    <div class="number">{{ $result->wrong_answers }}</div>
                    <div class="label">❌ @lang('messages.wrong_answers')</div>
                </div>
                <div class="stat-item blue">
                    <div class="number">{{ $result->score_percentage }}%</div>
                    <div class="label">📊 @lang('messages.percentage')</div>
                </div>
            </div>

            <div class="progress-result">
                <div class="bar {{ $barClass }}" style="width: 0%" id="progressBar">
                    {{ $result->score_percentage }}%
                </div>
            </div>

            <div class="level-badge {{ $levelClass }}">
                {{ $levelText }}
            </div>

            <div class="user-info">
                <i class="fas fa-user"></i> {{ $result->user->full_name ?? $result->user->name }}
                <span style="margin: 0 10px;">•</span>
                <i class="fas fa-calendar"></i> {{ $result->created_at->format('d.m.Y H:i') }}
            </div>

            <div class="btn-group-result">
                <!-- ===== ИСПРАВЛЕНА ССЫЛКА НА СКАЧИВАНИЕ СЕРТИФИКАТА ===== -->
                <a href="{{ route('certificate.download', $result->id) }}" class="btn-result btn-certificate" target="_blank">
                    <i class="fas fa-download"></i> @lang('messages.download_certificate')
                </a>
                <a href="{{ route('test.start', $result->subject) }}" class="btn-result btn-retry">
                    <i class="fas fa-redo"></i> @lang('messages.start_test')
                </a>
                <a href="{{ route('welcome') }}" class="btn-result btn-home">
                    <i class="fas fa-home"></i> @lang('messages.choose_subject')
                </a>
            </div>
        </div>
    </div>

    <!-- Конфетти для отличного результата -->
    @if($result->certificate_level == 'excellent')
    <div class="confetti-container" id="confettiContainer"></div>
    <script>
        function createConfetti() {
            const container = document.getElementById('confettiContainer');
            const colors = ['#ff6b6b', '#ffd93d', '#6bcb77', '#4d96ff', '#ff6bb5', '#845ef7'];
            
            for (let i = 0; i < 100; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.width = (Math.random() * 8 + 5) + 'px';
                confetti.style.height = (Math.random() * 8 + 5) + 'px';
                confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
                confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
                confetti.style.animationDelay = (Math.random() * 2) + 's';
                container.appendChild(confetti);
            }
        }
        
        createConfetti();
    </script>
    @endif

    <script>
        // Анимация прогресс-бара
        window.onload = function() {
            setTimeout(function() {
                document.getElementById('progressBar').style.width = '{{ $result->score_percentage }}%';
            }, 300);
        };
    </script>
</body>
</html>