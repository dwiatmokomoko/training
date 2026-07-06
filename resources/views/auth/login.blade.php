<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TNA System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --ma-green: #157347;
            --ma-dark-green: #0f5132;
            --ma-yellow: #d9a441;
            --line: #e2e8f0;
            --text-main: #182230;
            --text-muted: #667085;
        }

        body {
            min-height: 100vh;
            background: #f6f8fa;
            color: var(--text-main);
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .login-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(0, 1fr) 460px;
        }

        .login-brand {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem;
            background: #0b2f21;
            color: white;
        }

        .brand-mark {
            width: 54px;
            height: 54px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--ma-yellow);
            color: var(--ma-dark-green);
            font-weight: 900;
            font-size: 1.25rem;
        }

        .login-brand h1 {
            max-width: 720px;
            margin: 2rem 0 0;
            font-size: clamp(2rem, 4vw, 3.6rem);
            line-height: 1.05;
            font-weight: 850;
            letter-spacing: 0;
        }

        .login-brand p {
            max-width: 620px;
            margin-top: 1rem;
            color: rgba(255,255,255,0.72);
            line-height: 1.7;
        }

        .login-panel {
            display: flex;
            align-items: center;
            padding: 2rem;
            background: #ffffff;
            border-left: 1px solid rgba(255,255,255,0.08);
        }

        .login-card {
            width: 100%;
        }

        .login-card h2 {
            font-weight: 850;
            margin-bottom: 0.35rem;
        }

        .login-card .muted {
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        .form-control {
            border-color: var(--line);
            border-radius: 8px;
            padding: 0.72rem 0.8rem;
        }

        .form-control:focus {
            border-color: var(--ma-green);
            box-shadow: 0 0 0 3px rgba(21, 115, 71, 0.12);
        }

        .btn-login {
            width: 100%;
            border: 0;
            border-radius: 8px;
            padding: 0.78rem 1rem;
            background: var(--ma-green);
            color: white;
            font-weight: 750;
        }

        .btn-login:hover {
            background: var(--ma-dark-green);
            color: white;
        }

        .account-list {
            display: grid;
            gap: 0.5rem;
            margin-top: 1.4rem;
            padding: 0;
            list-style: none;
        }

        .account-list li {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.68rem 0.75rem;
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--text-muted);
            font-size: 0.88rem;
        }

        .account-list strong {
            color: var(--text-main);
        }

        @media (max-width: 991.98px) {
            .login-shell {
                grid-template-columns: 1fr;
            }

            .login-brand {
                min-height: 34vh;
                padding: 2rem;
            }

            .login-panel {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <main class="login-shell">
        <section class="login-brand">
            <div>
                <div class="d-flex align-items-center gap-3">
                    <div class="brand-mark">MA</div>
                    <div>
                        <h5 class="mb-0 fw-bold">TNA System</h5>
                        <small class="text-white-50">Mahkamah Agung</small>
                    </div>
                </div>
                <h1>Analisis kebutuhan pelatihan berbasis role.</h1>
                <p>Admin, petugas kepegawaian, dan pimpinan masuk dengan akun masing-masing agar input data, analisis, approval, dan laporan berjalan sesuai kewenangan.</p>
            </div>
            <small class="text-white-50">Sistem Analisa Kebutuhan Pelatihan</small>
        </section>

        <section class="login-panel">
            <div class="login-card">
                <h2>Masuk</h2>
                <p class="muted">Gunakan akun sesuai role pengguna.</p>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('login.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" autofocus required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>
                    <button type="submit" class="btn-login">
                        <i class="fas fa-right-to-bracket me-2"></i>
                        Login
                    </button>
                </form>

                <ul class="account-list">
                    <li><strong>Admin</strong><span>admin@pn-sleman.go.id</span></li>
                    <li><strong>Kepegawaian</strong><span>sdm@pn-sleman.go.id</span></li>
                    <li><strong>Pimpinan</strong><span>pimpinan@pn-sleman.go.id</span></li>
                </ul>
                <small class="d-block mt-2 text-muted">Password awal: <strong>password</strong></small>
            </div>
        </section>
    </main>
</body>
</html>
