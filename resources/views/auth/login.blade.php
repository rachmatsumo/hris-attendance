<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Login</title>

    <!-- Fonts & Bootstrap -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1d6ee5, #42a5f5);
            font-family: 'Nunito', sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 30px;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .login-card h2 {
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
            color: #1d6ee5;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(29,110,229,0.25);
            border-color: #1d6ee5;
        }
        .btn-primary {
            background-color: #1d6ee5;
            border-color: #1d6ee5;
        }
        .btn-primary:hover {
            background-color: #155ab6;
            border-color: #155ab6;
        }
        .forgot-password {
            font-size: 0.875rem;
        }
        /* @media (max-width: 576px) {
            .login-card {
                padding: 20px;
            }
        } */
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center p-4">
        <div class="login-card">
            <h2>{{ config('app.name', 'Laravel') }}</h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input id="email" type="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">Remember Me</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>

                @if (Route::has('password.request'))
                    <div class="text-center forgot-password">
                        <a href="{{ route('password.request') }}">Forgot Your Password?</a>
                    </div>
                @endif
            </form>
        </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
