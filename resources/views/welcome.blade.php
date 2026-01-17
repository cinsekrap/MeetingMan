<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'MeetingMan') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .brand-gradient {
                background: linear-gradient(135deg, #8838e0 0%, #355afe 100%);
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen brand-gradient flex flex-col items-center justify-center">
            <div class="text-center">
                <h1 class="text-5xl font-bold text-white mb-4">MeetingMan</h1>
                <p class="text-xl text-white/80 mb-8">Track your 1:1 meetings, actions, and objectives</p>

                <div class="space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-white text-purple-600 font-semibold rounded-lg hover:bg-gray-100 transition">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-white text-purple-600 font-semibold rounded-lg hover:bg-gray-100 transition">
                            Log In
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-white/10 text-white font-semibold rounded-lg hover:bg-white/20 transition border border-white/30">
                                Register
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <footer class="absolute bottom-6 text-white/60 text-sm">
                &copy; {{ date('Y') }} MeetingMan
            </footer>
        </div>
    </body>
</html>
