<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'responses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'alumni_id',
        'survey_id',
        'question_id',
        'answer',
    ];

    /**
     * Get the alumni that owns the response.
     */
    public function alumni()
    {
        return $this->belongsTo(Alumni::class);
    }

    /**
     * Get the survey that owns the response.
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Get the question that owns the response.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Format the answer based on question type
     */
    public function getFormattedAnswerAttribute()
    {
        $question = $this->question;
        
        if (!$question) {
            return $this->answer;
        }

        switch ($question->question_type) {
            case 'checkbox':
                $selectedOptions = json_decode($this->answer, true);
                if (is_array($selectedOptions)) {
                    return implode(', ', $selectedOptions);
                }
                return $this->answer;
            
            case 'select':
            case 'radio':
                return $this->answer;
                
            case 'rating':
                return $this->answer . ' / 5';
                
            default:
                return $this->answer;
        }
    }

    /**
     * Get responses for a specific alumni and survey
     */
    public static function getAlumniSurveyResponses($alumniId, $surveyId)
    {
        return self::where('alumni_id', $alumniId)
                ->where('survey_id', $surveyId)
                ->get();
    }
}