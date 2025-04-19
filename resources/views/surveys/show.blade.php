<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Survei') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $survey->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Periode: {{ $survey->start_date->format('d M Y') }} - {{ $survey->end_date->format('d M Y') }}
                            </p>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $survey->isActive() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $survey->isActive() ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                                <span class="ml-2 text-sm text-gray-500">
                                    {{ $respondentsCount }} responden
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex space-x-2">
                            @if(Gate::allows('admin'))
                                <a href="{{ route('surveys.edit', $survey->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                    Edit Survei
                                </a>
                                <a href="{{ route('surveys.questions.index', $survey->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Pertanyaan
                                </a>
                                <a href="{{ route('surveys.results', $survey->id) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Hasil
                                </a>
                            @elseif($survey->isActive() && !$hasResponded && Gate::allows('alumni'))
                                <a href="{{ route('surveys.fill', $survey->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Isi Survei
                                </a>
                            @elseif($hasResponded && Gate::allows('alumni'))
                                <span class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium bg-gray-100 text-gray-800">
                                    Sudah Diisi
                                </span>
                            @endif
                            <a href="{{ route('surveys.index') }}" class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 font-bold py-2 px-4 rounded">
                                Kembali
                            </a>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h4 class="font-medium text-gray-700 mb-2">Deskripsi</h4>
                        <p class="text-gray-600">{{ $survey->description ?: 'Tidak ada deskripsi' }}</p>
                    </div>

                    <div class="border-t pt-6">
                        <h4 class="font-medium text-gray-700 mb-4">Daftar Pertanyaan</h4>
                        
                        @if($survey->questions->count() > 0)
                            <div class="space-y-4">
                                @foreach($survey->questions as $question)
                                    <div class="border p-4 rounded-lg">
                                        <div class="flex justify-between">
                                            <div>
                                                <h5 class="font-medium">{{ $loop->iteration }}. {{ $question->question_text }}</h5>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    Tipe: {{ $question->question_type }}
                                                    @if($question->is_required)
                                                        <span class="text-red-500 ml-2">*Wajib</span>
                                                    @endif
                                                </p>
                                            </div>
                                            @if(Gate::allows('admin'))
                                                <div>
                                                    <a href="{{ route('surveys.questions.edit', [$survey->id, $question->id]) }}" class="text-yellow-600 hover:text-yellow-900 text-sm">
                                                        Edit
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        @if($question->hasOptions() && !empty($question->options))
                                            <div class="mt-2">
                                                <p class="text-xs text-gray-500">Opsi Jawaban:</p>
                                                <ul class="mt-1 pl-5 text-sm list-disc">
                                                    @foreach($question->options as $option)
                                                        <li>{{ $option }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">Belum ada pertanyaan dalam survei ini.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>