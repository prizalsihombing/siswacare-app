<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Pelanggaran Siswa') }}
        </h2>
    </x-slot>

    {{-- CDN CSS Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow">
            
            <form action="{{ route('violations.update', $violation->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Pilihan Siswa dengan Select2 --}}
                <div class="mb-4">
                    <label for="student_id" class="block font-medium text-sm text-gray-700 mb-1">Pilih Siswa</label>
                    <select name="student_id" id="student_id" class="select2 mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">-- Pilih atau Cari Siswa (NISN / Nama) --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ $violation->student_id == $student->id ? 'selected' : '' }}>
                                {{ $student->nisn }} - {{ $student->name }} ({{ $student->classModel->name ?? 'Tanpa Kelas' }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Jenis Pelanggaran --}}
                <div class="mb-4">
                    <label for="violation_select" class="block font-medium text-sm text-gray-700 mb-1">Jenis Pelanggaran</label>
                    <select id="violation_select" class="select2 mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Pilih atau Cari Jenis Pelanggaran --</option>
                        <optgroup label="Ringan">
                            <option value="Atribut / Seragam tidak lengkap" data-category="Ringan" data-points="5">Atribut / Seragam tidak lengkap (Poin: 5)</option>
                            <option value="Terlambat datang ke sekolah" data-category="Ringan" data-points="10">Terlambat datang ke sekolah (Poin: 10)</option>
                            <option value="Tidak mengikuti kegiatan upacara" data-category="Ringan" data-points="15">Tidak mengikuti kegiatan upacara (Poin: 15)</option>
                        </optgroup>
                        <optgroup label="Sedang">
                            <option value="Membolos / tidak masuk tanpa keterangan" data-category="Sedang" data-points="20">Membolos / tidak masuk tanpa keterangan (Poin: 20)</option>
                            <option value="Keluar sekolah tanpa izin saat jam pelajaran" data-category="Sedang" data-points="25">Keluar sekolah tanpa izin saat jam pelajaran (Poin: 25)</option>
                            <option value="Tidak bersopan santun kepada guru" data-category="Sedang" data-points="30">Tidak bersopan santun kepada guru (Poin: 30)</option>
                            <option value="Membawa gawai/ponsel di kelas tanpa izin" data-category="Sedang" data-points="20">Membawa gawai/ponsel di kelas tanpa izin (Poin: 20)</option>
                        </optgroup>
                        <optgroup label="Berat">
                            <option value="Merokok / menggunakan vape di lingkungan sekolah" data-category="Berat" data-points="50">Merokok / menggunakan vape di lingkungan sekolah (Poin: 50)</option>
                            <option value="Melakukan perundungan (bullying) / berkelahi" data-category="Berat" data-points="75">Melakukan perundungan (bullying) / berkelahi (Poin: 75)</option>
                            <option value="Membawa senjata tajam atau benda berbahaya" data-category="Berat" data-points="100">Membawa senjata tajam atau benda berbahaya (Poin: 100)</option>
                            <option value="Membawa atau mengonsumsi narkoba / miras" data-category="Berat" data-points="100">Membawa atau mengonsumsi narkoba / miras (Poin: 100)</option>
                        </optgroup>
                    </select>
                </div>

                {{-- Nama Pelanggaran --}}
                <div class="mb-4">
                    <label for="violation_name" class="block font-medium text-sm text-gray-700">Nama Pelanggaran (Teks Tersimpan)</label>
                    <input type="text" name="violation_name" id="violation_name" value="{{ old('violation_name', $violation->violation_name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50" readonly required>
                    @error('violation_name')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Kategori Pelanggaran --}}
                <div class="mb-4">
                    <label for="category" class="block font-medium text-sm text-gray-700">Kategori Pelanggaran</label>
                    <input type="text" name="category" id="category" value="{{ old('category', $violation->category) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50" readonly required>
                    @error('category')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Jumlah Poin --}}
                <div class="mb-4">
                    <label for="points" class="block font-medium text-sm text-gray-700">Jumlah Poin</label>
                    <input type="number" name="points" id="points" value="{{ old('points', $violation->points) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50" readonly required>
                    @error('points')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Tanggal Kejadian --}}
                <div class="mb-4">
                    <label for="date" class="block font-medium text-sm text-gray-700">Tanggal Kejadian</label>
                    <input type="date" name="date" id="date" value="{{ old('date', $violation->date) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    @error('date')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Keterangan Opsional --}}
                <div class="mb-4">
                    <label for="description" class="block font-medium text-sm text-gray-700">Keterangan (Opsional)</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Tambahkan catatan jika diperlukan...">{{ old('description', $violation->description) }}</textarea>
                    @error('description')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('violations.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md font-semibold text-sm hover:bg-gray-400 transition">
                        Batal
                    </a>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md font-semibold text-sm hover:bg-indigo-700 transition">
                        Perbarui Pelanggaran
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- CDN jQuery dan Script JS Select2 --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });

            $('#violation_select').on('change', function() {
                var selectedOption = $(this).find(':selected');
                var violationName = $(this).val();
                var category = selectedOption.data('category');
                var points = selectedOption.data('points');

                if (violationName) {
                    $('#violation_name').val(violationName);
                    $('#category').val(category || '');
                    $('#points').val(points || '');
                }
            });
        });
    </script>
</x-app-layout>