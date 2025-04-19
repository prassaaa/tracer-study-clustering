<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hasil Survei') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">Daftar Survei</h3>
                    <p class="mb-6 text-gray-600">Pilih survei untuk melihat jawaban responden</p>

                    @if($surveys->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($surveys as $survey)
                                <div class="border rounded-lg p-4 hover:bg-gray-50">
                                    <h4 class="font-semibold text-lg mb-2">{{ $survey->title }}</h4>
                                    <p class="text-sm text-gray-600 mb-2">{{ Str::limit($survey->description, 100) }}</p>
                                    
                                    <div class="flex justify-between items-center mt-4">
                                        <span class="text-sm text-gray-500">
                                            <span class="font-medium">Responden:</span> {{ $survey->responses_count }}
                                        </span>
                                        <a href="{{ route('responses.index', ['survey_id' => $survey->id]) }}" 
                                           class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-bold py-1 px-3 rounded">
                                            Lihat Jawaban
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $surveys->links() }}
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                            <p class="text-center text-yellow-700">
                                Belum ada survei yang tersedia.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>