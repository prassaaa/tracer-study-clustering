<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\Survey;
use App\Models\EmploymentData;
use App\Models\ClusterResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Cek role user
        if (Gate::allows('admin')) {
            return $this->adminDashboard();
        } else {
            return $this->alumniDashboard();
        }
    }

    /**
     * Display admin dashboard
     */
    private function adminDashboard()
    {
        // Statistik dasar
        $totalAlumni = Alumni::count();
        $totalSurveys = Survey::count();
        $activeSurveys = Survey::where('is_active', true)->count();
        
        // Statistik karir
        $employmentData = EmploymentData::where('is_current_job', true)->get();
        $totalEmployed = $employmentData->count();
        
        // Hitung rata-rata gaji jika data tersedia
        $averageSalary = 0;
        if ($totalEmployed > 0) {
            $averageSalary = $employmentData->avg('salary');
        }
        
        // Distribusi industri
        $industryDistribution = $employmentData->groupBy('industry')->map->count();
        
        // Rata-rata waktu tunggu kerja
        $averageWaitingPeriod = $employmentData->avg('waiting_period');
        
        // Ambil hasil clustering terbaru jika ada
        $latestCluster = ClusterResult::latest()->first();
        
        return view('admin.dashboard', compact(
            'totalAlumni', 
            'totalSurveys', 
            'activeSurveys', 
            'totalEmployed', 
            'averageSalary', 
            'industryDistribution', 
            'averageWaitingPeriod',
            'latestCluster'
        ));
    }

    /**
     * Display alumni dashboard
     */
    private function alumniDashboard()
    {
        // Dapatkan data alumni yang sedang login
        $alumni = Auth::user()->alumni;
        
        if (!$alumni) {
            // Jika belum memiliki profil alumni, arahkan ke halaman pembuatan profil
            return redirect()->route('alumni.create')
                ->with('message', 'Silakan lengkapi profil alumni Anda terlebih dahulu.');
        }
        
        // Dapatkan survei yang aktif dan belum dijawab
        $pendingSurveys = Survey::getActive()
            ->filter(function($survey) use ($alumni) {
                return !$survey->isAnsweredBy($alumni->id);
            });
        
        // Ambil data pekerjaan terkini
        $currentJob = $alumni->currentJob();
        
        // Dapatkan survei yang sudah dijawab
        $completedSurveys = Survey::whereHas('responses', function($query) use ($alumni) {
            $query->where('alumni_id', $alumni->id);
        })->get();
        
        return view('alumni.dashboard', compact(
            'alumni', 
            'pendingSurveys', 
            'currentJob', 
            'completedSurveys'
        ));
    }
}