<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\EmploymentData;
use App\Models\ClusterResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /**
     * Get alumni data for Python API.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAlumniData()
    {
        // Dapatkan semua data pekerjaan terkini dari alumni
        $employmentData = EmploymentData::where('is_current_job', true)
            ->with('alumni')
            ->get()
            ->map(function ($data) {
                return [
                    'id' => $data->alumni_id,
                    'nim' => $data->alumni->nim ?? 'Unknown',
                    'name' => $data->alumni->full_name ?? 'Unknown',
                    'graduation_year' => $data->alumni->graduation_year ?? 0,
                    'major' => $data->alumni->major ?? 'Unknown',
                    'company_name' => $data->company_name,
                    'position' => $data->position,
                    'industry' => $data->industry,
                    'salary' => $data->salary ?? 0,
                    'waiting_period' => $data->waiting_period ?? 0,
                    'is_relevant' => $data->is_relevant ? 1 : 0,
                ];
            });
            
        // Filter data yang tidak lengkap
        $employmentData = $employmentData->filter(function ($data) {
            return $data['id'] !== null;
        })->values();
        
        return response()->json([
            'data' => $employmentData,
            'count' => $employmentData->count(),
        ]);
    }
    
    /**
     * Save cluster results from Python API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveClusterResults(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'clusters' => 'required|array',
            'parameters' => 'required|array',
            'visualization' => 'nullable|array',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        try {
            // Simpan hasil clustering ke database
            $result = ClusterResult::create([
                'cluster_name' => 'Analisis ' . date('Y-m-d H:i:s'),
                'description' => 'Single Linkage Clustering untuk data alumni',
                'parameters' => $request->parameters,
                'results' => [
                    'clusters' => $request->clusters,
                ],
                'visualization_data' => $request->visualization ?? null,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Cluster results saved successfully',
                'id' => $result->id,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save cluster results',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Get cluster results by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getClusterResult($id)
    {
        $clusterResult = ClusterResult::find($id);
        
        if (!$clusterResult) {
            return response()->json([
                'success' => false,
                'message' => 'Cluster result not found',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $clusterResult,
        ]);
    }
    
    /**
     * Get the latest cluster result.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLatestClusterResult()
    {
        $clusterResult = ClusterResult::latest()->first();
        
        if (!$clusterResult) {
            return response()->json([
                'success' => false,
                'message' => 'No cluster results found',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $clusterResult,
        ]);
    }
}