<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nuptk',
        'name',
        'gender',
        'phone',
        'role_type',
        'role',
        'class_id',
        'subject',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}