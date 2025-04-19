<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pekerjaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold">{{ $employmentData->position }} di {{ $employmentData->company_name }}</h3>
                        @if($employmentData->is_current_job)
                            <span class="inline-block mt-1 bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                Pekerjaan Saat Ini
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-700 mb-3">Informasi Pekerjaan</h4>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-600">Industri</p>
                                    <p class="font-medium">
                                        @php
                                            $industries = App\Models\EmploymentData::getIndustryOptions();
                                            echo $industries[$employmentData->industry] ?? $employmentData->industry;
                                        @endphp
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Perusahaan</p>
                                    <p class="font-medium">{{ $employmentData->company_name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Posisi / Jabatan</p>
                                    <p class="font-medium">{{ $employmentData->position }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Gaji</p>
                                    <p class="font-medium">Rp {{ number_format($employmentData->salary, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Periode Kerja</p>
                                    <p class="font-medium">
                                        {{ $employmentData->start_date->format('d M Y') }} - 
                                        {{ $employmentData->is_current_job ? 'Sekarang' : ($employmentData->end_date ? $employmentData->end_date->format('d M Y') : 'N/A') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Lama Bekerja</p>
                                    <p class="font-medium">{{ $employmentData->duration_in_months }} bulan</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-700 mb-3">Informasi Tambahan</h4>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-600">Waktu Tunggu Kerja</p>
                                    <p class="font-medium">{{ $employmentData->waiting_period }} bulan setelah lulus</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Relevansi dengan Jurusan</p>
                                    <p class="font-medium">{{ $employmentData->is_relevant ? 'Ya' : 'Tidak' }}</p>
                                </div>
                                @if(Auth::user()->role === 'admin')
                                    <div>
                                        <p class="text-sm text-gray-600">Alumni</p>
                                        <p class="font-medium">{{ $employmentData->alumni->full_name }} ({{ $employmentData->alumni->nim }})</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-8 space-x-3">
                        <a href="{{ route('employment-data.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                            Kembali
                        </a>
                        <a href="{{ route('employment-data.edit', $employmentData->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('employment-data.destroy', $employmentData->id) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" 
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data pekerjaan ini?')">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>