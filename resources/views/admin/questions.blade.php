<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@lang('messages.admin_questions_title')</title>
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
        
        .question-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 20px;
            color: white;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        .question-card:hover {
            background: rgba(255,255,255,0.06);
            border-color: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .subject-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .subject-badge.html { background: rgba(227,79,38,0.2); color: #e34f26; }
        .subject-badge.css { background: rgba(21,114,182,0.2); color: #1572b6; }
        .subject-badge.sql { background: rgba(68,121,161,0.2); color: #4479a1; }
        .subject-badge.bootstrap { background: rgba(121,82,179,0.2); color: #7952b3; }
        .subject-badge.js { background: rgba(247,223,30,0.2); color: #f7df1e; }
        .subject-badge.laravel { background: rgba(255,45,32,0.2); color: #ff2d20; }
        
        .question-text { color: white; font-weight: 500; margin-bottom: 10px; font-size: 1rem; }
        .options-list { display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 10px; }
        .options-list .option { font-size: 0.85rem; padding: 3px 10px; border-radius: 6px; background: rgba(255,255,255,0.03); }
        .options-list .option.correct { color: #00e676; background: rgba(0,230,118,0.08); border: 1px solid rgba(0,230,118,0.15); }
        .options-list .option.wrong { color: rgba(255,255,255,0.4); }
        
        .btn-action-group { display: flex; gap: 8px; flex-shrink: 0; }
        .btn-edit {
            background: rgba(255,215,0,0.12);
            border: 1px solid rgba(255,215,0,0.15);
            color: #ffd700;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-edit:hover { background: rgba(255,215,0,0.2); transform: translateY(-2px); }
        .btn-delete {
            background: rgba(255,68,68,0.12);
            border: 1px solid rgba(255,68,68,0.15);
            color: #ff6b6b;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }
        .btn-delete:hover { background: rgba(255,68,68,0.2); transform: translateY(-2px); }
        
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
        .lang-switcher .lang-btn:hover { color: rgba(255,255,255,0.8); background: rgba(255,255,255,0.05); }
        .lang-switcher .lang-btn.active { color: #00e5ff; background: rgba(0,229,255,0.1); }
        .lang-switcher .lang-btn .flag { font-size: 1rem; margin-right: 4px; }
        .lang-switcher .divider { color: rgba(255,255,255,0.1); font-size: 1.2rem; }
        
        .btn-primary-glass {
            background: linear-gradient(135deg, #00bcd4, #00e5ff);
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary-glass:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,229,255,0.3); color: white; }
        
        .empty-state { text-align: center; padding: 60px 0; color: rgba(255,255,255,0.3); }
        .empty-state i { font-size: 3rem; margin-bottom: 15px; }
        .empty-state p { font-size: 1.2rem; }
        
        .alert { transition: opacity 0.5s ease; padding: 12px 20px; border-radius: 10px; }
        .alert-success { background: rgba(0,230,118,0.1); border: 1px solid rgba(0,230,118,0.2); color: #00e676; }
        .alert-danger { background: rgba(255,68,68,0.1); border: 1px solid rgba(255,68,68,0.2); color: #ff6b6b; }

        /* ===== КРАСИВОЕ МОДАЛЬНОЕ ОКНО ===== */
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
    </style>
</head>
<body>

    <!-- ===== МОДАЛЬНОЕ ОКНО УДАЛЕНИЯ ВОПРОСА ===== -->
    <div class="modal-custom" id="deleteQuestionModal">
        <div class="modal-box">
            <div class="icon danger">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h3>@lang('messages.admin_questions_confirm_delete')</h3>
            <p class="sub-text">Вы собираетесь удалить вопрос</p>
            <p style="color:#f72585; font-weight:600; font-size:1.1rem;" id="deleteQuestionText">Вопрос</p>
            <p style="font-size:0.9rem; color:#ff6b6b; background:rgba(255,68,68,0.05); padding:10px; border-radius:8px;">
                <i class="fas fa-exclamation-circle"></i> @lang('messages.admin_delete_warning')
            </p>
            <div class="btn-group">
                <button class="btn-modal btn-cancel" onclick="closeQuestionDeleteModal()">
                    <i class="fas fa-times"></i> @lang('messages.admin_cancel')
                </button>
                <button class="btn-modal btn-danger" id="confirmDeleteQuestionBtn">
                    <i class="fas fa-trash"></i> @lang('messages.admin_delete_confirm')
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
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <i class="fas fa-chart-pie"></i> @lang('messages.admin_statistics')
                    </a>
                    <a href="{{ route('admin.questions') }}" class="nav-link active">
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
                        <i class="fas fa-question-circle" style="color:#00e5ff;"></i> 
                        @lang('messages.admin_questions_title')
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
                        <a href="{{ route('admin.questions.create') }}" class="btn-primary-glass">
                            <i class="fas fa-plus"></i> @lang('messages.admin_questions_add')
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(isset($questions) && $questions->count() > 0)
                    @foreach($questions as $question)
                        @php 
                            $options = is_array($question->options) ? $question->options : json_decode($question->options, true); 
                            if (!is_array($options)) $options = ['', '', '', ''];
                        @endphp
                        <div class="question-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <span class="subject-badge {{ strtolower($question->subject) }}">{{ $question->subject }}</span>
                                        <span style="color:rgba(255,255,255,0.25); font-size:0.7rem;">ID: {{ $question->id }}</span>
                                    </div>
                                    <div class="question-text">{{ $question->question }}</div>
                                    <div class="options-list">
                                        @foreach($options as $key => $option)
                                            <span class="option {{ $key == $question->correct ? 'correct' : 'wrong' }}">
                                                {{ chr(65 + $key) }}. {{ $option }}
                                                @if($key == $question->correct)
                                                    <i class="fas fa-check-circle" style="color:#00e676; margin-left:4px;"></i>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="btn-action-group">
                                    <a href="{{ route('admin.questions.edit', $question->id) }}" class="btn-edit" title="@lang('messages.admin_questions_edit_action')">
                                        <i class="fas fa-pen"></i> @lang('messages.admin_questions_edit_action')
                                    </a>
                                    <button type="button" class="btn-delete" title="@lang('messages.admin_questions_delete')"
                                            onclick="showQuestionDeleteModal('{{ $question->id }}', '{{ addslashes($question->question) }}')">
                                        <i class="fas fa-trash"></i> @lang('messages.admin_questions_delete')
                                    </button>
                                    <form id="delete-question-form-{{ $question->id }}" action="{{ route('admin.questions.delete', $question->id) }}" method="POST" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>@lang('messages.admin_questions_empty')</p>
                        <a href="{{ route('admin.questions.create') }}" class="btn-primary-glass">
                            <i class="fas fa-plus"></i> @lang('messages.admin_questions_create_first')
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // ============================================================
        // ===== УДАЛЕНИЕ ВОПРОСА =====
        // ============================================================
        let deleteQuestionId = null;
        
        function showQuestionDeleteModal(questionId, questionText) {
            deleteQuestionId = questionId;
            document.getElementById('deleteQuestionText').textContent = questionText;
            document.getElementById('deleteQuestionModal').classList.add('active');
        }
        
        function closeQuestionDeleteModal() {
            document.getElementById('deleteQuestionModal').classList.remove('active');
            deleteQuestionId = null;
        }
        
        document.getElementById('confirmDeleteQuestionBtn').addEventListener('click', function() {
            if (deleteQuestionId) {
                var form = document.getElementById('delete-question-form-' + deleteQuestionId);
                if (form) {
                    form.submit();
                }
            }
            closeQuestionDeleteModal();
        });
        
        // Закрытие по клику вне модального окна
        document.getElementById('deleteQuestionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeQuestionDeleteModal();
            }
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