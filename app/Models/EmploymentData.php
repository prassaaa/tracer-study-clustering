<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentData extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employment_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'alumni_id',
        'company_name',
        'position',
        'industry',
        'salary',
        'waiting_period',
        'is_relevant',
        'start_date',
        'end_date',
        'is_current_job',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'salary' => 'decimal:2',
        'waiting_period' => 'integer',
        'is_relevant' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current_job' => 'boolean',
    ];

    /**
     * Get the alumni that owns the employment data.
     */
    public function alumni()
    {
        return $this->belongsTo(Alumni::class);
    }

    /**
     * Get available industry options
     */
    public static function getIndustryOptions()
    {
        return [
            'technology' => 'Information Technology',
            'healthcare' => 'Healthcare',
            'education' => 'Education',
            'finance' => 'Finance & Banking',
            'manufacturing' => 'Manufacturing',
            'retail' => 'Retail',
            'government' => 'Government',
            'media' => 'Media & Entertainment',
            'consulting' => 'Consulting',
            'construction' => 'Construction',
            'transportation' => 'Transportation',
            'agriculture' => 'Agriculture',
            'energy' => 'Energy',
            'hospitality' => 'Hospitality & Tourism',
            'telecom' => 'Telecommunications',
            'other' => 'Other',
        ];
    }

    /**
     * Get employment duration in months
     */
    public function getDurationInMonthsAttribute()
    {
        $endDate = $this->is_current_job ? now() : $this->end_date;
        
        if (!$endDate) {
            return 0;
        }
        
        return $this->start_date->diffInMonths($endDate);
    }

    /**
     * Format salary for display
     */
    public function getFormattedSalaryAttribute()
    {
        return 'Rp ' . number_format($this->salary, 0, ',', '.');
    }

    /**
     * Get data for clustering by alumni ID
     */
    public static function getDataForClustering($alumniIds = [])
    {
        $query = self::query()
                ->with('alumni')
                ->where('is_current_job', true);
                
        if (!empty($alumniIds)) {
            $query->whereIn('alumni_id', $alumniIds);
        }
        
        return $query->get();
    }
}