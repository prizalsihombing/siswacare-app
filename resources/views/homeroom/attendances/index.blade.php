<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Absensi Harian Kelas') }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Absensi Harian Kelas</h1>
                <p class="text-gray-600">Menampilkan Kelas: <span class="font-semibold text-indigo-600">{{ $classRoom->name }}</span></p>
            </div>
            <div>
                @if(auth()->user()->role !== 'student' && (auth()->user()->role === 'admin' || $isHomeroomOfThisClass))
                    <a href="{{ route('homeroom.attendances.recap', ['class_id' => $classRoom->id]) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition">
                        Lihat Rekapitulasi Semester
                    </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        {{-- Filter Pilih Kelas dan Tanggal --}}
        <div class="bg-white p-4 rounded-lg shadow mb-6 flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('homeroom.attendances.index') }}" class="flex flex-wrap items-center gap-4">
                @if(auth()->user()->role === 'student')
                    <div class="flex items-center space-x-2">
                        <span class="font-medium text-gray-700">Kelas:</span>
                        <span class="bg-gray-100 text-gray-800 px-3 py-1.5 rounded-md font-semibold border border-gray-300">
                            {{ $classRoom->name }}
                        </span>
                    </div>
                @else
                    <div class="flex items-center space-x-2">
                        <label for="class_id" class="font-medium text-gray-700">Pilih Kelas:</label>
                        <select name="class_id" id="class_id" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="this.form.submit()">
                            @foreach($classes as $cls)
                                <option value="{{ $cls->id }}" {{ $classRoom->id == $cls->id ? 'selected' : '' }}>
                                    {{ $cls->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="flex items-center space-x-2">
                    <label for="date" class="font-medium text-gray-700">Pilih Tanggal:</label>
                    <input type="date" name="date" id="date" value="{{ $date }}" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="this.form.submit()">
                </div>
            </form>
        </div>

        {{-- Form Input Absensi --}}
        <form action="{{ route('homeroom.attendances.store') }}" method="POST">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name="class_id" value="{{ $classRoom->id }}">

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status Kehadiran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan (Opsional)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($students as $index => $student)
                            @php
                                $currentAttendance = $attendances->get($student->id);
                                $selectedStatus = $currentAttendance ? $currentAttendance->status : 'Hadir';
                                $description = $currentAttendance ? $currentAttendance->description : '';
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex justify-center space-x-4">
                                        @foreach(['Hadir', 'Izin', 'Sakit', 'Alfa'] as $status)
                                            <label class="inline-flex items-center text-sm {{ $canEdit ? 'cursor-pointer' : 'cursor-not-allowed opacity-75' }}">
                                                <input type="radio" 
                                                       name="attendances[{{ $student->id }}][status]" 
                                                       value="{{ $status }}" 
                                                       {{ $selectedStatus == $status ? 'checked' : '' }} 
                                                       {{ !$canEdit ? 'disabled' : '' }}
                                                       class="form-radio text-indigo-600 focus:ring-indigo-500">
                                                <span class="ml-1 text-gray-700">{{ $status }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" 
                                           name="attendances[{{ $student->id }}][description]" 
                                           value="{{ $description }}" 
                                           {{ !$canEdit ? 'disabled readonly' : '' }}
                                           placeholder="Alasan (opsional)..." 
                                           class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 {{ !$canEdit ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada siswa terdaftar di kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->isNotEmpty() && $canEdit)
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg shadow hover:bg-green-700 transition font-medium">
                        Simpan Absensi Harian
                    </button>
                </div>
            @endif
        </form>
    </div>
</x-app-layout>