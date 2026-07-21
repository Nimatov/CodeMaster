<?php

namespace App\Exports;

use App\Models\TestResult;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ResultsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return TestResult::with('user')->get()->map(function($result) {
            return [
                'ID' => $result->id,
                'User' => $result->user->name ?? 'Unknown',
                'Email' => $result->user->email ?? 'Unknown',
                'Subject' => $result->subject,
                'Total Questions' => $result->total_questions,
                'Correct Answers' => $result->correct_answers,
                'Wrong Answers' => $result->wrong_answers,
                'Score %' => round($result->score_percentage, 2),
                'Time Spent (sec)' => $result->time_spent,
                'Date' => $result->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'User',
            'Email',
            'Subject',
            'Total Questions',
            'Correct Answers',
            'Wrong Answers',
            'Score %',
            'Time Spent (sec)',
            'Date'
        ];
    }
}