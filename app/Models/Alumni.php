<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumni extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'alumni';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'nim',
        'full_name',
        'graduation_year',
        'major',
        'phone',
        'address',
    ];

    /**
     * Get the user that owns the alumni profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the responses for the alumni.
     */
    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    /**
     * Get the employment data for the alumni.
     */
    public function employmentData()
    {
        return $this->hasMany(EmploymentData::class);
    }

    /**
     * Get the current job of the alumni.
     */
    public function currentJob()
    {
        return $this->employmentData()->where('is_current_job', true)->first();
    }

    /**
     * Get the alumni by user ID.
     */
    public static function findByUserId($userId)
    {
        return self::where('user_id', $userId)->first();
    }
}