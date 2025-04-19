<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Alumni') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('alumni.store') }}">
                        @csrf

                        <!-- NIM -->
                        <div class="mb-4">
                            <label for="nim" class="block text-sm font-medium text-gray-700">NIM</label>
                            <input type="text" name="nim" id="nim" value="{{ old('nim') }}" required
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('nim')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nama Lengkap -->
                        <div class="mb-4">
                            <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('full_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tahun Lulus -->
                        <div class="mb-4">
                            <label for="graduation_year" class="block text-sm font-medium text-gray-700">Tahun Lulus</label>
                            <input type="number" name="graduation_year" id="graduation_year" value="{{ old('graduation_year') }}" required min="1990" max="{{ date('Y') }}"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('graduation_year')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jurusan -->
                        <div class="mb-4">
                            <label for="major" class="block text-sm font-medium text-gray-700">Jurusan</label>
                            <select name="major" id="major" required
                                    class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <option value="">Pilih Jurusan</option>
                                @foreach($majors as $key => $value)
                                    <option value="{{ $key }}" {{ old('major') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('major')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nomor Telepon -->
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Alamat -->
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                            <textarea name="address" id="address" rows="3"
                                      class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jika Admin, Tambahkan Opsi untuk Membuat User -->
                        @if(Auth::user()->role === 'admin')
                            <div class="mb-4">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="create_user" id="create_user" value="1" {{ old('create_user') ? 'checked' : '' }}
                                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="create_user" class="font-medium text-gray-700">Buat User untuk Alumni</label>
                                        <p class="text-gray-500">Buat akun login untuk alumni ini</p>
                                    </div>
                                </div>
                            </div>

                            <div id="user_fields" class="mb-4 p-4 bg-gray-50 rounded-lg" style="display: none;">
                                <!-- Email -->
                                <div class="mb-4">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                                           class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="mb-4">
                                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                    <input type="password" name="password" id="password"
                                           class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('password')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Konfirmasi Password -->
                                <div class="mb-4">
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                           class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ Auth::user()->role === 'admin' ? route('alumni.index') : route('dashboard') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Toggle visibility of user fields based on checkbox
        document.addEventListener('DOMContentLoaded', function() {
            const createUserCheckbox = document.getElementById('create_user');
            const userFieldsDiv = document.getElementById('user_fields');
            
            if (createUserCheckbox && userFieldsDiv) {
                // Initial state
                userFieldsDiv.style.display = createUserCheckbox.checked ? 'block' : 'none';
                
                // Handle change
                createUserCheckbox.addEventListener('change', function() {
                    userFieldsDiv.style.display = this.checked ? 'block' : 'none';
                });
            }
        });
    </script>
    @endpush
</x-app-layout>