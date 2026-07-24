<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Surat Panggilan Orang Tua') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            
            <form action="{{ route('call-letters.store') }}" method="POST">
                @csrf

                {{-- Pilih Siswa (Poin >= 100) --}}
                <div class="mb-4">
                    <label for="student_id" class="block text-sm font-medium text-gray-700">Nama Siswa</label>
                    <select name="student_id" id="student_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($students as $student)
                            @php
                                $totalPoints = $student->violations->sum('points');
                            @endphp
                            <option value="{{ $student->id }}">
                                {{ $student->name }} - Kelas: {{ $student->classModel->name ?? '-' }} (Poin: {{ $totalPoints }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tanggal Pelaksanaan --}}
                <div class="mb-4">
                    <label for="date" class="block text-sm font-medium text-gray-700">Tanggal Pelaksanaan</label>
                    <input type="date" name="date" id="date" value="{{ old('date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>

                {{-- Pukul --}}
                <div class="mb-4">
                    <label for="time" class="block text-sm font-medium text-gray-700">Pukul</label>
                    <input type="time" name="time" id="time" value="{{ old('time') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>

                {{-- Keterangan Opsional --}}
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Keterangan Tambahan (Opsional)</label>
                    <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: Harap membawa buku catatan pelanggaran...">{{ old('notes') }}</textarea>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex items-center justify-end space-x-3 mt-6">
                    <a href="{{ route('call-letters.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-semibold hover:bg-gray-400">
                        Batal
                    </a>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-indigo-700">
                        Simpan & Generate Surat
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>