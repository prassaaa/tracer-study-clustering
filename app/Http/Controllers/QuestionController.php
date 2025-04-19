<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class QuestionController extends Controller
{
    /**
     * Display a listing of the questions for a survey.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function index(Survey $survey)
    {
        // Hanya admin yang bisa melihat daftar pertanyaan
        if (!Gate::allows('admin')) {
            return redirect()->route('surveys.show', $survey->id)
                ->with('error', 'Tidak memiliki izin untuk melihat daftar pertanyaan.');
        }
        
        $questions = $survey->questions()->orderBy('order')->get();
        
        return view('questions.index', compact('survey', 'questions'));
    }

    /**
     * Show the form for creating a new question.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function create(Survey $survey)
    {
        // Hanya admin yang bisa membuat pertanyaan
        if (!Gate::allows('admin')) {
            return redirect()->route('surveys.show', $survey->id)
                ->with('error', 'Tidak memiliki izin untuk membuat pertanyaan.');
        }
        
        $questionTypes = Question::getTypes();
        
        return view('questions.create', compact('survey', 'questionTypes'));
    }

    /**
     * Store a newly created question in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Survey $survey)
    {
        // Hanya admin yang bisa membuat pertanyaan
        if (!Gate::allows('admin')) {
            return redirect()->route('surveys.show', $survey->id)
                ->with('error', 'Tidak memiliki izin untuk membuat pertanyaan.');
        }
        
        $validatedData = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|string|in:' . implode(',', array_keys(Question::getTypes())),
            'is_required' => 'sometimes|boolean',
            'options' => 'required_if:question_type,select,radio,checkbox|nullable|string',
        ]);
        
        // Hitung order terbesar + 1
        $maxOrder = $survey->questions()->max('order') ?? 0;
        
        // Format options dari textarea menjadi array
        if (in_array($validatedData['question_type'], ['select', 'radio', 'checkbox']) && !empty($validatedData['options'])) {
            $options = array_map('trim', explode("\n", $validatedData['options']));
            $validatedData['options'] = array_filter($options, function($option) {
                return !empty($option);
            });
        } else {
            $validatedData['options'] = null;
        }
        
        $validatedData['survey_id'] = $survey->id;
        $validatedData['order'] = $maxOrder + 1;
        $validatedData['is_required'] = $request->has('is_required');
        
        $question = Question::create($validatedData);
        
        return redirect()->route('surveys.questions.index', $survey->id)
            ->with('success', 'Pertanyaan berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified question.
     *
     * @param  \App\Models\Survey  $survey
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(Survey $survey, Question $question)
    {
        // Hanya admin yang bisa mengedit pertanyaan
        if (!Gate::allows('admin')) {
            return redirect()->route('surveys.show', $survey->id)
                ->with('error', 'Tidak memiliki izin untuk mengedit pertanyaan.');
        }
        
        // Pastikan pertanyaan memang milik survei ini
        if ($question->survey_id !== $survey->id) {
            return redirect()->route('surveys.questions.index', $survey->id)
                ->with('error', 'Pertanyaan tidak ditemukan dalam survei ini.');
        }
        
        $questionTypes = Question::getTypes();
        
        // Format options dari array ke string untuk textarea
        if (is_array($question->options)) {
            $formattedOptions = implode("\n", $question->options);
        } else {
            $formattedOptions = '';
        }
        
        return view('questions.edit', compact('survey', 'question', 'questionTypes', 'formattedOptions'));
    }

    /**
     * Update the specified question in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Survey  $survey
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Survey $survey, Question $question)
    {
        // Hanya admin yang bisa mengedit pertanyaan
        if (!Gate::allows('admin')) {
            return redirect()->route('surveys.show', $survey->id)
                ->with('error', 'Tidak memiliki izin untuk mengedit pertanyaan.');
        }
        
        // Pastikan pertanyaan memang milik survei ini
        if ($question->survey_id !== $survey->id) {
            return redirect()->route('surveys.questions.index', $survey->id)
                ->with('error', 'Pertanyaan tidak ditemukan dalam survei ini.');
        }
        
        $validatedData = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|string|in:' . implode(',', array_keys(Question::getTypes())),
            'is_required' => 'sometimes|boolean',
            'options' => 'required_if:question_type,select,radio,checkbox|nullable|string',
        ]);
        
        // Format options dari textarea menjadi array
        if (in_array($validatedData['question_type'], ['select', 'radio', 'checkbox']) && !empty($validatedData['options'])) {
            $options = array_map('trim', explode("\n", $validatedData['options']));
            $validatedData['options'] = array_filter($options, function($option) {
                return !empty($option);
            });
        } else {
            $validatedData['options'] = null;
        }
        
        $validatedData['is_required'] = $request->has('is_required');
        
        $question->update($validatedData);
        
        return redirect()->route('surveys.questions.index', $survey->id)
            ->with('success', 'Pertanyaan berhasil diperbarui!');
    }

    /**
     * Remove the specified question from storage.
     *
     * @param  \App\Models\Survey  $survey
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(Survey $survey, Question $question)
    {
        // Hanya admin yang bisa menghapus pertanyaan
        if (!Gate::allows('admin')) {
            return redirect()->route('surveys.show', $survey->id)
                ->with('error', 'Tidak memiliki izin untuk menghapus pertanyaan.');
        }
        
        // Pastikan pertanyaan memang milik survei ini
        if ($question->survey_id !== $survey->id) {
            return redirect()->route('surveys.questions.index', $survey->id)
                ->with('error', 'Pertanyaan tidak ditemukan dalam survei ini.');
        }
        
        $question->delete();
        
        return redirect()->route('surveys.questions.index', $survey->id)
            ->with('success', 'Pertanyaan berhasil dihapus!');
    }
    
    /**
     * Reorder questions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function reorder(Request $request, Survey $survey)
    {
        // Hanya admin yang bisa mengubah urutan pertanyaan
        if (!Gate::allows('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'questions' => 'required|array',
            'questions.*' => 'integer|exists:questions,id',
        ]);
        
        $questions = $request->questions;
        
        // Update urutan pertanyaan
        foreach ($questions as $index => $questionId) {
            Question::where('id', $questionId)
                ->where('survey_id', $survey->id)
                ->update(['order' => $index + 1]);
        }
        
        return response()->json(['success' => true]);
    }
}