<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MeetingMan') }}</title>

        <!-- Fonts - Using Inter as Elza alternative (Adobe Fonts requires subscription) -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .brand-gradient {
                background: linear-gradient(135deg, #8838e0 0%, #355afe 100%);
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-50">
            @include('layouts.navigation')

            <!-- Admin Notifications Banner -->
            @auth
                @php
                    $unreadNotifications = auth()->user()->unreadAdminNotifications()->with('auditLog')->get();
                @endphp
                @if($unreadNotifications->count() > 0)
                    <div class="bg-amber-50 border-b border-amber-200">
                        <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
                            @foreach($unreadNotifications as $notification)
                                <div class="flex items-center justify-between mb-2 last:mb-0">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-amber-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-sm text-amber-800">{{ $notification->message }}</span>
                                    </div>
                                    <form action="{{ route('notifications.dismiss', $notification) }}" method="POST" class="ml-4">
                                        @csrf
                                        <button type="submit" class="text-amber-600 hover:text-amber-800 text-sm font-medium">Dismiss</button>
                                    </form>
                                </div>
                            @endforeach
                            @if($unreadNotifications->count() > 1)
                                <div class="mt-2 pt-2 border-t border-amber-200">
                                    <form action="{{ route('notifications.dismiss-all') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-amber-600 hover:text-amber-800 text-sm font-medium">Dismiss all</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endauth

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow-sm border-b border-gray-200">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
