<?php

namespace App\Http\Controllers;

use App\Models\Violation;
use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class ViolationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $role = $user->role;
        $classes = ClassModel::all();
        $classRoom = null; // Inisialisasi default agar tidak undefined

        $query = Violation::with(['student.classModel', 'user']);

        // JIKA ROLE SISWA
        if ($role === 'siswa' || $role === 'student') {
            $studentId = $user->student_id;

            if (!$studentId) {
                $student = Student::where('user_id', $user->id)
                            ->orWhere('nisn', $user->username)
                            ->first();
                $studentId = $student ? $student->id : null;
            }

            // Filter HANYA data pelanggaran milik siswa tersebut
            $query->where('student_id', $studentId)
                ->orderBy('date', 'desc');
        } 
        // JIKA ROLE ADMIN / TEACHER (GURU)
        else {
            // Ambil input class_id dari request secara murni (tanpa memaksa default ke kelas pertama)
            $classId = $request->input('class_id');

            // Jika class_id tidak kosong, filter berdasarkan kelas dan cari objek kelasnya
            if ($classId !== null && $classId !== '') {
                $query->whereHas('student', function($q) use ($classId) {
                    $q->where('class_id', $classId);
                });
                $classRoom = ClassModel::find($classId);
            }

            $query->join('students', 'violations.student_id', '=', 'students.id')
                ->orderBy('students.nisn', 'asc')
                ->select('violations.*');
        }

        $violations = $query->paginate(10)->withQueryString(); // withQueryString agar pagination tetap membawa parameter kelas

        return view('violations.index', compact('violations', 'classes', 'classRoom', 'role'));
    }

    public function create()
    {
        if (auth()->user()->role === 'siswa' || auth()->user()->role === 'student') {
            abort(403, 'Akses ditolak. Siswa tidak diizinkan menambah data.');
        }

        $students = Student::with('classModel')->orderBy('nisn', 'asc')->get();
        return view('violations.create', compact('students'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role === 'siswa' || auth()->user()->role === 'student') {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'category' => 'required|string',
            'violation_name' => 'required|string|max:255',
            'points' => 'required|integer|min:1',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        Violation::create([
            'student_id' => $request->student_id,
            'user_id' => auth()->id(),
            'category' => $request->category,
            'violation_name' => $request->violation_name,
            'points' => $request->points,
            'date' => $request->date,
            'description' => $request->description,
        ]);

        return redirect()->route('violations.index')->with('success', 'Data pelanggaran berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (auth()->user()->role === 'siswa' || auth()->user()->role === 'student') {
            abort(403, 'Akses ditolak.');
        }

        $violation = Violation::findOrFail($id);
        $students = Student::with('classModel')->orderBy('nisn', 'asc')->get();

        return view('violations.edit', compact('violation', 'students'));
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role === 'siswa' || auth()->user()->role === 'student') {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'category' => 'required|string',
            'violation_name' => 'required|string|max:255',
            'points' => 'required|integer|min:1',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $violation = Violation::findOrFail($id);
        $violation->update([
            'student_id' => $request->student_id,
            'category' => $request->category,
            'violation_name' => $request->violation_name,
            'points' => $request->points,
            'date' => $request->date,
            'description' => $request->description,
        ]);

        return redirect()->route('violations.index')->with('success', 'Data pelanggaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (auth()->user()->role === 'siswa' || auth()->user()->role === 'student') {
            abort(403, 'Akses ditolak.');
        }

        $violation = Violation::findOrFail($id);
        $violation->delete();

        return redirect()->route('violations.index')->with('success', 'Data pelanggaran berhasil dihapus.');
    }
}