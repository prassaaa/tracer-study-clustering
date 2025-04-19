<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Jawaban') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $survey->title }}</h3>
                            <p class="text-sm text-gray-600">Responden: {{ $alumni->full_name }} ({{ $alumni->nim }})</p>
                        </div>
                        <div>
                            <a href="{{ route('responses.index', ['survey_id' => $survey->id]) }}" class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 font-bold py-2 px-4 rounded">
                                Kembali
                            </a>
                        </div>
                    </div>

                    @if($responses->count() > 0)
                        <div class="space-y-6">
                            @foreach($responses as $response)
                                <div class="border p-4 rounded-lg">
                                    <h4 class="font-semibold">{{ $response->question->question_text }}</h4>
                                    <p class="text-sm text-gray-600 mb-2">Tipe: {{ $response->question->question_type }}</p>
                                    
                                    <div class="mt-2 p-3 bg-gray-50 rounded">
                                        <p class="font-medium">{{ $response->formatted_answer }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                            <p class="text-center text-yellow-700">
                                Tidak ada data jawaban yang tersedia.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>