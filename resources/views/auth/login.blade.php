<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracer Study Alumni - Login</title>
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
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }
        
        .login-container {
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
            padding: 3rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
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
        
        .forgot-password {
            display: block;
            text-align: right;
            color: var(--primary-blue);
            text-decoration: none;
            font-size: 0.9rem;
            margin-top: -0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            cursor: pointer;
        }
        
        .remember-me input {
            margin-right: 0.5rem;
            accent-color: var(--primary-blue);
            width: 16px;
            height: 16px;
        }
        
        .remember-me span {
            color: var(--gray-600);
            font-size: 0.9rem;
        }
        
        .login-btn {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--primary-blue);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .login-btn:hover {
            background-color: #1a365d;
        }
        
        .register-link {
            text-align: center;
            margin-top: 2rem;
            color: var(--gray-600);
            font-size: 0.9rem;
        }
        
        .register-link a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
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
    <div class="login-container">
        <div class="image-section">
            <div class="image-content">
                <h2>TRACER STUDY</h2>
                <p>Selamat datang di platform Tracer Study Alumni. Kami membantu institusi melacak dan mengevaluasi perjalanan karir para alumni.</p>
                <div style="width: 200px; height: 200px; margin: 0 auto; background-color: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; justify-content: center; align-items: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
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
                <h1>Masuk ke Akun Anda</h1>
                <p>Silakan masukkan kredensial Anda untuk melanjutkan</p>
            </div>
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="input-group">
                    <label class="input-label" for="email">Email</label>
                    <input class="input-field" type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email Anda" required autofocus>
                    @error('email')
                        <span class="error-message" style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="input-group">
                    <label class="input-label" for="password">Password</label>
                    <input class="input-field" type="password" id="password" name="password" placeholder="Masukkan password Anda" required>
                    @error('password')
                        <span class="error-message" style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-password">Lupa password?</a>
                @endif
                <label class="remember-me">
                    <input type="checkbox" name="remember" id="remember">
                    <span>Ingat saya</span>
                </label>
                <button type="submit" class="login-btn">Masuk</button>
                <div class="register-link">
                    Belum memiliki akun? <a href="{{ route('register') }}">Daftar sekarang</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>