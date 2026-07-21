<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Intervention\Image\Facades\Image;

class TestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function start($subject)
    {
        // Сохраняем время начала теста
        session(['test_start_time' => time()]);
        
        $questions = $this->getQuestions($subject);
        
        // Перемешиваем вопросы
        shuffle($questions);
        
        // Перемешиваем варианты ответов в каждом вопросе
        foreach ($questions as $key => $question) {
            $correctAnswer = $question['options'][$question['correct']];
            shuffle($question['options']);
            $newCorrectIndex = array_search($correctAnswer, $question['options']);
            $questions[$key]['correct'] = $newCorrectIndex;
        }
        
        return view('test.start', compact('subject', 'questions'));
    }

    public function submit(Request $request)
    {
        $subject = $request->subject;
        $answers = $request->answers;
        $correctAnswers = $this->getCorrectAnswers($subject);
        
        $correct = 0;
        $wrong = 0;
        
        foreach ($correctAnswers as $index => $correctAnswer) {
            if (isset($answers[$index]) && $answers[$index] == $correctAnswer) {
                $correct++;
            } else {
                $wrong++;
            }
        }
        
        $total = count($correctAnswers);
        $percentage = round(($correct / $total) * 100);
        
        if ($percentage >= 80) {
            $level = 'excellent';
        } elseif ($percentage >= 60) {
            $level = 'good';
        } else {
            $level = 'bad';
        }
        
        // ===== ВРЕМЯ, ЗАТРАЧЕННОЕ НА ТЕСТ =====
        $startTime = session('test_start_time', time());
        $timeSpent = time() - $startTime;
        
        session()->forget('test_start_time');
        
        $result = TestResult::create([
            'user_id' => Auth::id(),
            'subject' => $subject,
            'total_questions' => $total,
            'correct_answers' => $correct,
            'wrong_answers' => $wrong,
            'score_percentage' => $percentage,
            'certificate_level' => $level,
            'time_spent' => $timeSpent,
        ]);
        
        return redirect()->route('test.result', $result->id);
    }

    public function result($id)
    {
        $result = TestResult::with('user')->findOrFail($id);
        return view('test.result', compact('result'));
    }

    public function downloadCertificate($id)
    {
        try {
            $result = TestResult::with('user')->findOrFail($id);
            
            // Проверяем доступ
            if ($result->user_id != Auth::id() && !Auth::user()->isAdmin()) {
                abort(403);
            }
            
            // Удаляем старые сертификаты
            $this->cleanOldCertificates($id);
            
            // Генерируем новый сертификат
            $path = $this->generateCertificate($result);
            
            // Проверяем, существует ли файл
            if (!file_exists($path)) {
                throw new \Exception('Файл сертификата не найден');
            }
            
            // Скачиваем с очисткой кеша
            return response()->download($path, 'certificate_' . $result->user->name . '.png', [
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            \Log::error('Ошибка при скачивании сертификата: ' . $e->getMessage());
            return back()->with('error', 'Не удалось сгенерировать сертификат');
        }
    }

    /**
     * Очищает старые сертификаты для указанного результата
     */
    private function cleanOldCertificates($resultId)
    {
        $path = storage_path('app/public/certificates/');
        if (!file_exists($path)) {
            return;
        }

        $oldFiles = glob($path . 'certificate_' . $resultId . '_*.png');
        foreach ($oldFiles as $oldFile) {
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
    }

    // ============================================================
    // ГЕНЕРАЦИЯ СЕРТИФИКАТА
    // ============================================================
    private function generateCertificate($result)
    {
        $width = 1200;
        $height = 850;
        
        $img = Image::canvas($width, $height, '#1a1a2e');
        
        $img->rectangle(25, 25, $width-25, $height-25, function ($draw) {
            $draw->background('#ffffff');
        });
        
        $this->drawBorder($img, 30, 30, $width-30, $height-30, '#d4af37', 3);
        $this->drawBorder($img, 35, 35, $width-35, $height-35, '#c9a84c', 1);
        $this->drawBorder($img, 40, 40, $width-40, $height-40, '#f0e6d3', 1);
        
        $this->drawCornerDecoration($img, 45, 45, '#d4af37');
        $this->drawCornerDecoration($img, $width-45, 45, '#d4af37', 'top-right');
        $this->drawCornerDecoration($img, 45, $height-45, '#d4af37', 'bottom-left');
        $this->drawCornerDecoration($img, $width-45, $height-45, '#d4af37', 'bottom-right');
        
        $img->circle(40, 600, 70, function ($draw) {
            $draw->border(3, '#d4af37');
            $draw->background('rgba(212, 175, 55, 0.1)');
        });
        
        try {
            $img->text('★', 600, 65, function($font) {
                $font->size(25);
                $font->color('#d4af37');
                $font->align('center');
                $font->valign('top');
            });
        } catch (\Exception $e) {
            $img->text('★', 600, 65, function($font) {
                $font->size(25);
                $font->color('#d4af37');
                $font->align('center');
                $font->valign('top');
            });
        }
        
        try {
            $img->text('CERTIFICATE', $width/2, 140, function($font) {
                $font->size(70);
                $font->color('#1a1a2e');
                $font->align('center');
                $font->valign('top');
            });
        } catch (\Exception $e) {
            $img->text('CERTIFICATE', $width/2, 140, function($font) {
                $font->size(70);
                $font->color('#1a1a2e');
                $font->align('center');
                $font->valign('top');
            });
        }
        
        try {
            $img->text('OF ACHIEVEMENT', $width/2, 215, function($font) {
                $font->size(32);
                $font->color('#d4af37');
                $font->align('center');
                $font->valign('top');
            });
        } catch (\Exception $e) {
            $img->text('OF ACHIEVEMENT', $width/2, 215, function($font) {
                $font->size(32);
                $font->color('#d4af37');
                $font->align('center');
                $font->valign('top');
            });
        }
        
        $img->rectangle(300, 250, 900, 253, function ($draw) {
            $draw->background('#d4af37');
        });
        
        for ($i = 300; $i <= 900; $i += 40) {
            $img->circle(5, $i, 252, function ($draw) {
                $draw->background('#d4af37');
            });
        }
        
        try {
            $img->text('THIS CERTIFICATE IS PROUDLY PRESENTED TO', $width/2, 310, function($font) {
                $font->size(20);
                $font->color('#888888');
                $font->align('center');
                $font->valign('top');
            });
        } catch (\Exception $e) {
            $img->text('THIS CERTIFICATE IS PROUDLY PRESENTED TO', $width/2, 310, function($font) {
                $font->size(20);
                $font->color('#888888');
                $font->align('center');
                $font->valign('top');
            });
        }
        
        $name = strtoupper($result->user->full_name ?? $result->user->name);
        
        // ============================================================
        // ★★★ ИЗМЕНЯЙ КООРДИНАТЫ ИМЕНИ ЗДЕСЬ ★★★
        // ============================================================
        $nameX = 600;    // ← Позиция по горизонтали (центр 600)
        $nameY = 380;    // ← Позиция по вертикали (чем больше - тем ниже)
        $nameSize = 55;  // ← Размер шрифта
        
        try {
            $img->text($name, $nameX, $nameY, function($font) use ($nameSize) {
                $font->size($nameSize);
                $font->color('#1a1a2e');
                $font->align('center');
                $font->valign('top');
            });
        } catch (\Exception $e) {
            $img->text($name, $nameX, $nameY, function($font) use ($nameSize) {
                $font->size($nameSize);
                $font->color('#1a1a2e');
                $font->align('center');
                $font->valign('top');
            });
        }
        
        $img->rectangle(320, 445, 880, 448, function ($draw) {
            $draw->background('#d4af37');
        });
        
        try {
            $img->text('for successfully completing the examination in', $width/2, 490, function($font) {
                $font->size(18);
                $font->color('#888888');
                $font->align('center');
                $font->valign('top');
            });
        } catch (\Exception $e) {
            $img->text('for successfully completing the examination in', $width/2, 490, function($font) {
                $font->size(18);
                $font->color('#888888');
                $font->align('center');
                $font->valign('top');
            });
        }
        
        try {
            $img->text(strtoupper($result->subject), $width/2, 540, function($font) {
                $font->size(40);
                $font->color('#d4af37');
                $font->align('center');
                $font->valign('top');
            });
        } catch (\Exception $e) {
            $img->text(strtoupper($result->subject), $width/2, 540, function($font) {
                $font->size(40);
                $font->color('#d4af37');
                $font->align('center');
                $font->valign('top');
            });
        }
        
        try {
            $img->text($result->score_percentage . '%', $width/2, 600, function($font) {
                $font->size(48);
                $font->color('#1a1a2e');
                $font->align('center');
                $font->valign('top');
            });
        } catch (\Exception $e) {
            $img->text($result->score_percentage . '%', $width/2, 600, function($font) {
                $font->size(48);
                $font->color('#1a1a2e');
                $font->align('center');
                $font->valign('top');
            });
        }
        
        $levelColor = '#28a745';
        $levelText = 'EXCELLENT';
        $levelIcon = '🏆';
        if ($result->certificate_level == 'good') {
            $levelColor = '#ffc107';
            $levelText = 'GOOD';
            $levelIcon = '⭐';
        } elseif ($result->certificate_level == 'bad') {
            $levelColor = '#dc3545';
            $levelText = 'NEEDS IMPROVEMENT';
            $levelIcon = '📚';
        }
        
        try {
            $img->text($levelIcon . ' ' . $levelText, $width/2, 660, function($font) use ($levelColor) {
                $font->size(30);
                $font->color($levelColor);
                $font->align('center');
                $font->valign('top');
            });
        } catch (\Exception $e) {
            $img->text($levelIcon . ' ' . $levelText, $width/2, 660, function($font) use ($levelColor) {
                $font->size(30);
                $font->color($levelColor);
                $font->align('center');
                $font->valign('top');
            });
        }
        
        $img->rectangle(150, 710, 1050, 713, function ($draw) {
            $draw->background('#d4af37');
        });
        
        try {
            $img->text('_________________________', 300, 740, function($font) {
                $font->size(16);
                $font->color('#1a1a2e');
                $font->align('center');
                $font->valign('top');
            });
            $img->text('SIGNATURE', 300, 770, function($font) {
                $font->size(14);
                $font->color('#999999');
                $font->align('center');
                $font->valign('top');
            });
        } catch (\Exception $e) {
            $img->text('_________________________', 300, 740, function($font) {
                $font->size(16);
                $font->color('#1a1a2e');
                $font->align('center');
                $font->valign('top');
            });
            $img->text('SIGNATURE', 300, 770, function($font) {
                $font->size(14);
                $font->color('#999999');
                $font->align('center');
                $font->valign('top');
            });
        }
        
        try {
            $img->text('_________________________', 900, 740, function($font) {
                $font->size(16);
                $font->color('#1a1a2e');
                $font->align('center');
                $font->valign('top');
            });
            $img->text('DATE: ' . now()->format('d.m.Y'), 900, 770, function($font) {
                $font->size(14);
                $font->color('#999999');
                $font->align('center');
                $font->valign('top');
            });
        } catch (\Exception $e) {
            $img->text('_________________________', 900, 740, function($font) {
                $font->size(16);
                $font->color('#1a1a2e');
                $font->align('center');
                $font->valign('top');
            });
            $img->text('DATE: ' . now()->format('d.m.Y'), 900, 770, function($font) {
                $font->size(14);
                $font->color('#999999');
                $font->align('center');
                $font->valign('top');
            });
        }
        
        $img->circle(60, 600, 755, function ($draw) {
            $draw->border(3, '#d4af37');
            $draw->background('rgba(212, 175, 55, 0.05)');
        });
        
        try {
            $img->text('★', 600, 750, function($font) {
                $font->size(28);
                $font->color('#d4af37');
                $font->align('center');
                $font->valign('top');
            });
        } catch (\Exception $e) {
            $img->text('★', 600, 750, function($font) {
                $font->size(28);
                $font->color('#d4af37');
                $font->align('center');
                $font->valign('top');
            });
        }
        
        $certNumber = '#' . str_pad($result->id, 6, '0', STR_PAD_LEFT);
        try {
            $img->text('Certificate No: ' . $certNumber, $width/2, 820, function($font) {
                $font->size(12);
                $font->color('#cccccc');
                $font->align('center');
                $font->valign('top');
            });
        } catch (\Exception $e) {
            $img->text('Certificate No: ' . $certNumber, $width/2, 820, function($font) {
                $font->size(12);
                $font->color('#cccccc');
                $font->align('center');
                $font->valign('top');
            });
        }
        
        $path = storage_path('app/public/certificates/');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        
        $filename = 'certificate_' . $result->id . '_' . time() . '.png';
        $fullPath = $path . $filename;
        $img->save($fullPath, 95);
        
        return $fullPath;
    }

    // ============================================================
    // ВСПОМОГАТЕЛЬНЫЕ МЕТОДЫ ДЛЯ СЕРТИФИКАТА
    // ============================================================
    private function drawBorder($img, $x1, $y1, $x2, $y2, $color, $width)
    {
        for ($i = 0; $i < $width; $i++) {
            $img->rectangle($x1, $y1 + $i, $x2, $y1 + $i + 1, function ($draw) use ($color) {
                $draw->background($color);
            });
        }
        for ($i = 0; $i < $width; $i++) {
            $img->rectangle($x1, $y2 - $i - 1, $x2, $y2 - $i, function ($draw) use ($color) {
                $draw->background($color);
            });
        }
        for ($i = 0; $i < $width; $i++) {
            $img->rectangle($x1 + $i, $y1, $x1 + $i + 1, $y2, function ($draw) use ($color) {
                $draw->background($color);
            });
        }
        for ($i = 0; $i < $width; $i++) {
            $img->rectangle($x2 - $i - 1, $y1, $x2 - $i, $y2, function ($draw) use ($color) {
                $draw->background($color);
            });
        }
    }

    private function drawCornerDecoration($img, $x, $y, $color, $position = 'top-left')
    {
        $size = 25;
        $lineWidth = 3;
        
        if ($position == 'top-left') {
            for ($i = 0; $i < $lineWidth; $i++) {
                $img->rectangle($x, $y + $i, $x + $size, $y + $i + 1, function ($draw) use ($color) {
                    $draw->background($color);
                });
            }
            for ($i = 0; $i < $lineWidth; $i++) {
                $img->rectangle($x + $i, $y, $x + $i + 1, $y + $size, function ($draw) use ($color) {
                    $draw->background($color);
                });
            }
        } elseif ($position == 'top-right') {
            for ($i = 0; $i < $lineWidth; $i++) {
                $img->rectangle($x - $size, $y + $i, $x, $y + $i + 1, function ($draw) use ($color) {
                    $draw->background($color);
                });
            }
            for ($i = 0; $i < $lineWidth; $i++) {
                $img->rectangle($x - $i - 1, $y, $x - $i, $y + $size, function ($draw) use ($color) {
                    $draw->background($color);
                });
            }
        } elseif ($position == 'bottom-left') {
            for ($i = 0; $i < $lineWidth; $i++) {
                $img->rectangle($x, $y - $i - 1, $x + $size, $y - $i, function ($draw) use ($color) {
                    $draw->background($color);
                });
            }
            for ($i = 0; $i < $lineWidth; $i++) {
                $img->rectangle($x + $i, $y - $size, $x + $i + 1, $y, function ($draw) use ($color) {
                    $draw->background($color);
                });
            }
        } else {
            for ($i = 0; $i < $lineWidth; $i++) {
                $img->rectangle($x - $size, $y - $i - 1, $x, $y - $i, function ($draw) use ($color) {
                    $draw->background($color);
                });
            }
            for ($i = 0; $i < $lineWidth; $i++) {
                $img->rectangle($x - $i - 1, $y - $size, $x - $i, $y, function ($draw) use ($color) {
                    $draw->background($color);
                });
            }
        }
    }

    // ============================================================
    // ОСНОВНОЙ МЕТОД ПОЛУЧЕНИЯ ВОПРОСОВ
    // ============================================================
    private function getQuestions($subject)
    {
        $locale = App::getLocale();
        
        switch($subject) {
            case 'Html':
                return $this->getHtmlQuestions($locale);
            case 'Css':
                return $this->getCssQuestions($locale);
            case 'Sql':
                return $this->getSqlQuestions($locale);
            case 'Bootstrap':
                return $this->getBootstrapQuestions($locale);
            case 'JavaScript':
                return $this->getJavaScriptQuestions($locale);
            case 'Laravel':
                return $this->getLaravelQuestions($locale);
            default:
                return $this->getHtmlQuestions($locale);
        }
    }

    private function getCorrectAnswers($subject)
    {
        $questions = $this->getQuestions($subject);
        shuffle($questions);
        $answers = [];
        foreach ($questions as $question) {
            $answers[] = $question['correct'];
        }
        return $answers;
    }

    // ============================================================
    // HTML ВОПРОСЫ (30)
    // ============================================================
    private function getHtmlQuestions($locale)
    {
        if ($locale == 'uz') {
            return $this->getHtmlQuestionsUz();
        } elseif ($locale == 'ru') {
            return $this->getHtmlQuestionsRu();
        } else {
            return $this->getHtmlQuestionsEn();
        }
    }

    private function getHtmlQuestionsUz()
    {
        return [
            ['id' => 1, 'question' => 'HTML nimani anglatadi?', 'options' => ['Hyper Text Markup Language', 'High Tech Modern Language', 'Hyper Transfer Markup Language', 'Home Tool Markup Language'], 'correct' => 0],
            ['id' => 2, 'question' => 'Eng yuqori darajadagi sarlavha yaratish uchun qaysi teg ishlatiladi?', 'options' => ['<head>', '<h1>', '<h6>', '<header>'], 'correct' => 1],
            ['id' => 3, 'question' => 'Havola yaratish uchun qaysi teg ishlatiladi?', 'options' => ['<link>', '<a>', '<href>', '<url>'], 'correct' => 1],
            ['id' => 4, 'question' => 'Rasm qo\'shish uchun qaysi teg ishlatiladi?', 'options' => ['<img>', '<image>', '<pic>', '<src>'], 'correct' => 0],
            ['id' => 5, 'question' => 'Havola manzilini ko\'rsatish uchun qaysi atribut ishlatiladi?', 'options' => ['src', 'href', 'link', 'url'], 'correct' => 1],
            ['id' => 6, 'question' => 'Ro\'yxat yaratish uchun qaysi teg ishlatiladi?', 'options' => ['<list>', '<ul>', '<ol>', 'Ikkala <ul> va <ol>'], 'correct' => 3],
            ['id' => 7, 'question' => 'HTML da div nima?', 'options' => ['Guruhlash uchun blok element', 'Qator element', 'Jadval', 'Forma'], 'correct' => 0],
            ['id' => 8, 'question' => 'Jadval yaratish uchun qaysi teg ishlatiladi?', 'options' => ['<table>', '<tab>', '<tbl>', '<grid>'], 'correct' => 0],
            ['id' => 9, 'question' => 'Forma yaratish uchun qaysi teg ishlatiladi?', 'options' => ['<form>', '<input>', '<field>', '<submit>'], 'correct' => 0],
            ['id' => 10, 'question' => 'Rasmga yo\'lni ko\'rsatish uchun qaysi atribut ishlatiladi?', 'options' => ['href', 'src', 'alt', 'link'], 'correct' => 1],
            ['id' => 11, 'question' => 'HTML da qaysi teg eng katta matnni yaratadi?', 'options' => ['<h1>', '<h6>', '<p>', '<big>'], 'correct' => 0],
            ['id' => 12, 'question' => 'HTML da paragraf yaratish uchun qaysi teg ishlatiladi?', 'options' => ['<p>', '<text>', '<paragraph>', '<par>'], 'correct' => 0],
            ['id' => 13, 'question' => 'HTML da yozuvni qalin qilish uchun qaysi teg ishlatiladi?', 'options' => ['<b>', '<strong>', 'Ikkalasi ham', 'Biri ham emas'], 'correct' => 2],
            ['id' => 14, 'question' => 'HTML da yozuvni kursiv qilish uchun qaysi teg ishlatiladi?', 'options' => ['<i>', '<em>', 'Ikkalasi ham', 'Biri ham emas'], 'correct' => 2],
            ['id' => 15, 'question' => 'HTML da yangi qatordan boshlash uchun qaysi teg ishlatiladi?', 'options' => ['<br>', '<newline>', '<line>', '<enter>'], 'correct' => 0],
            ['id' => 16, 'question' => 'HTML da gorizontal chiziq chizish uchun qaysi teg ishlatiladi?', 'options' => ['<hr>', '<line>', '<hline>', '<hr> va <line>'], 'correct' => 0],
            ['id' => 17, 'question' => 'HTML da izoh qoldirish uchun qanday sintaksis ishlatiladi?', 'options' => ['<!-- -->', '/* */', '//', '#'], 'correct' => 0],
            ['id' => 18, 'question' => 'HTML da qaysi teg pastki chiziqli matn yaratadi?', 'options' => ['<u>', '<underline>', '<ins>', 'Barchasi'], 'correct' => 3],
            ['id' => 19, 'question' => 'HTML da qaysi teg o\'chirilgan matnni ko\'rsatadi?', 'options' => ['<del>', '<strike>', '<s>', 'Barchasi'], 'correct' => 3],
            ['id' => 20, 'question' => 'HTML da qaysi teg qavat orasidagi bo\'shliqni yaratadi?', 'options' => ['<br>', '<p>', '<div>', 'Barchasi'], 'correct' => 2],
            ['id' => 21, 'question' => 'HTML da qaysi atribut elementga unikal nom beradi?', 'options' => ['id', 'class', 'name', 'title'], 'correct' => 0],
            ['id' => 22, 'question' => 'HTML da qaysi atribut elementga klass beradi?', 'options' => ['id', 'class', 'name', 'type'], 'correct' => 1],
            ['id' => 23, 'question' => 'HTML da qaysi teg ro\'yxat elementlarini yaratadi?', 'options' => ['<li>', '<item>', '<list>', '<el>'], 'correct' => 0],
            ['id' => 24, 'question' => 'HTML da qaysi teg hujjat turini aniqlaydi?', 'options' => ['<!DOCTYPE>', '<html>', '<head>', '<body>'], 'correct' => 0],
            ['id' => 25, 'question' => 'HTML da qaysi teg hujjatning boshlang\'ich qismini ko\'rsatadi?', 'options' => ['<html>', '<head>', '<body>', '<title>'], 'correct' => 0],
            ['id' => 26, 'question' => 'HTML da qaysi teg hujjat sarlavhasini ko\'rsatadi?', 'options' => ['<title>', '<head>', '<h1>', '<header>'], 'correct' => 0],
            ['id' => 27, 'question' => 'HTML da qaysi teg video qo\'shadi?', 'options' => ['<video>', '<media>', '<movie>', '<player>'], 'correct' => 0],
            ['id' => 28, 'question' => 'HTML da qaysi teg audio qo\'shadi?', 'options' => ['<audio>', '<sound>', '<music>', '<player>'], 'correct' => 0],
            ['id' => 29, 'question' => 'HTML da qaysi teg iframe (ramka) qo\'shadi?', 'options' => ['<iframe>', '<frame>', '<window>', '<embed>'], 'correct' => 0],
            ['id' => 30, 'question' => 'HTML da qaysi teg navbatlanmagan ro\'yxat yaratadi?', 'options' => ['<ul>', '<ol>', '<li>', '<list>'], 'correct' => 0],
        ];
    }

    private function getHtmlQuestionsRu()
    {
        return [
            ['id' => 1, 'question' => 'Что означает HTML?', 'options' => ['Hyper Text Markup Language', 'High Tech Modern Language', 'Hyper Transfer Markup Language', 'Home Tool Markup Language'], 'correct' => 0],
            ['id' => 2, 'question' => 'Какой тег используется для создания заголовка самого высокого уровня?', 'options' => ['<head>', '<h1>', '<h6>', '<header>'], 'correct' => 1],
            ['id' => 3, 'question' => 'Какой тег используется для создания ссылки?', 'options' => ['<link>', '<a>', '<href>', '<url>'], 'correct' => 1],
            ['id' => 4, 'question' => 'Какой тег используется для вставки изображения?', 'options' => ['<img>', '<image>', '<pic>', '<src>'], 'correct' => 0],
            ['id' => 5, 'question' => 'Какой атрибут используется для указания адреса ссылки?', 'options' => ['src', 'href', 'link', 'url'], 'correct' => 1],
            ['id' => 6, 'question' => 'Какой тег используется для создания списка?', 'options' => ['<list>', '<ul>', '<ol>', 'И <ul> и <ol>'], 'correct' => 3],
            ['id' => 7, 'question' => 'Что такое div в HTML?', 'options' => ['Блочный элемент для группировки', 'Строчный элемент', 'Таблица', 'Форма'], 'correct' => 0],
            ['id' => 8, 'question' => 'Какой тег используется для создания таблицы?', 'options' => ['<table>', '<tab>', '<tbl>', '<grid>'], 'correct' => 0],
            ['id' => 9, 'question' => 'Какой тег используется для создания формы?', 'options' => ['<form>', '<input>', '<field>', '<submit>'], 'correct' => 0],
            ['id' => 10, 'question' => 'Какой атрибут используется для указания пути к изображению?', 'options' => ['href', 'src', 'alt', 'link'], 'correct' => 1],
            ['id' => 11, 'question' => 'Какой тег создает самый большой текст в HTML?', 'options' => ['<h1>', '<h6>', '<p>', '<big>'], 'correct' => 0],
            ['id' => 12, 'question' => 'Какой тег используется для создания параграфа в HTML?', 'options' => ['<p>', '<text>', '<paragraph>', '<par>'], 'correct' => 0],
            ['id' => 13, 'question' => 'Какой тег используется для жирного текста в HTML?', 'options' => ['<b>', '<strong>', 'Оба', 'Ни один'], 'correct' => 2],
            ['id' => 14, 'question' => 'Какой тег используется для курсивного текста в HTML?', 'options' => ['<i>', '<em>', 'Оба', 'Ни один'], 'correct' => 2],
            ['id' => 15, 'question' => 'Какой тег используется для переноса строки в HTML?', 'options' => ['<br>', '<newline>', '<line>', '<enter>'], 'correct' => 0],
            ['id' => 16, 'question' => 'Какой тег используется для горизонтальной линии в HTML?', 'options' => ['<hr>', '<line>', '<hline>', '<hr> и <line>'], 'correct' => 0],
            ['id' => 17, 'question' => 'Какой синтаксис используется для комментариев в HTML?', 'options' => ['<!-- -->', '/* */', '//', '#'], 'correct' => 0],
            ['id' => 18, 'question' => 'Какой тег создает подчеркнутый текст в HTML?', 'options' => ['<u>', '<underline>', '<ins>', 'Все'], 'correct' => 3],
            ['id' => 19, 'question' => 'Какой тег показывает зачеркнутый текст в HTML?', 'options' => ['<del>', '<strike>', '<s>', 'Все'], 'correct' => 3],
            ['id' => 20, 'question' => 'Какой тег создает отступ между блоками в HTML?', 'options' => ['<br>', '<p>', '<div>', 'Все'], 'correct' => 2],
            ['id' => 21, 'question' => 'Какой атрибут дает элементу уникальное имя в HTML?', 'options' => ['id', 'class', 'name', 'title'], 'correct' => 0],
            ['id' => 22, 'question' => 'Какой атрибут дает элементу класс в HTML?', 'options' => ['id', 'class', 'name', 'type'], 'correct' => 1],
            ['id' => 23, 'question' => 'Какой тег создает элементы списка в HTML?', 'options' => ['<li>', '<item>', '<list>', '<el>'], 'correct' => 0],
            ['id' => 24, 'question' => 'Какой тег определяет тип документа в HTML?', 'options' => ['<!DOCTYPE>', '<html>', '<head>', '<body>'], 'correct' => 0],
            ['id' => 25, 'question' => 'Какой тег показывает начало HTML документа?', 'options' => ['<html>', '<head>', '<body>', '<title>'], 'correct' => 0],
            ['id' => 26, 'question' => 'Какой тег показывает заголовок документа в HTML?', 'options' => ['<title>', '<head>', '<h1>', '<header>'], 'correct' => 0],
            ['id' => 27, 'question' => 'Какой тег добавляет видео в HTML?', 'options' => ['<video>', '<media>', '<movie>', '<player>'], 'correct' => 0],
            ['id' => 28, 'question' => 'Какой тег добавляет аудио в HTML?', 'options' => ['<audio>', '<sound>', '<music>', '<player>'], 'correct' => 0],
            ['id' => 29, 'question' => 'Какой тег добавляет iframe в HTML?', 'options' => ['<iframe>', '<frame>', '<window>', '<embed>'], 'correct' => 0],
            ['id' => 30, 'question' => 'Какой тег создает неупорядоченный список в HTML?', 'options' => ['<ul>', '<ol>', '<li>', '<list>'], 'correct' => 0],
        ];
    }

    private function getHtmlQuestionsEn()
    {
        return [
            ['id' => 1, 'question' => 'What does HTML stand for?', 'options' => ['Hyper Text Markup Language', 'High Tech Modern Language', 'Hyper Transfer Markup Language', 'Home Tool Markup Language'], 'correct' => 0],
            ['id' => 2, 'question' => 'Which tag creates the highest level heading?', 'options' => ['<head>', '<h1>', '<h6>', '<header>'], 'correct' => 1],
            ['id' => 3, 'question' => 'Which tag creates a link?', 'options' => ['<link>', '<a>', '<href>', '<url>'], 'correct' => 1],
            ['id' => 4, 'question' => 'Which tag inserts an image?', 'options' => ['<img>', '<image>', '<pic>', '<src>'], 'correct' => 0],
            ['id' => 5, 'question' => 'Which attribute specifies the link address?', 'options' => ['src', 'href', 'link', 'url'], 'correct' => 1],
            ['id' => 6, 'question' => 'Which tag creates a list?', 'options' => ['<list>', '<ul>', '<ol>', 'Both <ul> and <ol>'], 'correct' => 3],
            ['id' => 7, 'question' => 'What is div in HTML?', 'options' => ['Block element for grouping', 'Inline element', 'Table', 'Form'], 'correct' => 0],
            ['id' => 8, 'question' => 'Which tag creates a table?', 'options' => ['<table>', '<tab>', '<tbl>', '<grid>'], 'correct' => 0],
            ['id' => 9, 'question' => 'Which tag creates a form?', 'options' => ['<form>', '<input>', '<field>', '<submit>'], 'correct' => 0],
            ['id' => 10, 'question' => 'Which attribute specifies the image path?', 'options' => ['href', 'src', 'alt', 'link'], 'correct' => 1],
            ['id' => 11, 'question' => 'Which tag creates the largest text in HTML?', 'options' => ['<h1>', '<h6>', '<p>', '<big>'], 'correct' => 0],
            ['id' => 12, 'question' => 'Which tag creates a paragraph in HTML?', 'options' => ['<p>', '<text>', '<paragraph>', '<par>'], 'correct' => 0],
            ['id' => 13, 'question' => 'Which tag makes text bold in HTML?', 'options' => ['<b>', '<strong>', 'Both', 'None'], 'correct' => 2],
            ['id' => 14, 'question' => 'Which tag makes text italic in HTML?', 'options' => ['<i>', '<em>', 'Both', 'None'], 'correct' => 2],
            ['id' => 15, 'question' => 'Which tag creates a line break in HTML?', 'options' => ['<br>', '<newline>', '<line>', '<enter>'], 'correct' => 0],
            ['id' => 16, 'question' => 'Which tag creates a horizontal line in HTML?', 'options' => ['<hr>', '<line>', '<hline>', '<hr> and <line>'], 'correct' => 0],
            ['id' => 17, 'question' => 'Which syntax is used for comments in HTML?', 'options' => ['<!-- -->', '/* */', '//', '#'], 'correct' => 0],
            ['id' => 18, 'question' => 'Which tag creates underlined text in HTML?', 'options' => ['<u>', '<underline>', '<ins>', 'All'], 'correct' => 3],
            ['id' => 19, 'question' => 'Which tag shows strikethrough text in HTML?', 'options' => ['<del>', '<strike>', '<s>', 'All'], 'correct' => 3],
            ['id' => 20, 'question' => 'Which tag creates spacing between blocks in HTML?', 'options' => ['<br>', '<p>', '<div>', 'All'], 'correct' => 2],
            ['id' => 21, 'question' => 'Which attribute gives an element a unique name in HTML?', 'options' => ['id', 'class', 'name', 'title'], 'correct' => 0],
            ['id' => 22, 'question' => 'Which attribute gives an element a class in HTML?', 'options' => ['id', 'class', 'name', 'type'], 'correct' => 1],
            ['id' => 23, 'question' => 'Which tag creates list items in HTML?', 'options' => ['<li>', '<item>', '<list>', '<el>'], 'correct' => 0],
            ['id' => 24, 'question' => 'Which tag defines the document type in HTML?', 'options' => ['<!DOCTYPE>', '<html>', '<head>', '<body>'], 'correct' => 0],
            ['id' => 25, 'question' => 'Which tag shows the start of HTML document?', 'options' => ['<html>', '<head>', '<body>', '<title>'], 'correct' => 0],
            ['id' => 26, 'question' => 'Which tag shows the document title in HTML?', 'options' => ['<title>', '<head>', '<h1>', '<header>'], 'correct' => 0],
            ['id' => 27, 'question' => 'Which tag adds video in HTML?', 'options' => ['<video>', '<media>', '<movie>', '<player>'], 'correct' => 0],
            ['id' => 28, 'question' => 'Which tag adds audio in HTML?', 'options' => ['<audio>', '<sound>', '<music>', '<player>'], 'correct' => 0],
            ['id' => 29, 'question' => 'Which tag adds iframe in HTML?', 'options' => ['<iframe>', '<frame>', '<window>', '<embed>'], 'correct' => 0],
            ['id' => 30, 'question' => 'Which tag creates an unordered list in HTML?', 'options' => ['<ul>', '<ol>', '<li>', '<list>'], 'correct' => 0],
        ];
    }

    // ============================================================
    // CSS ВОПРОСЫ (30)
    // ============================================================
    private function getCssQuestions($locale)
    {
        if ($locale == 'uz') {
            return $this->getCssQuestionsUz();
        } elseif ($locale == 'ru') {
            return $this->getCssQuestionsRu();
        } else {
            return $this->getCssQuestionsEn();
        }
    }

    private function getCssQuestionsUz()
    {
        return [
            ['id' => 1, 'question' => 'CSS nimani anglatadi?', 'options' => ['Cascading Style Sheets', 'Creative Style Sheets', 'Computer Style Sheets', 'Colorful Style Sheets'], 'correct' => 0],
            ['id' => 2, 'question' => 'CSS da to\'g\'ri sintaksis qaysi?', 'options' => ['body:color=black;', 'body {color: black;}', '{body:color=black;}', 'body {color=black;}'], 'correct' => 1],
            ['id' => 3, 'question' => 'CSS da matn rangini qanday o\'zgartirish mumkin?', 'options' => ['color:', 'text-color:', 'font-color:', 'text:'], 'correct' => 0],
            ['id' => 4, 'question' => 'CSS da shrift o\'lchamini qanday o\'zgartirish mumkin?', 'options' => ['font-size:', 'text-size:', 'size:', 'font:'], 'correct' => 0],
            ['id' => 5, 'question' => 'CSS da matnni qalin qilish uchun qaysi xususiyat ishlatiladi?', 'options' => ['font-weight: bold;', 'text-weight: bold;', 'bold: true;', 'font: bold;'], 'correct' => 0],
            ['id' => 6, 'question' => 'CSS da fon rangini o\'zgartirish uchun qaysi xususiyat ishlatiladi?', 'options' => ['background-color:', 'bg-color:', 'color:', 'background:'], 'correct' => 0],
            ['id' => 7, 'question' => 'CSS da elementni markazga joylashtirish uchun qaysi xususiyat ishlatiladi?', 'options' => ['margin: auto;', 'text-align: center;', 'Ikkalasi ham', 'Biri ham emas'], 'correct' => 2],
            ['id' => 8, 'question' => 'CSS da qaysi xususiyat matnni o\'ngga tekislaydi?', 'options' => ['text-align: right;', 'align: right;', 'float: right;', 'position: right;'], 'correct' => 0],
            ['id' => 9, 'question' => 'CSS da qaysi xususiyat chegara (border) yaratadi?', 'options' => ['border:', 'outline:', 'frame:', 'edge:'], 'correct' => 0],
            ['id' => 10, 'question' => 'CSS da qaysi xususiyat soya (shadow) qo\'shadi?', 'options' => ['box-shadow:', 'text-shadow:', 'Ikkalasi ham', 'Biri ham emas'], 'correct' => 2],
            ['id' => 11, 'question' => 'CSS da qaysi xususiyat elementlarni yashirish uchun ishlatiladi?', 'options' => ['display: none;', 'visibility: hidden;', 'Ikkalasi ham', 'Biri ham emas'], 'correct' => 2],
            ['id' => 12, 'question' => 'CSS da qaysi xususiyat elementning kengligini o\'zgartirish uchun ishlatiladi?', 'options' => ['width:', 'height:', 'size:', 'dimension:'], 'correct' => 0],
            ['id' => 13, 'question' => 'CSS da qaysi xususiyat elementning balandligini o\'zgartirish uchun ishlatiladi?', 'options' => ['height:', 'width:', 'size:', 'dimension:'], 'correct' => 0],
            ['id' => 14, 'question' => 'CSS da qaysi xususiyat matn orasidagi masofani o\'zgartirish uchun ishlatiladi?', 'options' => ['letter-spacing:', 'word-spacing:', 'Ikkalasi ham', 'Biri ham emas'], 'correct' => 2],
            ['id' => 15, 'question' => 'CSS da qaysi xususiyat chiziq ostida matn yaratish uchun ishlatiladi?', 'options' => ['text-decoration: underline;', 'underline: true;', 'text-underline: yes;', 'decoration: underline;'], 'correct' => 0],
            ['id' => 16, 'question' => 'CSS da qaysi xususiyat elementni aylantirish uchun ishlatiladi?', 'options' => ['transform: rotate();', 'rotate: ;', 'spin: ;', 'turn: ;'], 'correct' => 0],
            ['id' => 17, 'question' => 'CSS da qaysi xususiyat elementning shaffofligini o\'zgartirish uchun ishlatiladi?', 'options' => ['opacity:', 'transparency:', 'alpha:', 'visible:'], 'correct' => 0],
            ['id' => 18, 'question' => 'CSS da qaysi xususiyat elementni suzuvchi qilish uchun ishlatiladi?', 'options' => ['float:', 'position:', 'display:', 'align:'], 'correct' => 0],
            ['id' => 19, 'question' => 'CSS da qaysi xususiyat elementning pozitsiyasini belgilash uchun ishlatiladi?', 'options' => ['position:', 'location:', 'place:', 'set:'], 'correct' => 0],
            ['id' => 20, 'question' => 'CSS da qaysi xususiyat elementning pastki qismidan masofani belgilaydi?', 'options' => ['bottom:', 'margin-bottom:', 'padding-bottom:', 'Ikkalasi ham'], 'correct' => 3],
            ['id' => 21, 'question' => 'CSS da qaysi xususiyat elementning o\'ng qismidan masofani belgilaydi?', 'options' => ['right:', 'margin-right:', 'padding-right:', 'Ikkalasi ham'], 'correct' => 3],
            ['id' => 22, 'question' => 'CSS da qaysi xususiyat elementning chap qismidan masofani belgilaydi?', 'options' => ['left:', 'margin-left:', 'padding-left:', 'Ikkalasi ham'], 'correct' => 3],
            ['id' => 23, 'question' => 'CSS da qaysi xususiyat elementning yuqori qismidan masofani belgilaydi?', 'options' => ['top:', 'margin-top:', 'padding-top:', 'Ikkalasi ham'], 'correct' => 3],
            ['id' => 24, 'question' => 'CSS da qaysi xususiyat matnni katta harflar bilan yozish uchun ishlatiladi?', 'options' => ['text-transform: uppercase;', 'uppercase: ;', 'transform: uppercase;', 'case: upper;'], 'correct' => 0],
            ['id' => 25, 'question' => 'CSS da qaysi xususiyat fon rasmini o\'rnatish uchun ishlatiladi?', 'options' => ['background-image:', 'image:', 'bg-image:', 'background:'], 'correct' => 0],
            ['id' => 26, 'question' => 'CSS da qaysi xususiyat elementlarni qatorlab joylashtirish uchun ishlatiladi?', 'options' => ['display: flex;', 'display: grid;', 'Ikkalasi ham', 'Biri ham emas'], 'correct' => 2],
            ['id' => 27, 'question' => 'CSS da qaysi xususiyat animatsiya yaratish uchun ishlatiladi?', 'options' => ['animation:', 'transition:', 'Ikkalasi ham', 'Biri ham emas'], 'correct' => 2],
            ['id' => 28, 'question' => 'CSS da qaysi xususiyat burchaklarni yumaloq qilish uchun ishlatiladi?', 'options' => ['border-radius:', 'round:', 'curve:', 'radius:'], 'correct' => 0],
            ['id' => 29, 'question' => 'CSS da qaysi xususiyat matn yo\'nalishini o\'zgartirish uchun ishlatiladi?', 'options' => ['direction:', 'text-direction:', 'dir:', 'align:'], 'correct' => 0],
            ['id' => 30, 'question' => 'CSS da qaysi xususiyat elementning ko\'rinishini o\'zgartirish uchun ishlatiladi?', 'options' => ['visibility:', 'display:', 'Ikkalasi ham', 'Biri ham emas'], 'correct' => 2],
        ];
    }

    private function getCssQuestionsRu()
    {
        return [
            ['id' => 1, 'question' => 'Что означает CSS?', 'options' => ['Cascading Style Sheets', 'Creative Style Sheets', 'Computer Style Sheets', 'Colorful Style Sheets'], 'correct' => 0],
            ['id' => 2, 'question' => 'Какой правильный синтаксис CSS?', 'options' => ['body:color=black;', 'body {color: black;}', '{body:color=black;}', 'body {color=black;}'], 'correct' => 1],
            ['id' => 3, 'question' => 'Как изменить цвет текста в CSS?', 'options' => ['color:', 'text-color:', 'font-color:', 'text:'], 'correct' => 0],
            ['id' => 4, 'question' => 'Как изменить размер шрифта в CSS?', 'options' => ['font-size:', 'text-size:', 'size:', 'font:'], 'correct' => 0],
            ['id' => 5, 'question' => 'Как сделать текст жирным в CSS?', 'options' => ['font-weight: bold;', 'text-weight: bold;', 'bold: true;', 'font: bold;'], 'correct' => 0],
            ['id' => 6, 'question' => 'Как изменить цвет фона в CSS?', 'options' => ['background-color:', 'bg-color:', 'color:', 'background:'], 'correct' => 0],
            ['id' => 7, 'question' => 'Как центрировать элемент в CSS?', 'options' => ['margin: auto;', 'text-align: center;', 'Оба', 'Ни один'], 'correct' => 2],
            ['id' => 8, 'question' => 'Какое свойство выравнивает текст вправо в CSS?', 'options' => ['text-align: right;', 'align: right;', 'float: right;', 'position: right;'], 'correct' => 0],
            ['id' => 9, 'question' => 'Какое свойство создает границу в CSS?', 'options' => ['border:', 'outline:', 'frame:', 'edge:'], 'correct' => 0],
            ['id' => 10, 'question' => 'Какое свойство добавляет тень в CSS?', 'options' => ['box-shadow:', 'text-shadow:', 'Оба', 'Ни один'], 'correct' => 2],
            ['id' => 11, 'question' => 'Какое свойство скрывает элементы в CSS?', 'options' => ['display: none;', 'visibility: hidden;', 'Оба', 'Ни один'], 'correct' => 2],
            ['id' => 12, 'question' => 'Какое свойство изменяет ширину элемента в CSS?', 'options' => ['width:', 'height:', 'size:', 'dimension:'], 'correct' => 0],
            ['id' => 13, 'question' => 'Какое свойство изменяет высоту элемента в CSS?', 'options' => ['height:', 'width:', 'size:', 'dimension:'], 'correct' => 0],
            ['id' => 14, 'question' => 'Какое свойство изменяет межбуквенный интервал в CSS?', 'options' => ['letter-spacing:', 'word-spacing:', 'Оба', 'Ни один'], 'correct' => 2],
            ['id' => 15, 'question' => 'Какое свойство подчеркивает текст в CSS?', 'options' => ['text-decoration: underline;', 'underline: true;', 'text-underline: yes;', 'decoration: underline;'], 'correct' => 0],
            ['id' => 16, 'question' => 'Какое свойство вращает элемент в CSS?', 'options' => ['transform: rotate();', 'rotate: ;', 'spin: ;', 'turn: ;'], 'correct' => 0],
            ['id' => 17, 'question' => 'Какое свойство изменяет прозрачность в CSS?', 'options' => ['opacity:', 'transparency:', 'alpha:', 'visible:'], 'correct' => 0],
            ['id' => 18, 'question' => 'Какое свойство делает элемент плавающим в CSS?', 'options' => ['float:', 'position:', 'display:', 'align:'], 'correct' => 0],
            ['id' => 19, 'question' => 'Какое свойство устанавливает позицию элемента в CSS?', 'options' => ['position:', 'location:', 'place:', 'set:'], 'correct' => 0],
            ['id' => 20, 'question' => 'Какое свойство устанавливает отступ снизу в CSS?', 'options' => ['bottom:', 'margin-bottom:', 'padding-bottom:', 'Оба'], 'correct' => 3],
            ['id' => 21, 'question' => 'Какое свойство устанавливает отступ справа в CSS?', 'options' => ['right:', 'margin-right:', 'padding-right:', 'Оба'], 'correct' => 3],
            ['id' => 22, 'question' => 'Какое свойство устанавливает отступ слева в CSS?', 'options' => ['left:', 'margin-left:', 'padding-left:', 'Оба'], 'correct' => 3],
            ['id' => 23, 'question' => 'Какое свойство устанавливает отступ сверху в CSS?', 'options' => ['top:', 'margin-top:', 'padding-top:', 'Оба'], 'correct' => 3],
            ['id' => 24, 'question' => 'Какое свойство делает текст заглавными буквами в CSS?', 'options' => ['text-transform: uppercase;', 'uppercase: ;', 'transform: uppercase;', 'case: upper;'], 'correct' => 0],
            ['id' => 25, 'question' => 'Какое свойство устанавливает фоновое изображение в CSS?', 'options' => ['background-image:', 'image:', 'bg-image:', 'background:'], 'correct' => 0],
            ['id' => 26, 'question' => 'Какое свойство создает flex/grid макет в CSS?', 'options' => ['display: flex;', 'display: grid;', 'Оба', 'Ни один'], 'correct' => 2],
            ['id' => 27, 'question' => 'Какое свойство создает анимацию в CSS?', 'options' => ['animation:', 'transition:', 'Оба', 'Ни один'], 'correct' => 2],
            ['id' => 28, 'question' => 'Какое свойство скругляет углы в CSS?', 'options' => ['border-radius:', 'round:', 'curve:', 'radius:'], 'correct' => 0],
            ['id' => 29, 'question' => 'Какое свойство изменяет направление текста в CSS?', 'options' => ['direction:', 'text-direction:', 'dir:', 'align:'], 'correct' => 0],
            ['id' => 30, 'question' => 'Какое свойство изменяет видимость элемента в CSS?', 'options' => ['visibility:', 'display:', 'Оба', 'Ни один'], 'correct' => 2],
        ];
    }

    private function getCssQuestionsEn()
    {
        return [
            ['id' => 1, 'question' => 'What does CSS stand for?', 'options' => ['Cascading Style Sheets', 'Creative Style Sheets', 'Computer Style Sheets', 'Colorful Style Sheets'], 'correct' => 0],
            ['id' => 2, 'question' => 'Which is the correct CSS syntax?', 'options' => ['body:color=black;', 'body {color: black;}', '{body:color=black;}', 'body {color=black;}'], 'correct' => 1],
            ['id' => 3, 'question' => 'How to change text color in CSS?', 'options' => ['color:', 'text-color:', 'font-color:', 'text:'], 'correct' => 0],
            ['id' => 4, 'question' => 'How to change font size in CSS?', 'options' => ['font-size:', 'text-size:', 'size:', 'font:'], 'correct' => 0],
            ['id' => 5, 'question' => 'How to make text bold in CSS?', 'options' => ['font-weight: bold;', 'text-weight: bold;', 'bold: true;', 'font: bold;'], 'correct' => 0],
            ['id' => 6, 'question' => 'How to change background color in CSS?', 'options' => ['background-color:', 'bg-color:', 'color:', 'background:'], 'correct' => 0],
            ['id' => 7, 'question' => 'How to center an element in CSS?', 'options' => ['margin: auto;', 'text-align: center;', 'Both', 'None'], 'correct' => 2],
            ['id' => 8, 'question' => 'Which property aligns text to the right?', 'options' => ['text-align: right;', 'align: right;', 'float: right;', 'position: right;'], 'correct' => 0],
            ['id' => 9, 'question' => 'Which property creates a border in CSS?', 'options' => ['border:', 'outline:', 'frame:', 'edge:'], 'correct' => 0],
            ['id' => 10, 'question' => 'Which property adds shadow in CSS?', 'options' => ['box-shadow:', 'text-shadow:', 'Both', 'None'], 'correct' => 2],
            ['id' => 11, 'question' => 'Which property hides elements in CSS?', 'options' => ['display: none;', 'visibility: hidden;', 'Both', 'None'], 'correct' => 2],
            ['id' => 12, 'question' => 'Which property changes element width?', 'options' => ['width:', 'height:', 'size:', 'dimension:'], 'correct' => 0],
            ['id' => 13, 'question' => 'Which property changes element height?', 'options' => ['height:', 'width:', 'size:', 'dimension:'], 'correct' => 0],
            ['id' => 14, 'question' => 'Which property changes letter spacing?', 'options' => ['letter-spacing:', 'word-spacing:', 'Both', 'None'], 'correct' => 2],
            ['id' => 15, 'question' => 'Which property underlines text?', 'options' => ['text-decoration: underline;', 'underline: true;', 'text-underline: yes;', 'decoration: underline;'], 'correct' => 0],
            ['id' => 16, 'question' => 'Which property rotates an element?', 'options' => ['transform: rotate();', 'rotate: ;', 'spin: ;', 'turn: ;'], 'correct' => 0],
            ['id' => 17, 'question' => 'Which property changes opacity?', 'options' => ['opacity:', 'transparency:', 'alpha:', 'visible:'], 'correct' => 0],
            ['id' => 18, 'question' => 'Which property makes an element float?', 'options' => ['float:', 'position:', 'display:', 'align:'], 'correct' => 0],
            ['id' => 19, 'question' => 'Which property sets position?', 'options' => ['position:', 'location:', 'place:', 'set:'], 'correct' => 0],
            ['id' => 20, 'question' => 'Which property sets bottom spacing?', 'options' => ['bottom:', 'margin-bottom:', 'padding-bottom:', 'Both'], 'correct' => 3],
            ['id' => 21, 'question' => 'Which property sets right spacing?', 'options' => ['right:', 'margin-right:', 'padding-right:', 'Both'], 'correct' => 3],
            ['id' => 22, 'question' => 'Which property sets left spacing?', 'options' => ['left:', 'margin-left:', 'padding-left:', 'Both'], 'correct' => 3],
            ['id' => 23, 'question' => 'Which property sets top spacing?', 'options' => ['top:', 'margin-top:', 'padding-top:', 'Both'], 'correct' => 3],
            ['id' => 24, 'question' => 'Which property makes text uppercase?', 'options' => ['text-transform: uppercase;', 'uppercase: ;', 'transform: uppercase;', 'case: upper;'], 'correct' => 0],
            ['id' => 25, 'question' => 'Which property sets background image?', 'options' => ['background-image:', 'image:', 'bg-image:', 'background:'], 'correct' => 0],
            ['id' => 26, 'question' => 'Which property creates flex/grid layout?', 'options' => ['display: flex;', 'display: grid;', 'Both', 'None'], 'correct' => 2],
            ['id' => 27, 'question' => 'Which property creates animation?', 'options' => ['animation:', 'transition:', 'Both', 'None'], 'correct' => 2],
            ['id' => 28, 'question' => 'Which property rounds corners?', 'options' => ['border-radius:', 'round:', 'curve:', 'radius:'], 'correct' => 0],
            ['id' => 29, 'question' => 'Which property changes text direction?', 'options' => ['direction:', 'text-direction:', 'dir:', 'align:'], 'correct' => 0],
            ['id' => 30, 'question' => 'Which property changes element visibility?', 'options' => ['visibility:', 'display:', 'Both', 'None'], 'correct' => 2],
        ];
    }

    // ============================================================
    // SQL ВОПРОСЫ (30)
    // ============================================================
    private function getSqlQuestions($locale)
    {
        if ($locale == 'uz') {
            return $this->getSqlQuestionsUz();
        } elseif ($locale == 'ru') {
            return $this->getSqlQuestionsRu();
        } else {
            return $this->getSqlQuestionsEn();
        }
    }

    private function getSqlQuestionsUz()
    {
        return [
            ['id' => 1, 'question' => 'SQL nimani anglatadi?', 'options' => ['Structured Query Language', 'Standard Query Language', 'Simple Query Language', 'System Query Language'], 'correct' => 0],
            ['id' => 2, 'question' => 'Ma\'lumotlar bazasidan ma\'lumot olish uchun qaysi buyruq ishlatiladi?', 'options' => ['SELECT', 'GET', 'FETCH', 'EXTRACT'], 'correct' => 0],
            ['id' => 3, 'question' => 'Jadval yaratish uchun qaysi buyruq ishlatiladi?', 'options' => ['CREATE TABLE', 'MAKE TABLE', 'NEW TABLE', 'ADD TABLE'], 'correct' => 0],
            ['id' => 4, 'question' => 'Jadvalni o\'chirish uchun qaysi buyruq ishlatiladi?', 'options' => ['DROP TABLE', 'DELETE TABLE', 'REMOVE TABLE', 'CLEAR TABLE'], 'correct' => 0],
            ['id' => 5, 'question' => 'Ma\'lumot qo\'shish uchun qaysi buyruq ishlatiladi?', 'options' => ['INSERT INTO', 'ADD INTO', 'PUT INTO', 'CREATE INTO'], 'correct' => 0],
            ['id' => 6, 'question' => 'Ma\'lumotni yangilash uchun qaysi buyruq ishlatiladi?', 'options' => ['UPDATE', 'MODIFY', 'CHANGE', 'ALTER'], 'correct' => 0],
            ['id' => 7, 'question' => 'Ma\'lumotni o\'chirish uchun qaysi buyruq ishlatiladi?', 'options' => ['DELETE FROM', 'REMOVE FROM', 'CLEAR FROM', 'ERASE FROM'], 'correct' => 0],
            ['id' => 8, 'question' => 'Qaysi buyruq ma\'lumotlarni saralaydi?', 'options' => ['ORDER BY', 'SORT BY', 'GROUP BY', 'ARRANGE BY'], 'correct' => 0],
            ['id' => 9, 'question' => 'Qaysi buyruq ma\'lumotlarni guruhlaydi?', 'options' => ['GROUP BY', 'ORDER BY', 'HAVING', 'WHERE'], 'correct' => 0],
            ['id' => 10, 'question' => 'Qaysi buyruq shartni tekshiradi?', 'options' => ['WHERE', 'IF', 'CHECK', 'CONDITION'], 'correct' => 0],
            ['id' => 11, 'question' => 'SQL da qaysi operator "teng emas" ni bildiradi?', 'options' => ['!=', '<>', 'Ikkalasi ham', 'Biri ham emas'], 'correct' => 2],
            ['id' => 12, 'question' => 'Qaysi funksiya eng katta qiymatni qaytaradi?', 'options' => ['MAX()', 'MIN()', 'SUM()', 'AVG()'], 'correct' => 0],
            ['id' => 13, 'question' => 'Qaysi funksiya eng kichik qiymatni qaytaradi?', 'options' => ['MIN()', 'MAX()', 'SUM()', 'AVG()'], 'correct' => 0],
            ['id' => 14, 'question' => 'Qaysi funksiya o\'rtacha qiymatni qaytaradi?', 'options' => ['AVG()', 'SUM()', 'COUNT()', 'MAX()'], 'correct' => 0],
            ['id' => 15, 'question' => 'Qaysi funksiya qiymatlar yig\'indisini qaytaradi?', 'options' => ['SUM()', 'AVG()', 'COUNT()', 'MAX()'], 'correct' => 0],
            ['id' => 16, 'question' => 'Qaysi funksiya qatorlar sonini qaytaradi?', 'options' => ['COUNT()', 'SUM()', 'AVG()', 'MAX()'], 'correct' => 0],
            ['id' => 17, 'question' => 'SQL da qaysi kalit so\'z unikal qiymatlarni qaytaradi?', 'options' => ['DISTINCT', 'UNIQUE', 'DIFFERENT', 'ONLY'], 'correct' => 0],
            ['id' => 18, 'question' => 'Qaysi buyruq ikkita jadvalni birlashtiradi?', 'options' => ['JOIN', 'MERGE', 'COMBINE', 'UNION'], 'correct' => 0],
            ['id' => 19, 'question' => 'Qaysi JOIN barcha mos keladigan qatorlarni qaytaradi?', 'options' => ['INNER JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'FULL JOIN'], 'correct' => 0],
            ['id' => 20, 'question' => 'Qaysi JOIN chap jadvaldagi barcha qatorlarni qaytaradi?', 'options' => ['LEFT JOIN', 'RIGHT JOIN', 'INNER JOIN', 'FULL JOIN'], 'correct' => 0],
            ['id' => 21, 'question' => 'Qaysi JOIN o\'ng jadvaldagi barcha qatorlarni qaytaradi?', 'options' => ['RIGHT JOIN', 'LEFT JOIN', 'INNER JOIN', 'FULL JOIN'], 'correct' => 0],
            ['id' => 22, 'question' => 'Qaysi buyruq yangi ma\'lumotlar bazasi yaratadi?', 'options' => ['CREATE DATABASE', 'NEW DATABASE', 'ADD DATABASE', 'MAKE DATABASE'], 'correct' => 0],
            ['id' => 23, 'question' => 'Qaysi buyruq ma\'lumotlar bazasini o\'chiradi?', 'options' => ['DROP DATABASE', 'DELETE DATABASE', 'REMOVE DATABASE', 'CLEAR DATABASE'], 'correct' => 0],
            ['id' => 24, 'question' => 'Qaysi buyruq jadval tuzilishini o\'zgartiradi?', 'options' => ['ALTER TABLE', 'MODIFY TABLE', 'CHANGE TABLE', 'UPDATE TABLE'], 'correct' => 0],
            ['id' => 25, 'question' => 'Qaysi buyruq jadvalga ustun qo\'shadi?', 'options' => ['ALTER TABLE ADD', 'ALTER TABLE INSERT', 'ALTER TABLE CREATE', 'ALTER TABLE NEW'], 'correct' => 0],
            ['id' => 26, 'question' => 'Qaysi buyruq jadvaldan ustun o\'chiradi?', 'options' => ['ALTER TABLE DROP', 'ALTER TABLE DELETE', 'ALTER TABLE REMOVE', 'ALTER TABLE CLEAR'], 'correct' => 0],
            ['id' => 27, 'question' => 'SQL da qaysi ma\'lumot turi matn uchun ishlatiladi?', 'options' => ['VARCHAR', 'INT', 'DATE', 'BOOLEAN'], 'correct' => 0],
            ['id' => 28, 'question' => 'SQL da qaysi ma\'lumot turi butun son uchun ishlatiladi?', 'options' => ['INT', 'VARCHAR', 'DATE', 'BOOLEAN'], 'correct' => 0],
            ['id' => 29, 'question' => 'Qaysi buyruq ma\'lumotlarni vaqtinchalik saqlaydi?', 'options' => ['TEMPORARY TABLE', 'TEMP TABLE', 'Ikkalasi ham', 'Biri ham emas'], 'correct' => 2],
            ['id' => 30, 'question' => 'SQL da qaysi operator "LIKE" bilan birga ishlatiladi?', 'options' => ['%', '*', '?', '#'], 'correct' => 0],
        ];
    }

    private function getSqlQuestionsRu()
    {
        return [
            ['id' => 1, 'question' => 'Что означает SQL?', 'options' => ['Structured Query Language', 'Standard Query Language', 'Simple Query Language', 'System Query Language'], 'correct' => 0],
            ['id' => 2, 'question' => 'Какая команда используется для получения данных из базы данных?', 'options' => ['SELECT', 'GET', 'FETCH', 'EXTRACT'], 'correct' => 0],
            ['id' => 3, 'question' => 'Какая команда создает таблицу?', 'options' => ['CREATE TABLE', 'MAKE TABLE', 'NEW TABLE', 'ADD TABLE'], 'correct' => 0],
            ['id' => 4, 'question' => 'Какая команда удаляет таблицу?', 'options' => ['DROP TABLE', 'DELETE TABLE', 'REMOVE TABLE', 'CLEAR TABLE'], 'correct' => 0],
            ['id' => 5, 'question' => 'Какая команда добавляет данные?', 'options' => ['INSERT INTO', 'ADD INTO', 'PUT INTO', 'CREATE INTO'], 'correct' => 0],
            ['id' => 6, 'question' => 'Какая команда обновляет данные?', 'options' => ['UPDATE', 'MODIFY', 'CHANGE', 'ALTER'], 'correct' => 0],
            ['id' => 7, 'question' => 'Какая команда удаляет данные?', 'options' => ['DELETE FROM', 'REMOVE FROM', 'CLEAR FROM', 'ERASE FROM'], 'correct' => 0],
            ['id' => 8, 'question' => 'Какая команда сортирует данные?', 'options' => ['ORDER BY', 'SORT BY', 'GROUP BY', 'ARRANGE BY'], 'correct' => 0],
            ['id' => 9, 'question' => 'Какая команда группирует данные?', 'options' => ['GROUP BY', 'ORDER BY', 'HAVING', 'WHERE'], 'correct' => 0],
            ['id' => 10, 'question' => 'Какая команда проверяет условия?', 'options' => ['WHERE', 'IF', 'CHECK', 'CONDITION'], 'correct' => 0],
            ['id' => 11, 'question' => 'Какой оператор означает "не равно" в SQL?', 'options' => ['!=', '<>', 'Оба', 'Ни один'], 'correct' => 2],
            ['id' => 12, 'question' => 'Какая функция возвращает максимальное значение?', 'options' => ['MAX()', 'MIN()', 'SUM()', 'AVG()'], 'correct' => 0],
            ['id' => 13, 'question' => 'Какая функция возвращает минимальное значение?', 'options' => ['MIN()', 'MAX()', 'SUM()', 'AVG()'], 'correct' => 0],
            ['id' => 14, 'question' => 'Какая функция возвращает среднее значение?', 'options' => ['AVG()', 'SUM()', 'COUNT()', 'MAX()'], 'correct' => 0],
            ['id' => 15, 'question' => 'Какая функция возвращает сумму значений?', 'options' => ['SUM()', 'AVG()', 'COUNT()', 'MAX()'], 'correct' => 0],
            ['id' => 16, 'question' => 'Какая функция возвращает количество строк?', 'options' => ['COUNT()', 'SUM()', 'AVG()', 'MAX()'], 'correct' => 0],
            ['id' => 17, 'question' => 'Какое ключевое слово возвращает уникальные значения в SQL?', 'options' => ['DISTINCT', 'UNIQUE', 'DIFFERENT', 'ONLY'], 'correct' => 0],
            ['id' => 18, 'question' => 'Какая команда объединяет две таблицы?', 'options' => ['JOIN', 'MERGE', 'COMBINE', 'UNION'], 'correct' => 0],
            ['id' => 19, 'question' => 'Какой JOIN возвращает совпадающие строки?', 'options' => ['INNER JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'FULL JOIN'], 'correct' => 0],
            ['id' => 20, 'question' => 'Какой JOIN возвращает все строки из левой таблицы?', 'options' => ['LEFT JOIN', 'RIGHT JOIN', 'INNER JOIN', 'FULL JOIN'], 'correct' => 0],
            ['id' => 21, 'question' => 'Какой JOIN возвращает все строки из правой таблицы?', 'options' => ['RIGHT JOIN', 'LEFT JOIN', 'INNER JOIN', 'FULL JOIN'], 'correct' => 0],
            ['id' => 22, 'question' => 'Какая команда создает базу данных?', 'options' => ['CREATE DATABASE', 'NEW DATABASE', 'ADD DATABASE', 'MAKE DATABASE'], 'correct' => 0],
            ['id' => 23, 'question' => 'Какая команда удаляет базу данных?', 'options' => ['DROP DATABASE', 'DELETE DATABASE', 'REMOVE DATABASE', 'CLEAR DATABASE'], 'correct' => 0],
            ['id' => 24, 'question' => 'Какая команда изменяет структуру таблицы?', 'options' => ['ALTER TABLE', 'MODIFY TABLE', 'CHANGE TABLE', 'UPDATE TABLE'], 'correct' => 0],
            ['id' => 25, 'question' => 'Какая команда добавляет столбец в таблицу?', 'options' => ['ALTER TABLE ADD', 'ALTER TABLE INSERT', 'ALTER TABLE CREATE', 'ALTER TABLE NEW'], 'correct' => 0],
            ['id' => 26, 'question' => 'Какая команда удаляет столбец из таблицы?', 'options' => ['ALTER TABLE DROP', 'ALTER TABLE DELETE', 'ALTER TABLE REMOVE', 'ALTER TABLE CLEAR'], 'correct' => 0],
            ['id' => 27, 'question' => 'Какой тип данных используется для текста в SQL?', 'options' => ['VARCHAR', 'INT', 'DATE', 'BOOLEAN'], 'correct' => 0],
            ['id' => 28, 'question' => 'Какой тип данных используется для целых чисел в SQL?', 'options' => ['INT', 'VARCHAR', 'DATE', 'BOOLEAN'], 'correct' => 0],
            ['id' => 29, 'question' => 'Какая команда создает временные таблицы?', 'options' => ['TEMPORARY TABLE', 'TEMP TABLE', 'Оба', 'Ни один'], 'correct' => 2],
            ['id' => 30, 'question' => 'Какой оператор используется с "LIKE" в SQL?', 'options' => ['%', '*', '?', '#'], 'correct' => 0],
        ];
    }

    private function getSqlQuestionsEn()
    {
        return [
            ['id' => 1, 'question' => 'What does SQL stand for?', 'options' => ['Structured Query Language', 'Standard Query Language', 'Simple Query Language', 'System Query Language'], 'correct' => 0],
            ['id' => 2, 'question' => 'Which command is used to retrieve data from database?', 'options' => ['SELECT', 'GET', 'FETCH', 'EXTRACT'], 'correct' => 0],
            ['id' => 3, 'question' => 'Which command creates a table?', 'options' => ['CREATE TABLE', 'MAKE TABLE', 'NEW TABLE', 'ADD TABLE'], 'correct' => 0],
            ['id' => 4, 'question' => 'Which command deletes a table?', 'options' => ['DROP TABLE', 'DELETE TABLE', 'REMOVE TABLE', 'CLEAR TABLE'], 'correct' => 0],
            ['id' => 5, 'question' => 'Which command adds data?', 'options' => ['INSERT INTO', 'ADD INTO', 'PUT INTO', 'CREATE INTO'], 'correct' => 0],
            ['id' => 6, 'question' => 'Which command updates data?', 'options' => ['UPDATE', 'MODIFY', 'CHANGE', 'ALTER'], 'correct' => 0],
            ['id' => 7, 'question' => 'Which command deletes data?', 'options' => ['DELETE FROM', 'REMOVE FROM', 'CLEAR FROM', 'ERASE FROM'], 'correct' => 0],
            ['id' => 8, 'question' => 'Which command sorts data?', 'options' => ['ORDER BY', 'SORT BY', 'GROUP BY', 'ARRANGE BY'], 'correct' => 0],
            ['id' => 9, 'question' => 'Which command groups data?', 'options' => ['GROUP BY', 'ORDER BY', 'HAVING', 'WHERE'], 'correct' => 0],
            ['id' => 10, 'question' => 'Which command checks conditions?', 'options' => ['WHERE', 'IF', 'CHECK', 'CONDITION'], 'correct' => 0],
            ['id' => 11, 'question' => 'Which operator means "not equal" in SQL?', 'options' => ['!=', '<>', 'Both', 'None'], 'correct' => 2],
            ['id' => 12, 'question' => 'Which function returns maximum value?', 'options' => ['MAX()', 'MIN()', 'SUM()', 'AVG()'], 'correct' => 0],
            ['id' => 13, 'question' => 'Which function returns minimum value?', 'options' => ['MIN()', 'MAX()', 'SUM()', 'AVG()'], 'correct' => 0],
            ['id' => 14, 'question' => 'Which function returns average value?', 'options' => ['AVG()', 'SUM()', 'COUNT()', 'MAX()'], 'correct' => 0],
            ['id' => 15, 'question' => 'Which function returns sum of values?', 'options' => ['SUM()', 'AVG()', 'COUNT()', 'MAX()'], 'correct' => 0],
            ['id' => 16, 'question' => 'Which function returns number of rows?', 'options' => ['COUNT()', 'SUM()', 'AVG()', 'MAX()'], 'correct' => 0],
            ['id' => 17, 'question' => 'Which keyword returns unique values?', 'options' => ['DISTINCT', 'UNIQUE', 'DIFFERENT', 'ONLY'], 'correct' => 0],
            ['id' => 18, 'question' => 'Which command joins two tables?', 'options' => ['JOIN', 'MERGE', 'COMBINE', 'UNION'], 'correct' => 0],
            ['id' => 19, 'question' => 'Which JOIN returns matching rows?', 'options' => ['INNER JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'FULL JOIN'], 'correct' => 0],
            ['id' => 20, 'question' => 'Which JOIN returns all rows from left table?', 'options' => ['LEFT JOIN', 'RIGHT JOIN', 'INNER JOIN', 'FULL JOIN'], 'correct' => 0],
            ['id' => 21, 'question' => 'Which JOIN returns all rows from right table?', 'options' => ['RIGHT JOIN', 'LEFT JOIN', 'INNER JOIN', 'FULL JOIN'], 'correct' => 0],
            ['id' => 22, 'question' => 'Which command creates database?', 'options' => ['CREATE DATABASE', 'NEW DATABASE', 'ADD DATABASE', 'MAKE DATABASE'], 'correct' => 0],
            ['id' => 23, 'question' => 'Which command drops database?', 'options' => ['DROP DATABASE', 'DELETE DATABASE', 'REMOVE DATABASE', 'CLEAR DATABASE'], 'correct' => 0],
            ['id' => 24, 'question' => 'Which command alters table structure?', 'options' => ['ALTER TABLE', 'MODIFY TABLE', 'CHANGE TABLE', 'UPDATE TABLE'], 'correct' => 0],
            ['id' => 25, 'question' => 'Which command adds column to table?', 'options' => ['ALTER TABLE ADD', 'ALTER TABLE INSERT', 'ALTER TABLE CREATE', 'ALTER TABLE NEW'], 'correct' => 0],
            ['id' => 26, 'question' => 'Which command drops column from table?', 'options' => ['ALTER TABLE DROP', 'ALTER TABLE DELETE', 'ALTER TABLE REMOVE', 'ALTER TABLE CLEAR'], 'correct' => 0],
            ['id' => 27, 'question' => 'Which data type is used for text in SQL?', 'options' => ['VARCHAR', 'INT', 'DATE', 'BOOLEAN'], 'correct' => 0],
            ['id' => 28, 'question' => 'Which data type is used for integers in SQL?', 'options' => ['INT', 'VARCHAR', 'DATE', 'BOOLEAN'], 'correct' => 0],
            ['id' => 29, 'question' => 'Which command creates temporary tables?', 'options' => ['TEMPORARY TABLE', 'TEMP TABLE', 'Both', 'None'], 'correct' => 2],
            ['id' => 30, 'question' => 'Which operator is used with "LIKE" in SQL?', 'options' => ['%', '*', '?', '#'], 'correct' => 0],
        ];
    }

    // ============================================================
    // BOOTSTRAP ВОПРОСЫ (30)
    // ============================================================
    private function getBootstrapQuestions($locale)
    {
        if ($locale == 'uz') {
            return $this->getBootstrapQuestionsUz();
        } elseif ($locale == 'ru') {
            return $this->getBootstrapQuestionsRu();
        } else {
            return $this->getBootstrapQuestionsEn();
        }
    }

    private function getBootstrapQuestionsUz()
    {
        return [
            ['id' => 1, 'question' => 'Bootstrap nima?', 'options' => ['Frontend framework', 'Backend framework', 'Database', 'Programming language'], 'correct' => 0],
            ['id' => 2, 'question' => 'Bootstrap qaysi kompaniya tomonidan yaratilgan?', 'options' => ['Twitter', 'Facebook', 'Google', 'Microsoft'], 'correct' => 0],
            ['id' => 3, 'question' => 'Bootstrap ning eng so\'nggi versiyasi qaysi?', 'options' => ['Bootstrap 5', 'Bootstrap 4', 'Bootstrap 3', 'Bootstrap 2'], 'correct' => 0],
            ['id' => 4, 'question' => 'Bootstrap da qaysi klass matnni markazga joylashtiradi?', 'options' => ['text-center', 'text-middle', 'align-center', 'center-text'], 'correct' => 0],
            ['id' => 5, 'question' => 'Bootstrap da qaysi klass tugma yaratadi?', 'options' => ['btn', 'button', 'btn-default', 'btn-primary'], 'correct' => 0],
            ['id' => 6, 'question' => 'Bootstrap da qaysi klass asosiy tugma yaratadi?', 'options' => ['btn-primary', 'btn-default', 'btn-success', 'btn-danger'], 'correct' => 0],
            ['id' => 7, 'question' => 'Bootstrap da qaysi klass muvaffaqiyatli tugma yaratadi?', 'options' => ['btn-success', 'btn-primary', 'btn-danger', 'btn-warning'], 'correct' => 0],
            ['id' => 8, 'question' => 'Bootstrap da qaysi klass xavfli tugma yaratadi?', 'options' => ['btn-danger', 'btn-primary', 'btn-success', 'btn-warning'], 'correct' => 0],
            ['id' => 9, 'question' => 'Bootstrap da qaysi klass ogohlantirish tugmasi yaratadi?', 'options' => ['btn-warning', 'btn-primary', 'btn-success', 'btn-danger'], 'correct' => 0],
            ['id' => 10, 'question' => 'Bootstrap da qaysi klass katta tugma yaratadi?', 'options' => ['btn-lg', 'btn-large', 'btn-big', 'btn-size'], 'correct' => 0],
            ['id' => 11, 'question' => 'Bootstrap da qaysi klass kichik tugma yaratadi?', 'options' => ['btn-sm', 'btn-small', 'btn-mini', 'btn-tiny'], 'correct' => 0],
            ['id' => 12, 'question' => 'Bootstrap da qaysi klass blok tugma yaratadi?', 'options' => ['btn-block', 'btn-full', 'btn-100', 'btn-stretch'], 'correct' => 0],
            ['id' => 13, 'question' => 'Bootstrap da qaysi klass jadval yaratadi?', 'options' => ['table', 'table-striped', 'table-bordered', 'table-hover'], 'correct' => 0],
            ['id' => 14, 'question' => 'Bootstrap da qaysi klass chiziqli jadval yaratadi?', 'options' => ['table-striped', 'table', 'table-bordered', 'table-hover'], 'correct' => 0],
            ['id' => 15, 'question' => 'Bootstrap da qaysi klass chegarali jadval yaratadi?', 'options' => ['table-bordered', 'table', 'table-striped', 'table-hover'], 'correct' => 0],
            ['id' => 16, 'question' => 'Bootstrap da qaysi klass yozuv ustiga kelganda rang o\'zgartiradi?', 'options' => ['table-hover', 'table', 'table-striped', 'table-bordered'], 'correct' => 0],
            ['id' => 17, 'question' => 'Bootstrap da qaysi klass rasmni yumaloq qiladi?', 'options' => ['rounded', 'rounded-circle', 'img-rounded', 'img-circle'], 'correct' => 0],
            ['id' => 18, 'question' => 'Bootstrap da qaysi klass rasmni doira shaklida qiladi?', 'options' => ['rounded-circle', 'rounded', 'img-rounded', 'img-circle'], 'correct' => 0],
            ['id' => 19, 'question' => 'Bootstrap da qaysi klass rasmni kichik qiladi?', 'options' => ['img-fluid', 'img-thumbnail', 'img-responsive', 'img-small'], 'correct' => 0],
            ['id' => 20, 'question' => 'Bootstrap da qaysi klass rasmga ramka qo\'shadi?', 'options' => ['img-thumbnail', 'img-fluid', 'img-responsive', 'img-border'], 'correct' => 0],
            ['id' => 21, 'question' => 'Bootstrap da qaysi klass matn rangini yashil qiladi?', 'options' => ['text-success', 'text-primary', 'text-danger', 'text-warning'], 'correct' => 0],
            ['id' => 22, 'question' => 'Bootstrap da qaysi klass matn rangini qizil qiladi?', 'options' => ['text-danger', 'text-success', 'text-primary', 'text-warning'], 'correct' => 0],
            ['id' => 23, 'question' => 'Bootstrap da qaysi klass matn rangini sariq qiladi?', 'options' => ['text-warning', 'text-success', 'text-primary', 'text-danger'], 'correct' => 0],
            ['id' => 24, 'question' => 'Bootstrap da qaysi klass matn rangini ko\'k qiladi?', 'options' => ['text-primary', 'text-success', 'text-danger', 'text-warning'], 'correct' => 0],
            ['id' => 25, 'question' => 'Bootstrap da qaysi klass fon rangini yashil qiladi?', 'options' => ['bg-success', 'bg-primary', 'bg-danger', 'bg-warning'], 'correct' => 0],
            ['id' => 26, 'question' => 'Bootstrap da qaysi klass fon rangini qizil qiladi?', 'options' => ['bg-danger', 'bg-success', 'bg-primary', 'bg-warning'], 'correct' => 0],
            ['id' => 27, 'question' => 'Bootstrap da qaysi klass fon rangini sariq qiladi?', 'options' => ['bg-warning', 'bg-success', 'bg-primary', 'bg-danger'], 'correct' => 0],
            ['id' => 28, 'question' => 'Bootstrap da qaysi klass fon rangini ko\'k qiladi?', 'options' => ['bg-primary', 'bg-success', 'bg-danger', 'bg-warning'], 'correct' => 0],
            ['id' => 29, 'question' => 'Bootstrap da qaysi klass yashirish uchun ishlatiladi?', 'options' => ['d-none', 'hidden', 'invisible', 'display-none'], 'correct' => 0],
            ['id' => 30, 'question' => 'Bootstrap da qaysi klass ko\'rsatish uchun ishlatiladi?', 'options' => ['d-block', 'visible', 'show', 'display-block'], 'correct' => 0],
        ];
    }

    private function getBootstrapQuestionsRu()
    {
        return [
            ['id' => 1, 'question' => 'Что такое Bootstrap?', 'options' => ['Frontend фреймворк', 'Backend фреймворк', 'База данных', 'Язык программирования'], 'correct' => 0],
            ['id' => 2, 'question' => 'Какая компания создала Bootstrap?', 'options' => ['Twitter', 'Facebook', 'Google', 'Microsoft'], 'correct' => 0],
            ['id' => 3, 'question' => 'Какая последняя версия Bootstrap?', 'options' => ['Bootstrap 5', 'Bootstrap 4', 'Bootstrap 3', 'Bootstrap 2'], 'correct' => 0],
            ['id' => 4, 'question' => 'Какой класс центрирует текст в Bootstrap?', 'options' => ['text-center', 'text-middle', 'align-center', 'center-text'], 'correct' => 0],
            ['id' => 5, 'question' => 'Какой класс создает кнопку в Bootstrap?', 'options' => ['btn', 'button', 'btn-default', 'btn-primary'], 'correct' => 0],
            ['id' => 6, 'question' => 'Какой класс создает основную кнопку в Bootstrap?', 'options' => ['btn-primary', 'btn-default', 'btn-success', 'btn-danger'], 'correct' => 0],
            ['id' => 7, 'question' => 'Какой класс создает кнопку успеха в Bootstrap?', 'options' => ['btn-success', 'btn-primary', 'btn-danger', 'btn-warning'], 'correct' => 0],
            ['id' => 8, 'question' => 'Какой класс создает опасную кнопку в Bootstrap?', 'options' => ['btn-danger', 'btn-primary', 'btn-success', 'btn-warning'], 'correct' => 0],
            ['id' => 9, 'question' => 'Какой класс создает предупреждающую кнопку в Bootstrap?', 'options' => ['btn-warning', 'btn-primary', 'btn-success', 'btn-danger'], 'correct' => 0],
            ['id' => 10, 'question' => 'Какой класс создает большую кнопку в Bootstrap?', 'options' => ['btn-lg', 'btn-large', 'btn-big', 'btn-size'], 'correct' => 0],
            ['id' => 11, 'question' => 'Какой класс создает маленькую кнопку в Bootstrap?', 'options' => ['btn-sm', 'btn-small', 'btn-mini', 'btn-tiny'], 'correct' => 0],
            ['id' => 12, 'question' => 'Какой класс создает блочную кнопку в Bootstrap?', 'options' => ['btn-block', 'btn-full', 'btn-100', 'btn-stretch'], 'correct' => 0],
            ['id' => 13, 'question' => 'Какой класс создает таблицу в Bootstrap?', 'options' => ['table', 'table-striped', 'table-bordered', 'table-hover'], 'correct' => 0],
            ['id' => 14, 'question' => 'Какой класс создает полосатую таблицу в Bootstrap?', 'options' => ['table-striped', 'table', 'table-bordered', 'table-hover'], 'correct' => 0],
            ['id' => 15, 'question' => 'Какой класс создает таблицу с границами в Bootstrap?', 'options' => ['table-bordered', 'table', 'table-striped', 'table-hover'], 'correct' => 0],
            ['id' => 16, 'question' => 'Какой класс добавляет эффект при наведении на таблицу в Bootstrap?', 'options' => ['table-hover', 'table', 'table-striped', 'table-bordered'], 'correct' => 0],
            ['id' => 17, 'question' => 'Какой класс скругляет углы изображения в Bootstrap?', 'options' => ['rounded', 'rounded-circle', 'img-rounded', 'img-circle'], 'correct' => 0],
            ['id' => 18, 'question' => 'Какой класс делает изображение круглым в Bootstrap?', 'options' => ['rounded-circle', 'rounded', 'img-rounded', 'img-circle'], 'correct' => 0],
            ['id' => 19, 'question' => 'Какой класс делает изображение адаптивным в Bootstrap?', 'options' => ['img-fluid', 'img-thumbnail', 'img-responsive', 'img-small'], 'correct' => 0],
            ['id' => 20, 'question' => 'Какой класс добавляет рамку-миниатюру к изображению в Bootstrap?', 'options' => ['img-thumbnail', 'img-fluid', 'img-responsive', 'img-border'], 'correct' => 0],
            ['id' => 21, 'question' => 'Какой класс делает текст зеленым в Bootstrap?', 'options' => ['text-success', 'text-primary', 'text-danger', 'text-warning'], 'correct' => 0],
            ['id' => 22, 'question' => 'Какой класс делает текст красным в Bootstrap?', 'options' => ['text-danger', 'text-success', 'text-primary', 'text-warning'], 'correct' => 0],
            ['id' => 23, 'question' => 'Какой класс делает текст желтым в Bootstrap?', 'options' => ['text-warning', 'text-success', 'text-primary', 'text-danger'], 'correct' => 0],
            ['id' => 24, 'question' => 'Какой класс делает текст синим в Bootstrap?', 'options' => ['text-primary', 'text-success', 'text-danger', 'text-warning'], 'correct' => 0],
            ['id' => 25, 'question' => 'Какой класс делает фон зеленым в Bootstrap?', 'options' => ['bg-success', 'bg-primary', 'bg-danger', 'bg-warning'], 'correct' => 0],
            ['id' => 26, 'question' => 'Какой класс делает фон красным в Bootstrap?', 'options' => ['bg-danger', 'bg-success', 'bg-primary', 'bg-warning'], 'correct' => 0],
            ['id' => 27, 'question' => 'Какой класс делает фон желтым в Bootstrap?', 'options' => ['bg-warning', 'bg-success', 'bg-primary', 'bg-danger'], 'correct' => 0],
            ['id' => 28, 'question' => 'Какой класс делает фон синим в Bootstrap?', 'options' => ['bg-primary', 'bg-success', 'bg-danger', 'bg-warning'], 'correct' => 0],
            ['id' => 29, 'question' => 'Какой класс скрывает элемент в Bootstrap?', 'options' => ['d-none', 'hidden', 'invisible', 'display-none'], 'correct' => 0],
            ['id' => 30, 'question' => 'Какой класс показывает элемент в Bootstrap?', 'options' => ['d-block', 'visible', 'show', 'display-block'], 'correct' => 0],
        ];
    }

    private function getBootstrapQuestionsEn()
    {
        return [
            ['id' => 1, 'question' => 'What is Bootstrap?', 'options' => ['Frontend framework', 'Backend framework', 'Database', 'Programming language'], 'correct' => 0],
            ['id' => 2, 'question' => 'Which company created Bootstrap?', 'options' => ['Twitter', 'Facebook', 'Google', 'Microsoft'], 'correct' => 0],
            ['id' => 3, 'question' => 'What is the latest version of Bootstrap?', 'options' => ['Bootstrap 5', 'Bootstrap 4', 'Bootstrap 3', 'Bootstrap 2'], 'correct' => 0],
            ['id' => 4, 'question' => 'Which class centers text in Bootstrap?', 'options' => ['text-center', 'text-middle', 'align-center', 'center-text'], 'correct' => 0],
            ['id' => 5, 'question' => 'Which class creates a button?', 'options' => ['btn', 'button', 'btn-default', 'btn-primary'], 'correct' => 0],
            ['id' => 6, 'question' => 'Which class creates primary button?', 'options' => ['btn-primary', 'btn-default', 'btn-success', 'btn-danger'], 'correct' => 0],
            ['id' => 7, 'question' => 'Which class creates success button?', 'options' => ['btn-success', 'btn-primary', 'btn-danger', 'btn-warning'], 'correct' => 0],
            ['id' => 8, 'question' => 'Which class creates danger button?', 'options' => ['btn-danger', 'btn-primary', 'btn-success', 'btn-warning'], 'correct' => 0],
            ['id' => 9, 'question' => 'Which class creates warning button?', 'options' => ['btn-warning', 'btn-primary', 'btn-success', 'btn-danger'], 'correct' => 0],
            ['id' => 10, 'question' => 'Which class creates large button?', 'options' => ['btn-lg', 'btn-large', 'btn-big', 'btn-size'], 'correct' => 0],
            ['id' => 11, 'question' => 'Which class creates small button?', 'options' => ['btn-sm', 'btn-small', 'btn-mini', 'btn-tiny'], 'correct' => 0],
            ['id' => 12, 'question' => 'Which class creates block button?', 'options' => ['btn-block', 'btn-full', 'btn-100', 'btn-stretch'], 'correct' => 0],
            ['id' => 13, 'question' => 'Which class creates table?', 'options' => ['table', 'table-striped', 'table-bordered', 'table-hover'], 'correct' => 0],
            ['id' => 14, 'question' => 'Which class creates striped table?', 'options' => ['table-striped', 'table', 'table-bordered', 'table-hover'], 'correct' => 0],
            ['id' => 15, 'question' => 'Which class creates bordered table?', 'options' => ['table-bordered', 'table', 'table-striped', 'table-hover'], 'correct' => 0],
            ['id' => 16, 'question' => 'Which class adds hover effect to table?', 'options' => ['table-hover', 'table', 'table-striped', 'table-bordered'], 'correct' => 0],
            ['id' => 17, 'question' => 'Which class rounds image corners?', 'options' => ['rounded', 'rounded-circle', 'img-rounded', 'img-circle'], 'correct' => 0],
            ['id' => 18, 'question' => 'Which class makes image a circle?', 'options' => ['rounded-circle', 'rounded', 'img-rounded', 'img-circle'], 'correct' => 0],
            ['id' => 19, 'question' => 'Which class makes image fluid?', 'options' => ['img-fluid', 'img-thumbnail', 'img-responsive', 'img-small'], 'correct' => 0],
            ['id' => 20, 'question' => 'Which class adds thumbnail border to image?', 'options' => ['img-thumbnail', 'img-fluid', 'img-responsive', 'img-border'], 'correct' => 0],
            ['id' => 21, 'question' => 'Which class makes text green?', 'options' => ['text-success', 'text-primary', 'text-danger', 'text-warning'], 'correct' => 0],
            ['id' => 22, 'question' => 'Which class makes text red?', 'options' => ['text-danger', 'text-success', 'text-primary', 'text-warning'], 'correct' => 0],
            ['id' => 23, 'question' => 'Which class makes text yellow?', 'options' => ['text-warning', 'text-success', 'text-primary', 'text-danger'], 'correct' => 0],
            ['id' => 24, 'question' => 'Which class makes text blue?', 'options' => ['text-primary', 'text-success', 'text-danger', 'text-warning'], 'correct' => 0],
            ['id' => 25, 'question' => 'Which class makes background green?', 'options' => ['bg-success', 'bg-primary', 'bg-danger', 'bg-warning'], 'correct' => 0],
            ['id' => 26, 'question' => 'Which class makes background red?', 'options' => ['bg-danger', 'bg-success', 'bg-primary', 'bg-warning'], 'correct' => 0],
            ['id' => 27, 'question' => 'Which class makes background yellow?', 'options' => ['bg-warning', 'bg-success', 'bg-primary', 'bg-danger'], 'correct' => 0],
            ['id' => 28, 'question' => 'Which class makes background blue?', 'options' => ['bg-primary', 'bg-success', 'bg-danger', 'bg-warning'], 'correct' => 0],
            ['id' => 29, 'question' => 'Which class hides element?', 'options' => ['d-none', 'hidden', 'invisible', 'display-none'], 'correct' => 0],
            ['id' => 30, 'question' => 'Which class shows element?', 'options' => ['d-block', 'visible', 'show', 'display-block'], 'correct' => 0],
        ];
    }

    // ============================================================
    // JAVASCRIPT ВОПРОСЫ (30)
    // ============================================================
    private function getJavaScriptQuestions($locale)
    {
        if ($locale == 'uz') {
            return $this->getJavaScriptQuestionsUz();
        } elseif ($locale == 'ru') {
            return $this->getJavaScriptQuestionsRu();
        } else {
            return $this->getJavaScriptQuestionsEn();
        }
    }

    private function getJavaScriptQuestionsUz()
    {
        return [
            ['id' => 1, 'question' => 'JavaScript nima?', 'options' => ['Dasturlash tili', 'Stil tili', 'Markup tili', 'Ma\'lumotlar bazasi'], 'correct' => 0],
            ['id' => 2, 'question' => 'JavaScript da o\'zgaruvchi e\'lon qilish uchun qaysi kalit so\'z ishlatiladi?', 'options' => ['let', 'var', 'const', 'Barchasi'], 'correct' => 3],
            ['id' => 3, 'question' => 'JavaScript da qaysi funksiya matnni konsolga chiqaradi?', 'options' => ['console.log()', 'print()', 'echo()', 'write()'], 'correct' => 0],
            ['id' => 4, 'question' => 'JavaScript da qaysi operator tenglikni tekshiradi?', 'options' => ['===', '==', 'Ikkalasi ham', 'Biri ham emas'], 'correct' => 2],
            ['id' => 5, 'question' => 'JavaScript da qaysi kalit so\'z shart yaratish uchun ishlatiladi?', 'options' => ['if', 'for', 'while', 'switch'], 'correct' => 0],
            ['id' => 6, 'question' => 'JavaScript da qaysi kalit so\'z sikl yaratish uchun ishlatiladi?', 'options' => ['for', 'if', 'else', 'switch'], 'correct' => 0],
            ['id' => 7, 'question' => 'JavaScript da funksiya qanday e\'lon qilinadi?', 'options' => ['function myFunc()', 'def myFunc()', 'func myFunc()', 'create myFunc()'], 'correct' => 0],
            ['id' => 8, 'question' => 'JavaScript da qaysi ma\'lumot turi matn uchun ishlatiladi?', 'options' => ['string', 'number', 'boolean', 'object'], 'correct' => 0],
            ['id' => 9, 'question' => 'JavaScript da qaysi ma\'lumot turi son uchun ishlatiladi?', 'options' => ['number', 'string', 'boolean', 'object'], 'correct' => 0],
            ['id' => 10, 'question' => 'JavaScript da qaysi ma\'lumot turi mantiqiy qiymat uchun ishlatiladi?', 'options' => ['boolean', 'string', 'number', 'object'], 'correct' => 0],
            ['id' => 11, 'question' => 'JavaScript da qaysi metod massivga element qo\'shadi?', 'options' => ['push()', 'add()', 'append()', 'insert()'], 'correct' => 0],
            ['id' => 12, 'question' => 'JavaScript da qaysi metod massivdan element o\'chiradi?', 'options' => ['pop()', 'remove()', 'delete()', 'cut()'], 'correct' => 0],
            ['id' => 13, 'question' => 'JavaScript da qaysi metod massivni saralaydi?', 'options' => ['sort()', 'order()', 'arrange()', 'sortArray()'], 'correct' => 0],
            ['id' => 14, 'question' => 'JavaScript da qaysi metod satr uzunligini qaytaradi?', 'options' => ['length', 'size', 'count', 'len'], 'correct' => 0],
            ['id' => 15, 'question' => 'JavaScript da qaysi metod satrdan qism olish uchun ishlatiladi?', 'options' => ['substring()', 'slice()', 'substr()', 'Barchasi'], 'correct' => 3],
            ['id' => 16, 'question' => 'JavaScript da DOM nima?', 'options' => ['Document Object Model', 'Data Object Model', 'Document Oriented Model', 'Data Oriented Model'], 'correct' => 0],
            ['id' => 17, 'question' => 'JavaScript da elementni topish uchun qaysi metod ishlatiladi?', 'options' => ['getElementById()', 'querySelector()', 'Ikkalasi ham', 'Biri ham emas'], 'correct' => 2],
            ['id' => 18, 'question' => 'JavaScript da element matnini o\'zgartirish uchun qaysi xususiyat ishlatiladi?', 'options' => ['innerHTML', 'textContent', 'Ikkalasi ham', 'Biri ham emas'], 'correct' => 2],
            ['id' => 19, 'question' => 'JavaScript da hodisalarni boshqarish uchun qaysi metod ishlatiladi?', 'options' => ['addEventListener()', 'onclick', 'Ikkalasi ham', 'Biri ham emas'], 'correct' => 2],
            ['id' => 20, 'question' => 'JavaScript da JSON nima?', 'options' => ['JavaScript Object Notation', 'Java Object Notation', 'JavaScript Oriented Notation', 'Java Oriented Notation'], 'correct' => 0],
            ['id' => 21, 'question' => 'JavaScript da qaysi metod JSON ni obyektga aylantiradi?', 'options' => ['JSON.parse()', 'JSON.stringify()', 'JSON.convert()', 'JSON.toObject()'], 'correct' => 0],
            ['id' => 22, 'question' => 'JavaScript da qaysi metod obyektni JSON ga aylantiradi?', 'options' => ['JSON.stringify()', 'JSON.parse()', 'JSON.convert()', 'JSON.toJSON()'], 'correct' => 0],
            ['id' => 23, 'question' => 'JavaScript da qaysi kalit so\'z xatoliklarni boshqarish uchun ishlatiladi?', 'options' => ['try...catch', 'error', 'throw', 'handle'], 'correct' => 0],
            ['id' => 24, 'question' => 'JavaScript da qaysi kalit so\'z sinf yaratish uchun ishlatiladi?', 'options' => ['class', 'constructor', 'new', 'object'], 'correct' => 0],
            ['id' => 25, 'question' => 'JavaScript da qaysi kalit so\'z meros olish uchun ishlatiladi?', 'options' => ['extends', 'inherits', 'parent', 'super'], 'correct' => 0],
            ['id' => 26, 'question' => 'JavaScript da qaysi metod massiv elementlarini birlashtiradi?', 'options' => ['concat()', 'merge()', 'join()', 'combine()'], 'correct' => 0],
            ['id' => 27, 'question' => 'JavaScript da qaysi metod massivning oxirgi elementini qaytaradi?', 'options' => ['pop()', 'push()', 'shift()', 'unshift()'], 'correct' => 0],
            ['id' => 28, 'question' => 'JavaScript da qaysi metod massivning birinchi elementini qaytaradi?', 'options' => ['shift()', 'pop()', 'push()', 'unshift()'], 'correct' => 0],
            ['id' => 29, 'question' => 'JavaScript da qaysi kalit so\'z o\'zgaruvchini doimiy qiladi?', 'options' => ['const', 'let', 'var', 'static'], 'correct' => 0],
            ['id' => 30, 'question' => 'JavaScript da qaysi kalit so\'z sinfdan obyekt yaratish uchun ishlatiladi?', 'options' => ['new', 'create', 'make', 'instance'], 'correct' => 0],
        ];
    }

    private function getJavaScriptQuestionsRu()
    {
        return [
            ['id' => 1, 'question' => 'Что такое JavaScript?', 'options' => ['Язык программирования', 'Язык стилей', 'Язык разметки', 'База данных'], 'correct' => 0],
            ['id' => 2, 'question' => 'Какое ключевое слово используется для объявления переменной в JavaScript?', 'options' => ['let', 'var', 'const', 'Все'], 'correct' => 3],
            ['id' => 3, 'question' => 'Какая функция выводит текст в консоль в JavaScript?', 'options' => ['console.log()', 'print()', 'echo()', 'write()'], 'correct' => 0],
            ['id' => 4, 'question' => 'Какой оператор проверяет равенство в JavaScript?', 'options' => ['===', '==', 'Оба', 'Ни один'], 'correct' => 2],
            ['id' => 5, 'question' => 'Какое ключевое слово создает условие в JavaScript?', 'options' => ['if', 'for', 'while', 'switch'], 'correct' => 0],
            ['id' => 6, 'question' => 'Какое ключевое слово создает цикл в JavaScript?', 'options' => ['for', 'if', 'else', 'switch'], 'correct' => 0],
            ['id' => 7, 'question' => 'Как объявить функцию в JavaScript?', 'options' => ['function myFunc()', 'def myFunc()', 'func myFunc()', 'create myFunc()'], 'correct' => 0],
            ['id' => 8, 'question' => 'Какой тип данных используется для текста в JavaScript?', 'options' => ['string', 'number', 'boolean', 'object'], 'correct' => 0],
            ['id' => 9, 'question' => 'Какой тип данных используется для чисел в JavaScript?', 'options' => ['number', 'string', 'boolean', 'object'], 'correct' => 0],
            ['id' => 10, 'question' => 'Какой тип данных используется для логических значений в JavaScript?', 'options' => ['boolean', 'string', 'number', 'object'], 'correct' => 0],
            ['id' => 11, 'question' => 'Какой метод добавляет элемент в массив в JavaScript?', 'options' => ['push()', 'add()', 'append()', 'insert()'], 'correct' => 0],
            ['id' => 12, 'question' => 'Какой метод удаляет последний элемент из массива в JavaScript?', 'options' => ['pop()', 'remove()', 'delete()', 'cut()'], 'correct' => 0],
            ['id' => 13, 'question' => 'Какой метод сортирует массив в JavaScript?', 'options' => ['sort()', 'order()', 'arrange()', 'sortArray()'], 'correct' => 0],
            ['id' => 14, 'question' => 'Какое свойство возвращает длину строки в JavaScript?', 'options' => ['length', 'size', 'count', 'len'], 'correct' => 0],
            ['id' => 15, 'question' => 'Какой метод извлекает часть строки в JavaScript?', 'options' => ['substring()', 'slice()', 'substr()', 'Все'], 'correct' => 3],
            ['id' => 16, 'question' => 'Что такое DOM в JavaScript?', 'options' => ['Document Object Model', 'Data Object Model', 'Document Oriented Model', 'Data Oriented Model'], 'correct' => 0],
            ['id' => 17, 'question' => 'Какой метод находит элемент в JavaScript?', 'options' => ['getElementById()', 'querySelector()', 'Оба', 'Ни один'], 'correct' => 2],
            ['id' => 18, 'question' => 'Какое свойство изменяет текст элемента в JavaScript?', 'options' => ['innerHTML', 'textContent', 'Оба', 'Ни один'], 'correct' => 2],
            ['id' => 19, 'question' => 'Какой метод обрабатывает события в JavaScript?', 'options' => ['addEventListener()', 'onclick', 'Оба', 'Ни один'], 'correct' => 2],
            ['id' => 20, 'question' => 'Что такое JSON в JavaScript?', 'options' => ['JavaScript Object Notation', 'Java Object Notation', 'JavaScript Oriented Notation', 'Java Oriented Notation'], 'correct' => 0],
            ['id' => 21, 'question' => 'Какой метод преобразует JSON в объект в JavaScript?', 'options' => ['JSON.parse()', 'JSON.stringify()', 'JSON.convert()', 'JSON.toObject()'], 'correct' => 0],
            ['id' => 22, 'question' => 'Какой метод преобразует объект в JSON в JavaScript?', 'options' => ['JSON.stringify()', 'JSON.parse()', 'JSON.convert()', 'JSON.toJSON()'], 'correct' => 0],
            ['id' => 23, 'question' => 'Какое ключевое слово обрабатывает ошибки в JavaScript?', 'options' => ['try...catch', 'error', 'throw', 'handle'], 'correct' => 0],
            ['id' => 24, 'question' => 'Какое ключевое слово создает класс в JavaScript?', 'options' => ['class', 'constructor', 'new', 'object'], 'correct' => 0],
            ['id' => 25, 'question' => 'Какое ключевое слово используется для наследования в JavaScript?', 'options' => ['extends', 'inherits', 'parent', 'super'], 'correct' => 0],
            ['id' => 26, 'question' => 'Какой метод объединяет массивы в JavaScript?', 'options' => ['concat()', 'merge()', 'join()', 'combine()'], 'correct' => 0],
            ['id' => 27, 'question' => 'Какой метод возвращает последний элемент массива в JavaScript?', 'options' => ['pop()', 'push()', 'shift()', 'unshift()'], 'correct' => 0],
            ['id' => 28, 'question' => 'Какой метод возвращает первый элемент массива в JavaScript?', 'options' => ['shift()', 'pop()', 'push()', 'unshift()'], 'correct' => 0],
            ['id' => 29, 'question' => 'Какое ключевое слово делает переменную константой в JavaScript?', 'options' => ['const', 'let', 'var', 'static'], 'correct' => 0],
            ['id' => 30, 'question' => 'Какое ключевое слово создает объект из класса в JavaScript?', 'options' => ['new', 'create', 'make', 'instance'], 'correct' => 0],
        ];
    }

    private function getJavaScriptQuestionsEn()
    {
        return [
            ['id' => 1, 'question' => 'What is JavaScript?', 'options' => ['Programming language', 'Style language', 'Markup language', 'Database'], 'correct' => 0],
            ['id' => 2, 'question' => 'Which keyword is used to declare a variable in JavaScript?', 'options' => ['let', 'var', 'const', 'All of them'], 'correct' => 3],
            ['id' => 3, 'question' => 'Which function prints text to console in JavaScript?', 'options' => ['console.log()', 'print()', 'echo()', 'write()'], 'correct' => 0],
            ['id' => 4, 'question' => 'Which operator checks equality in JavaScript?', 'options' => ['===', '==', 'Both', 'None'], 'correct' => 2],
            ['id' => 5, 'question' => 'Which keyword creates a condition in JavaScript?', 'options' => ['if', 'for', 'while', 'switch'], 'correct' => 0],
            ['id' => 6, 'question' => 'Which keyword creates a loop in JavaScript?', 'options' => ['for', 'if', 'else', 'switch'], 'correct' => 0],
            ['id' => 7, 'question' => 'How to declare a function in JavaScript?', 'options' => ['function myFunc()', 'def myFunc()', 'func myFunc()', 'create myFunc()'], 'correct' => 0],
            ['id' => 8, 'question' => 'Which data type is used for text in JavaScript?', 'options' => ['string', 'number', 'boolean', 'object'], 'correct' => 0],
            ['id' => 9, 'question' => 'Which data type is used for numbers in JavaScript?', 'options' => ['number', 'string', 'boolean', 'object'], 'correct' => 0],
            ['id' => 10, 'question' => 'Which data type is used for boolean values in JavaScript?', 'options' => ['boolean', 'string', 'number', 'object'], 'correct' => 0],
            ['id' => 11, 'question' => 'Which method adds an element to an array in JavaScript?', 'options' => ['push()', 'add()', 'append()', 'insert()'], 'correct' => 0],
            ['id' => 12, 'question' => 'Which method removes the last element from an array in JavaScript?', 'options' => ['pop()', 'remove()', 'delete()', 'cut()'], 'correct' => 0],
            ['id' => 13, 'question' => 'Which method sorts an array in JavaScript?', 'options' => ['sort()', 'order()', 'arrange()', 'sortArray()'], 'correct' => 0],
            ['id' => 14, 'question' => 'Which method returns the length of a string in JavaScript?', 'options' => ['length', 'size', 'count', 'len'], 'correct' => 0],
            ['id' => 15, 'question' => 'Which method extracts a part of a string in JavaScript?', 'options' => ['substring()', 'slice()', 'substr()', 'All of them'], 'correct' => 3],
            ['id' => 16, 'question' => 'What is DOM in JavaScript?', 'options' => ['Document Object Model', 'Data Object Model', 'Document Oriented Model', 'Data Oriented Model'], 'correct' => 0],
            ['id' => 17, 'question' => 'Which method finds an element in JavaScript?', 'options' => ['getElementById()', 'querySelector()', 'Both', 'None'], 'correct' => 2],
            ['id' => 18, 'question' => 'Which property changes element text in JavaScript?', 'options' => ['innerHTML', 'textContent', 'Both', 'None'], 'correct' => 2],
            ['id' => 19, 'question' => 'Which method handles events in JavaScript?', 'options' => ['addEventListener()', 'onclick', 'Both', 'None'], 'correct' => 2],
            ['id' => 20, 'question' => 'What is JSON in JavaScript?', 'options' => ['JavaScript Object Notation', 'Java Object Notation', 'JavaScript Oriented Notation', 'Java Oriented Notation'], 'correct' => 0],
            ['id' => 21, 'question' => 'Which method converts JSON to object in JavaScript?', 'options' => ['JSON.parse()', 'JSON.stringify()', 'JSON.convert()', 'JSON.toObject()'], 'correct' => 0],
            ['id' => 22, 'question' => 'Which method converts object to JSON in JavaScript?', 'options' => ['JSON.stringify()', 'JSON.parse()', 'JSON.convert()', 'JSON.toJSON()'], 'correct' => 0],
            ['id' => 23, 'question' => 'Which keyword handles errors in JavaScript?', 'options' => ['try...catch', 'error', 'throw', 'handle'], 'correct' => 0],
            ['id' => 24, 'question' => 'Which keyword creates a class in JavaScript?', 'options' => ['class', 'constructor', 'new', 'object'], 'correct' => 0],
            ['id' => 25, 'question' => 'Which keyword is used for inheritance in JavaScript?', 'options' => ['extends', 'inherits', 'parent', 'super'], 'correct' => 0],
            ['id' => 26, 'question' => 'Which method merges arrays in JavaScript?', 'options' => ['concat()', 'merge()', 'join()', 'combine()'], 'correct' => 0],
            ['id' => 27, 'question' => 'Which method returns the last element of an array in JavaScript?', 'options' => ['pop()', 'push()', 'shift()', 'unshift()'], 'correct' => 0],
            ['id' => 28, 'question' => 'Which method returns the first element of an array in JavaScript?', 'options' => ['shift()', 'pop()', 'push()', 'unshift()'], 'correct' => 0],
            ['id' => 29, 'question' => 'Which keyword makes a variable constant in JavaScript?', 'options' => ['const', 'let', 'var', 'static'], 'correct' => 0],
            ['id' => 30, 'question' => 'Which keyword creates an object from a class in JavaScript?', 'options' => ['new', 'create', 'make', 'instance'], 'correct' => 0],
        ];
    }

    // ============================================================
    // LARAVEL ВОПРОСЫ (30)
    // ============================================================
    private function getLaravelQuestions($locale)
    {
        if ($locale == 'uz') {
            return $this->getLaravelQuestionsUz();
        } elseif ($locale == 'ru') {
            return $this->getLaravelQuestionsRu();
        } else {
            return $this->getLaravelQuestionsEn();
        }
    }

    private function getLaravelQuestionsUz()
    {
        return [
            ['id' => 1, 'question' => 'Laravel nima?', 'options' => ['PHP framework', 'Python framework', 'JavaScript framework', 'Java framework'], 'correct' => 0],
            ['id' => 2, 'question' => 'Laravel qaysi dasturlash tilida yozilgan?', 'options' => ['PHP', 'Python', 'JavaScript', 'Java'], 'correct' => 0],
            ['id' => 3, 'question' => 'Laravel da qaysi buyruq yangi loyiha yaratadi?', 'options' => ['composer create-project laravel/laravel', 'laravel new', 'php artisan new', 'composer new laravel'], 'correct' => 0],
            ['id' => 4, 'question' => 'Laravel da qaysi buyruq serverni ishga tushiradi?', 'options' => ['php artisan serve', 'php artisan start', 'php artisan run', 'php artisan server'], 'correct' => 0],
            ['id' => 5, 'question' => 'Laravel da qaysi buyruq migratsiyani ishga tushiradi?', 'options' => ['php artisan migrate', 'php artisan migration', 'php artisan db:migrate', 'php artisan migrate:run'], 'correct' => 0],
            ['id' => 6, 'question' => 'Laravel da qaysi buyruq kontroller yaratadi?', 'options' => ['php artisan make:controller', 'php artisan create:controller', 'php artisan controller:make', 'php artisan new:controller'], 'correct' => 0],
            ['id' => 7, 'question' => 'Laravel da qaysi buyruq model yaratadi?', 'options' => ['php artisan make:model', 'php artisan create:model', 'php artisan model:make', 'php artisan new:model'], 'correct' => 0],
            ['id' => 8, 'question' => 'Laravel da qaysi buyruq migratsiya yaratadi?', 'options' => ['php artisan make:migration', 'php artisan create:migration', 'php artisan migration:make', 'php artisan new:migration'], 'correct' => 0],
            ['id' => 9, 'question' => 'Laravel da qaysi fayl marshrutlar uchun ishlatiladi?', 'options' => ['web.php', 'routes.php', 'api.php', 'console.php'], 'correct' => 0],
            ['id' => 10, 'question' => 'Laravel da qaysi fayl konfiguratsiyalar uchun ishlatiladi?', 'options' => ['.env', 'config.php', 'settings.php', 'env.php'], 'correct' => 0],
            ['id' => 11, 'question' => 'Laravel da qaysi ORM ishlatiladi?', 'options' => ['Eloquent', 'Doctrine', 'Propel', 'RedBean'], 'correct' => 0],
            ['id' => 12, 'question' => 'Laravel da qaysi til shablonsizlash uchun ishlatiladi?', 'options' => ['Blade', 'Twig', 'Smarty', 'Mustache'], 'correct' => 0],
            ['id' => 13, 'question' => 'Laravel da qaysi buyruq keshni tozalaydi?', 'options' => ['php artisan cache:clear', 'php artisan clear:cache', 'php artisan cache:flush', 'php artisan flush:cache'], 'correct' => 0],
            ['id' => 14, 'question' => 'Laravel da qaysi buyruq marshrutlarni tozalaydi?', 'options' => ['php artisan route:clear', 'php artisan clear:route', 'php artisan route:flush', 'php artisan flush:route'], 'correct' => 0],
            ['id' => 15, 'question' => 'Laravel da qaysi buyruq konfiguratsiyani tozalaydi?', 'options' => ['php artisan config:clear', 'php artisan clear:config', 'php artisan config:flush', 'php artisan flush:config'], 'correct' => 0],
            ['id' => 16, 'question' => 'Laravel da qaysi buyruq ko\'rinishlarni tozalaydi?', 'options' => ['php artisan view:clear', 'php artisan clear:view', 'php artisan view:flush', 'php artisan flush:view'], 'correct' => 0],
            ['id' => 17, 'question' => 'Laravel da qaysi buyruq autoloadni qayta yuklaydi?', 'options' => ['composer dump-autoload', 'composer reload', 'composer update', 'composer install'], 'correct' => 0],
            ['id' => 18, 'question' => 'Laravel da qaysi xususiyat autentifikatsiya uchun ishlatiladi?', 'options' => ['Auth', 'Login', 'Session', 'User'], 'correct' => 0],
            ['id' => 19, 'question' => 'Laravel da qaysi xususiyat ma\'lumotlar bazasi so\'rovlari uchun ishlatiladi?', 'options' => ['DB', 'Query', 'SQL', 'Database'], 'correct' => 0],
            ['id' => 20, 'question' => 'Laravel da qaysi xususiyat seanslar uchun ishlatiladi?', 'options' => ['Session', 'Cookie', 'Cache', 'Storage'], 'correct' => 0],
            ['id' => 21, 'question' => 'Laravel da qaysi fayl ma\'lumotlar bazasi ulanishi uchun ishlatiladi?', 'options' => ['.env', 'database.php', 'config.php', 'env.php'], 'correct' => 0],
            ['id' => 22, 'question' => 'Laravel da qaysi buyruq queue ishini ishga tushiradi?', 'options' => ['php artisan queue:work', 'php artisan queue:start', 'php artisan queue:run', 'php artisan queue:listen'], 'correct' => 0],
            ['id' => 23, 'question' => 'Laravel da qaysi xususiyat email yuborish uchun ishlatiladi?', 'options' => ['Mail', 'Email', 'Send', 'Message'], 'correct' => 0],
            ['id' => 24, 'question' => 'Laravel da qaysi xususiyat fayl saqlash uchun ishlatiladi?', 'options' => ['Storage', 'File', 'Upload', 'Save'], 'correct' => 0],
            ['id' => 25, 'question' => 'Laravel da qaysi buyruq seeder yaratadi?', 'options' => ['php artisan make:seeder', 'php artisan create:seeder', 'php artisan seeder:make', 'php artisan new:seeder'], 'correct' => 0],
            ['id' => 26, 'question' => 'Laravel da qaysi buyruq factory yaratadi?', 'options' => ['php artisan make:factory', 'php artisan create:factory', 'php artisan factory:make', 'php artisan new:factory'], 'correct' => 0],
            ['id' => 27, 'question' => 'Laravel da qaysi buyruq test yaratadi?', 'options' => ['php artisan make:test', 'php artisan create:test', 'php artisan test:make', 'php artisan new:test'], 'correct' => 0],
            ['id' => 28, 'question' => 'Laravel da qaysi buyruq middleware yaratadi?', 'options' => ['php artisan make:middleware', 'php artisan create:middleware', 'php artisan middleware:make', 'php artisan new:middleware'], 'correct' => 0],
            ['id' => 29, 'question' => 'Laravel da qaysi buyruq request yaratadi?', 'options' => ['php artisan make:request', 'php artisan create:request', 'php artisan request:make', 'php artisan new:request'], 'correct' => 0],
            ['id' => 30, 'question' => 'Laravel da qaysi buyruq provider yaratadi?', 'options' => ['php artisan make:provider', 'php artisan create:provider', 'php artisan provider:make', 'php artisan new:provider'], 'correct' => 0],
        ];
    }

    private function getLaravelQuestionsRu()
    {
        return [
            ['id' => 1, 'question' => 'Что такое Laravel?', 'options' => ['PHP фреймворк', 'Python фреймворк', 'JavaScript фреймворк', 'Java фреймворк'], 'correct' => 0],
            ['id' => 2, 'question' => 'На каком языке программирования написан Laravel?', 'options' => ['PHP', 'Python', 'JavaScript', 'Java'], 'correct' => 0],
            ['id' => 3, 'question' => 'Какая команда создает новый проект Laravel?', 'options' => ['composer create-project laravel/laravel', 'laravel new', 'php artisan new', 'composer new laravel'], 'correct' => 0],
            ['id' => 4, 'question' => 'Какая команда запускает сервер Laravel?', 'options' => ['php artisan serve', 'php artisan start', 'php artisan run', 'php artisan server'], 'correct' => 0],
            ['id' => 5, 'question' => 'Какая команда выполняет миграции в Laravel?', 'options' => ['php artisan migrate', 'php artisan migration', 'php artisan db:migrate', 'php artisan migrate:run'], 'correct' => 0],
            ['id' => 6, 'question' => 'Какая команда создает контроллер в Laravel?', 'options' => ['php artisan make:controller', 'php artisan create:controller', 'php artisan controller:make', 'php artisan new:controller'], 'correct' => 0],
            ['id' => 7, 'question' => 'Какая команда создает модель в Laravel?', 'options' => ['php artisan make:model', 'php artisan create:model', 'php artisan model:make', 'php artisan new:model'], 'correct' => 0],
            ['id' => 8, 'question' => 'Какая команда создает миграцию в Laravel?', 'options' => ['php artisan make:migration', 'php artisan create:migration', 'php artisan migration:make', 'php artisan new:migration'], 'correct' => 0],
            ['id' => 9, 'question' => 'Какой файл используется для маршрутов в Laravel?', 'options' => ['web.php', 'routes.php', 'api.php', 'console.php'], 'correct' => 0],
            ['id' => 10, 'question' => 'Какой файл используется для конфигурации в Laravel?', 'options' => ['.env', 'config.php', 'settings.php', 'env.php'], 'correct' => 0],
            ['id' => 11, 'question' => 'Какой ORM используется в Laravel?', 'options' => ['Eloquent', 'Doctrine', 'Propel', 'RedBean'], 'correct' => 0],
            ['id' => 12, 'question' => 'Какой язык шаблонов используется в Laravel?', 'options' => ['Blade', 'Twig', 'Smarty', 'Mustache'], 'correct' => 0],
            ['id' => 13, 'question' => 'Какая команда очищает кеш в Laravel?', 'options' => ['php artisan cache:clear', 'php artisan clear:cache', 'php artisan cache:flush', 'php artisan flush:cache'], 'correct' => 0],
            ['id' => 14, 'question' => 'Какая команда очищает маршруты в Laravel?', 'options' => ['php artisan route:clear', 'php artisan clear:route', 'php artisan route:flush', 'php artisan flush:route'], 'correct' => 0],
            ['id' => 15, 'question' => 'Какая команда очищает конфигурацию в Laravel?', 'options' => ['php artisan config:clear', 'php artisan clear:config', 'php artisan config:flush', 'php artisan flush:config'], 'correct' => 0],
            ['id' => 16, 'question' => 'Какая команда очищает представления в Laravel?', 'options' => ['php artisan view:clear', 'php artisan clear:view', 'php artisan view:flush', 'php artisan flush:view'], 'correct' => 0],
            ['id' => 17, 'question' => 'Какая команда перезагружает автозагрузку в Laravel?', 'options' => ['composer dump-autoload', 'composer reload', 'composer update', 'composer install'], 'correct' => 0],
            ['id' => 18, 'question' => 'Какая функция используется для аутентификации в Laravel?', 'options' => ['Auth', 'Login', 'Session', 'User'], 'correct' => 0],
            ['id' => 19, 'question' => 'Какая функция используется для запросов к базе данных в Laravel?', 'options' => ['DB', 'Query', 'SQL', 'Database'], 'correct' => 0],
            ['id' => 20, 'question' => 'Какая функция используется для сессий в Laravel?', 'options' => ['Session', 'Cookie', 'Cache', 'Storage'], 'correct' => 0],
            ['id' => 21, 'question' => 'Какой файл используется для подключения к базе данных в Laravel?', 'options' => ['.env', 'database.php', 'config.php', 'env.php'], 'correct' => 0],
            ['id' => 22, 'question' => 'Какая команда запускает обработчик очередей в Laravel?', 'options' => ['php artisan queue:work', 'php artisan queue:start', 'php artisan queue:run', 'php artisan queue:listen'], 'correct' => 0],
            ['id' => 23, 'question' => 'Какая функция используется для отправки писем в Laravel?', 'options' => ['Mail', 'Email', 'Send', 'Message'], 'correct' => 0],
            ['id' => 24, 'question' => 'Какая функция используется для хранения файлов в Laravel?', 'options' => ['Storage', 'File', 'Upload', 'Save'], 'correct' => 0],
            ['id' => 25, 'question' => 'Какая команда создает сидер в Laravel?', 'options' => ['php artisan make:seeder', 'php artisan create:seeder', 'php artisan seeder:make', 'php artisan new:seeder'], 'correct' => 0],
            ['id' => 26, 'question' => 'Какая команда создает фабрику в Laravel?', 'options' => ['php artisan make:factory', 'php artisan create:factory', 'php artisan factory:make', 'php artisan new:factory'], 'correct' => 0],
            ['id' => 27, 'question' => 'Какая команда создает тест в Laravel?', 'options' => ['php artisan make:test', 'php artisan create:test', 'php artisan test:make', 'php artisan new:test'], 'correct' => 0],
            ['id' => 28, 'question' => 'Какая команда создает посредник (middleware) в Laravel?', 'options' => ['php artisan make:middleware', 'php artisan create:middleware', 'php artisan middleware:make', 'php artisan new:middleware'], 'correct' => 0],
            ['id' => 29, 'question' => 'Какая команда создает запрос (request) в Laravel?', 'options' => ['php artisan make:request', 'php artisan create:request', 'php artisan request:make', 'php artisan new:request'], 'correct' => 0],
            ['id' => 30, 'question' => 'Какая команда создает провайдер в Laravel?', 'options' => ['php artisan make:provider', 'php artisan create:provider', 'php artisan provider:make', 'php artisan new:provider'], 'correct' => 0],
        ];
    }

    private function getLaravelQuestionsEn()
    {
        return [
            ['id' => 1, 'question' => 'What is Laravel?', 'options' => ['PHP framework', 'Python framework', 'JavaScript framework', 'Java framework'], 'correct' => 0],
            ['id' => 2, 'question' => 'What language is Laravel written in?', 'options' => ['PHP', 'Python', 'JavaScript', 'Java'], 'correct' => 0],
            ['id' => 3, 'question' => 'Which command creates a new Laravel project?', 'options' => ['composer create-project laravel/laravel', 'laravel new', 'php artisan new', 'composer new laravel'], 'correct' => 0],
            ['id' => 4, 'question' => 'Which command starts the Laravel server?', 'options' => ['php artisan serve', 'php artisan start', 'php artisan run', 'php artisan server'], 'correct' => 0],
            ['id' => 5, 'question' => 'Which command runs migrations in Laravel?', 'options' => ['php artisan migrate', 'php artisan migration', 'php artisan db:migrate', 'php artisan migrate:run'], 'correct' => 0],
            ['id' => 6, 'question' => 'Which command creates a controller in Laravel?', 'options' => ['php artisan make:controller', 'php artisan create:controller', 'php artisan controller:make', 'php artisan new:controller'], 'correct' => 0],
            ['id' => 7, 'question' => 'Which command creates a model in Laravel?', 'options' => ['php artisan make:model', 'php artisan create:model', 'php artisan model:make', 'php artisan new:model'], 'correct' => 0],
            ['id' => 8, 'question' => 'Which command creates a migration in Laravel?', 'options' => ['php artisan make:migration', 'php artisan create:migration', 'php artisan migration:make', 'php artisan new:migration'], 'correct' => 0],
            ['id' => 9, 'question' => 'Which file is used for routes in Laravel?', 'options' => ['web.php', 'routes.php', 'api.php', 'console.php'], 'correct' => 0],
            ['id' => 10, 'question' => 'Which file is used for configuration in Laravel?', 'options' => ['.env', 'config.php', 'settings.php', 'env.php'], 'correct' => 0],
            ['id' => 11, 'question' => 'Which ORM is used in Laravel?', 'options' => ['Eloquent', 'Doctrine', 'Propel', 'RedBean'], 'correct' => 0],
            ['id' => 12, 'question' => 'Which templating language is used in Laravel?', 'options' => ['Blade', 'Twig', 'Smarty', 'Mustache'], 'correct' => 0],
            ['id' => 13, 'question' => 'Which command clears cache in Laravel?', 'options' => ['php artisan cache:clear', 'php artisan clear:cache', 'php artisan cache:flush', 'php artisan flush:cache'], 'correct' => 0],
            ['id' => 14, 'question' => 'Which command clears routes in Laravel?', 'options' => ['php artisan route:clear', 'php artisan clear:route', 'php artisan route:flush', 'php artisan flush:route'], 'correct' => 0],
            ['id' => 15, 'question' => 'Which command clears config in Laravel?', 'options' => ['php artisan config:clear', 'php artisan clear:config', 'php artisan config:flush', 'php artisan flush:config'], 'correct' => 0],
            ['id' => 16, 'question' => 'Which command clears views in Laravel?', 'options' => ['php artisan view:clear', 'php artisan clear:view', 'php artisan view:flush', 'php artisan flush:view'], 'correct' => 0],
            ['id' => 17, 'question' => 'Which command reloads autoload in Laravel?', 'options' => ['composer dump-autoload', 'composer reload', 'composer update', 'composer install'], 'correct' => 0],
            ['id' => 18, 'question' => 'Which feature is used for authentication in Laravel?', 'options' => ['Auth', 'Login', 'Session', 'User'], 'correct' => 0],
            ['id' => 19, 'question' => 'Which feature is used for database queries in Laravel?', 'options' => ['DB', 'Query', 'SQL', 'Database'], 'correct' => 0],
            ['id' => 20, 'question' => 'Which feature is used for sessions in Laravel?', 'options' => ['Session', 'Cookie', 'Cache', 'Storage'], 'correct' => 0],
            ['id' => 21, 'question' => 'Which file is used for database connection in Laravel?', 'options' => ['.env', 'database.php', 'config.php', 'env.php'], 'correct' => 0],
            ['id' => 22, 'question' => 'Which command runs queue worker in Laravel?', 'options' => ['php artisan queue:work', 'php artisan queue:start', 'php artisan queue:run', 'php artisan queue:listen'], 'correct' => 0],
            ['id' => 23, 'question' => 'Which feature is used for sending emails in Laravel?', 'options' => ['Mail', 'Email', 'Send', 'Message'], 'correct' => 0],
            ['id' => 24, 'question' => 'Which feature is used for file storage in Laravel?', 'options' => ['Storage', 'File', 'Upload', 'Save'], 'correct' => 0],
            ['id' => 25, 'question' => 'Which command creates a seeder in Laravel?', 'options' => ['php artisan make:seeder', 'php artisan create:seeder', 'php artisan seeder:make', 'php artisan new:seeder'], 'correct' => 0],
            ['id' => 26, 'question' => 'Which command creates a factory in Laravel?', 'options' => ['php artisan make:factory', 'php artisan create:factory', 'php artisan factory:make', 'php artisan new:factory'], 'correct' => 0],
            ['id' => 27, 'question' => 'Which command creates a test in Laravel?', 'options' => ['php artisan make:test', 'php artisan create:test', 'php artisan test:make', 'php artisan new:test'], 'correct' => 0],
            ['id' => 28, 'question' => 'Which command creates middleware in Laravel?', 'options' => ['php artisan make:middleware', 'php artisan create:middleware', 'php artisan middleware:make', 'php artisan new:middleware'], 'correct' => 0],
            ['id' => 29, 'question' => 'Which command creates a request in Laravel?', 'options' => ['php artisan make:request', 'php artisan create:request', 'php artisan request:make', 'php artisan new:request'], 'correct' => 0],
            ['id' => 30, 'question' => 'Which command creates a provider in Laravel?', 'options' => ['php artisan make:provider', 'php artisan create:provider', 'php artisan provider:make', 'php artisan new:provider'], 'correct' => 0],
        ];
    }
}