<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Pekerjaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Riwayat Pekerjaan</h3>
                        <a href="{{ route('employment-data.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Tambah Pekerjaan
                        </a>
                    </div>

                    @if(count($employmentData) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-2 px-4 border-b text-left">Perusahaan</th>
                                        <th class="py-2 px-4 border-b text-left">Posisi</th>
                                        <th class="py-2 px-4 border-b text-left">Industri</th>
                                        <th class="py-2 px-4 border-b text-left">Periode</th>
                                        <th class="py-2 px-4 border-b text-left">Status</th>
                                        <th class="py-2 px-4 border-b text-left">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employmentData as $job)
                                        <tr>
                                            <td class="py-2 px-4 border-b">{{ $job->company_name }}</td>
                                            <td class="py-2 px-4 border-b">{{ $job->position }}</td>
                                            <td class="py-2 px-4 border-b">{{ $job->industry }}</td>
                                            <td class="py-2 px-4 border-b">
                                                {{ $job->start_date->format('M Y') }} - 
                                                {{ $job->is_current_job ? 'Sekarang' : ($job->end_date ? $job->end_date->format('M Y') : 'N/A') }}
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                @if($job->is_current_job)
                                                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                                        Pekerjaan Saat Ini
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('employment-data.show', $job->id) }}" class="text-blue-600 hover:text-blue-900">Detail</a>
                                                    <a href="{{ route('employment-data.edit', $job->id) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                                    <form method="POST" action="{{ route('employment-data.destroy', $job->id) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                            <p class="text-center text-yellow-700">
                                Anda belum menambahkan data pekerjaan. 
                                <a href="{{ route('employment-data.create') }}" class="text-blue-600 hover:underline">
                                    Tambah data pekerjaan sekarang
                                </a>.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>