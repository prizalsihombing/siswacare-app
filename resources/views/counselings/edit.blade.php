<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Layanan BK') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            
            <form action="{{ route('counselings.update', $counseling->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Pilih Siswa --}}
                <div class="mb-4">
                    <label for="student_id" class="block text-sm font-medium text-gray-700">Siswa</label>
                    <select name="student_id" id="student_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ $counseling->student_id == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} ({{ $student->classModel->name ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tanggal Bimbingan --}}
                <div class="mb-4">
                    <label for="date" class="block text-sm font-medium text-gray-700">Tanggal Bimbingan</label>
                    <input type="date" name="date" id="date" value="{{ old('date', $counseling->date) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>

                {{-- Pukul --}}
                <div class="mb-4">
                    <label for="time" class="block text-sm font-medium text-gray-700">Pukul</label>
                    <input type="time" name="time" id="time" value="{{ old('time', $counseling->time) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex items-center justify-end space-x-3 mt-6">
                    <a href="{{ route('counselings.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-semibold hover:bg-gray-400">
                        Batal
                    </a>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-indigo-700">
                        Perbarui
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>