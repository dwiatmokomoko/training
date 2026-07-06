<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - TNA SAW PN Sleman</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-stone-950">
    <main class="grid min-h-screen place-items-center px-4">
        <section class="w-full max-w-md rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
            <div class="mb-6 flex items-center gap-3">
                <div class="grid h-12 w-12 place-items-center rounded-md bg-ma-gold font-black text-ma-red">MA</div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-ma-red">PN Sleman</p>
                    <h1 class="text-xl font-bold">Login TNA SAW</h1>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="grid gap-4">
                @csrf
                <label class="form-label">
                    Email
                    <input name="email" type="email" value="{{ old('email', 'admin@pn-sleman.go.id') }}" class="form-input" required autofocus>
                </label>
                <label class="form-label">
                    Password
                    <input name="password" type="password" class="form-input" required>
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-stone-700">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-stone-300 text-ma-red">
                    Ingat saya
                </label>
                <button class="btn-primary w-full" type="submit">Masuk</button>
            </form>

            <div class="mt-5 rounded-md bg-stone-100 p-3 text-xs leading-relaxed text-stone-600">
                Akun demo: admin@pn-sleman.go.id, kepegawaian@pn-sleman.go.id, pimpinan@pn-sleman.go.id. Password semua: password.
            </div>
        </section>
    </main>
</body>
</html>
