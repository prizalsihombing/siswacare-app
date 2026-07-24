<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Layanan BK') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            
            @php
                $role = auth()->user()->role;
            @endphp

            {{-- Pesan Sukses --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filter Pilih Kelas (Muncul untuk Admin & Guru) --}}
            @if($role !== 'siswa' && $role !== 'student')
                <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200 flex items-center justify-between">
                    <form method="GET" action="{{ route('counselings.index') }}" class="flex items-center space-x-3 w-full">
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

                    {{-- Tombol Tambah Bimbingan (Hanya untuk Admin, jika Guru tidak boleh tambah, ubah kondisi ini. Berdasarkan permintaan "guru: fitur tambah dihilangkan", kita batasi hanya untuk role selain guru/siswa atau sesuaikan) --}}
                    {{-- Asumsi: Admin bisa tambah, Guru bertindak sebagai pengawas/pembaca saja --}}
                    @if($role === 'admin' || $role === 'administrator')
                        <a href="{{ route('counselings.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-indigo-700 whitespace-nowrap ml-4">
                            + Tambah Bimbingan
                        </a>
                    @endif
                </div>
            @endif

            <div class="flex justify-between items-center mb-4">
                <div class="text-lg font-medium text-gray-700">
                    @if($role === 'siswa' || $role === 'student')
                        Riwayat Layanan BK Anda
                    @else
                        Riwayat Layanan BK Kelas: {{ $classes->where('id', request('class_id'))->first()->name ?? 'Semua Kelas' }}
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
                            <th class="px-4 py-3 text-left">Pelanggaran</th>
                            <th class="px-4 py-3 text-left">Tgl Bimbingan</th>
                            <th class="px-4 py-3 text-left">Pukul</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            {{-- Kolom Aksi hanya muncul untuk Admin --}}
                            @if($role === 'admin' || $role === 'administrator')
                                <th class="px-4 py-3 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-800">
                        @forelse($counselings as $index => $item)
                            <tr>
                                <td class="px-4 py-3">{{ $counselings->firstItem() + $index }}</td>
                                <td class="px-4 py-3 font-medium">{{ $item->student->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $item->student->classModel->name ?? '-' }}</td>
                                <td class="px-4 py-3 font-semibold text-red-600">{{ $item->violation->violation_name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ date('d-m-Y', strtotime($item->date)) }}</td>
                                <td class="px-4 py-3 font-medium text-indigo-600">{{ date('H:i', strtotime($item->time)) }} WIB</td>
                                <td class="px-4 py-3">
                                    {{-- Status tampil sebagai Badge statis untuk Guru & Siswa. Hanya Admin yang punya dropdown --}}
                                    @if($role === 'admin' || $role === 'administrator')
                                        <form action="{{ route('counselings.updateStatus', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" onchange="this.form.submit()" class="text-xs font-medium rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 
                                                {{ $item->status == 'diproses' ? 'bg-yellow-50 text-yellow-800' : ($item->status == 'tindak lanjut' ? 'bg-blue-50 text-blue-800' : 'bg-green-50 text-green-800') }}">
                                                <option value="diproses" {{ $item->status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                                <option value="tindak lanjut" {{ $item->status == 'tindak lanjut' ? 'selected' : '' }}>Tindak Lanjut</option>
                                                <option value="selesai" {{ $item->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                            </select>
                                        </form>
                                    @else
                                        @if($item->status == 'diproses')
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-1 rounded">Diproses</span>
                                        @elseif($item->status == 'tindak lanjut')
                                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-1 rounded">Tindak Lanjut</span>
                                        @else
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded">Selesai</span>
                                        @endif
                                    @endif
                                </td>
                                
                                {{-- Tombol Aksi (Edit & Hapus) hanya untuk Admin --}}
                                @if($role === 'admin' || $role === 'administrator')
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="{{ route('counselings.edit', $item->id) }}" class="text-yellow-600 hover:text-yellow-900 font-medium text-xs bg-yellow-50 px-2.5 py-1 rounded border border-yellow-200">
                                                Edit
                                            </a>
                                            <form action="{{ route('counselings.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
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
                                <td colspan="{{ ($role === 'admin' || $role === 'administrator') ? 8 : 7 }}" class="px-4 py-6 text-center text-gray-500">
                                    Belum ada data layanan BK yg tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $counselings->links() }}
            </div>

        </div>
    </div>
</x-app-layout>