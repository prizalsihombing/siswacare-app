<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Data Guru
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <!-- Header & Tombol Tambah -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Daftar Tenaga Pengajar</h1>
            <button onclick="openModal('addModal')" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow">
                + Tambah Guru
            </button>
        </div>

        <!-- Alert Notifikasi -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter & Pencarian -->
        <div class="bg-white p-4 rounded-lg shadow mb-6 flex flex-col md:flex-row justify-between gap-4">
            <form method="GET" action="{{ route('admin.teachers.index') }}" class="flex flex-col md:flex-row gap-4 w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau NUPTK..." class="border border-gray-300 rounded-lg px-4 py-2 w-full md:w-1/3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <div class="flex gap-2">
                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg">Cari</button>
                    <a href="{{ route('admin.teachers.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg flex items-center">Reset</a>
                </div>
            </form>
        </div>

        <!-- Tabel Data Guru -->
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NUPTK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Guru</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. HP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profesi / Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($teachers as $index => $teacher)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $teachers->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $teacher->nuptk }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $teacher->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $teacher->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $teacher->phone ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                                @if($teacher->role_type == 'Wali Kelas')
                                    <span class="text-blue-600">Wali Kelas {{ $teacher->classModel->name ?? '' }}</span>
                                @else
                                    <span class="text-gray-600">Guru Mapel</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $teacher->subject ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $teacher->status == 'Aktif' ? 'bg-green-100 text-green-800' : ($teacher->status == 'Cuti' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $teacher->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium flex justify-center gap-2">
                                <button onclick="openEditModal({{ json_encode($teacher) }})" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded">Edit</button>
                                <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data guru dan akun login ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data guru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $teachers->withQueryString()->links() }}
        </div>
    </div>

    <!-- MODAL TAMBAH GURU -->
    <div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex justify-center items-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold mb-4">Tambah Guru Baru</h2>
            <form action="{{ route('admin.teachers.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">NUPTK (Username & Password Default)</label>
                    <input type="text" name="nuptk" required class="border rounded-lg w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap & Gelar</label>
                    <input type="text" name="name" required class="border rounded-lg w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Jenis Kelamin</label>
                    <select name="gender" required class="border rounded-lg w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">No. HP / WhatsApp</label>
                    <input type="text" name="phone" class="border rounded-lg w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Profesi / Role</label>
                    <select name="role_type" id="add_role_type" onchange="toggleAddClassInput()" required class="border rounded-lg w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Guru Mapel">Guru Mapel</option>
                        <option value="Wali Kelas">Wali Kelas</option>
                    </select>
                </div>
                <div class="mb-4 hidden" id="addClassContainer">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Kelas Binaan (Wali Kelas)</label>
                    <select name="class_id" class="border rounded-lg w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes ?? [] as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Mata Pelajaran yang Dibawakan</label>
                    <input type="text" name="subject" placeholder="Contoh: Matematika / Fisika" class="border rounded-lg w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                    <select name="status" required class="border rounded-lg w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Aktif">Aktif</option>
                        <option value="Cuti">Cuti</option>
                        <option value="Keluar">Keluar</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal('addModal')" class="bg-gray-300 px-4 py-2 rounded-lg">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT GURU -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex justify-center items-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold mb-4">Edit Data Guru</h2>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">NUPTK</label>
                    <input type="text" id="edit_nuptk" name="nuptk" required class="border rounded-lg w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap & Gelar</label>
                    <input type="text" id="edit_name" name="name" required class="border rounded-lg w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Jenis Kelamin</label>
                    <select id="edit_gender" name="gender" required class="border rounded-lg w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">No. HP / WhatsApp</label>
                    <input type="text" id="edit_phone" name="phone" class="border rounded-lg w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Profesi / Role</label>
                    <select name="role_type" id="edit_role_type" onchange="toggleEditClassInput()" required class="border rounded-lg w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Guru Mapel">Guru Mapel</option>
                        <option value="Wali Kelas">Wali Kelas</option>
                    </select>
                </div>
                <div class="mb-4 hidden" id="editClassContainer">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Kelas Binaan (Wali Kelas)</label>
                    <select name="class_id" id="edit_class_id" class="border rounded-lg w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes ?? [] as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Mata Pelajaran yang Dibawakan</label>
                    <input type="text" id="edit_subject" name="subject" class="border rounded-lg w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                    <select id="edit_status" name="status" required class="border rounded-lg w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Aktif">Aktif</option>
                        <option value="Cuti">Cuti</option>
                        <option value="Keluar">Keluar</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal('editModal')" class="bg-gray-300 px-4 py-2 rounded-lg">Batal</button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg">Perbarui</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Handler Modal & Dynamic Input -->
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
        function toggleAddClassInput() {
            let roleType = document.getElementById('add_role_type').value;
            let container = document.getElementById('addClassContainer');
            if (roleType === 'Wali Kelas') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }
        function toggleEditClassInput() {
            let roleType = document.getElementById('edit_role_type').value;
            let container = document.getElementById('editClassContainer');
            if (roleType === 'Wali Kelas') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }
        function openEditModal(teacher) {
            document.getElementById('editForm').action = "/admin/teachers/" + teacher.id;
            document.getElementById('edit_nuptk').value = teacher.nuptk;
            document.getElementById('edit_name').value = teacher.name;
            document.getElementById('edit_gender').value = teacher.gender;
            document.getElementById('edit_phone').value = teacher.phone || '';
            document.getElementById('edit_role_type').value = teacher.role_type;
            document.getElementById('edit_subject').value = teacher.subject || '';
            document.getElementById('edit_status').value = teacher.status;
            
            toggleEditClassInput();
            if(teacher.role_type === 'Wali Kelas') {
                document.getElementById('edit_class_id').value = teacher.class_id;
            }

            openModal('editModal');
        }
    </script>
</x-app-layout>