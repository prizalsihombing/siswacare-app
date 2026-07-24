<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Teacher;

class HomeroomAttendanceController extends Controller
{
    // 1. Halaman Utama / Input Absensi Harian
    public function index(Request $request)
    {
        $user = auth()->user();

        $classId = null;
        $canEdit = false;
        $isHomeroomOfThisClass = false;

        // Cek apakah user yang login adalah Siswa
        if ($user->role === 'student' || $user->role === 'siswa') { // Menangani 'student' atau 'siswa'
            $studentProfile = Student::where('user_id', $user->id)->first();
            
            if (!$studentProfile || !$studentProfile->class_id) {
                return redirect()->back()->with('error', 'Data kelas siswa tidak ditemukan.');
            }

            $classId = $studentProfile->class_id;
            $canEdit = false; 
            
            // PENTING: Batasi classes hanya 1 item agar jumlahnya tidak 6
            $classRoom = ClassModel::find($classId);
            $classes = collect([$classRoom]); 
        } else {
            $classes = ClassModel::all();
            $classId = $request->input('class_id', $classes->first()?->id);
            $teacher = Teacher::where('user_id', $user->id)->first();
            
            $isHomeroomOfThisClass = ($teacher && $teacher->class_id == $classId);
            $canEdit = ($user->role === 'admin') || $isHomeroomOfThisClass;
            
            $classRoom = ClassModel::find($classId);
        }

        if (!$classRoom) {
            return redirect()->back()->with('error', 'Data kelas tidak ditemukan.');
        }

        $date = $request->input('date', date('Y-m-d'));
        $students = Student::where('class_id', $classRoom->id)->orderBy('name', 'asc')->get();

        $attendances = Attendance::where('date', $date)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        return view('homeroom.attendances.index', compact(
            'classRoom', 
            'classes', 
            'students', 
            'attendances', 
            'date', 
            'isHomeroomOfThisClass', 
            'canEdit'
        ));
    }

    // 2. Proses Menyimpan Absensi
    public function store(Request $request)
    {
        $user = auth()->user();

        // Jika siswa mencoba mengakses proses store secara paksa, tolak
        if ($user->role === 'student') {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menyimpan atau mengubah absensi.');
        }

        $classId = $request->input('class_id');
        $teacher = Teacher::where('user_id', $user->id)->first();
        $canEdit = ($user->role === 'admin') || ($teacher && $teacher->class_id == $classId);

        if (!$canEdit) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk mengubah absensi kelas ini.');
        }

        $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.status' => 'required|in:Hadir,Izin,Sakit,Alfa',
            'attendances.*.description' => 'nullable|string|max:255',
        ]);

        $date = $request->input('date');

        foreach ($request->attendances as $studentId => $data) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date' => $date,
                ],
                [
                    'user_id' => auth()->id(),
                    'status' => $data['status'],
                    'description' => $data['description'] ?? null,
                ]
            );
        }

        return redirect()->route('homeroom.attendances.index', ['class_id' => $classId, 'date' => $date])
            ->with('success', 'Data absensi harian berhasil disimpan.');
    }

    // 3. Rekapitulasi Semester
    public function recapitulation(Request $request)
    {
        $user = auth()->user();

        // Siswa tidak diizinkan membuka halaman rekapitulasi semester
        if ($user->role === 'student') {
            return redirect()->route('homeroom.attendances.index')->with('error', 'Anda tidak memiliki akses ke halaman rekapitulasi.');
        }

        $classes = ClassModel::all();
        $classId = $request->input('class_id', null);

        if ($user->role === 'admin') {
            $classId = $classId ?? $classes->first()?->id;
        } else {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if (!$teacher || !$teacher->class_id) {
                return redirect()->back()->with('error', 'Anda tidak terdaftar sebagai wali kelas di kelas manapun.');
            }
            $classId = $teacher->class_id;
        }

        $classRoom = ClassModel::find($classId);
        $semester = $request->input('semester', 'ganjil');
        
        $year = date('Y');
        $startMonth = $semester == 'ganjil' ? "$year-07-01" : "$year-01-01";
        $endMonth = $semester == 'ganjil' ? "$year-12-31" : "$year-06-30";

        $students = Student::where('class_id', $classRoom->id)
            ->withCount([
                'attendances as hadir_count' => fn($q) => $q->whereBetween('date', [$startMonth, $endMonth])->where('status', 'Hadir'),
                'attendances as izin_count' => fn($q) => $q->whereBetween('date', [$startMonth, $endMonth])->where('status', 'Izin'),
                'attendances as sakit_count' => fn($q) => $q->whereBetween('date', [$startMonth, $endMonth])->where('status', 'Sakit'),
                'attendances as alfa_count' => fn($q) => $q->whereBetween('date', [$startMonth, $endMonth])->where('status', 'Alfa'),
            ])
            ->orderBy('name', 'asc')
            ->get();

        return view('homeroom.attendances.recap', compact('classRoom', 'classes', 'students', 'semester', 'classId'));
    }
}