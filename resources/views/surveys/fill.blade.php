<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengisian Survei') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">{{ $survey->title }}</h3>
                    <p class="mb-6 text-gray-600">{{ $survey->description }}</p>

                    <form method="POST" action="{{ route('surveys.submit', $survey->id) }}">
                        @csrf

                        @foreach($questions as $question)
                            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                                <div class="mb-2">
                                    <label for="question_{{ $question->id }}" class="block font-medium text-gray-700">
                                        {{ $question->question_text }}
                                        @if($question->is_required)
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                </div>

                                @switch($question->question_type)
                                    @case('text')
                                        <input type="text" id="question_{{ $question->id }}" name="answers[{{ $question->id }}]" 
                                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                               {{ $question->is_required ? 'required' : '' }}>
                                        @break
                                        
                                    @case('textarea')
                                        <textarea id="question_{{ $question->id }}" name="answers[{{ $question->id }}]" rows="3" 
                                                  class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                  {{ $question->is_required ? 'required' : '' }}></textarea>
                                        @break
                                        
                                    @case('number')
                                        <input type="number" id="question_{{ $question->id }}" name="answers[{{ $question->id }}]" 
                                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                               {{ $question->is_required ? 'required' : '' }}>
                                        @break
                                        
                                    @case('select')
                                        <select id="question_{{ $question->id }}" name="answers[{{ $question->id }}]" 
                                                class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                {{ $question->is_required ? 'required' : '' }}>
                                            <option value="">-- Pilih Jawaban --</option>
                                            @foreach($question->options as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        </select>
                                        @break
                                        
                                    @case('radio')
                                        <div class="mt-2 space-y-2">
                                            @foreach($question->options as $option)
                                                <div class="flex items-center">
                                                    <input type="radio" id="option_{{ $question->id }}_{{ $loop->index }}" 
                                                           name="answers[{{ $question->id }}]" value="{{ $option }}" 
                                                           class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                                                           {{ $question->is_required ? 'required' : '' }}>
                                                    <label for="option_{{ $question->id }}_{{ $loop->index }}" class="ml-3 block text-sm font-medium text-gray-700">
                                                        {{ $option }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @break
                                        
                                    @case('checkbox')
                                        <div class="mt-2 space-y-2">
                                            @foreach($question->options as $option)
                                                <div class="flex items-center">
                                                    <input type="checkbox" id="option_{{ $question->id }}_{{ $loop->index }}" 
                                                           name="answers[{{ $question->id }}][]" value="{{ $option }}" 
                                                           class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                                    <label for="option_{{ $question->id }}_{{ $loop->index }}" class="ml-3 block text-sm font-medium text-gray-700">
                                                        {{ $option }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @break
                                        
                                    @case('date')
                                        <input type="date" id="question_{{ $question->id }}" name="answers[{{ $question->id }}]" 
                                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                               {{ $question->is_required ? 'required' : '' }}>
                                        @break
                                        
                                    @case('rating')
                                        <div class="mt-2 flex space-x-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <div class="flex flex-col items-center">
                                                    <input type="radio" id="rating_{{ $question->id }}_{{ $i }}" 
                                                           name="answers[{{ $question->id }}]" value="{{ $i }}" 
                                                           class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                                                           {{ $question->is_required ? 'required' : '' }}>
                                                    <label for="rating_{{ $question->id }}_{{ $i }}" class="mt-1 text-sm text-gray-700">
                                                        {{ $i }}
                                                    </label>
                                                </div>
                                            @endfor
                                        </div>
                                        @break
                                        
                                    @default
                                        <input type="text" id="question_{{ $question->id }}" name="answers[{{ $question->id }}]" 
                                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                               {{ $question->is_required ? 'required' : '' }}>
                                @endswitch

                                @error('answers.' . $question->id)
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('surveys.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Kirim Jawaban
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>