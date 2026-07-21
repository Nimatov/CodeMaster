<?php

namespace App\Http\Controllers;

use App\Models\TestResult;
use App\Models\User;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Exports\ResultsExport;  // <-- ДОБАВЬ ЭТО!

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $users = User::all();
        $results = TestResult::with('user')->get();
        
        $stats = [];
        foreach ($users as $user) {
            $userResults = $results->where('user_id', $user->id);
            
            $stats[$user->id] = [
                'total_tests' => $userResults->count(),
                'total_correct' => $userResults->sum('correct_answers'),
                'total_wrong' => $userResults->sum('wrong_answers'),
                'avg_percentage' => $userResults->avg('score_percentage') ?? 0,
                'avg_time' => $userResults->avg('time_spent') ?? 0,
                'avg_time_formatted' => $this->formatTime($userResults->avg('time_spent') ?? 0),
                'subjects' => []
            ];
            
            $subjects = ['Html', 'Css', 'Sql', 'Bootstrap', 'JavaScript', 'Laravel'];
            foreach ($subjects as $subject) {
                $subjectResults = $userResults->where('subject', $subject);
                $stats[$user->id]['subjects'][$subject] = [
                    'total' => $subjectResults->count(),
                    'correct' => $subjectResults->sum('correct_answers'),
                    'percentage' => $subjectResults->avg('score_percentage') ?? 0,
                    'best_score' => $subjectResults->max('score_percentage') ?? 0,
                    'avg_time' => $subjectResults->avg('time_spent') ?? 0,
                    'avg_time_formatted' => $this->formatTime($subjectResults->avg('time_spent') ?? 0),
                ];
            }
        }
        
        return view('admin.dashboard', compact('users', 'results', 'stats'));
    }

    // ===== ФОРМАТИРОВАНИЕ ВРЕМЕНИ =====
    public function formatTime($seconds)
    {
        if (!$seconds || $seconds <= 0) return '—';
        
        $minutes = floor($seconds / 60);
        $secs = $seconds % 60;
        
        if ($minutes > 0) {
            return $minutes . ' мин ' . $secs . ' сек';
        }
        return $secs . ' сек';
    }

    // ============================================================
    // УПРАВЛЕНИЕ ПОЛЬЗОВАТЕЛЯМИ
    // ============================================================
    
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id == Auth::id()) {
            return redirect()->back()->with('error', 'Вы не можете удалить самого себя!');
        }
        
        TestResult::where('user_id', $user->id)->delete();
        $user->delete();
        
        return redirect()->back()->with('success', 'Пользователь успешно удален!');
    }
    
    public function blockUser($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id == Auth::id()) {
            return redirect()->back()->with('error', 'Вы не можете заблокировать самого себя!');
        }
        
        $user->is_blocked = !$user->is_blocked;
        $user->save();
        
        $status = $user->is_blocked ? 'заблокирован' : 'разблокирован';
        return redirect()->back()->with('success', "Пользователь {$status}!");
    }
    
    public function userDetails($id)
    {
        $user = User::findOrFail($id);
        $results = TestResult::where('user_id', $user->id)->get();
        
        // Форматируем время для каждого результата
        foreach ($results as $result) {
            $result->time_formatted = $this->formatTime($result->time_spent);
        }
        
        return view('admin.user_details', compact('user', 'results'));
    }
    
    // ============================================================
    // УПРАВЛЕНИЕ ВОПРОСАМИ
    // ============================================================
    
    public function questions()
    {
        $questions = Question::all();
        return view('admin.questions', compact('questions'));
    }
    
    public function createQuestion()
    {
        return view('admin.create_question');
    }
    
    public function storeQuestion(Request $request)
    {
        $request->validate([
            'subject' => 'required|in:Html,Css,Sql,Bootstrap,JavaScript,Laravel',
            'question' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct' => 'required|integer|min:0|max:3',
        ]);
        
        Question::create([
            'subject' => $request->subject,
            'question' => $request->question,
            'options' => json_encode([
                $request->option_a,
                $request->option_b,
                $request->option_c,
                $request->option_d
            ]),
            'correct' => $request->correct,
        ]);
        
        return redirect()->route('admin.questions')->with('success', 'Вопрос добавлен!');
    }
    
    public function editQuestion($id)
    {
        $question = Question::findOrFail($id);
        return view('admin.edit_question', compact('question'));
    }
    
    public function updateQuestion(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|in:Html,Css,Sql,Bootstrap,JavaScript,Laravel',
            'question' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct' => 'required|integer|min:0|max:3',
        ]);
        
        $question = Question::findOrFail($id);
        $question->update([
            'subject' => $request->subject,
            'question' => $request->question,
            'options' => json_encode([
                $request->option_a,
                $request->option_b,
                $request->option_c,
                $request->option_d
            ]),
            'correct' => $request->correct,
        ]);
        
        return redirect()->route('admin.questions')->with('success', 'Вопрос обновлен!');
    }
    
    // ============================================================
    // ЭКСПОРТ (ИСПРАВЛЕНО!)
    // ============================================================
    
    public function exportUsers()
    {
        return Excel::download(new UsersExport, 'users_' . date('Y-m-d') . '.xlsx');
    }
    
    public function exportResults()
    {
        // ИСПРАВЛЕНО: теперь использует ResultsExport
        return Excel::download(new ResultsExport, 'results_' . date('Y-m-d') . '.xlsx');
    }
    
    public function exportUsersCsv()
    {
        return Excel::download(new UsersExport, 'users_' . date('Y-m-d') . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }
    
    public function exportResultsCsv()
    {
        // ИСПРАВЛЕНО: теперь использует ResultsExport
        return Excel::download(new ResultsExport, 'results_' . date('Y-m-d') . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }
    
    public function deleteQuestion($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();
        
        return redirect()->route('admin.questions')->with('success', 'Вопрос удален!');
    }
}