<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClusterResult extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cluster_results';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cluster_name',
        'description',
        'parameters',
        'results',
        'visualization_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'parameters' => 'json',
        'results' => 'json',
        'visualization_data' => 'json',
    ];

    /**
     * Get cluster summary statistics
     */
    public function getSummaryAttribute()
    {
        $results = $this->results;
        
        if (empty($results) || !isset($results['clusters'])) {
            return null;
        }
        
        $summary = [];
        
        foreach ($results['clusters'] as $clusterId => $cluster) {
            $summary[$clusterId] = [
                'count' => count($cluster['members'] ?? []),
                'centroid' => $cluster['centroid'] ?? null,
            ];
        }
        
        return $summary;
    }

    /**
     * Get alumni in a specific cluster
     */
    public function getAlumniInCluster($clusterId)
    {
        $results = $this->results;
        
        if (empty($results) || !isset($results['clusters'][$clusterId]['members'])) {
            return collect();
        }
        
        $alumniIds = $results['clusters'][$clusterId]['members'];
        return Alumni::whereIn('id', $alumniIds)->get();
    }

    /**
     * Get all alumni with their cluster assignments
     */
    public function getAllAlumniWithClusters()
    {
        $results = $this->results;
        $clusterMap = [];
        
        if (empty($results) || !isset($results['clusters'])) {
            return collect();
        }
        
        foreach ($results['clusters'] as $clusterId => $cluster) {
            foreach ($cluster['members'] as $alumniId) {
                $clusterMap[$alumniId] = $clusterId;
            }
        }
        
        $alumni = Alumni::whereIn('id', array_keys($clusterMap))->get();
        
        return $alumni->map(function ($alumnus) use ($clusterMap) {
            $alumnus->cluster_id = $clusterMap[$alumnus->id] ?? null;
            return $alumnus;
        });
    }

    /**
     * Get the latest cluster result
     */
    public static function getLatest()
    {
        return self::latest()->first();
    }
}