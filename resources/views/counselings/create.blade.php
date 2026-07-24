<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Jadwal Layanan BK') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <form action="{{ route('counselings.store') }}" method="POST">
                @csrf

                {{-- Pilih Siswa --}}
                <div class="mb-4">
                    <label for="student_id" class="block font-medium text-sm text-gray-700">Nama Siswa</label>
                    <select name="student_id" id="student_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" data-violations='@json($student->violations)'>
                                {{ $student->name }} ({{ $student->classModel->name ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Pelanggaran Terkait (Otomatis Terisi Teks Semua Pelanggaran) --}}
                <div class="mb-4">
                    <label for="violation_text" class="block font-medium text-sm text-gray-700">Pelanggaran Terkait</label>
                    {{-- Input tersembunyi untuk mengirim ID pelanggaran pertama/utama jika database butuh violation_id --}}
                    <input type="hidden" name="violation_id" id="violation_id">
                    
                    {{-- Kolom teks yang otomatis terisi dan bisa dibaca/dilihat --}}
                    <input type="text" id="violation_display" class="mt-1 block w-full bg-gray-50 border-gray-300 rounded-md shadow-sm text-gray-600 cursor-not-allowed" placeholder="Otomatis terisi setelah memilih siswa..." readonly>
                </div>

                {{-- Tanggal Bimbingan --}}
                <div class="mb-4">
                    <label for="date" class="block font-medium text-sm text-gray-700">Tanggal Bimbingan</label>
                    <input type="date" name="date" id="date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>

                {{-- Pukul (Waktu) --}}
                <div class="mb-4">
                    <label for="time" class="block font-medium text-sm text-gray-700">Pukul</label>
                    <input type="time" name="time" id="time" value="{{ date('H:i') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('counselings.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-semibold hover:bg-gray-400">Batal</a>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-indigo-700">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('student_id').addEventListener('change', function() {
            let selectedOption = this.options[this.selectedIndex];
            let violations = JSON.parse(selectedOption.getAttribute('data-violations') || '[]');
            
            let violationDisplay = document.getElementById('violation_display');
            let violationIdInput = document.getElementById('violation_id');

            if (violations.length > 0) {
                // Gabungkan semua nama pelanggaran menjadi satu kalimat/teks
                let names = violations.map(v => `${v.violation_name} (Poin: ${v.points})`).join(', ');
                violationDisplay.value = names;
                
                // Ambil ID pelanggaran pertama untuk disimpan ke database (jika kolom tabelnya foreign key ID)
                violationIdInput.value = violations[0].id;
            } else {
                violationDisplay.value = 'Tidak ada catatan pelanggaran.';
                violationIdInput.value = '';
            }
        });
    </script>
</x-app-layout>