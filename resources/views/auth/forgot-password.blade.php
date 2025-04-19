<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracer Study Alumni - Reset Password</title>
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
        
        .reset-container {
            max-width: 600px;
            width: 100%;
            background-color: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 3rem 2rem;
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
            margin-bottom: 1rem;
        }
        
        .form-header p {
            color: var(--gray-600);
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .status-message {
            padding: 1rem;
            background-color: var(--very-light-blue);
            border-radius: 8px;
            margin-bottom: 1.5rem;
            color: var(--primary-blue);
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
        
        .reset-btn {
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
        
        .reset-btn:hover {
            background-color: #1a365d;
        }
        
        .button-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
        }
        
        .back-link {
            color: var(--gray-600);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: var(--primary-blue);
        }
        
        @media (min-width: 640px) {
            .reset-container {
                padding: 3rem;
            }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="logo-container">
            <div class="logo">TS</div>
            <div class="logo-text">
                TRACER STUDY
                <span>Evaluasi Data Alumni</span>
            </div>
        </div>
        <div class="form-header">
            <h1>Reset Password</h1>
            <p>Lupa password Anda? Tidak masalah. Cukup berikan alamat email Anda dan kami akan mengirimkan tautan reset password yang memungkinkan Anda memilih yang baru.</p>
        </div>
        
        @if (session('status'))
            <div class="status-message">
                {{ session('status') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="input-group">
                <label class="input-label" for="email">Email</label>
                <input class="input-field" type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email Anda" required autofocus>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="button-container">
                <a href="{{ route('login') }}" class="back-link">Kembali ke halaman login</a>
                <button type="submit" class="reset-btn">Kirim Link Reset Password</button>
            </div>
        </form>
    </div>
</body>
</html>