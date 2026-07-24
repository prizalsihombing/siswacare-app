<?php

namespace App\Http\Controllers;

use App\Models\Counseling;
use App\Models\Student;
use App\Models\Violation;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class CounselingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Counseling::with(['student.classModel', 'violation']);

        // Jika yang login adalah siswa, tampilkan data pelanggaran miliknya sendiri
        if ($user->role === 'siswa' || $user->role === 'student') {
            // Cari data siswa di tabel students di mana kolom user_id SAMA DENGAN id user yang sedang login
            $student = \App\Models\Student::where('user_id', $user->id)->first();
            
            if ($student) {
                $query->where('student_id', $student->id);
            } else {
                $query->where('student_id', 0); // Jika tidak ketemu, kosongkan tabel
            }
        } else {
            // Jika guru atau admin, filter berdasarkan kelas jika dipilih di dropdown
            if ($request->has('class_id') && $request->class_id != '') {
                $query->whereHas('student', function($q) use ($request) {
                    $q->where('class_id', $request->class_id);
                });
            }
        }

        $counselings = $query->paginate(10);
        $classes = \App\Models\ClassModel::all();

        return view('counselings.index', compact('counselings', 'classes'));
    }

    public function create()
    {
        if (auth()->user()->role === 'siswa' || auth()->user()->role === 'student') {
            abort(403);
        }

        $students = Student::with(['classModel', 'violations'])
            ->get()
            ->filter(function($student) {
                $totalPoints = $student->violations->sum('points');
                return $totalPoints >= 50 && $totalPoints <= 99;
            });

        return view('counselings.create', compact('students'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role === 'siswa' || auth()->user()->role === 'student') {
            abort(403);
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'violation_id' => 'nullable|exists:violations,id',
            'date' => 'required|date',
            'time' => 'required',
        ]);

        Counseling::create([
            'student_id' => $request->student_id,
            'violation_id' => $request->violation_id,
            'user_id' => auth()->id(),
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'diproses', // Status otomatis saat pertama kali ditambahkan
        ]);

        return redirect()->route('counselings.index')->with('success', 'Jadwal Layanan BK berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (auth()->user()->role === 'siswa' || auth()->user()->role === 'student') {
            abort(403);
        }

        $counseling = Counseling::findOrFail($id);
        
        $students = Student::with(['classModel', 'violations'])
            ->get()
            ->filter(function($student) use ($counseling) {
                $totalPoints = $student->violations->sum('points');
                return ($totalPoints >= 50 && $totalPoints <= 99) || $student->id === $counseling->student_id;
            });

        return view('counselings.edit', compact('counseling', 'students'));
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role === 'siswa' || auth()->user()->role === 'student') {
            abort(403);
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'violation_id' => 'nullable|exists:violations,id',
            'date' => 'required|date',
            'time' => 'required',
        ]);

        $counseling = Counseling::findOrFail($id);
        $counseling->update([
            'student_id' => $request->student_id,
            'violation_id' => $request->violation_id,
            'date' => $request->date,
            'time' => $request->time,
        ]);

        return redirect()->route('counselings.index')->with('success', 'Jadwal Layanan BK berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (auth()->user()->role === 'siswa' || auth()->user()->role === 'student') {
            abort(403);
        }

        $counseling = Counseling::findOrFail($id);
        $counseling->delete();

        return redirect()->route('counselings.index')->with('success', 'Data Layanan BK berhasil dihapus.');
    }

    public function updateStatus(Request $request, $id)
    {
        if (auth()->user()->role === 'siswa' || auth()->user()->role === 'student') {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:diproses,tindak lanjut,selesai',
        ]);

        $counseling = Counseling::findOrFail($id);
        $counseling->update([
            'status' => $request->status
        ]);

        return redirect()->route('counselings.index')->with('success', 'Status layanan BK berhasil diperbarui.');
    }
}