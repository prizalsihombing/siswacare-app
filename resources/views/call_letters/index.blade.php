<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Surat Panggilan Orang Tua') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            
            @php
                $role = auth()->user()->role;
                $isAdmin = ($role === 'admin' || $role === 'administrator');
                $isGuru = ($role === 'guru' || $role === 'teacher');
                $isSiswa = ($role === 'siswa' || $role === 'student');
            @endphp

            {{-- Pesan Sukses --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filter & Tombol Tambah (Hanya untuk Admin) --}}
            @if($isAdmin)
                <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200 flex items-center justify-between">
                    <form method="GET" action="{{ route('call-letters.index') }}" class="flex items-center space-x-3 w-full">
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

                    <a href="{{ route('call-letters.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-indigo-700 whitespace-nowrap ml-4">
                        + Tambah Panggilan
                    </a>
                </div>
            @endif

            <div class="flex justify-between items-center mb-4">
                <div class="text-lg font-medium text-gray-700">
                    @if($isSiswa)
                        Riwayat Surat Panggilan Anda
                    @elseif($isGuru)
                        Riwayat Surat Panggilan Siswa
                    @else
                        Riwayat Surat Panggilan Kelas: {{ $classes->where('id', request('class_id'))->first()->name ?? 'Semua Kelas' }}
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">No</th>
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3 text-left">Kelas</th>
                            <th class="px-4 py-3 text-left">Masalah</th>
                            <th class="px-4 py-3 text-left">Tgl Pelaksanaan</th>
                            <th class="px-4 py-3 text-left">Pukul</th>
                            <th class="px-4 py-3 text-center">SPO</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Keterangan</th>
                            @if($isAdmin)
                                <th class="px-4 py-3 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-800">
                        @forelse($callLetters as $index => $item)
                            <tr>
                                <td class="px-4 py-3">{{ $callLetters->firstItem() + $index }}</td>
                                <td class="px-4 py-3 font-medium">{{ $item->student->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $item->student->classModel->name ?? '-' }}</td>
                                <td class="px-4 py-3 font-semibold text-red-600">{{ $item->problem }}</td>
                                <td class="px-4 py-3">{{ date('d-m-Y', strtotime($item->date)) }}</td>
                                <td class="px-4 py-3 font-medium text-indigo-600">{{ date('H:i', strtotime($item->time)) }} WIB</td>
                                
                                {{-- Kolom SPO --}}
                                <td class="px-4 py-3 text-center">
                                    @if($isAdmin)
                                        <a href="https://wa.me/{{ $item->student->parent_phone ?? '' }}?text={{ urlencode($item->letter_content) }}" target="_blank" class="inline-flex items-center text-green-600 hover:text-green-900 font-medium text-xs bg-green-50 px-2.5 py-1 rounded border border-green-200">
                                            Kirim WA
                                        </a>
                                    @else
                                        <span class="text-green-600 font-medium text-xs bg-green-50 px-2.5 py-1 rounded border border-green-200">
                                            Terkirim
                                        </span>
                                    @endif
                                </td>

                                {{-- Kolom Status --}}
                                <td class="px-4 py-3">
                                    @if($isAdmin)
                                        <form action="{{ route('call-letters.updateStatus', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" onchange="this.form.submit()" class="text-xs font-medium rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="Belum Hadir" {{ $item->status == 'Belum Hadir' ? 'selected' : '' }}>Belum Hadir</option>
                                                <option value="Sudah Hadir" {{ $item->status == 'Sudah Hadir' ? 'selected' : '' }}>Sudah Hadir</option>
                                                <option value="Penjadwalan Ulang" {{ $item->status == 'Penjadwalan Ulang' ? 'selected' : '' }}>Penjadwalan Ulang</option>
                                            </select>
                                        </form>
                                    @else
                                        <span class="px-2.5 py-1 rounded text-xs font-medium 
                                            {{ $item->status == 'Sudah Hadir' ? 'bg-green-100 text-green-800' : ($item->status == 'Penjadwalan Ulang' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ $item->status }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Kolom Keterangan --}}
                                <td class="px-4 py-3">
                                    @if($isAdmin)
                                        <form action="{{ route('call-letters.updateNotes', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="notes" value="{{ $item->notes }}" placeholder="Isi keterangan..." onchange="this.form.submit()" class="text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full">
                                        </form>
                                    @else
                                        <span class="text-gray-600 text-xs">{{ $item->notes ?? '-' }}</span>
                                    @endif
                                </td>

                                {{-- Kolom Aksi (Hanya untuk Admin) --}}
                                @if($isAdmin)
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <form action="{{ route('call-letters.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Selesaikan kasus ini? Data Surat Panggilan, Layanan BK, dan Pelanggaran untuk siswa ini akan terhapus otomatis.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-green-700 hover:text-white font-medium text-xs bg-green-50 hover:bg-green-600 px-3 py-1 rounded border border-green-300 transition-colors">
                                                    Selesai
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? 10 : 9 }}" class="px-4 py-6 text-center text-gray-500">
                                    Belum ada data surat panggilan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $callLetters->links() }}
            </div>

        </div>
    </div>
</x-app-layout>