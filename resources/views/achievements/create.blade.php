<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Data Prestasi Siswa') }}
        </h2>
        <!-- Tom Select CSS -->
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow">
            
            <form action="{{ route('achievements.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Pilih Siswa --}}
                <div class="mb-4">
                    <label for="student_id" class="block font-medium text-sm text-gray-700 mb-1">Pilih Siswa</label>
                    <select name="student_id" id="student_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-indigo-500 @error('student_id') border-red-500 @enderror" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} (Kelas: {{ $student->classModel->name ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Nama Prestasi --}}
                <div class="mb-4">
                    <label for="title" class="block font-medium text-sm text-gray-700 mb-1">Nama Prestasi / Kejuaraan</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="Contoh: Juara Olimpiade Matematika " class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror" required>
                    @error('title')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    {{-- Tingkat --}}
                    <div>
                        <label for="level" class="block font-medium text-sm text-gray-700 mb-1">Tingkat</label>
                        <select name="level" id="level" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-indigo-500" required>
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="Sekolah">Sekolah</option>
                            <option value="Kecamatan">Kecamatan</option>
                            <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                            <option value="Provinsi">Provinsi</option>
                            <option value="Nasional">Nasional</option>
                            <option value="Internasional">Internasional</option>
                        </select>
                        @error('level')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Peringkat --}}
                    <div>
                        <label for="rank" class="block font-medium text-sm text-gray-700 mb-1">Peringkat / Capaian</label>
                        <input type="text" name="rank" id="rank" value="{{ old('rank') }}" placeholder="Contoh: Juara 1 / Medali Emas" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-indigo-500" required>
                        @error('rank')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    {{-- Tanggal --}}
                    <div>
                        <label for="date" class="block font-medium text-sm text-gray-700 mb-1">Tanggal Perolehan</label>
                        <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-indigo-500" required>
                        @error('date')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Upload Bukti --}}
                    <div>
                        <label for="bukti" class="block font-medium text-sm text-gray-700 mb-1">Bukti Upload (Foto / PDF, Maks: 2MB)</label>
                        <input type="file" name="bukti" id="bukti" class="w-full border border-gray-300 rounded-md px-2 py-1.5 text-sm bg-white text-gray-500 file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        @error('bukti')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Keterangan --}}
                <div class="mb-6">
                    <label for="description" class="block font-medium text-sm text-gray-700 mb-1">Keterangan Tambahan (Opsional)</label>
                    <textarea name="description" id="description" rows="3" placeholder="Catatan tambahan jika ada..." class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('achievements.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-400 transition">
                        Batal
                    </a>
                    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-semibold shadow hover:bg-indigo-700 transition">
                        Simpan Prestasi
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        new TomSelect("#student_id", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
    </script>
</x-app-layout>