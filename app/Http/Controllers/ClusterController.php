<?php

namespace App\Http\Controllers;

use App\Models\ClusterResult;
use App\Models\EmploymentData;
use App\Models\Alumni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class ClusterController extends Controller
{
    /**
     * Python API URL untuk clustering
     */
    protected $pythonApiUrl;
    
    public function __construct()
    {
        // URL API Python diambil dari config atau env
        $this->pythonApiUrl = config('services.python_api.url', 'http://localhost:5000');
    }
    
    /**
     * Show the clustering analysis form.
     *
     * @return \Illuminate\Http\Response
     */
    public function analyze()
    {
        // Hanya admin yang bisa melakukan analisis clustering
        if (Gate::allows('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak memiliki izin untuk mengakses halaman ini.');
        }
        
        // Dapatkan jumlah alumni yang memiliki data pekerjaan
        $alumniCount = EmploymentData::where('is_current_job', true)
            ->distinct('alumni_id')
            ->count('alumni_id');
            
        // Jumlah minimal alumni untuk clustering
        $minAlumniForClustering = 5;
        
        // Periksa jika jumlah alumni cukup untuk clustering
        $canCluster = $alumniCount >= $minAlumniForClustering;
        
        // Dapatkan hasil clustering terbaru
        $latestClusterResult = ClusterResult::latest()->first();
        
        return view('clustering.analyze', compact('alumniCount', 'canCluster', 'minAlumniForClustering', 'latestClusterResult'));
    }
    
    /**
     * Process clustering using Python API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function process(Request $request)
    {
        // Hanya admin yang bisa melakukan analisis clustering
        if (Gate::allows('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak memiliki izin untuk melakukan analisis clustering.');
        }
        
        // Validasi input
        $validatedData = $request->validate([
            'variables' => 'required|array',
            'variables.*' => 'in:salary,waiting_period,is_relevant',
            'distance_metric' => 'required|in:euclidean,manhattan,cosine',
            'threshold' => 'required|numeric|min:0',
        ]);
        
        try {
            // Persiapkan data untuk dikirim ke API Python
            $employmentData = EmploymentData::where('is_current_job', true)
                ->with('alumni')
                ->get()
                ->map(function ($data) {
                    return [
                        'id' => $data->alumni_id,
                        'salary' => $data->salary ?? 0,
                        'waiting_period' => $data->waiting_period ?? 0,
                        'is_relevant' => $data->is_relevant ? 1 : 0,
                        'industry' => $data->industry,
                        'name' => $data->alumni->full_name ?? 'Unknown',
                        'nim' => $data->alumni->nim ?? 'Unknown',
                    ];
                });
                
            // Filter data yang tidak lengkap
            $employmentData = $employmentData->filter(function ($data) {
                return $data['id'] !== null;
            })->values();
            
            if ($employmentData->isEmpty()) {
                return redirect()->route('clustering.analyze')
                    ->with('error', 'Tidak ada data yang cukup untuk analisis clustering.');
            }
            
            // Kirim request ke Python API
            $response = Http::timeout(60)->post($this->pythonApiUrl . '/cluster', [
                'data' => $employmentData->toArray(),
                'variables' => $validatedData['variables'],
                'distance_metric' => $validatedData['distance_metric'],
                'threshold' => $validatedData['threshold'],
            ]);
            
            if (!$response->successful()) {
                Log::error('Python API Error: ' . $response->body());
                return redirect()->route('clustering.analyze')
                    ->with('error', 'Gagal menghubungi API Python: ' . $response->status());
            }
            
            $clusterResult = $response->json();
            
            if (!isset($clusterResult['clusters'])) {
                return redirect()->route('clustering.analyze')
                    ->with('error', 'Format respons API tidak valid.');
            }
            
            // Simpan hasil clustering ke database
            $result = ClusterResult::create([
                'cluster_name' => 'Analisis ' . date('Y-m-d H:i:s'),
                'description' => 'Single Linkage Clustering untuk data alumni',
                'parameters' => [
                    'variables' => $validatedData['variables'],
                    'distance_metric' => $validatedData['distance_metric'],
                    'threshold' => $validatedData['threshold'],
                ],
                'results' => $clusterResult,
                'visualization_data' => $clusterResult['visualization'] ?? null,
            ]);
            
            return redirect()->route('clustering.results', $result->id)
                ->with('success', 'Analisis clustering berhasil dilakukan!');
            
        } catch (\Exception $e) {
            Log::error('Clustering Error: ' . $e->getMessage());
            return redirect()->route('clustering.analyze')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Show clustering results.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Hanya admin yang bisa melihat hasil clustering
        if (Gate::allows('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak memiliki izin untuk melihat hasil clustering.');
        }
        
        $clusterResult = ClusterResult::findOrFail($id);
        
        // Jika tidak ada hasil, redirect ke halaman analyze
        if (!$clusterResult || empty($clusterResult->results)) {
            return redirect()->route('clustering.analyze')
                ->with('error', 'Hasil clustering tidak ditemukan.');
        }
        
        // Dapatkan semua alumni dengan cluster masing-masing
        $alumniWithClusters = $clusterResult->getAllAlumniWithClusters();
        
        // Hitung statistik per cluster
        $clusterStats = [];
        $results = $clusterResult->results;
        
        if (isset($results['clusters'])) {
            foreach ($results['clusters'] as $clusterId => $cluster) {
                $clusterMembers = $alumniWithClusters->filter(function ($alumni) use ($clusterId) {
                    return $alumni->cluster_id == $clusterId;
                });
                
                $employmentData = EmploymentData::whereIn('alumni_id', $clusterMembers->pluck('id'))
                    ->where('is_current_job', true)
                    ->get();
                
                $clusterStats[$clusterId] = [
                    'count' => $clusterMembers->count(),
                    'avg_salary' => $employmentData->avg('salary') ?? 0,
                    'avg_waiting_period' => $employmentData->avg('waiting_period') ?? 0,
                    'relevance_rate' => $employmentData->where('is_relevant', true)->count() / max(1, $employmentData->count()) * 100,
                    'industry_distribution' => $employmentData->groupBy('industry')->map->count(),
                    'members' => $clusterMembers,
                ];
            }
        }
        
        // Dapatkan data untuk visualisasi
        $visualizationData = $clusterResult->visualization_data;
        
        return view('clustering.results', compact('clusterResult', 'clusterStats', 'visualizationData'));
    }
    
    /**
     * Compare multiple clustering results.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function compare(Request $request)
    {
        // Hanya admin yang bisa membandingkan hasil clustering
        if (Gate::allows('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak memiliki izin untuk membandingkan hasil clustering.');
        }
        
        // Validasi input
        $validatedData = $request->validate([
            'cluster_ids' => 'required|array',
            'cluster_ids.*' => 'exists:cluster_results,id',
        ]);
        
        // Ambil hasil clustering yang dipilih
        $clusterResults = ClusterResult::whereIn('id', $validatedData['cluster_ids'])->get();
        
        if ($clusterResults->count() < 2) {
            return redirect()->route('clustering.analyze')
                ->with('error', 'Pilih minimal 2 hasil clustering untuk dibandingkan.');
        }
        
        // Siapkan data perbandingan
        $comparisonData = [];
        
        foreach ($clusterResults as $result) {
            $clusters = $result->results['clusters'] ?? [];
            $clusterCount = count($clusters);
            
            // Hitung rata-rata anggota per cluster
            $totalMembers = 0;
            foreach ($clusters as $cluster) {
                $totalMembers += count($cluster['members'] ?? []);
            }
            $avgMembersPerCluster = $clusterCount > 0 ? $totalMembers / $clusterCount : 0;
            
            $comparisonData[] = [
                'id' => $result->id,
                'name' => $result->cluster_name,
                'date' => $result->created_at->format('Y-m-d H:i:s'),
                'cluster_count' => $clusterCount,
                'total_members' => $totalMembers,
                'avg_members_per_cluster' => $avgMembersPerCluster,
                'parameters' => $result->parameters,
            ];
        }
        
        return view('clustering.compare', compact('clusterResults', 'comparisonData'));
    }
    
    /**
     * Export clustering results to Excel.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function export($id)
    {
        // Hanya admin yang bisa mengekspor hasil clustering
        if (Gate::allows('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak memiliki izin untuk mengekspor hasil clustering.');
        }
        
        $clusterResult = ClusterResult::findOrFail($id);
        
        // Jika tidak ada hasil, redirect ke halaman analyze
        if (!$clusterResult || empty($clusterResult->results)) {
            return redirect()->route('clustering.analyze')
                ->with('error', 'Hasil clustering tidak ditemukan.');
        }
        
        // Dapatkan semua alumni dengan cluster masing-masing
        $alumniWithClusters = $clusterResult->getAllAlumniWithClusters();
        
        // Prepare data for export
        $exportData = [];
        
        // Header row
        $exportData[] = [
            'NIM', 
            'Nama Alumni', 
            'Tahun Lulus', 
            'Jurusan', 
            'Cluster', 
            'Perusahaan', 
            'Posisi', 
            'Industri', 
            'Gaji', 
            'Waktu Tunggu (bulan)', 
            'Relevan dengan Jurusan'
        ];
        
        // Data rows
        foreach ($alumniWithClusters as $alumni) {
            $employmentData = EmploymentData::where('alumni_id', $alumni->id)
                ->where('is_current_job', true)
                ->first();
                
            if ($employmentData) {
                $exportData[] = [
                    $alumni->nim,
                    $alumni->full_name,
                    $alumni->graduation_year,
                    $alumni->major,
                    'Cluster ' . ($alumni->cluster_id + 1), // +1 untuk index 0-based
                    $employmentData->company_name,
                    $employmentData->position,
                    $employmentData->industry,
                    $employmentData->salary,
                    $employmentData->waiting_period,
                    $employmentData->is_relevant ? 'Ya' : 'Tidak',
                ];
            }
        }
        
        // Ekspor ke Excel
        return Excel::download(
            new \App\Exports\ClusterExport($exportData),
            'cluster_results_' . $id . '.xlsx'
        );
    }
}