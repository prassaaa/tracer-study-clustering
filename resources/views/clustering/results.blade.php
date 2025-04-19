<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hasil Clustering') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold">Single Linkage Clustering</h3>
                            <p class="text-sm text-gray-600">Analisis pada {{ $clusterResult->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('clustering.export', $clusterResult->id) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Export Excel
                            </a>
                            <a href="{{ route('clustering.analyze') }}" class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 font-bold py-2 px-4 rounded">
                                Analisis Baru
                            </a>
                        </div>
                    </div>

                    <!-- Parameter yang Digunakan -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-semibold mb-3">Parameter Analisis</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-gray-600 text-sm">Variabel</p>
                                <ul class="mt-1 pl-5 text-sm list-disc">
                                    @foreach($clusterResult->parameters['variables'] as $variable)
                                        <li>
                                            @if($variable == 'salary')
                                                Gaji
                                            @elseif($variable == 'waiting_period')
                                                Waktu Tunggu Kerja
                                            @elseif($variable == 'is_relevant')
                                                Relevansi Pekerjaan
                                            @else
                                                {{ $variable }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Metrik Jarak</p>
                                <p class="font-medium">
                                    @if($clusterResult->parameters['distance_metric'] == 'euclidean')
                                        Euclidean
                                    @elseif($clusterResult->parameters['distance_metric'] == 'manhattan')
                                        Manhattan
                                    @elseif($clusterResult->parameters['distance_metric'] == 'cosine')
                                        Cosine
                                    @else
                                        {{ $clusterResult->parameters['distance_metric'] }}
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Threshold</p>
                                <p class="font-medium">{{ $clusterResult->parameters['threshold'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Visualisasi Dendrogram -->
                    @if(isset($visualizationData) && $visualizationData)
                        <div class="mb-6">
                            <h4 class="font-semibold mb-3">Dendrogram Clustering</h4>
                            <div class="bg-white p-2 border rounded-lg" style="height: 400px;">
                                <div id="dendrogram" style="width: 100%; height: 380px;"></div>
                            </div>
                        </div>
                    @endif

                    <!-- Perbandingan Cluster -->
                    <div class="mb-6">
                        <h4 class="font-semibold mb-3">Perbandingan Antar Cluster</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-2 px-4 border-b text-left">Cluster</th>
                                        <th class="py-2 px-4 border-b text-left">Jumlah Alumni</th>
                                        <th class="py-2 px-4 border-b text-left">Rata-rata Gaji</th>
                                        <th class="py-2 px-4 border-b text-left">Rata-rata Waktu Tunggu</th>
                                        <th class="py-2 px-4 border-b text-left">Tingkat Relevansi</th>
                                        <th class="py-2 px-4 border-b text-left">Industri Dominan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clusterStats as $clusterId => $stats)
                                        <tr>
                                            <td class="py-2 px-4 border-b font-medium">Cluster {{ $clusterId + 1 }}</td>
                                            <td class="py-2 px-4 border-b">{{ $stats['count'] }} alumni</td>
                                            <td class="py-2 px-4 border-b">Rp {{ number_format($stats['avg_salary'], 0, ',', '.') }}</td>
                                            <td class="py-2 px-4 border-b">{{ number_format($stats['avg_waiting_period'], 1) }} bulan</td>
                                            <td class="py-2 px-4 border-b">{{ number_format($stats['relevance_rate'], 1) }}%</td>
                                            <td class="py-2 px-4 border-b">
                                                @if(count($stats['industry_distribution']) > 0)
                                                    @php
                                                        $dominantIndustry = array_keys($stats['industry_distribution']->toArray())[0] ?? 'N/A';
                                                    @endphp
                                                    {{ $dominantIndustry }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Detail Per Cluster -->
                    <div class="mb-6">
                        <h4 class="font-semibold mb-3">Detail Cluster</h4>
                        
                        <div x-data="{ activeTab: 0 }">
                            <!-- Tab Buttons -->
                            <div class="border-b border-gray-200">
                                <nav class="flex -mb-px space-x-8">
                                    @foreach($clusterStats as $clusterId => $stats)
                                        <button class="py-2 px-1 border-b-2 font-medium text-sm"
                                                :class="activeTab === {{ $clusterId }} ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                                @click="activeTab = {{ $clusterId }}">
                                            Cluster {{ $clusterId + 1 }} ({{ $stats['count'] }})
                                        </button>
                                    @endforeach
                                </nav>
                            </div>
                            
                            <!-- Tab Content -->
                            @foreach($clusterStats as $clusterId => $stats)
                                <div x-show="activeTab === {{ $clusterId }}" class="py-4">
                                    @if($stats['count'] > 0)
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full bg-white border">
                                                <thead class="bg-gray-100">
                                                    <tr>
                                                        <th class="py-2 px-4 border-b text-left">NIM</th>
                                                        <th class="py-2 px-4 border-b text-left">Nama</th>
                                                        <th class="py-2 px-4 border-b text-left">Tahun Lulus</th>
                                                        <th class="py-2 px-4 border-b text-left">Jurusan</th>
                                                        <th class="py-2 px-4 border-b text-left">Perusahaan</th>
                                                        <th class="py-2 px-4 border-b text-left">Gaji</th>
                                                        <th class="py-2 px-4 border-b text-left">Waktu Tunggu</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($stats['members'] as $alumni)
                                                        <tr>
                                                            <td class="py-2 px-4 border-b">{{ $alumni->nim }}</td>
                                                            <td class="py-2 px-4 border-b">{{ $alumni->full_name }}</td>
                                                            <td class="py-2 px-4 border-b">{{ $alumni->graduation_year }}</td>
                                                            <td class="py-2 px-4 border-b">{{ $alumni->major }}</td>
                                                            <td class="py-2 px-4 border-b">
                                                                @if($alumni->currentJob)
                                                                    {{ $alumni->currentJob->company_name }}
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td class="py-2 px-4 border-b">
                                                                @if($alumni->currentJob)
                                                                    Rp {{ number_format($alumni->currentJob->salary, 0, ',', '.') }}
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td class="py-2 px-4 border-b">
                                                                @if($alumni->currentJob)
                                                                    {{ $alumni->currentJob->waiting_period }} bulan
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-center text-gray-500">Tidak ada alumni dalam cluster ini.</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($visualizationData) && $visualizationData)
        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/d3@7"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Visualisasi data dari server
                const dendrogramData = @json($visualizationData);
                
                // Implementasi visualisasi dendrogram menggunakan D3.js
                const width = document.getElementById('dendrogram').clientWidth;
                const height = document.getElementById('dendrogram').clientHeight;
                
                const svg = d3.select('#dendrogram')
                    .append('svg')
                    .attr('width', width)
                    .attr('height', height)
                    .append('g')
                    .attr('transform', `translate(40, 0)`);
                
                // Render dendrogram berdasarkan data dari Python API
                // Code D3.js untuk rendering dendrogram akan ditambahkan di sini
                // ...
            });
        </script>
        @endpush
    @endif
</x-app-layout>