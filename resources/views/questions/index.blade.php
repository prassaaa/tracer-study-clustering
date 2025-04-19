<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pertanyaan Survei') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold">Pertanyaan untuk: {{ $survey->title }}</h3>
                            <p class="text-sm text-gray-600">Periode: {{ $survey->start_date->format('d M Y') }} - {{ $survey->end_date->format('d M Y') }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('surveys.questions.create', $survey->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Tambah Pertanyaan
                            </a>
                            <a href="{{ route('surveys.index') }}" class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 font-bold py-2 px-4 rounded">
                                Kembali ke Survei
                            </a>
                        </div>
                    </div>

                    @if($questions->count() > 0)
                        <div id="questions-container" class="space-y-4">
                            @foreach($questions as $question)
                                <div class="question-item border rounded-lg p-4 bg-gray-50" data-id="{{ $question->id }}">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold">{{ $question->question_text }}</h4>
                                            <p class="text-sm text-gray-600">Tipe: {{ $question->question_type }}</p>
                                            @if($question->is_required)
                                                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Wajib</span>
                                            @endif
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('surveys.questions.edit', [$survey->id, $question->id]) }}" class="text-yellow-600 hover:text-yellow-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('surveys.questions.destroy', [$survey->id, $question->id]) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                            <div class="cursor-move text-gray-400 handle">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                                                </svg>
                                            </div>
                                        </div>
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
                        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                            <p class="text-center text-yellow-700">
                                Survei ini belum memiliki pertanyaan. 
                                <a href="{{ route('surveys.questions.create', $survey->id) }}" class="text-blue-600 hover:underline">
                                    Tambahkan pertanyaan sekarang
                                </a>.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const questionsContainer = document.getElementById('questions-container');
            
            if (questionsContainer) {
                const sortable = new Sortable(questionsContainer, {
                    handle: '.handle',
                    animation: 150,
                    onEnd: function(evt) {
                        const questionIds = Array.from(questionsContainer.querySelectorAll('.question-item'))
                            .map(item => item.dataset.id);
                            
                        // Kirim data pengurutan ke server
                        fetch("{{ route('surveys.questions.reorder', $survey->id) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                questions: questionIds
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log('Pengurutan berhasil disimpan');
                            } else {
                                console.error('Gagal menyimpan pengurutan');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>