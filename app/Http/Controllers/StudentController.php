<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    // Menampilkan daftar siswa dengan fitur pencarian dan filter kelas
    public function index(Request $request)
    {
        $query = Student::with(['classModel', 'user']);

        // Fitur Pencarian berdasarkan Nama atau NISN
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Fitur Filter berdasarkan Kelas
        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }

        $students = $query->paginate(10);
        $classes = ClassModel::all();

        return view('admin.students.index', compact('students', 'classes'));
    }

    // Menyimpan data siswa baru dan membuat akun login otomatis
    public function store(Request $request)
    {
        // 1. Validasi input dari form
        $request->validate([
            'nisn' => 'required|unique:students,nisn',
            'name' => 'required|string|max:255',
            'class_id' => 'required',
            'gender' => 'required',
            'status' => 'required',
        ]);

        // 2. Buat akun User (Tanpa email, NISN sebagai username & password)
        $user = User::create([
            'name' => $request->name,
            'username' => $request->nisn, // NISN sebagai username
            'password' => Hash::make($request->nisn), // NISN sebagai password default yang di-hash
            'role' => 'siswa', // Role otomatis siswa
        ]);

        // 3. Simpan data ke tabel students
        Student::create([
            'user_id' => $user->id,
            'nisn' => $request->nisn,
            'name' => $request->name,
            'class_id' => $request->class_id,
            'gender' => $request->gender,
            'guardian_phone' => $request->guardian_phone,
            'status' => $request->status,
        ]);

        // 4. Kembali dengan pesan sukses
        return redirect()->route('admin.students.index')->with('success', 'Data siswa dan akun berhasil ditambahkan!');
    }

    // Mengupdate data siswa
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'nisn' => 'required|unique:students,nisn,' . $student->id,
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'gender' => 'required|in:L,P',
            'guardian_phone' => 'nullable|string|max:20',
            'status' => 'required|in:Aktif,Keluar',
        ]);

        // Jika status diubah menjadi Keluar, sistem menghapus siswa & akun otomatis (sesuai request)
        if ($request->status == 'Keluar') {
            $student->user->delete(); // Hapus akun user
            $student->delete();       // Hapus data siswa
            return redirect()->back()->with('success', 'Siswa berstatus Keluar, data dan akun telah dihapus otomatis oleh sistem.');
        }

        // Update data biasa jika status Aktif
        $student->update([
            'class_id' => $request->class_id,
            'nisn' => $request->nisn,
            'name' => $request->name,
            'gender' => $request->gender,
            'guardian_phone' => $request->guardian_phone,
            'status' => $request->status,
        ]);

        // Update juga nama/username di tabel users jika berubah
        $student->user->update([
            'name' => $request->name,
            'username' => $request->nisn,
        ]);

        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui!');
    }

    // Hapus data siswa manual
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        
        // Hapus akun user terkait secara otomatis
        if ($student->user) {
            $student->user->delete();
        }
        
        $student->delete();

        return redirect()->back()->with('success', 'Data siswa beserta akun loginnya berhasil dihapus.');
    }
}