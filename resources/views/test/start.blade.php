<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="google" content="notranslate">
    <title>@lang('messages.test') {{ $subject }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #00b4d8 0%, #7209b7 40%, #f72585 70%, #00b4d8 100%);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
            padding: 20px 0;
        }
        
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .test-wrapper { max-width: 1000px; margin: 0 auto; padding: 0 15px; }
        
        .test-header {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 20px 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .test-header .subject-info h2 { font-weight: 700; color: #0a1628; margin: 0; }
        .test-header .subject-info .badge-subject {
            background: linear-gradient(135deg, #00bcd4, #00e5ff);
            color: white;
            padding: 5px 20px;
            border-radius: 50px;
            font-size: 0.9rem;
            display: inline-block;
            margin-top: 5px;
        }
        
        .timer-container {
            background: linear-gradient(135deg, #f72585, #7209b7);
            padding: 12px 25px;
            border-radius: 50px;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 200px;
            justify-content: center;
            box-shadow: 0 0 40px rgba(247, 37, 133, 0.2);
        }
        .timer-container i { font-size: 1.5rem; }
        .timer-display {
            font-size: 1.8rem;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            letter-spacing: 2px;
        }
        .timer-warning { animation: pulse 1s ease-in-out infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        
        .progress-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 15px 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        .progress-info { display: flex; justify-content: space-between; margin-bottom: 8px; font-weight: 600; color: #0a1628; }
        .progress { height: 10px; border-radius: 50px; background: #eef2f7; }
        .progress-bar { background: linear-gradient(90deg, #00bcd4, #00e5ff); transition: width 0.5s ease; border-radius: 50px; }
        
        .question-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 25px 30px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border-left: 5px solid #00bcd4;
            transition: all 0.3s;
        }
        .question-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.12); }
        .question-card.answered { border-left-color: #00e5ff; }
        .question-header { display: flex; align-items: center; gap: 12px; margin-bottom: 15px; }
        .question-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #00bcd4, #00e5ff);
            color: white;
            border-radius: 50%;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        .question-text { font-weight: 600; font-size: 1.1rem; color: #0a1628; margin: 0; }
        .options-group { padding-left: 47px; }
        .option-label {
            display: flex;
            align-items: center;
            padding: 12px 18px;
            margin: 8px 0;
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            color: #0a1628;
        }
        .option-label:hover { border-color: #00bcd4; background: #f0f3ff; }
        .option-label input[type="radio"] {
            width: 18px;
            height: 18px;
            margin-right: 12px;
            cursor: pointer;
            accent-color: #00bcd4;
            flex-shrink: 0;
        }
        .option-label.selected { border-color: #00bcd4; background: #e8edff; }
        .option-label .option-letter {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background: #eef2f7;
            border-radius: 50%;
            font-weight: 700;
            color: #00bcd4;
            margin-right: 12px;
            flex-shrink: 0;
            font-size: 0.85rem;
        }
        .option-label.selected .option-letter { background: #00bcd4; color: white; }
        
        .submit-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            text-align: center;
            margin-top: 30px;
            position: sticky;
            bottom: 20px;
        }
        .btn-submit {
            background: linear-gradient(135deg, #00bcd4, #00e5ff);
            color: white;
            padding: 15px 60px;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.2rem;
            transition: all 0.3s;
            cursor: pointer;
            box-shadow: 0 0 30px rgba(0, 229, 255, 0.2);
        }
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 50px rgba(0, 229, 255, 0.4);
            color: white;
        }
        .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none !important; }
        .btn-submit i { margin-right: 10px; }
        
        .status-remaining {
            margin-top: 10px;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 10px;
            display: inline-block;
        }
        .status-remaining.pending {
            background: rgba(255, 107, 107, 0.1);
            color: #ff6b6b;
        }
        .status-remaining.complete {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        /* ===== МОДАЛЬНОЕ ОКНО ВЫХОДА ===== */
        .exit-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(10, 22, 40, 0.85);
            backdrop-filter: blur(20px);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }
        .exit-overlay.active { display: flex; }
        
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
        
        /* ===== МОДАЛЬНОЕ ОКНО ВРЕМЕНИ ===== */
        .timeout-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(10, 22, 40, 0.85);
            backdrop-filter: blur(20px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .timeout-overlay.active { display: flex; }
        .timeout-modal {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            padding: 45px 50px;
            max-width: 480px;
            width: 90%;
            text-align: center;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.4);
            animation: modalPop 0.4s ease;
        }
        .timeout-modal .icon { font-size: 4rem; margin-bottom: 20px; }
        .timeout-modal h3 { font-weight: 700; margin-bottom: 10px; color: #0a1628; }
        .timeout-modal p { color: #666; margin-bottom: 25px; }
        .timeout-modal .btn-auto-submit {
            background: linear-gradient(135deg, #00bcd4, #00e5ff);
            color: white;
            border: none;
            padding: 12px 40px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 0 30px rgba(0, 229, 255, 0.2);
        }
        .timeout-modal .btn-auto-submit:hover {
            transform: scale(1.05);
            box-shadow: 0 0 50px rgba(0, 229, 255, 0.4);
        }
        
        /* ===== УВЕДОМЛЕНИЯ ===== */
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
            box-shadow: 0 10px 40px rgba(255,0,0,0.3);
            max-width: 90%;
            text-align: center;
            animation: slideUp 0.4s ease;
        }
        .notification.success { background: #28a745; }
        .notification.warning { background: #ffc107; color: #333; }
        
        @keyframes slideUp {
            from { transform: translateX(-50%) translateY(100px); opacity: 0; }
            to { transform: translateX(-50%) translateY(0); opacity: 1; }
        }
        
        @media (max-width: 768px) {
            .test-header { flex-direction: column; gap: 15px; text-align: center; }
            .test-header .subject-info h2 { font-size: 1.3rem; }
            .timer-container { width: 100%; }
            .options-group { padding-left: 0; }
            .question-card { padding: 20px; }
            .btn-submit { padding: 12px 30px; font-size: 1rem; width: 100%; }
            .exit-modal { padding: 30px 25px; }
            .exit-modal .btn-exit { padding: 10px 25px; font-size: 0.9rem; min-width: 100px; }
        }
    </style>
</head>
<body>
    <div class="test-wrapper">
        <!-- Header -->
        <div class="test-header">
            <div class="subject-info">
                <h2>📝 @lang('messages.test')</h2>
                <span class="badge-subject">{{ $subject }}</span>
            </div>
            <div class="timer-container" id="timerContainer">
                <i class="fas fa-clock"></i>
                <span class="timer-display" id="timerDisplay">30:00</span>
            </div>
        </div>

        <!-- Progress -->
        <div class="progress-container">
            <div class="progress-info">
                <span>📊 @lang('messages.questions')</span>
                <span id="progressText">0 / {{ count($questions) }}</span>
            </div>
            <div class="progress">
                <div class="progress-bar" id="progressBar" style="width: 0%"></div>
            </div>
        </div>

        <!-- Questions -->
        <form id="testForm" action="{{ route('test.submit') }}" method="POST">
            @csrf
            <input type="hidden" name="subject" value="{{ $subject }}">
            <input type="hidden" name="timeout" id="timeoutInput" value="0">
            
            @foreach($questions as $index => $question)
                <div class="question-card" data-question="{{ $index + 1 }}">
                    <div class="question-header">
                        <span class="question-number">{{ $index + 1 }}</span>
                        <span class="question-text">{{ $question['question'] }}</span>
                    </div>
                    <div class="options-group">
                        @foreach($question['options'] as $optionIndex => $option)
                            <label class="option-label" onclick="selectOption(this)">
                                <input type="radio" 
                                       name="answers[{{ $index }}]" 
                                       value="{{ $optionIndex }}"
                                       onchange="updateProgress()">
                                <span class="option-letter">{{ chr(65 + $optionIndex) }}</span>
                                {{ $option }}
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
            
            <div class="submit-section">
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-check-circle"></i> @lang('messages.result')
                </button>
                <div id="statusRemaining" class="status-remaining pending">
                    ⏳ @lang('messages.remaining_questions') <span id="remainingCount">{{ count($questions) }}</span>
                </div>
            </div>
        </form>
    </div>

    <!-- ===== МОДАЛЬНОЕ ОКНО ПРИ ОКОНЧАНИИ ВРЕМЕНИ ===== -->
    <div class="timeout-overlay" id="timeoutModal">
        <div class="timeout-modal">
            <div class="icon">⏰</div>
            <h3>⏰ @lang('messages.timeout_title')</h3>
            <p>@lang('messages.timeout_message')</p>
            <button class="btn-auto-submit" onclick="autoSubmit()">
                <i class="fas fa-check"></i> @lang('messages.show_result')
            </button>
        </div>
    </div>

    <!-- ===== МОДАЛЬНОЕ ОКНО ВЫХОДА ===== -->
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

    <script>
          // Right-click bloklash
    window.addEventListener('contextmenu', e => {
      e.preventDefault();
      return false;
    });


    window.addEventListener('keydown', function(e){
      if (e.key === 'F12') return e.preventDefault();
      if (e.ctrlKey && e.shiftKey && ['I','J','C'].includes(e.key)) return e.preventDefault();
      if (e.ctrlKey && ['u','U','s','S'].includes(e.key)) return e.preventDefault();
    }, true);


    (function(){
      const th = 160;
      function checkDevTools(){
        if (
          window.outerWidth - window.innerWidth > th ||
          window.outerHeight - window.innerHeight > th
        ) {
          document.documentElement.innerHTML =
            '<div style="font-size:30px;text-align:center;margin-top:20%;">DevTools ochildi — sahifa yopildi.</div>';
        }
      }
      setInterval(checkDevTools, 500);
    })();

    function clearClipboard(){
      try {
        navigator.clipboard.writeText(""); 
      } catch(e){}
    }


    window.addEventListener('keyup', function(e){
      if (e.key === 'PrintScreen') clearClipboard(); 
    });

    setInterval(clearClipboard, 300);
        // ============================================================
        // ===== ЗАЩИТА ОТ НАВИГАЦИИ НАЗАД (МОДАЛЬНОЕ ОКНО) =====
        // ============================================================
        var isSubmitted = false;
        var timeLeft = 30 * 60;
        var timerInterval;
        var isTimeOut = false;

        // Блокируем историю при загрузке
        history.pushState(null, '', window.location.href);

        // Перехват нажатия "Назад"
        window.addEventListener('popstate', function(e) {
            if (!isSubmitted && timeLeft > 0) {
                e.preventDefault();
                showExitModal();
                // Возвращаемся в историю, чтобы модальное окно не пропало
                history.pushState(null, '', window.location.href);
                return false;
            }
        });

        // Дополнительная блокировка каждые 500мс
        setInterval(function() {
            if (window.location.hash === '#') {
                history.pushState(null, '', window.location.href);
            }
        }, 500);

        // ============================================================
        // ===== ТАЙМЕР =====
        // ============================================================
        function startTimer() {
            timerInterval = setInterval(function() {
                timeLeft--;

                var minutes = Math.floor(timeLeft / 60);
                var seconds = timeLeft % 60;

                var display = document.getElementById('timerDisplay');
                if (display) {
                    display.textContent =
                        String(minutes).padStart(2, '0') + ':' +
                        String(seconds).padStart(2, '0');
                }

                if (timeLeft <= 300) {
                    var container = document.getElementById('timerContainer');
                    if (container) {
                        container.classList.add('timer-warning');
                    }
                    if (display) {
                        display.style.color = '#ff6b6b';
                    }
                }

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    isTimeOut = true;
                    
                    var modal = document.getElementById('timeoutModal');
                    if (modal) {
                        modal.classList.add('active');
                    }
                    
                    var input = document.getElementById('timeoutInput');
                    if (input) {
                        input.value = '1';
                    }
                    
                    setTimeout(function() {
                        autoSubmit();
                    }, 2000);
                }
            }, 1000);
        }

        function autoSubmit() {
            var modal = document.getElementById('timeoutModal');
            if (modal) {
                modal.classList.remove('active');
            }
            
            var btn = document.getElementById('submitBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> @lang('messages.submitting')';
            }
            
            var form = document.getElementById('testForm');
            if (form && !isSubmitted) {
                isSubmitted = true;
                form.submit();
            }
        }

        // ============================================================
        // ===== ПРОГРЕСС =====
        // ============================================================
        function updateProgress() {
            var total = {{ count($questions) }};
            var radios = document.querySelectorAll('input[type="radio"]:checked');
            var answered = radios.length;
            var remaining = total - answered;

            var progressText = document.getElementById('progressText');
            if (progressText) {
                progressText.textContent = answered + ' / ' + total;
            }

            var progressBar = document.getElementById('progressBar');
            if (progressBar) {
                var percentage = (answered / total) * 100;
                progressBar.style.width = percentage + '%';
            }

            var statusDiv = document.getElementById('statusRemaining');
            var remainingSpan = document.getElementById('remainingCount');
            if (statusDiv && remainingSpan) {
                remainingSpan.textContent = remaining;
                if (remaining === 0) {
                    statusDiv.className = 'status-remaining complete';
                    statusDiv.innerHTML = '✅ @lang('messages.all_answered')';
                } else {
                    statusDiv.className = 'status-remaining pending';
                    statusDiv.innerHTML = '⏳ @lang('messages.remaining_questions') <span id="remainingCount">' + remaining + '</span>';
                }
            }

            document.querySelectorAll('.question-card').forEach(function(card) {
                var radio = card.querySelector('input[type="radio"]:checked');
                if (radio) {
                    card.classList.add('answered');
                } else {
                    card.classList.remove('answered');
                }
            });
        }

        function selectOption(label) {
            var radio = label.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }

            var group = label.closest('.options-group');
            if (group) {
                group.querySelectorAll('.option-label').forEach(function(el) {
                    el.classList.remove('selected');
                });
            }

            label.classList.add('selected');
            updateProgress();
        }

        // ============================================================
        // ===== МОДАЛЬНОЕ ОКНО ВЫХОДА =====
        // ============================================================
        function showExitModal() {
            if (isSubmitted || timeLeft <= 0) return;
            document.getElementById('exitModal').classList.add('active');
        }

        function closeExitModal() {
            document.getElementById('exitModal').classList.remove('active');
        }

        function confirmExit() {
            document.getElementById('exitModal').classList.remove('active');
            isSubmitted = true;
            document.getElementById('testForm').submit();
        }

        // ============================================================
        // ===== ЗАПУСК =====
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            startTimer();
            updateProgress();

            var form = document.getElementById('testForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (timeLeft <= 0) {
                        return true;
                    }
                    
                    // Проверка: все ли вопросы отвечены
                    var total = {{ count($questions) }};
                    var radios = document.querySelectorAll('input[type="radio"]:checked');
                    if (radios.length !== total) {
                        e.preventDefault();
                        var remaining = total - radios.length;
                        showNotification('⚠️ @lang('messages.answer_all_questions') (' + remaining + ' @lang('messages.remaining') )', 'warning');
                        
                        document.querySelectorAll('.question-card').forEach(function(card) {
                            var radio = card.querySelector('input[type="radio"]:checked');
                            if (!radio) {
                                card.style.borderLeftColor = '#ff4444';
                                card.style.background = 'rgba(255, 68, 68, 0.05)';
                                setTimeout(function() {
                                    card.style.borderLeftColor = '';
                                    card.style.background = '';
                                }, 3000);
                            }
                        });
                        
                        var firstUnanswered = document.querySelector('.question-card:not(.answered)');
                        if (firstUnanswered) {
                            firstUnanswered.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        return false;
                    }
                    
                    isSubmitted = true;
                    var btn = document.getElementById('submitBtn');
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> @lang('messages.submitting')';
                    }
                });
            }
        });

        // ============================================================
        // ===== УВЕДОМЛЕНИЯ =====
        // ============================================================
        function showNotification(message, type) {
            document.querySelectorAll('.notification').forEach(function(el) { el.remove(); });
            
            var div = document.createElement('div');
            div.className = 'notification' + (type ? ' ' + type : '');
            div.textContent = message;
            document.body.appendChild(div);
            
            setTimeout(function() {
                div.style.opacity = '0';
                div.style.transition = 'opacity 0.5s';
                setTimeout(function() {
                    div.remove();
                }, 500);
            }, 3500);
        }

        // Делаем функции глобальными
        window.closeExitModal = closeExitModal;
        window.confirmExit = confirmExit;
        window.showExitModal = showExitModal;
        window.autoSubmit = autoSubmit;

        console.log('%c🔒 Защита от навигации назад АКТИВИРОВАНА!', 'color: #00e5ff; font-size: 18px; font-weight: bold;');
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>