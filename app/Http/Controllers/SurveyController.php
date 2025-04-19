<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Question;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SurveyController extends Controller
{
    /**
     * Display a listing of the surveys.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Admin melihat semua survei
        if (!Gate::allows('admin')) {
            $surveys = Survey::orderBy('created_at', 'desc')->paginate(10);
            return view('surveys.index', compact('surveys'));
        }
        
        // Alumni hanya melihat survei yang aktif
        $surveys = Survey::getActive();
        return view('surveys.index', compact('surveys'));
    }

    /**
     * Show the form for creating a new survey.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Hanya admin yang bisa membuat survei baru
        if (!Gate::allows('admin')) {
            return redirect()->route('surveys.index')
                ->with('error', 'Tidak memiliki izin untuk membuat survei.');
        }
        
        return view('surveys.create');
    }

    /**
     * Store a newly created survey in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Hanya admin yang bisa membuat survei baru
        if (!Gate::allows('admin')) {
            return redirect()->route('surveys.index')
                ->with('error', 'Tidak memiliki izin untuk membuat survei.');
        }
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'sometimes|boolean',
        ]);
        
        $validatedData['is_active'] = $request->has('is_active');
        
        $survey = Survey::create($validatedData);
        
        return redirect()->route('surveys.questions.create', $survey->id)
            ->with('success', 'Survei berhasil dibuat! Silakan tambahkan pertanyaan.');
    }

    /**
     * Display the specified survey.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function show(Survey $survey)
    {
        // Load questions untuk survei ini
        $survey->load('questions');
        
        // Hitung jumlah responden yang sudah mengisi survei ini
        $respondentsCount = Response::where('survey_id', $survey->id)
            ->select('alumni_id')
            ->distinct()
            ->count();
        
        // Jika user adalah alumni, cek apakah sudah mengisi survei atau belum
        $hasResponded = false;
        if (Gate::allows('alumni') && Auth::user()->alumni) {
            $hasResponded = $survey->isAnsweredBy(Auth::user()->alumni->id);
        }
        
        return view('surveys.show', compact('survey', 'respondentsCount', 'hasResponded'));
    }

    /**
     * Show the form for editing the specified survey.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function edit(Survey $survey)
    {
        // Hanya admin yang bisa mengedit survei
        if (!Gate::allows('admin')) {
            return redirect()->route('surveys.index')
                ->with('error', 'Tidak memiliki izin untuk mengedit survei.');
        }
        
        return view('surveys.edit', compact('survey'));
    }

    /**
     * Update the specified survey in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Survey $survey)
    {
        // Hanya admin yang bisa mengedit survei
        if (!Gate::allows('admin')) {
            return redirect()->route('surveys.index')
                ->with('error', 'Tidak memiliki izin untuk mengedit survei.');
        }
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'sometimes|boolean',
        ]);
        
        $validatedData['is_active'] = $request->has('is_active');
        
        $survey->update($validatedData);
        
        return redirect()->route('surveys.show', $survey->id)
            ->with('success', 'Survei berhasil diperbarui!');
    }

    /**
     * Remove the specified survey from storage.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function destroy(Survey $survey)
    {
        // Hanya admin yang bisa menghapus survei
        if (!Gate::allows('admin')) {
            return redirect()->route('surveys.index')
                ->with('error', 'Tidak memiliki izin untuk menghapus survei.');
        }
        
        // Hapus semua data terkait survei ini (questions dan responses)
        DB::transaction(function () use ($survey) {
            // Questions dan responses akan dihapus cascade karena foreign key constraint
            $survey->delete();
        });
        
        return redirect()->route('surveys.index')
            ->with('success', 'Survei berhasil dihapus!');
    }
    
    /**
     * Show the survey form to be filled by alumni.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function fill(Survey $survey)
    {
        // Cek apakah survei aktif
        if (!$survey->isActive()) {
            return redirect()->route('surveys.index')
                ->with('error', 'Survei tidak aktif atau sudah berakhir.');
        }
        
        // Cek apakah user adalah alumni
        if (Gate::allows('alumni') && Auth::user()->alumni) {
            return redirect()->route('surveys.index')
                ->with('error', 'Hanya alumni yang dapat mengisi survei.');
        }
        
        $alumni = Auth::user()->alumni;
        
        // Cek apakah alumni sudah pernah mengisi survei ini
        if ($survey->isAnsweredBy($alumni->id)) {
            return redirect()->route('surveys.index')
                ->with('error', 'Anda sudah mengisi survei ini sebelumnya.');
        }
        
        // Load pertanyaan survei
        $questions = $survey->questions;
        
        return view('surveys.fill', compact('survey', 'questions'));
    }
    
    /**
     * Submit survey responses.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function submit(Request $request, Survey $survey)
    {
        // Cek apakah survei aktif
        if (!$survey->isActive()) {
            return redirect()->route('surveys.index')
                ->with('error', 'Survei tidak aktif atau sudah berakhir.');
        }
        
        // Cek apakah user adalah alumni
        if (Gate::allows('alumni') && Auth::user()->alumni) {
            return redirect()->route('surveys.index')
                ->with('error', 'Hanya alumni yang dapat mengisi survei.');
        }
        
        $alumni = Auth::user()->alumni;
        
        // Cek apakah alumni sudah pernah mengisi survei ini
        if ($survey->isAnsweredBy($alumni->id)) {
            return redirect()->route('surveys.index')
                ->with('error', 'Anda sudah mengisi survei ini sebelumnya.');
        }
        
        // Ambil semua pertanyaan dalam survei
        $questions = $survey->questions;
        
        // Validasi semua jawaban yang required
        $validationRules = [];
        foreach ($questions as $question) {
            if ($question->is_required) {
                $validationRules['answers.' . $question->id] = 'required';
            }
        }
        
        $validated = $request->validate($validationRules);
        
        // Simpan semua jawaban
        DB::transaction(function () use ($request, $survey, $questions, $alumni) {
            foreach ($questions as $question) {
                // Jika pertanyaan tidak dijawab (opsional) maka skip
                if (!isset($request->answers[$question->id])) {
                    continue;
                }
                
                $answer = $request->answers[$question->id];
                
                // Format jawaban berdasarkan tipe pertanyaan
                if ($question->question_type === 'checkbox' && is_array($answer)) {
                    $answer = json_encode($answer);
                }
                
                Response::create([
                    'alumni_id' => $alumni->id,
                    'survey_id' => $survey->id,
                    'question_id' => $question->id,
                    'answer' => $answer,
                ]);
            }
        });
        
        return redirect()->route('surveys.index')
            ->with('success', 'Terima kasih telah mengisi survei!');
    }
    
    /**
     * View survey results (admin only).
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function results(Survey $survey)
    {
        // Hanya admin yang bisa melihat hasil survei
        if (Gate::denies('admin')) {
            return redirect()->route('surveys.index')
                ->with('error', 'Tidak memiliki izin untuk melihat hasil survei.');
        }
        
        // Load semua pertanyaan dan jawaban
        $survey->load(['questions', 'responses.alumni']);
        
        // Hitung jumlah responden
        $respondentsCount = Response::where('survey_id', $survey->id)
            ->select('alumni_id')
            ->distinct()
            ->count();
        
        // Siapkan data hasil untuk setiap pertanyaan
        $results = [];
        foreach ($survey->questions as $question) {
            $responses = $survey->responses()->where('question_id', $question->id)->get();
            
            // Format hasil berdasarkan tipe pertanyaan
            $questionResult = [
                'question' => $question,
                'responses' => $responses,
                'count' => $responses->count(),
            ];
            
            // Untuk pertanyaan pilihan, hitung distribusi jawaban
            if (in_array($question->question_type, ['radio', 'select', 'checkbox'])) {
                $distribution = [];
                
                foreach ($responses as $response) {
                    if ($question->question_type === 'checkbox') {
                        $selectedOptions = json_decode($response->answer, true);
                        if (is_array($selectedOptions)) {
                            foreach ($selectedOptions as $option) {
                                if (!isset($distribution[$option])) {
                                    $distribution[$option] = 0;
                                }
                                $distribution[$option]++;
                            }
                        }
                    } else {
                        if (!isset($distribution[$response->answer])) {
                            $distribution[$response->answer] = 0;
                        }
                        $distribution[$response->answer]++;
                    }
                }
                
                $questionResult['distribution'] = $distribution;
            }
            
            $results[] = $questionResult;
        }
        
        return view('surveys.results', compact('survey', 'results', 'respondentsCount'));
    }
}