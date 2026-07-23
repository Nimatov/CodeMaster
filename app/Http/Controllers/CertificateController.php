<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestResult;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;

class CertificateController extends Controller
{
    public function generate($resultId)
    {
        $result = TestResult::with('user')->findOrFail($resultId);

        $language = app()->getLocale();
        
        // Порог 60%: если больше или равно 60 — 'pass', иначе — 'fail'
        $score = $result->score_percentage ?? 0;
        $type = $score >= 60 ? 'pass' : 'fail';

        // Проверяем шаблон с расширением .jpg
        $templatePath = public_path("images/certificates/{$type}_{$language}.jpg");

        if (!file_exists($templatePath)) {
            $templatePath = public_path("images/certificates/{$type}_en.jpg");
        }

        if (!file_exists($templatePath)) {
            return $this->generateFallback($result, $language, $type);
        }

        $img = Image::make($templatePath);

        // ============================================================
        // КООРДИНАТЫ И РАЗМЕР ИМЕНИ НА СЕРТИФИКАТЕ
        // ============================================================
        $x = 500;
        $y = 165;
        $fontSize = 75;

        // ============================================================
        // ИМЯ ПОЛЬЗОВАТЕЛЯ
        // ============================================================
        $userName = $result->user->full_name ?? $result->user->name;
        $name = strtoupper($userName);

        // ============================================================
        // НАДЕЖНОЕ ПОДКЛЮЧЕНИЕ ШРИФТА ДЛЯ КИРИЛЛИЦЫ
        // ============================================================
        $fontPath = public_path('fonts/arialmt.ttf');
        if (!file_exists($fontPath)) {
            $fontPath = base_path('public/fonts/arialmt.ttf');
        }

        try {
            $img->text($name, $x, $y, function ($font) use ($fontSize, $fontPath) {
                $font->size($fontSize);
                $font->color('#1a237e');
                $font->align('center');
                $font->valign('top');
                
                if (file_exists($fontPath)) {
                    $font->file($fontPath);
                }
            });
        } catch (\Exception $e) {
            Log::error('Certificate Font Error: ' . $e->getMessage());
            $img->text($name, $x, $y, function ($font) use ($fontSize) {
                $font->size($fontSize);
                $font->color('#1a237e');
                $font->align('center');
                $font->valign('top');
            });
        }

        return $this->save($img, $result);
    }

    public function show($resultId)
    {
        $result = TestResult::findOrFail($resultId);
        $this->cleanOldCertificates($resultId);
        $path = $this->generate($resultId);
        
        return response()->file($path, [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function download($resultId)
    {
        $result = TestResult::findOrFail($resultId);
        $this->cleanOldCertificates($resultId);
        $path = $this->generate($resultId);
        
        return response()->download($path, 'certificate.png', [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    private function generateFallback($result, $language, $type)
    {
        $img = Image::canvas(1200, 850, $type == 'pass' ? '#1a237e' : '#dc3545');

        $img->rectangle(30, 30, 1170, 820, function ($d) {
            $d->background('#ffffff');
        });

        $img->text('MasterCoding', 600, 150, function ($f) {
            $f->size(50);
            $f->color('#1a237e');
            $f->align('center');
            $f->valign('top');
        });

        $title = $type == 'pass' ? 'CERTIFICATE OF ACHIEVEMENT' : 'CERTIFICATE OF PARTICIPATION';
        $img->text($title, 600, 220, function ($f) {
            $f->size(30);
            $f->color('#c9a84c');
            $f->align('center');
            $f->valign('top');
        });

        $userName = $result->user->full_name ?? $result->user->name;
        $name = strtoupper($userName);
        $img->text($name, 600, 380, function ($f) {
            $f->size(75);
            $f->color('#1a237e');
            $f->align('center');
            $f->valign('top');
        });

        $img->text('_________________________', 350, 600, function ($f) {
            $f->size(16);
            $f->color('#333');
            $f->align('center');
            $f->valign('top');
        });
        $img->text('Founder, MasterCoding', 350, 630, function ($f) {
            $f->size(14);
            $f->color('#666');
            $f->align('center');
            $f->valign('top');
        });

        $img->text('_________________________', 850, 600, function ($f) {
            $f->size(16);
            $f->color('#333');
            $f->align('center');
            $f->valign('top');
        });
        $img->text('Ai Academy', 850, 630, function ($f) {
            $f->size(14);
            $f->color('#666');
            $f->align('center');
            $f->valign('top');
        });

        $dateLabels = [
            'uz' => 'Berilgan sana:',
            'ru' => 'Дата вручения:',
            'en' => 'Awarded on:'
        ];
        $dateLabel = $dateLabels[$language] ?? 'Date:';
        $img->text($dateLabel . ' ' . now()->format('d.m.Y'), 600, 700, function ($f) {
            $f->size(18);
            $f->color('#666');
            $f->align('center');
            $f->valign('top');
        });

        return $this->save($img, $result);
    }

    private function save($img, $result)
    {
        $path = storage_path('app/public/certificates/');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $this->cleanOldCertificates($result->id);

        $file = 'certificate_' . $result->id . '_' . time() . '.png';
        $full = $path . $file;
        $img->save($full, 95);

        return $full;
    }

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
}