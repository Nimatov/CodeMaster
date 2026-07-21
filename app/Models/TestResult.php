<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    protected $table = 'test_results';
    
    protected $fillable = [
        'user_id', 'subject', 'total_questions', 
        'correct_answers', 'wrong_answers', 
        'score_percentage', 'certificate_level'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}