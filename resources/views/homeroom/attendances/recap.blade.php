<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rekapitulasi Absensi Semester') }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Rekapitulasi Absensi Semester</h1>
                <p class="text-gray-600">Menampilkan Kelas: <span class="font-semibold text-indigo-600">{{ $classRoom->name }}</span></p>
            </div>
            <div>
                <a href="{{ route('homeroom.attendances.index', ['class_id' => $classRoom->id]) }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-700 transition">
                    Kembali ke Absensi Harian
                </a>
            </div>
        </div>

        {{-- Filter Kelas (Khusus Admin) dan Semester --}}
        <div class="bg-white p-4 rounded-lg shadow mb-6 flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('homeroom.attendances.recap') }}" class="flex flex-wrap items-center gap-4">
                @if(auth()->user()->role === 'admin')
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
                    <label for="semester" class="font-medium text-gray-700">Pilih Semester:</label>
                    <select name="semester" id="semester" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="this.form.submit()">
                        <option value="ganjil" {{ $semester == 'ganjil' ? 'selected' : '' }}>Semester Ganjil (Juli - Desember)</option>
                        <option value="genap" {{ $semester == 'genap' ? 'selected' : '' }}>Semester Genap (Januari - Juni)</option>
                    </select>
                </div>
            </form>
        </div>

        {{-- Tabel Rekapitulasi --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-green-600 uppercase tracking-wider">Hadir</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-blue-600 uppercase tracking-wider">Izin</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-yellow-600 uppercase tracking-wider">Sakit</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-red-600 uppercase tracking-wider">Alfa</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($students as $index => $student)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-green-600">{{ $student->hadir_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-blue-600">{{ $student->izin_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-yellow-600">{{ $student->sakit_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-red-600">{{ $student->alfa_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data siswa pada kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>