<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassModel; // Panggil model ClassModel yang sudah kita buat

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar kelas yang ingin dimasukkan
        $classes = [
            'X - A',
            'X - B',
            'XI - A',
            'XI - B',
            'XII - A',
            'XII - B',
        ];

        // Looping untuk memasukkan data ke database (mencegah duplikat jika dijalankan 2x)
        foreach ($classes as $className) {
            ClassModel::firstOrCreate(['name' => $className]);
        }
    }
}