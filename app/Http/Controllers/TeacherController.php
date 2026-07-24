<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $teachers = Teacher::with(['user', 'classModel'])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('nuptk', 'like', "%{$search}%");
            })
            ->paginate(10);

        $classes = ClassModel::all();

        return view('admin.teachers.index', compact('teachers', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nuptk' => 'required|unique:teachers,nuptk|unique:users,username',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string|max:20',
            'role_type' => 'required|in:Wali Kelas,Guru Mapel',
            'class_id' => 'required_if:role_type,Wali Kelas|nullable|exists:classes,id',
            'subject' => 'nullable|string|max:255',
            'status' => 'required|in:Aktif,Cuti,Keluar',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->nuptk,
            'password' => Hash::make($request->nuptk),
            'role' => 'teacher',
        ]);

        Teacher::create([
            'user_id' => $user->id,
            'nuptk' => $request->nuptk,
            'name' => $request->name,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'role_type' => $request->role_type,
            'class_id' => $request->role_type == 'Wali Kelas' ? $request->class_id : null,
            'subject' => $request->subject,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.teachers.index')->with('success', 'Data guru dan akun login otomatis berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);

        $request->validate([
            'nuptk' => 'required|unique:teachers,nuptk,' . $teacher->id,
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string|max:20',
            'role_type' => 'required|in:Wali Kelas,Guru Mapel',
            'class_id' => 'required_if:role_type,Wali Kelas|nullable|exists:classes,id',
            'subject' => 'nullable|string|max:255',
            'status' => 'required|in:Aktif,Cuti,Keluar',
        ]);

        $teacher->update([
            'nuptk' => $request->nuptk,
            'name' => $request->name,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'role_type' => $request->role_type,
            'class_id' => $request->role_type == 'Wali Kelas' ? $request->class_id : null,
            'subject' => $request->subject,
            'status' => $request->status,
        ]);

        if ($teacher->user_id) {
            User::where('id', $teacher->user_id)->update([
                'name' => $request->name,
                'username' => $request->nuptk,
            ]);
        }

        return redirect()->route('admin.teachers.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);

        if ($teacher->user_id) {
            User::where('id', $teacher->user_id)->delete();
        }

        $teacher->delete();

        return redirect()->route('admin.teachers.index')->with('success', 'Data guru dan akun terkait berhasil dihapus.');
    }
}