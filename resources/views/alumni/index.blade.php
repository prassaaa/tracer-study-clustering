<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Alumni') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Data Alumni</h3>
                        <a href="{{ route('alumni.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Tambah Alumni
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">NIM</th>
                                    <th class="py-2 px-4 border-b text-left">Nama</th>
                                    <th class="py-2 px-4 border-b text-left">Jurusan</th>
                                    <th class="py-2 px-4 border-b text-left">Tahun Lulus</th>
                                    <th class="py-2 px-4 border-b text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($alumni as $alumnus)
                                    <tr>
                                        <td class="py-2 px-4 border-b">{{ $alumnus->nim }}</td>
                                        <td class="py-2 px-4 border-b">{{ $alumnus->full_name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $alumnus->major }}</td>
                                        <td class="py-2 px-4 border-b">{{ $alumnus->graduation_year }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('alumni.show', $alumnus->id) }}" class="text-blue-600 hover:text-blue-900">Detail</a>
                                                <a href="{{ route('alumni.edit', $alumnus->id) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                                <form method="POST" action="{{ route('alumni.destroy', $alumnus->id) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-2 px-4 border-b text-center">Tidak ada data alumni.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $alumni->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>