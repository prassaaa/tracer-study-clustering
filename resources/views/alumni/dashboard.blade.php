<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Alumni') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Profil Alumni -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Profil Alumni</h3>
                        <a href="{{ route('alumni.edit') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit Profil
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">NIM</p>
                            <p class="font-semibold">{{ $alumni->nim }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Nama Lengkap</p>
                            <p class="font-semibold">{{ $alumni->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Tahun Lulus</p>
                            <p class="font-semibold">{{ $alumni->graduation_year }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Jurusan</p>
                            <p class="font-semibold">{{ $alumni->major }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Survei yang Belum Dijawab -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">Survei yang Perlu Diisi</h3>
                    
                    @if($pendingSurveys->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($pendingSurveys as $survey)
                                <div class="border rounded-lg p-4 hover:bg-gray-50">
                                    <h4 class="font-semibold mb-2">{{ $survey->title }}</h4>
                                    <p class="text-sm text-gray-600 mb-2">{{ Str::limit($survey->description, 100) }}</p>
                                    <p class="text-xs text-gray-500 mb-4">
                                        Periode: {{ $survey->start_date->format('d M Y') }} - {{ $survey->end_date->format('d M Y') }}
                                    </p>
                                    <a href="{{ route('surveys.fill', $survey->id) }}" 
                                       class="bg-green-500 hover:bg-green-700 text-white text-sm font-bold py-1 px-3 rounded">
                                        Isi Survei
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">Tidak ada survei aktif yang perlu diisi saat ini.</p>
                    @endif
                </div>
            </div>

            <!-- Data Pekerjaan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Data Pekerjaan</h3>
                        <a href="{{ route('employment-data.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Tambah Pekerjaan
                        </a>
                    </div>
                    
                    @if($currentJob)
                        <div class="border p-4 rounded-lg mb-4">
                            <div class="flex justify-between">
                                <div>
                                    <h4 class="font-semibold text-lg">{{ $currentJob->position }}</h4>
                                    <p class="text-gray-600">{{ $currentJob->company_name }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Pekerjaan Saat Ini</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 mt-4 gap-2 text-sm">
                                <div>
                                    <p class="text-gray-600">Industri</p>
                                    <p>{{ $currentJob->industry }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Gaji</p>
                                    <p>{{ $currentJob->formatted_salary }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Mulai Kerja</p>
                                    <p>{{ $currentJob->start_date->format('d M Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Relevan dengan Jurusan</p>
                                    <p>{{ $currentJob->is_relevant ? 'Ya' : 'Tidak' }}</p>
                                </div>
                            </div>
                            <div class="mt-4 flex space-x-2">
                                <a href="{{ route('employment-data.edit', $currentJob->id) }}" 
                                   class="bg-yellow-500 hover:bg-yellow-700 text-white text-xs font-bold py-1 px-2 rounded">
                                    Edit
                                </a>
                                <a href="{{ route('employment-data.show', $currentJob->id) }}" 
                                   class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-1 px-2 rounded">
                                    Detail
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('employment-data.index') }}" class="text-blue-600 hover:underline text-sm">
                            Lihat semua riwayat pekerjaan →
                        </a>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                            <p class="text-center text-yellow-700">
                                Anda belum menambahkan data pekerjaan. 
                                <a href="{{ route('employment-data.create') }}" class="text-blue-600 hover:underline">
                                    Tambah data pekerjaan sekarang
                                </a>.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>