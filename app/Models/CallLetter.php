<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'problem',
        'date',
        'time',
        'letter_content',
        'status',
        'notes',
    ];

    // Relasi ke tabel Siswa
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}