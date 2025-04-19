<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'surveys';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the questions for the survey.
     */
    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    /**
     * Get the responses for the survey.
     */
    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    /**
     * Get active surveys.
     */
    public static function getActive()
    {
        return self::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check if survey is active.
     */
    public function isActive()
    {
        return $this->is_active && 
               $this->start_date->lte(now()) && 
               $this->end_date->gte(now());
    }

    /**
     * Check if survey has been answered by an alumni.
     */
    public function isAnsweredBy($alumniId)
    {
        return $this->responses()->where('alumni_id', $alumniId)->exists();
    }
}