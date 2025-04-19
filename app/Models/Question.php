<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'questions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'survey_id',
        'question_text',
        'question_type',
        'options',
        'is_required',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'json',
        'is_required' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the survey that owns the question.
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Get the responses for the question.
     */
    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    /**
     * Question types available
     */
    public static function getTypes()
    {
        return [
            'text' => 'Short Text',
            'textarea' => 'Long Text',
            'number' => 'Number',
            'select' => 'Dropdown',
            'radio' => 'Multiple Choice (Single Answer)',
            'checkbox' => 'Multiple Choice (Multiple Answers)',
            'date' => 'Date',
            'rating' => 'Rating',
        ];
    }

    /**
     * Check if question has options
     */
    public function hasOptions()
    {
        return in_array($this->question_type, ['select', 'radio', 'checkbox']);
    }

    /**
     * Get response from an alumni
     */
    public function getResponseFrom($alumniId)
    {
        return $this->responses()
            ->where('alumni_id', $alumniId)
            ->first();
    }
}