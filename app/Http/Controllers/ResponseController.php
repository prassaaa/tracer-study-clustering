<?php

namespace App\Http\Controllers;

use App\Models\Response;
use App\Models\Survey;
use App\Models\Alumni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class ResponseController extends Controller
{
    /**
     * Display a listing of responses for a survey.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Hanya admin yang bisa melihat daftar jawaban
        if (!Gate::allows('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak memiliki izin untuk melihat jawaban.');
        }
        
        $surveyId = $request->query('survey_id');
        
        if ($surveyId) {
            $survey = Survey::findOrFail($surveyId);
            
            // Dapatkan semua alumni yang telah menjawab survei ini
            $respondents = DB::table('responses')
                ->where('survey_id', $surveyId)
                ->join('alumni', 'responses.alumni_id', '=', 'alumni.id')
                ->select('alumni.id', 'alumni.full_name', 'alumni.nim', DB::raw('COUNT(responses.id) as response_count'))
                ->groupBy('alumni.id', 'alumni.full_name', 'alumni.nim')
                ->paginate(15);
                
            return view('responses.index', compact('survey', 'respondents'));
        }
        
        // Jika tidak ada survey_id, tampilkan daftar survei
        $surveys = Survey::withCount(['responses' => function($query) {
            $query->select(DB::raw('count(distinct alumni_id)'));
        }])->paginate(10);
        
        return view('responses.surveys', compact('surveys'));
    }

    /**
     * Show the specified response.
     *
     * @param  int  $surveyId
     * @param  int  $alumniId
     * @return \Illuminate\Http\Response
     */
    public function show($surveyId, $alumniId)
    {
        // Hanya admin yang bisa melihat detail jawaban
        if (!Gate::allows('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak memiliki izin untuk melihat jawaban.');
        }
        
        $survey = Survey::findOrFail($surveyId);
        $alumni = Alumni::findOrFail($alumniId);
        
        // Dapatkan semua jawaban dari alumni untuk survei ini
        $responses = Response::where('survey_id', $surveyId)
            ->where('alumni_id', $alumniId)
            ->with('question')
            ->get();
            
        // Cek jika tidak ada jawaban
        if ($responses->isEmpty()) {
            return redirect()->route('responses.index', ['survey_id' => $surveyId])
                ->with('error', 'Alumni belum mengisi survei ini.');
        }
        
        return view('responses.show', compact('survey', 'alumni', 'responses'));
    }

    /**
     * Export responses to Excel.
     *
     * @param  int  $surveyId
     * @return \Illuminate\Http\Response
     */
    public function export($surveyId)
    {
        // Hanya admin yang bisa mengekspor jawaban
        if (!Gate::allows('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak memiliki izin untuk mengekspor jawaban.');
        }
        
        $survey = Survey::with(['questions' => function($query) {
            $query->orderBy('order');
        }])->findOrFail($surveyId);
        
        // Dapatkan semua alumni yang telah menjawab survei
        $respondents = DB::table('responses')
            ->where('survey_id', $surveyId)
            ->join('alumni', 'responses.alumni_id', '=', 'alumni.id')
            ->select('alumni.id', 'alumni.full_name', 'alumni.nim')
            ->groupBy('alumni.id', 'alumni.full_name', 'alumni.nim')
            ->get();
            
        // Siapkan data untuk ekspor
        $exportData = [];
        
        // Header row
        $headers = ['NIM', 'Nama'];
        foreach ($survey->questions as $question) {
            $headers[] = $question->question_text;
        }
        $exportData[] = $headers;
        
        // Data rows
        foreach ($respondents as $respondent) {
            $rowData = [$respondent->nim, $respondent->full_name];
            
            foreach ($survey->questions as $question) {
                $response = Response::where('survey_id', $surveyId)
                    ->where('alumni_id', $respondent->id)
                    ->where('question_id', $question->id)
                    ->first();
                    
                if ($response) {
                    $formattedAnswer = $response->formatted_answer;
                    $rowData[] = $formattedAnswer;
                } else {
                    $rowData[] = '';
                }
            }
            
            $exportData[] = $rowData;
        }
        
        // Ekspor ke Excel
        return Excel::download(
            new \App\Exports\SurveyExport($exportData),
            'survey_responses_' . $surveyId . '.xlsx'
        );
    }
    
    /**
     * Delete all responses for a specific alumni and survey.
     *
     * @param  int  $surveyId
     * @param  int  $alumniId
     * @return \Illuminate\Http\Response
     */
    public function destroy($surveyId, $alumniId)
    {
        // Hanya admin yang bisa menghapus jawaban
        if (!Gate::allows('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak memiliki izin untuk menghapus jawaban.');
        }
        
        Response::where('survey_id', $surveyId)
            ->where('alumni_id', $alumniId)
            ->delete();
            
        return redirect()->route('responses.index', ['survey_id' => $surveyId])
            ->with('success', 'Jawaban alumni berhasil dihapus!');
    }
}