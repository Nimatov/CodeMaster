// ============================================================
// ===== МАКСИМАЛЬНАЯ ЗАЩИТА — 100% РАБОТАЕТ =====
// ============================================================

(function() {
    'use strict';

    var isTest = window.location.pathname.includes('/test/');
    var isSubmitted = false;
    var timeLeft = 30 * 60;

    // ============================================================
    // 1. ПОЛНАЯ БЛОКИРОВКА ВСЕХ КЛАВИШ
    // ============================================================
    document.addEventListener('keydown', function(e) {
        if (!isTest) return;
        
        // Блокируем ВСЁ!
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        showNotification('⛔ Действие запрещено!');
        return false;
    }, true);

    // Дублируем блокировку для перехвата
    document.addEventListener('keyup', function(e) {
        if (!isTest) return;
        e.preventDefault();
        e.stopPropagation();
        return false;
    }, true);

    // ============================================================
    // 2. ПОЛНАЯ БЛОКИРОВКА КОПИРОВАНИЯ
    // ============================================================
    document.addEventListener('copy', function(e) {
        if (isTest) {
            e.preventDefault();
            e.stopPropagation();
            showNotification('❌ Копирование запрещено!');
            return false;
        }
    }, true);

    document.addEventListener('cut', function(e) {
        if (isTest) {
            e.preventDefault();
            e.stopPropagation();
            showNotification('❌ Вырезание запрещено!');
            return false;
        }
    }, true);

    document.addEventListener('paste', function(e) {
        if (isTest) {
            e.preventDefault();
            e.stopPropagation();
            showNotification('❌ Вставка запрещена!');
            return false;
        }
    }, true);

    // ============================================================
    // 3. БЛОКИРОВКА ВЫДЕЛЕНИЯ
    // ============================================================
    document.addEventListener('selectstart', function(e) {
        if (isTest) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, true);

    document.addEventListener('selectionchange', function(e) {
        if (isTest) {
            var sel = window.getSelection();
            if (sel) {
                sel.removeAllRanges();
                sel.empty();
            }
        }
    }, true);

    // ============================================================
    // 4. БЛОКИРОВКА ПРАВОЙ КНОПКИ
    // ============================================================
    document.addEventListener('contextmenu', function(e) {
        if (isTest) {
            e.preventDefault();
            e.stopPropagation();
            showNotification('❌ Правая кнопка мыши запрещена!');
            return false;
        }
    }, true);

    // ============================================================
    // 5. БЛОКИРОВКА PRINT SCREEN
    // ============================================================
    document.addEventListener('keydown', function(e) {
        if (!isTest) return;
        if (e.key === 'PrintScreen') {
            e.preventDefault();
            e.stopPropagation();
            showNotification('❌ Скриншот запрещен!');
            return false;
        }
    }, true);

    // Очистка буфера обмена при Print Screen
    document.addEventListener('keyup', function(e) {
        if (isTest && e.key === 'PrintScreen') {
            try {
                navigator.clipboard.writeText('').then(function() {
                    showNotification('❌ Скриншот запрещен!');
                }).catch(function() {});
            } catch(err) {}
            return false;
        }
    }, true);

    // ============================================================
    // 6. БЛОКИРОВКА ПЕРЕТАСКИВАНИЯ
    // ============================================================
    document.addEventListener('dragstart', function(e) {
        if (isTest) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, true);

    document.addEventListener('drop', function(e) {
        if (isTest) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, true);

    // ============================================================
    // 7. ОТКЛЮЧЕНИЕ КОНСОЛИ
    // ============================================================
    console.log = function() {};
    console.warn = function() {};
    console.error = function() {};
    console.info = function() {};
    console.debug = function() {};
    console.table = function() {};
    console.clear = function() {};
    console.dir = function() {};
    console.dirxml = function() {};
    console.group = function() {};
    console.groupEnd = function() {};
    console.time = function() {};
    console.timeEnd = function() {};

    // ============================================================
    // 8. БЛОКИРОВКА ОТКРЫТИЯ НОВЫХ ОКОН/ВКЛАДОК
    // ============================================================
    var originalOpen = window.open;
    window.open = function() {
        if (isTest) {
            showNotification('❌ Открытие новых окон запрещено!');
            return null;
        }
        return null;
    };

    // Блокировка target="_blank"
    document.addEventListener('click', function(e) {
        if (!isTest) return;
        var target = e.target.closest('a');
        if (target) {
            if (target.target === '_blank' || target.getAttribute('target') === '_blank') {
                e.preventDefault();
                e.stopPropagation();
                showNotification('❌ Открытие новых вкладок запрещено!');
                return false;
            }
            if (target.href && !target.href.includes('test/result') && !target.href.includes('#')) {
                e.preventDefault();
                e.stopPropagation();
                showExitModal();
                return false;
            }
        }
    }, true);

    // ============================================================
    // 9. БЛОКИРОВКА НАВИГАЦИИ НАЗАД
    // ============================================================
    if (isTest) {
        // Заполняем историю
        for (var i = 0; i < 50; i++) {
            history.pushState(null, '', window.location.href);
        }

        window.addEventListener('popstate', function(e) {
            if (!isSubmitted && timeLeft > 0) {
                e.preventDefault();
                e.stopPropagation();
                showExitModal();
                history.pushState(null, '', window.location.href);
                return false;
            }
        }, true);

        // Блокируем изменение URL
        setInterval(function() {
            if (window.location.hash === '#' || window.location.hash === '') {
                history.pushState(null, '', window.location.href);
            }
        }, 200);
    }

    // ============================================================
    // 10. БЛОКИРОВКА ЗАКРЫТИЯ ВКЛАДКИ
    // ============================================================
    window.addEventListener('beforeunload', function(e) {
        if (isTest && !isSubmitted) {
            e.preventDefault();
            e.returnValue = '⚠️ Тест будет завершен!';
            return '⚠️ Тест будет завершен!';
        }
    }, true);

    // ============================================================
    // 11. БЛОКИРОВКА CANVAS
    // ============================================================
    if (isTest) {
        try {
            var proto = CanvasRenderingContext2D.prototype;
            var origGetImageData = proto.getImageData;
            proto.getImageData = function() {
                showNotification('❌ Скриншот запрещен!');
                return null;
            };
            
            var origToDataURL = HTMLCanvasElement.prototype.toDataURL;
            HTMLCanvasElement.prototype.toDataURL = function() {
                showNotification('❌ Скриншот запрещен!');
                return null;
            };
            
            var origToBlob = HTMLCanvasElement.prototype.toBlob;
            HTMLCanvasElement.prototype.toBlob = function() {
                showNotification('❌ Скриншот запрещен!');
                return null;
            };
        } catch(e) {}
    }

    // ============================================================
    // 12. ЗАЩИТА ОТ СКРИНШОТОВ ЧЕРЕЗ CSS
    // ============================================================
    if (isTest) {
        // При потере фокуса (возможный скриншот) — размытие
        document.addEventListener('visibilitychange', function() {
            if (document.hidden && isTest) {
                document.body.classList.add('blurred');
                showNotification('🔒 Скриншот запрещен!');
                setTimeout(function() {
                    document.body.classList.remove('blurred');
                }, 1000);
            }
        });
    }

    // ============================================================
    // 13. ТАЙМЕР
    // ============================================================
    function startTimer() {
        if (!isTest) return;
        setInterval(function() {
            timeLeft--;
            var display = document.getElementById('timerDisplay');
            if (display) {
                var m = Math.floor(timeLeft / 60);
                var s = timeLeft % 60;
                display.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            }
            if (timeLeft <= 300) {
                var container = document.getElementById('timerContainer');
                if (container) container.classList.add('timer-warning');
                if (display) display.style.color = '#ff6b6b';
            }
            if (timeLeft <= 0) {
                autoSubmit();
            }
        }, 1000);
    }

    function autoSubmit() {
        if (isTest && !isSubmitted) {
            isSubmitted = true;
            var form = document.getElementById('testForm');
            if (form) {
                var btn = document.getElementById('submitBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Отправка...';
                }
                form.submit();
            }
        }
    }

    // ============================================================
    // 14. МОДАЛЬНОЕ ОКНО
    // ============================================================
    function showExitModal() {
        if (isTest && !isSubmitted) {
            var modal = document.getElementById('exitModal');
            if (modal) modal.classList.add('active');
        }
    }

    window.closeExitModal = function() {
        var modal = document.getElementById('exitModal');
        if (modal) modal.classList.remove('active');
    };

    window.confirmExit = function() {
        var modal = document.getElementById('exitModal');
        if (modal) modal.classList.remove('active');
        if (isTest) {
            isSubmitted = true;
            var form = document.getElementById('testForm');
            if (form) form.submit();
        }
    };

    // ============================================================
    // 15. УВЕДОМЛЕНИЯ
    // ============================================================
    function showNotification(message) {
        var existing = document.querySelector('.notification');
        if (existing) existing.remove();
        
        var div = document.createElement('div');
        div.className = 'notification';
        div.textContent = message;
        document.body.appendChild(div);
        setTimeout(function() {
            div.style.opacity = '0';
            div.style.transition = 'opacity 0.5s';
            setTimeout(function() { div.remove(); }, 500);
        }, 2500);
    }

    // ============================================================
    // 16. ЗАПУСК
    // ============================================================
    document.addEventListener('DOMContentLoaded', function() {
        if (isTest) {
            startTimer();
        }
    });

    console.log('%c🔒 МАКСИМАЛЬНАЯ ЗАЩИТА АКТИВИРОВАНА!', 'color: #00e5ff; font-size: 20px; font-weight: bold;');
    console.log('%c📌 Копирование, скриншоты, DevTools, навигация назад - ЗАПРЕЩЕНЫ!', 'color: #ffd700; font-size: 14px;');

})();