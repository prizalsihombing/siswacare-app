<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    // Tambahkan baris ini agar semua kolom diizinkan untuk disimpan
    protected $fillable = [
        'user_id',
        'nisn',
        'name',
        'class_id',
        'gender',
        'guardian_phone',
        'status',
    ];

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Tambahkan relasi ini untuk rekapitulasi absensi
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id', 'id');
    }

    // Tambahkan relasi ini untuk data prestasi siswa
    public function achievements()
    {
        return $this->hasMany(Achievement::class, 'student_id', 'id');
    }

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }

    // Fungsi helper untuk menghitung total poin siswa secara otomatis
    public function getTotalPointsAttribute()
    {
        return $this->violations()->sum('points');
    }
}