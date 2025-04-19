<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Survei') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(Auth::user()->role === 'admin')
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold">Manajemen Survei</h3>
                            <a href="{{ route('surveys.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Buat Survei Baru
                            </a>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($surveys as $survey)
                            <div class="border rounded-lg overflow-hidden shadow-md">
                                <div class="bg-gray-50 p-4">
                                    <h4 class="font-semibold text-lg mb-2">{{ $survey->title }}</h4>
                                    <p class="text-sm text-gray-600">{{ Str::limit($survey->description, 100) }}</p>
                                </div>
                                <div class="p-4">
                                    <div class="mb-3">
                                        <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full {{ $survey->isActive() ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                            {{ $survey->isActive() ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                        <span class="text-xs text-gray-500 ml-2">
                                            {{ $survey->start_date->format('d M Y') }} - {{ $survey->end_date->format('d M Y') }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center">
                                        @if(Auth::user()->role === 'admin')
                                            <div class="flex space-x-2">
                                                <a href="{{ route('surveys.edit', $survey->id) }}" class="text-sm text-yellow-600 hover:text-yellow-900">Edit</a>
                                                <a href="{{ route('surveys.questions.index', $survey->id) }}" class="text-sm text-blue-600 hover:text-blue-900">Pertanyaan</a>
                                                <a href="{{ route('surveys.results', $survey->id) }}" class="text-sm text-green-600 hover:text-green-900">Hasil</a>
                                            </div>
                                            <form method="POST" action="{{ route('surveys.destroy', $survey->id) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus survei ini?')">Hapus</button>
                                            </form>
                                        @else
                                            <a href="{{ route('surveys.show', $survey->id) }}" class="text-sm text-blue-600 hover:text-blue-900">Lihat Detail</a>
                                            
                                            @if($survey->isActive())
                                                @if($survey->isAnsweredBy(Auth::user()->alumni->id ?? 0))
                                                    <span class="text-xs font-semibold py-1 px-2 bg-gray-200 text-gray-700 rounded-full">Sudah Diisi</span>
                                                @else
                                                    <a href="{{ route('surveys.fill', $survey->id) }}" class="inline-block bg-green-500 hover:bg-green-700 text-white text-xs font-bold py-1 px-2 rounded">
                                                        Isi Survei
                                                    </a>
                                                @endif
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3">
                                <p class="text-center text-gray-500">Tidak ada survei yang tersedia saat ini.</p>
                            </div>
                        @endforelse
                    </div>

                    @if(method_exists($surveys, 'links'))
                        <div class="mt-6">
                            {{ $surveys->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>