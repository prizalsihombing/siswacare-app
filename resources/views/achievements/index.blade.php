<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Prestasi Siswa') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- Notifikasi Sukses --}}
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        {{-- Filter Kelas dan Tombol Tambah (Hanya untuk Admin / Guru) --}}
        @if($role !== 'student' && $role !== 'siswa')
            <div class="bg-white p-4 rounded-lg shadow mb-6 flex items-center justify-between gap-4">
                <form method="GET" action="{{ route('achievements.index') }}" class="flex items-center space-x-3">
                    <label for="class_id" class="font-medium text-sm text-gray-700">Pilih Kelas:</label>
                    <select name="class_id" id="class_id" onchange="this.form.submit()" class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        
                        <option value="">-- Semua Kelas --</option>

                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                {{-- Tombol Tambah Prestasi HANYA MUNCUL UNTUK ADMIN --}}
                @if($role === 'admin')
                    <a href="{{ route('achievements.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition font-semibold text-sm whitespace-nowrap">
                        + Tambah Prestasi
                    </a>
                @endif
            </div>
        @endif

        {{-- Tabel Riwayat Prestasi --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 bg-gray-50 border-b border-gray-200 font-bold text-gray-700">
                @if($role === 'student' || $role === 'siswa')
                    Riwayat Prestasi Pribadi
                @else
                    Riwayat Prestasi Kelas: {{ request('class_id') ? ($classRoom->name ?? 'Semua Kelas') : 'Semua Kelas' }}
                @endif
            </div>
            
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Kelas</th>
                        <th class="px-4 py-3 text-left">Prestasi</th>
                        <th class="px-4 py-3 text-left">Peringkat</th>
                        <th class="px-4 py-3 text-left">Tingkat</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Keterangan</th>
                        <th class="px-4 py-3 text-center">Bukti Upload</th>
                        @if($role === 'admin')
                            <th class="px-4 py-3 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-800">
                    @forelse($achievements as $index => $item)
                        <tr>
                            <td class="px-4 py-3">{{ $achievements->firstItem() + $index }}</td>
                            <td class="px-4 py-3 font-medium">{{ $item->student->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $item->student->classModel->name ?? '-' }}</td>
                            <td class="px-4 py-3 font-semibold text-indigo-600">{{ $item->title }}</td>
                            <td class="px-4 py-3">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">{{ $item->rank }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">{{ $item->level }}</span>
                            </td>
                            <td class="px-4 py-3">{{ date('d-m-Y', strtotime($item->date)) }}</td>
                            <td class="px-4 py-3 text-gray-500 italic">{{ $item->description ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($item->bukti)
                                    <a href="{{ asset('storage/' . $item->bukti) }}" target="_blank" class="text-indigo-600 hover:underline font-medium text-xs bg-indigo-50 px-2.5 py-1 rounded border border-indigo-200">
                                        Lihat File
                                    </a>
                                @else
                                    <span class="text-gray-400 text-xs">Tidak ada</span>
                                @endif
                            </td>
                            {{-- Tombol Edit & Hapus HANYA MUNCUL untuk Admin --}}
                            @if($role === 'admin')
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('achievements.edit', $item->id) }}" class="text-yellow-600 hover:text-yellow-900 font-medium text-xs bg-yellow-50 px-2.5 py-1 rounded border border-yellow-200">
                                            Edit
                                        </a>
                                        <form action="{{ route('achievements.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data prestasi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-xs bg-red-50 px-2.5 py-1 rounded border border-red-200">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $role === 'admin' ? 10 : 9 }}" class="px-4 py-6 text-center text-gray-500">
                                Belum ada data prestasi yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $achievements->links() }}
        </div>
    </div>
</x-app-layout>