<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Pekerjaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('employment-data.update', $employment->id) }}">
                        @csrf
                        @method('PUT')

                        @if(Auth::user()->role === 'admin')
                            <!-- Pilih Alumni (hanya untuk admin) -->
                            <div class="mb-4">
                                <label for="alumni_id" class="block text-sm font-medium text-gray-700">Alumni</label>
                                <select name="alumni_id" id="alumni_id" required
                                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="">-- Pilih Alumni --</option>
                                    @foreach($alumni as $alumnus)
                                        <option value="{{ $alumnus->id }}" {{ old('alumni_id', $employment->alumni_id) == $alumnus->id ? 'selected' : '' }}>
                                            {{ $alumnus->nim }} - {{ $alumnus->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('alumni_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Nama Perusahaan -->
                        <div class="mb-4">
                            <label for="company_name" class="block text-sm font-medium text-gray-700">Nama Perusahaan</label>
                            <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $employment->company_name) }}" required
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('company_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Posisi -->
                        <div class="mb-4">
                            <label for="position" class="block text-sm font-medium text-gray-700">Posisi / Jabatan</label>
                            <input type="text" name="position" id="position" value="{{ old('position', $employment->position) }}" required
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('position')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Industri -->
                        <div class="mb-4">
                            <label for="industry" class="block text-sm font-medium text-gray-700">Industri</label>
                            <select name="industry" id="industry" required
                                    class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <option value="">-- Pilih Industri --</option>
                                @foreach($industries as $key => $value)
                                    <option value="{{ $key }}" {{ old('industry', $employment->industry) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('industry')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Salary -->
                        <div class="mb-4">
                            <label for="salary" class="block text-sm font-medium text-gray-700">Gaji (Rupiah)</label>
                            <input type="number" name="salary" id="salary" value="{{ old('salary', $employment->salary) }}" min="0" step="100000"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('salary')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Waktu Tunggu -->
                        <div class="mb-4">
                            <label for="waiting_period" class="block text-sm font-medium text-gray-700">Waktu Tunggu Kerja (bulan)</label>
                            <input type="number" name="waiting_period" id="waiting_period" value="{{ old('waiting_period', $employment->waiting_period) }}" required min="0" step="1"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <p class="mt-1 text-xs text-gray-500">Berapa lama (dalam bulan) Anda mendapatkan pekerjaan ini setelah lulus</p>
                            @error('waiting_period')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Relevansi dengan Jurusan -->
                        <div class="mb-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="is_relevant" id="is_relevant" value="1" {{ old('is_relevant', $employment->is_relevant) ? 'checked' : '' }}
                                           class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_relevant" class="font-medium text-gray-700">Relevan dengan Jurusan</label>
                                    <p class="text-gray-500">Apakah pekerjaan ini sesuai dengan jurusan yang ditempuh saat kuliah</p>
                                </div>
                            </div>
                        </div>

                        <!-- Tanggal Mulai -->
                        <div class="mb-4">
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $employment->start_date) }}" required
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('start_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pekerjaan Saat Ini -->
                        <div class="mb-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="is_current_job" id="is_current_job" value="1" {{ old('is_current_job', $employment->is_current_job) ? 'checked' : '' }}
                                           class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_current_job" class="font-medium text-gray-700">Pekerjaan Saat Ini</label>
                                    <p class="text-gray-500">Apakah ini pekerjaan Anda saat ini</p>
                                </div>
                            </div>
                        </div>

                        <!-- Tanggal Selesai (muncul jika bukan pekerjaan saat ini) -->
                        <div id="end_date_container" class="mb-4" style="display: {{ $employment->is_current_job ? 'none' : 'block' }};">
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $employment->end_date) }}"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('end_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('employment-data.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isCurrentJobCheckbox = document.getElementById('is_current_job');
            const endDateContainer = document.getElementById('end_date_container');
            const endDateInput = document.getElementById('end_date');
            
            // Function to toggle end date visibility
            function toggleEndDate() {
                if (isCurrentJobCheckbox.checked) {
                    endDateContainer.style.display = 'none';
                    endDateInput.value = '';
                    endDateInput.required = false;
                } else {
                    endDateContainer.style.display = 'block';
                    endDateInput.required = true;
                }
            }
            
            // Initial state
            toggleEndDate();
            
            // Handle change
            isCurrentJobCheckbox.addEventListener('change', toggleEndDate);
        });
    </script>
    @endpush
</x-app-layout>