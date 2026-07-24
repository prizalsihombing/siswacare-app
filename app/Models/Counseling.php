<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counseling extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'violation_id',
        'user_id',
        'date',
        'time',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function violation()
    {
        return $this->belongsTo(Violation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}