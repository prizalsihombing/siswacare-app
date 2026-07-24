<?php

namespace App\Http\Controllers;

use App\Models\CallLetter;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\Counseling;
use App\Models\Violation;

class CallLetterController extends Controller
{
    // Menampilkan daftar Surat Panggilan
    public function index(Request $request)
    {
        $user = auth()->user();
        $role = $user->role;

        $classes = \App\Models\ClassModel::all();
        $query = CallLetter::with(['student.classModel']);

        // Jika yang login adalah siswa, hanya tampilkan surat panggilan miliknya sendiri
        if ($role === 'siswa' || $role === 'student') {
            $student = \App\Models\Student::where('user_id', $user->id)->first();
            $query->where('student_id', $student ? $student->id : null);
        } 
        // Jika filter kelas dipilih (untuk admin/guru)
        elseif ($request->filled('class_id')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        $callLetters = $query->paginate(10);

        return view('call_letters.index', compact('callLetters', 'classes', 'role'));
    }

    // Menampilkan Form Tambah
    public function create()
    {
        // Hanya ambil siswa yang total poin pelanggarannya >= 100
        $students = Student::with(['classModel', 'violations'])->get()->filter(function ($student) {
            $totalPoints = $student->violations->sum('points');
            return $totalPoints >= 100;
        });

        return view('call_letters.create', compact('students'));
    }

    // Menyimpan Data Surat Panggilan Baru
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'time' => 'required',
        ]);

        $student = Student::with(['classModel', 'violations'])->findOrFail($request->student_id);
        
        // Ambil daftar pelanggaran siswa untuk dimasukkan ke teks masalah
        $violationNames = $student->violations->pluck('violation_name')->implode(', ');
        $problemText = $violationNames ?: 'Pelanggaran Disiplin Sekolah';

        // Ambil nama kelas dengan aman
        $className = $student->classModel->name ?? '-';

        // Generate teks surat otomatis sesuai template yang disepakati
        $dateFormatted = date('d-m-Y', strtotime($request->date));
        $timeFormatted = $request->time;
        
        $letterContent = "Assalamu'alaikum Wr. Wb.\n" .
                         "Yth. Bapak/Ibu Wali Murid dari {$student->name} ({$className}),\n\n" .
                         "Melalui pesan ini, pihak sekolah ingin mengundang Bapak/Ibu untuk datang ke sekolah guna membahas perkembangan terkait {$problemText} yang terjadi baru-baru ini.\n" .
                         "Pertemuan ini dijadwalkan pada:\n\n" .
                         "📅 Tanggal: {$dateFormatted}\n" .
                         "⏰ Pukul: {$timeFormatted} WIB\n" .
                         "📍 Tempat: Ruang Bimbingan Konseling (BK)\n\n" .
                         "Ditunggu kehadiran dan kerjasamanya ya Bapak/Ibu. Terima kasih.\n\n" .
                         "Hormat kami,\n" .
                         "Manajemen Sekolah / Guru BK";

        CallLetter::create([
            'student_id' => $request->student_id,
            'problem' => $problemText,
            'date' => $request->date,
            'time' => $request->time,
            'letter_content' => $letterContent,
            'status' => 'Belum Hadir',
            'notes' => $request->notes,
        ]);

        return redirect()->route('call-letters.index')->with('success', 'Surat Panggilan berhasil dibuat dan otomatis tergenerate!');
    }

    // Update Status Kehadiran
    public function updateStatus(Request $request, $id)
    {
        $callLetter = CallLetter::findOrFail($id);
        $callLetter->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Status berhasil diperbarui!');
    }

    // Menghapus Data
    public function destroy($id)
    {
        $callLetter = CallLetter::findOrFail($id);
        $studentId = $callLetter->student_id;

        // 1. Hapus data Surat Panggilan
        $callLetter->delete();

        // 2. Hapus semua data Layanan BK milik siswa tersebut
        Counseling::where('student_id', $studentId)->delete();

        // 3. Hapus semua data Pelanggaran milik siswa tersebut
        Violation::where('student_id', $studentId)->delete();

        // Perbaikan di sini (menggunakan titik koma setelah route dan titik sebelum with)
        return redirect()->route('call-letters.index')
            ->with('success', 'Kasus siswa selesai. Data surat panggilan, layanan BK, dan pelanggaran siswa terkait telah dibersihkan.');
    }

    // Update Keterangan
    public function updateNotes(Request $request, $id)
    {
        $callLetter = CallLetter::findOrFail($id);
        $callLetter->update([
            'notes' => $request->notes
        ]);

        return redirect()->back()->with('success', 'Keterangan berhasil diperbarui!');
    }
}
