<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Statistik Alumni -->
                        <div class="bg-blue-100 p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-2">Total Alumni</h3>
                            <p class="text-3xl font-bold">{{ $totalAlumni }}</p>
                        </div>

                        <!-- Statistik Survei -->
                        <div class="bg-green-100 p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-2">Survei Aktif</h3>
                            <p class="text-3xl font-bold">{{ $activeSurveys }} / {{ $totalSurveys }}</p>
                        </div>

                        <!-- Statistik Employment -->
                        <div class="bg-yellow-100 p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-2">Alumni Bekerja</h3>
                            <p class="text-3xl font-bold">{{ $totalEmployed }}</p>
                        </div>

                        <!-- Rata-rata Gaji -->
                        <div class="bg-purple-100 p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-2">Rata-rata Gaji</h3>
                            <p class="text-3xl font-bold">Rp {{ number_format($averageSalary, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Grafik Distribusi Industri -->
                    <div class="mt-8 p-4 bg-white rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-4">Distribusi Industri</h3>
                        <div id="industry-chart" style="height: 300px;"></div>
                    </div>

                    <!-- Link ke Clustering -->
                    <div class="mt-8 p-4 bg-gray-100 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-2">Analisis Cluster</h3>
                        <p class="mb-4">Lakukan analisis clustering untuk mengelompokkan alumni berdasarkan data karir.</p>
                        <a href="{{ route('clustering.analyze') }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Mulai Analisis Clustering
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Perbaiki penggunaan Collection dengan mengubahnya ke array
        const industries = @json($industryDistribution->keys());
        const counts = @json($industryDistribution->values());
        
        const options = {
            series: [{
                name: 'Alumni',
                data: counts
            }],
            chart: {
                type: 'bar',
                height: 300
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                }
            },
            xaxis: {
                categories: industries,
            }
        };
        
        const chart = new ApexCharts(document.querySelector("#industry-chart"), options);
        chart.render();
    });
</script>
@endpush
</x-app-layout>