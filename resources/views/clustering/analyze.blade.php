<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Analisis Clustering') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">Single Linkage Clustering</h3>
                    <p class="mb-6 text-gray-600">
                        Analisis ini akan mengelompokkan alumni berdasarkan karakteristik karir mereka menggunakan algoritma Single Linkage Clustering.
                    </p>

                    @if(!$canCluster)
                        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-6">
                            <p class="text-center text-yellow-700">
                                Jumlah alumni dengan data pekerjaan ({{ $alumniCount }}) kurang dari yang dibutuhkan untuk clustering ({{ $minAlumniForClustering }}).
                                <br>
                                Silakan tambahkan lebih banyak data alumni dan pekerjaan terlebih dahulu.
                            </p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('clustering.process') }}">
                        @csrf

                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold mb-3">Variabel untuk Clustering</h4>
                            <p class="text-sm text-gray-600 mb-4">Pilih variabel yang akan digunakan untuk mengelompokkan alumni:</p>
                            
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="variables[]" id="var_salary" value="salary" checked
                                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="var_salary" class="font-medium text-gray-700">Gaji</label>
                                        <p class="text-gray-500">Tingkat pendapatan alumni</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="variables[]" id="var_waiting_period" value="waiting_period" checked
                                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="var_waiting_period" class="font-medium text-gray-700">Waktu Tunggu Kerja</label>
                                        <p class="text-gray-500">Berapa lama alumni mendapatkan pekerjaan setelah lulus</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="variables[]" id="var_is_relevant" value="is_relevant" checked
                                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="var_is_relevant" class="font-medium text-gray-700">Relevansi Pekerjaan</label>
                                        <p class="text-gray-500">Apakah pekerjaan relevan dengan jurusan yang diambil</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold mb-3">Parameter Clustering</h4>
                            
                            <div class="mb-4">
                                <label for="distance_metric" class="block text-sm font-medium text-gray-700">Metrik Jarak</label>
                                <select name="distance_metric" id="distance_metric" required
                                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="euclidean">Euclidean</option>
                                    <option value="manhattan">Manhattan</option>
                                    <option value="cosine">Cosine</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Metode pengukuran jarak antar data yang digunakan dalam clustering</p>
                            </div>
                            
                            <div class="mb-4">
                                <label for="threshold" class="block text-sm font-medium text-gray-700">Threshold</label>
                                <input type="number" name="threshold" id="threshold" value="0.5" step="0.1" min="0" max="2" required
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <p class="mt-1 text-xs text-gray-500">Nilai batas untuk membentuk cluster (semakin besar, semakin sedikit cluster yang terbentuk)</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" {{ !$canCluster ? 'disabled' : '' }}>
                                Mulai Analisis
                            </button>
                        </div>
                    </form>

                    @if($latestClusterResult)
                        <div class="mt-8 border-t pt-6">
                            <h4 class="font-semibold mb-3">Hasil Analisis Terbaru</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm mb-2">
                                    <span class="font-medium">Tanggal Analisis:</span> {{ $latestClusterResult->created_at->format('d M Y H:i') }}
                                </p>
                                <p class="text-sm mb-4">
                                    <span class="font-medium">Jumlah Cluster:</span> {{ count($latestClusterResult->results['clusters'] ?? []) }}
                                </p>
                                <div class="flex justify-end">
                                    <a href="{{ route('clustering.results', $latestClusterResult->id) }}" 
                                       class="bg-green-500 hover:bg-green-700 text-white text-sm font-bold py-1 px-3 rounded">
                                        Lihat Hasil
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>