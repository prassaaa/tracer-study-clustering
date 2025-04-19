<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracer Study Alumni - Register</title>
    <style>
        :root {
            --primary-blue: #1e40af;
            --light-blue: #3b82f6;
            --very-light-blue: #dbeafe;
            --white: #ffffff;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-800: #1f2937;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--gray-100);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem 1rem;
        }
        
        .register-container {
            display: flex;
            max-width: 1000px;
            width: 100%;
            background-color: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .image-section {
            flex: 1;
            background-color: var(--primary-blue);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: var(--white);
            padding: 2rem;
            position: relative;
            overflow: hidden;
            display: none; /* Hidden on mobile */
        }
        
        .image-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMTAwIDEwMCI+PHBhdGggZmlsbD0icmdiYSgyNTUsMjU1LDI1NSwwLjEpIiBkPSJNMTEsMTFtLTgsLjVhMTUsOCAwIDEsMCAxNiwwYTE1LDggMCAxLDAgLTE2LDBNNTAsMTVtLTgsLjVhMTUsOCAwIDEsMCAxNiwwYTE1LDggMCAxLDAgLTE2LDBNOTAsMjBtLTgsLjVhMTUsOCAwIDEsMCAxNiwwYTE1LDggMCAxLDAgLTE2LDBNNzAsNDVtLTgsLjVhMTUsOCAwIDEsMCAxNiwwYTE1LDggMCAxLDAgLTE2LDBNMzAsNTBtLTgsLjVhMTUsOCAwIDEsMCAxNiwwYTE1LDggMCAxLDAgLTE2LDBNODAsODBtLTgsLjVhMTUsOCAwIDEsMCAxNiwwYTE1LDggMCAxLDAgLTE2LDBNMjAsODBtLTgsLjVhMTUsOCAwIDEsMCAxNiwwYTE1LDggMCAxLDAgLTE2LDBNNTAsOTBtLTgsLjVhMTUsOCAwIDEsMCAxNiwwYTE1LDggMCAxLDAgLTE2LDAiLz48L3N2Zz4=');
            opacity: 0.2;
        }
        
        .image-content {
            z-index: 1;
            text-align: center;
        }
        
        .image-content h2 {
            font-size: 2.2rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .image-content p {
            margin-bottom: 2rem;
            line-height: 1.6;
            max-width: 400px;
        }
        
        .form-section {
            flex: 1;
            padding: 2.5rem 2rem;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 2rem;
        }
        
        .logo {
            width: 50px;
            height: 50px;
            background-color: var(--primary-blue);
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .logo-text {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--gray-800);
            line-height: 1.2;
        }
        
        .logo-text span {
            display: block;
            font-size: 0.9rem;
            color: var(--gray-600);
            font-weight: normal;
        }
        
        .form-header {
            margin-bottom: 2rem;
        }
        
        .form-header h1 {
            font-size: 1.8rem;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
        }
        
        .form-header p {
            color: var(--gray-600);
        }
        
        .input-group {
            margin-bottom: 1.5rem;
        }
        
        .input-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--gray-800);
        }
        
        .input-field {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            outline: none;
            border-color: var(--light-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }
        
        .button-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
        }
        
        .login-link {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .login-link:hover {
            text-decoration: underline;
        }
        
        .register-btn {
            padding: 0.75rem 1.5rem;
            background-color: var(--primary-blue);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .register-btn:hover {
            background-color: #1a365d;
        }
        
        @media (min-width: 768px) {
            .image-section {
                display: flex;
            }
            
            .form-section {
                padding: 3rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="image-section">
            <div class="image-content">
                <h2>TRACER STUDY</h2>
                <p>Bergabunglah dengan platform Tracer Study Alumni untuk membantu kami melacak dan mengevaluasi perjalanan karir para lulusan.</p>
                <div style="width: 200px; height: 200px; margin: 0 auto; background-color: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; justify-content: center; align-items: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                </div>
            </div>
        </div>
        <div class="form-section">
            <div class="logo-container">
                <div class="logo">TS</div>
                <div class="logo-text">
                    TRACER STUDY
                    <span>Evaluasi Data Alumni</span>
                </div>
            </div>
            <div class="form-header">
                <h1>Daftar Akun Baru</h1>
                <p>Silakan lengkapi informasi di bawah ini untuk mendaftar</p>
            </div>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <div class="input-group">
                    <label class="input-label" for="name">Nama Lengkap</label>
                    <input class="input-field" type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap Anda" required autofocus>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="input-group">
                    <label class="input-label" for="email">Email</label>
                    <input class="input-field" type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email Anda" required>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="input-group">
                    <label class="input-label" for="password">Password</label>
                    <input class="input-field" type="password" id="password" name="password" placeholder="Masukkan password Anda" required>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="input-group">
                    <label class="input-label" for="password_confirmation">Konfirmasi Password</label>
                    <input class="input-field" type="password" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi password Anda" required>
                    @error('password_confirmation')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="button-container">
                    <a href="{{ route('login') }}" class="login-link">Sudah memiliki akun? Login</a>
                    <button type="submit" class="register-btn">Daftar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>