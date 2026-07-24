<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $role = $user->role ?? ''; 

        $classes = ClassModel::all();
        $classRoom = null;
        $classId = $request->input('class_id'); // Ambil input tanpa memaksa ke kelas pertama

        // 1. Logika untuk Role SISWA
        if ($role === 'student' || $role === 'siswa') {
            $studentProfile = Student::where('user_id', $user->id)->first();
            
            if (!$studentProfile) {
                return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
            }

            $classRoom = $studentProfile->classModel;
            $classes = collect([$classRoom]); 
            
            $achievements = Achievement::with('student.classModel')
                ->where('student_id', $studentProfile->id)
                ->latest()
                ->paginate(10)
                ->withQueryString();

        // 2. Logika untuk Role GURU / WALI KELAS & ADMIN
        } else {
            // Jika kelas dipilih (tidak kosong)
            if ($classId !== null && $classId !== '') {
                $classRoom = ClassModel::find($classId);
                $studentIds = Student::where('class_id', $classId)->pluck('id');
                
                $achievements = Achievement::with('student.classModel')
                    ->whereIn('student_id', $studentIds)
                    ->latest()
                    ->paginate(10)
                    ->withQueryString();
            } else {
                // Jika memilih "-- Semua Kelas --" (kosong)
                $achievements = Achievement::with('student.classModel')
                    ->latest()
                    ->paginate(10)
                    ->withQueryString();
            }
        }

        return view('achievements.index', compact('classes', 'classRoom', 'achievements', 'role'));
    }

    public function create()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        $classes = ClassModel::all();
        $students = Student::orderBy('name', 'asc')->get();
        return view('achievements.create', compact('classes', 'students'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'title' => 'required|string|max:255',
            'level' => 'required|string',
            'rank' => 'required|string',
            'date' => 'required|date',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'description' => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/bukti-prestasi'), $filename);
            $data['bukti'] = 'bukti-prestasi/' . $filename;
        }

        Achievement::create($data);

        return redirect()->route('achievements.index')->with('success', 'Data prestasi berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        $achievement = Achievement::findOrFail($id);

        if ($achievement->bukti && file_exists(public_path('storage/' . $achievement->bukti))) {
            unlink(public_path('storage/' . $achievement->bukti));
        }

        $achievement->delete();

        return redirect()->route('achievements.index')->with('success', 'Data prestasi berhasil dihapus.');
    }

    public function edit($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        $achievement = \App\Models\Achievement::findOrFail($id);
        $students = \App\Models\Student::with('classModel')->get();
        
        return view('achievements.edit', compact('achievement', 'students'));
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        $achievement = \App\Models\Achievement::findOrFail($id);

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'title' => 'required|string|max:255',
            'level' => 'required|string',
            'rank' => 'required|string',
            'date' => 'required|date',
            'bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'description' => 'nullable|string',
        ]);

        $data = $request->except('bukti');

        if ($request->hasFile('bukti')) {
            // Hapus file lama jika ada
            if ($achievement->bukti && \Storage::disk('public')->exists($achievement->bukti)) {
                \Storage::disk('public')->delete($achievement->bukti);
            }
            $data['bukti'] = $request->file('bukti')->store('achievements', 'public');
        }

        $achievement->update($data);

        return redirect()->route('achievements.index')->with('success', 'Data prestasi berhasil diperbarui.');
    }
}