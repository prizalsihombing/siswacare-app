<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    // Menentukan nama tabel secara eksplisit agar sesuai dengan database (tabel 'classes')
    protected $table = 'classes';

    protected $fillable = [
        'name', // Sesuaikan dengan kolom yang ada pada tabel classes Anda
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}