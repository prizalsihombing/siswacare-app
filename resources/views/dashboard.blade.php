<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @php
        $user = Auth::user();
        $role = $user->role; 
        
        $teacherProfile = null;
        $homeroomStudentCount = 0;
        $homeroomClassName = '';

        if ($role === 'teacher') {
            // Ambil data dari tabel teachers berdasarkan user_id
            $teacherProfile = \App\Models\Teacher::where('user_id', $user->id)->first();
            
            // Jika guru tersebut adalah Wali Kelas dan memiliki class_id
            if ($teacherProfile && $teacherProfile->role_type === 'Wali Kelas' && $teacherProfile->class_id) {
                $homeroomClass = \App\Models\ClassModel::find($teacherProfile->class_id);
                if ($homeroomClass) {
                    $homeroomClassName = $homeroomClass->name;
                    $homeroomStudentCount = \App\Models\Student::where('class_id', $homeroomClass->id)->count();
                }
            }
        }

        // Deteksi Data Siswa yang sedang login
        $loggedStudent = null;
        if ($role === 'siswa' || $role === 'student') {
            $loggedStudent = \App\Models\Student::where('user_id', $user->id)->first();
        }
    @endphp

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- Sambutan / Banner Singkat --}}
        <div class="bg-indigo-600 rounded-lg shadow-sm p-6 mb-6 text-white">
            <h3 class="text-2xl font-bold">Selamat Datang, {{ $user->name }}! 👋</h3>
            <p class="text-indigo-100 text-sm mt-1">Berikut adalah ringkasan data kedisiplinan dan aktivitas sistem Siswacare.</p>
        </div>

        {{-- Kotak Statistik Utama (Menyesuaikan Role) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            
            {{-- 1. Kotak Total Siswa --}}
            @if($role === 'admin' || $role === 'administrator')
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Siswa Aktif</p>
                        <h4 class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Student::count() }}</h4>
                    </div>
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>
            @elseif($teacherProfile && $teacherProfile->role_type === 'Wali Kelas')
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Siswa (Kelas {{ $homeroomClassName }})</p>
                        <h4 class="text-3xl font-bold text-gray-800 mt-1">{{ $homeroomStudentCount }}</h4>
                    </div>
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>
            @endif
            {{-- Catatan: Jika Guru Mapel, kotak total siswa ini otomatis tidak akan dimunculkan. --}}

            {{-- 2. Total Guru (Hanya untuk Admin) --}}
            @if($role === 'admin' || $role === 'administrator')
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Guru / Staff</p>
                        <h4 class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Teacher::count() }}</h4>
                    </div>
                    <div class="p-3 bg-green-50 text-green-600 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                </div>
            @endif

            {{-- 3. Total Kasus Pelanggaran --}}
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Kasus Pelanggaran</p>
                    <h4 class="text-3xl font-bold text-gray-800 mt-1">
                        @if($role === 'siswa' || $role === 'student')
                            {{ $loggedStudent ? \App\Models\Violation::where('student_id', $loggedStudent->id)->count() : 0 }}
                        @else
                            {{ \App\Models\Violation::count() }}
                        @endif
                    </h4>
                </div>
                <div class="p-3 bg-yellow-50 text-yellow-600 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>

            {{-- 4. Surat Panggilan Aktif --}}
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Surat Panggilan Aktif</p>
                    <h4 class="text-3xl font-bold text-red-600 mt-1">
                        @if($role === 'siswa' || $role === 'student')
                            {{ $loggedStudent ? \App\Models\CallLetter::where('student_id', $loggedStudent->id)->where('status', '!=', 'Sudah Hadir')->count() : 0 }}
                        @else
                            {{ \App\Models\CallLetter::where('status', '!=', 'Sudah Hadir')->count() }}
                        @endif
                    </h4>
                </div>
                <div class="p-3 bg-red-50 text-red-600 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
            </div>

        </div>

        {{-- Bagian Tabel Ringkasan Aktivitas Terbaru --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- Tabel Surat Panggilan Terbaru --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-bold text-gray-800 text-base">Surat Panggilan Orang Tua Terbaru</h4>
                    <a href="{{ route('call-letters.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Lihat Semua &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50 text-gray-700 uppercase">
                            <tr>
                                <th class="px-3 py-2 text-left">Nama</th>
                                <th class="px-3 py-2 text-left">Kelas</th>
                                <th class="px-3 py-2 text-left">Tanggal</th>
                                <th class="px-3 py-2 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-700">
                            @php
                                $callQuery = \App\Models\CallLetter::with('student.classModel')->latest();
                                if ($role === 'siswa' || $role === 'student') {
                                    $callQuery->where('student_id', $loggedStudent ? $loggedStudent->id : 0);
                                }
                                $recentCalls = $callQuery->take(5)->get();
                            @endphp

                            @forelse($recentCalls as $call)
                                <tr>
                                    <td class="px-3 py-2 font-medium">{{ $call->student->name ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $call->student->classModel->name ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ date('d-m-Y', strtotime($call->date)) }}</td>
                                    <td class="px-3 py-2">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold 
                                            {{ $call->status == 'Sudah Hadir' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $call->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-4 text-center text-gray-400">Belum ada data surat panggilan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tabel Pelanggaran Terbaru --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-bold text-gray-800 text-base">Catatan Pelanggaran Terbaru</h4>
                    <a href="{{ route('violations.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Lihat Semua &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50 text-gray-700 uppercase">
                            <tr>
                                <th class="px-3 py-2 text-left">Nama Siswa</th>
                                <th class="px-3 py-2 text-left">Pelanggaran</th>
                                <th class="px-3 py-2 text-center">Poin</th>
                                <th class="px-3 py-2 text-left">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-700">
                            @php
                                $violationQuery = \App\Models\Violation::with('student')->latest();
                                if ($role === 'siswa' || $role === 'student') {
                                    $violationQuery->where('student_id', $loggedStudent ? $loggedStudent->id : 0);
                                }
                                $recentViolations = $violationQuery->take(5)->get();
                            @endphp

                            @forelse($recentViolations as $v)
                                <tr>
                                    <td class="px-3 py-2 font-medium">{{ $v->student->name ?? '-' }}</td>
                                    <td class="px-3 py-2 text-red-600 truncate max-w-xs">{{ $v->violation_name }}</td>
                                    <td class="px-3 py-2 text-center font-bold text-red-600">+{{ $v->points }}</td>
                                    <td class="px-3 py-2">{{ date('d-m-Y', strtotime($v->date)) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-4 text-center text-gray-400">Belum ada catatan pelanggaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>