<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'title',
        'level',
        'rank',
        'date',
        'bukti',
        'description',
    ];

    // Relasi ke Model Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}