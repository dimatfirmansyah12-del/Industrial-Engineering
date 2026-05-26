<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Login - {{ config('app.name', 'IE Monitoring') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --mouse-x: 50%;
                --mouse-y: 50%;
            }

            body {
                background: #050816;
            }

            .ie-login-shell {
                background:
                    radial-gradient(circle at var(--mouse-x) var(--mouse-y), rgba(56, 189, 248, 0.2), transparent 28rem),
                    radial-gradient(circle at 15% 12%, rgba(34, 197, 94, 0.18), transparent 24rem),
                    radial-gradient(circle at 84% 80%, rgba(59, 130, 246, 0.22), transparent 28rem),
                    linear-gradient(135deg, #050816 0%, #08111f 46%, #0f172a 100%);
            }

            .blueprint-grid {
                background-image:
                    linear-gradient(rgba(125, 211, 252, 0.07) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(125, 211, 252, 0.07) 1px, transparent 1px);
                background-size: 42px 42px;
                mask-image: linear-gradient(to bottom, black, transparent 90%);
            }

            .scan-line {
                animation: scan 6s linear infinite;
            }

            @keyframes scan {
                0% { transform: translateY(-100%); opacity: 0; }
                12% { opacity: 1; }
                90% { opacity: 0.55; }
                100% { transform: translateY(120vh); opacity: 0; }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <main id="loginScene" class="ie-login-shell relative min-h-screen overflow-hidden px-4 py-6 text-white sm:px-6 lg:px-8">
            <div class="blueprint-grid pointer-events-none absolute inset-0 opacity-80"></div>
            <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-cyan-300/60"></div>
            <div class="scan-line pointer-events-none absolute left-0 top-0 h-20 w-full bg-gradient-to-b from-transparent via-cyan-300/10 to-transparent"></div>

            <section class="relative mx-auto grid min-h-[calc(100vh-3rem)] w-full max-w-7xl items-center gap-8 lg:grid-cols-[1.05fr_.95fr]">
                <aside class="hidden lg:block">
                    <div class="max-w-2xl">
                        <h1 class="text-5xl font-extrabold leading-tight tracking-tight text-white xl:text-6xl">
                            Industrial Engineering
                            <span class="block bg-gradient-to-r from-cyan-200 via-white to-emerald-200 bg-clip-text text-transparent">Monitoring Control</span>
                        </h1>

                        <p class="mt-6 max-w-xl text-lg leading-8 text-slate-300">
                            Login ke pusat kontrol request IE untuk memantau proses request dalam satu alur digital.
                        </p>
                    </div>
                </aside>

                <section class="mx-auto w-full max-w-md lg:ml-auto">
                    <div class="relative overflow-hidden rounded-3xl border border-cyan-200/20 bg-slate-950/70 p-6 shadow-2xl shadow-cyan-950/40 backdrop-blur-xl sm:p-8">
                        <div class="pointer-events-none absolute -right-24 -top-24 h-48 w-48 rounded-full bg-cyan-300/20 blur-3xl"></div>
                        <div class="pointer-events-none absolute -bottom-24 -left-20 h-48 w-48 rounded-full bg-emerald-300/10 blur-3xl"></div>

                        <div class="relative">
                            <p class="text-xs font-bold uppercase tracking-[0.28em] text-cyan-200">Access Gateway</p>
                            <h2 class="mt-3 text-3xl font-extrabold text-white">Masuk ke Dashboard</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-300">
                                Gunakan akun yang sudah terdaftar untuk melanjutkan monitoring request Industrial Engineering.
                            </p>
                        </div>

                        <div class="relative mt-6">
                            <x-auth-session-status class="mb-4 text-sm text-emerald-200" :status="session('status')" />

                            @if ($errors->any())
                                <div class="mb-5 rounded-2xl border border-red-300/30 bg-red-500/10 p-4 text-sm text-red-100">
                                    <p class="font-bold">Login belum berhasil:</p>
                                    <ul class="mt-2 list-disc space-y-1 pl-5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                                @csrf

                                <div>
                                    <label for="email" class="block text-sm font-bold text-cyan-50">Email</label>
                                    <div class="mt-2 rounded-2xl border border-cyan-200/20 bg-white/[0.08] p-1 transition focus-within:border-cyan-300 focus-within:bg-white/[0.12]">
                                        <input
                                            id="email"
                                            class="block h-12 w-full rounded-xl border-0 bg-transparent px-4 text-sm text-white placeholder:text-slate-500 focus:ring-0"
                                            type="email"
                                            name="email"
                                            value="{{ old('email') }}"
                                            required
                                            autofocus
                                            autocomplete="username"
                                            placeholder="name@company.com"
                                        >
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between gap-3">
                                        <label for="password" class="block text-sm font-bold text-cyan-50">Password</label>
                                        @if (Route::has('password.request'))
                                            <a class="text-xs font-bold text-cyan-200 transition hover:text-white" href="{{ route('password.request') }}">
                                                Lupa password?
                                            </a>
                                        @endif
                                    </div>
                                    <div class="mt-2 rounded-2xl border border-cyan-200/20 bg-white/[0.08] p-1 transition focus-within:border-cyan-300 focus-within:bg-white/[0.12]">
                                        <input
                                            id="password"
                                            class="block h-12 w-full rounded-xl border-0 bg-transparent px-4 text-sm text-white placeholder:text-slate-500 focus:ring-0"
                                            type="password"
                                            name="password"
                                            required
                                            autocomplete="current-password"
                                            placeholder="Masukkan password"
                                        >
                                    </div>
                                </div>

                                <label for="remember_me" class="flex items-center gap-3 text-sm font-medium text-slate-300">
                                    <input id="remember_me" type="checkbox" class="rounded border-cyan-200/30 bg-slate-900 text-cyan-400 shadow-sm focus:ring-cyan-400" name="remember">
                                    <span>Ingat saya di perangkat ini</span>
                                </label>

                                <button type="submit" class="group relative flex h-[52px] w-full items-center justify-center overflow-hidden rounded-2xl bg-cyan-300 px-5 py-4 text-sm font-extrabold uppercase tracking-[0.18em] text-slate-950 shadow-lg shadow-cyan-500/30 transition hover:-translate-y-0.5 hover:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-200 focus:ring-offset-2 focus:ring-offset-slate-950">
                                    <span class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/40 to-transparent transition duration-700 group-hover:translate-x-full"></span>
                                    <span class="relative">Login</span>
                                </button>
                            </form>

                            @if (Route::has('register'))
                                <p class="mt-6 text-center text-sm text-slate-400">
                                    Belum punya akun?
                                    <a href="{{ route('register') }}" class="font-bold text-cyan-200 transition hover:text-white">Daftar user baru</a>
                                </p>
                            @endif
                        </div>
                    </div>
                </section>
            </section>
        </main>

        <script>
            const loginScene = document.getElementById('loginScene');

            loginScene?.addEventListener('pointermove', (event) => {
                const rect = loginScene.getBoundingClientRect();
                const x = ((event.clientX - rect.left) / rect.width) * 100;
                const y = ((event.clientY - rect.top) / rect.height) * 100;

                loginScene.style.setProperty('--mouse-x', `${x}%`);
                loginScene.style.setProperty('--mouse-y', `${y}%`);
            });
        </script>
    </body>
</html>
