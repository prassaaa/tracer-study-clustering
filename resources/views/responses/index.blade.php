<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Responden Survei') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $survey->title }}</h3>
                            <p class="text-sm text-gray-600">Periode: {{ $survey->start_date->format('d M Y') }} - {{ $survey->end_date->format('d M Y') }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('responses.export', $survey->id) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Export Excel
                            </a>
                            <a href="{{ route('responses.index') }}" class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 font-bold py-2 px-4 rounded">
                                Kembali
                            </a>
                        </div>
                    </div>

                    @if($respondents->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-2 px-4 border-b text-left">NIM</th>
                                        <th class="py-2 px-4 border-b text-left">Nama</th>
                                        <th class="py-2 px-4 border-b text-left">Jumlah Jawaban</th>
                                        <th class="py-2 px-4 border-b text-left">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($respondents as $respondent)
                                        <tr>
                                            <td class="py-2 px-4 border-b">{{ $respondent->nim }}</td>
                                            <td class="py-2 px-4 border-b">{{ $respondent->full_name }}</td>
                                            <td class="py-2 px-4 border-b">{{ $respondent->response_count }}</td>
                                            <td class="py-2 px-4 border-b">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('responses.show', [$survey->id, $respondent->id]) }}" class="text-blue-600 hover:text-blue-900">Lihat Detail</a>
                                                    <form method="POST" action="{{ route('responses.destroy', [$survey->id, $respondent->id]) }}" class="inline">
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

                        <div class="mt-6">
                            {{ $respondents->links() }}
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                            <p class="text-center text-yellow-700">
                                Belum ada responden yang mengisi survei ini.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>