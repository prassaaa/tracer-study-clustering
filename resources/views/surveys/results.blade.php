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
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $survey->title }}</h3>
                            <p class="text-sm text-gray-600">Periode: {{ $survey->start_date->format('d M Y') }} - {{ $survey->end_date->format('d M Y') }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('responses.export', $survey->id) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Export Excel
                            </a>
                            <a href="{{ route('surveys.index') }}" class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 font-bold py-2 px-4 rounded">
                                Kembali
                            </a>
                        </div>
                    </div>

                    <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                        <p class="text-center">
                            <span class="font-semibold">Total Responden:</span> {{ $respondentsCount }} alumni
                        </p>
                    </div>

                    <!-- Hasil per Pertanyaan -->
                    <div class="space-y-8 mt-6">
                        @foreach($results as $questionResult)
                            <div class="border rounded-lg p-4">
                                <h4 class="font-semibold mb-2">{{ $questionResult['question']->question_text }}</h4>
                                <p class="text-sm text-gray-600 mb-4">
                                    Tipe: {{ $questionResult['question']->question_type }} | 
                                    Dijawab: {{ $questionResult['count'] }} responden
                                </p>

                                @switch($questionResult['question']->question_type)
                                    @case('text')
                                    @case('textarea')
                                        <div class="space-y-2 max-h-64 overflow-y-auto p-2 bg-gray-50 rounded">
                                            @foreach($questionResult['responses'] as $response)
                                                <div class="p-2 border-b">
                                                    <p class="text-sm">{{ $response->answer }}</p>
                                                    <p class="text-xs text-gray-500">- {{ $response->alumni->full_name }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                        @break
                                        
                                    @case('radio')
                                    @case('select')
                                        @if(isset($questionResult['distribution']))
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <canvas id="chart_{{ $questionResult['question']->id }}" height="200"></canvas>
                                                </div>
                                                <div>
                                                    <table class="min-w-full bg-white">
                                                        <thead class="bg-gray-100">
                                                            <tr>
                                                                <th class="py-2 px-4 border-b text-left">Opsi</th>
                                                                <th class="py-2 px-4 border-b text-left">Jumlah</th>
                                                                <th class="py-2 px-4 border-b text-left">Persentase</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($questionResult['distribution'] as $option => $count)
                                                                <tr>
                                                                    <td class="py-2 px-4 border-b">{{ $option }}</td>
                                                                    <td class="py-2 px-4 border-b">{{ $count }}</td>
                                                                    <td class="py-2 px-4 border-b">
                                                                        {{ number_format(($count / $questionResult['count']) * 100, 1) }}%
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                        @break
                                        
                                    @case('checkbox')
                                        @if(isset($questionResult['distribution']))
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <canvas id="chart_{{ $questionResult['question']->id }}" height="200"></canvas>
                                                </div>
                                                <div>
                                                    <table class="min-w-full bg-white">
                                                        <thead class="bg-gray-100">
                                                            <tr>
                                                                <th class="py-2 px-4 border-b text-left">Opsi</th>
                                                                <th class="py-2 px-4 border-b text-left">Jumlah</th>
                                                                <th class="py-2 px-4 border-b text-left">Persentase dari Responden</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($questionResult['distribution'] as $option => $count)
                                                                <tr>
                                                                    <td class="py-2 px-4 border-b">{{ $option }}</td>
                                                                    <td class="py-2 px-4 border-b">{{ $count }}</td>
                                                                    <td class="py-2 px-4 border-b">
                                                                        {{ number_format(($count / $questionResult['count']) * 100, 1) }}%
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                        @break
                                        
                                    @case('number')
                                    @case('rating')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                @php
                                                    $values = $questionResult['responses']->pluck('answer')->map(function($item) {
                                                        return (float) $item;
                                                    });
                                                    $average = $values->avg();
                                                    $min = $values->min();
                                                    $max = $values->max();
                                                @endphp
                                                
                                                <canvas id="chart_{{ $questionResult['question']->id }}" height="200"></canvas>
                                            </div>
                                            <div>
                                                <div class="grid grid-cols-3 gap-4 mt-4">
                                                    <div class="p-3 bg-blue-50 rounded-lg text-center">
                                                        <p class="text-sm text-gray-600">Rata-rata</p>
                                                        <p class="text-xl font-bold">{{ number_format($average, 1) }}</p>
                                                    </div>
                                                    <div class="p-3 bg-green-50 rounded-lg text-center">
                                                        <p class="text-sm text-gray-600">Minimum</p>
                                                        <p class="text-xl font-bold">{{ number_format($min, 1) }}</p>
                                                    </div>
                                                    <div class="p-3 bg-purple-50 rounded-lg text-center">
                                                        <p class="text-sm text-gray-600">Maksimum</p>
                                                        <p class="text-xl font-bold">{{ number_format($max, 1) }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @break
                                        
                                    @default
                                        <div class="space-y-2 max-h-64 overflow-y-auto p-2 bg-gray-50 rounded">
                                            @foreach($questionResult['responses'] as $response)
                                                <div class="p-2 border-b">
                                                    <p class="text-sm">{{ $response->answer }}</p>
                                                    <p class="text-xs text-gray-500">- {{ $response->alumni->full_name }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                @endswitch
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($results as $questionResult)
                @if(in_array($questionResult['question']->question_type, ['radio', 'select', 'checkbox']) && isset($questionResult['distribution']))
                    // Buat pie chart untuk pertanyaan pilihan
                    new Chart(
                        document.getElementById('chart_{{ $questionResult['question']->id }}'),
                        {
                            type: 'pie',
                            data: {
                                labels: @json(array_keys($questionResult['distribution'])),
                                datasets: [{
                                    data: @json(array_values($questionResult['distribution'])),
                                    backgroundColor: [
                                        'rgba(54, 162, 235, 0.6)',
                                        'rgba(255, 99, 132, 0.6)',
                                        'rgba(255, 206, 86, 0.6)',
                                        'rgba(75, 192, 192, 0.6)',
                                        'rgba(153, 102, 255, 0.6)',
                                        'rgba(255, 159, 64, 0.6)',
                                        'rgba(199, 199, 199, 0.6)',
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'right',
                                    }
                                }
                            }
                        }
                    );
                @elseif(in_array($questionResult['question']->question_type, ['number', 'rating']))
                    // Buat histogram untuk pertanyaan numerik
                    @php
                        $values = $questionResult['responses']->pluck('answer')->map(function($item) {
                            return (float) $item;
                        });
                        
                        // Membuat bins untuk histogram
                        $min = floor($values->min());
                        $max = ceil($values->max());
                        $bins = [];
                        $binLabels = [];
                        
                        if ($questionResult['question']->question_type == 'rating') {
                            // Rating 1-5
                            for ($i = 1; $i <= 5; $i++) {
                                $binLabels[] = $i;
                                $bins[] = $values->filter(function($value) use ($i) {
                                    return $value == $i;
                                })->count();
                            }
                        } else {
                            // Buat 5-10 bins untuk data numerik umum
                            $step = max(1, ceil(($max - $min) / 8));
                            for ($i = $min; $i <= $max; $i += $step) {
                                $binLabels[] = $i . '-' . ($i + $step - 1);
                                $bins[] = $values->filter(function($value) use ($i, $step) {
                                    return $value >= $i && $value < ($i + $step);
                                })->count();
                            }
                        }
                    @endphp
                    
                    new Chart(
                        document.getElementById('chart_{{ $questionResult['question']->id }}'),
                        {
                            type: 'bar',
                            data: {
                                labels: @json($binLabels),
                                datasets: [{
                                    label: 'Jumlah Responden',
                                    data: @json($bins),
                                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            precision: 0
                                        }
                                    }
                                }
                            }
                        }
                    );
                @endif
            @endforeach
        });
    </script>
    @endpush
</x-app-layout>