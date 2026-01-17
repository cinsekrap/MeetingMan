<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MeetingMan') }}</title>

        <!-- Fonts -->
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
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex brand-gradient">
            <!-- Left side - Info -->
            <div class="hidden lg:flex lg:w-1/2 flex-col justify-center px-12 xl:px-24">
                <h1 class="text-4xl xl:text-5xl font-bold text-white mb-6">
                    {{ \App\Models\SiteSetting::get('site_name', 'MeetingMan') }}
                </h1>
                <p class="text-xl text-purple-100 mb-8">
                    The simple way to manage your 1:1 meetings and keep your team on track.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start">
                        <svg class="h-6 w-6 text-purple-200 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-purple-100">Track meeting notes, action items, and follow-ups in one place</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-6 w-6 text-purple-200 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-purple-100">Set objectives and monitor progress over time</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-6 w-6 text-purple-200 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-purple-100">Send meeting summaries and action reminders via email</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-6 w-6 text-purple-200 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-purple-100">Collaborate with your team in shared workspaces</span>
                    </li>
                </ul>
            </div>

            <!-- Right side - Login form -->
            <div class="w-full lg:w-1/2 flex flex-col justify-center items-center px-6 py-12">
                <div class="lg:hidden mb-8 text-center">
                    <h1 class="text-3xl font-bold text-white mb-2">
                        {{ \App\Models\SiteSetting::get('site_name', 'MeetingMan') }}
                    </h1>
                    <p class="text-purple-100">Manage your 1:1 meetings with ease</p>
                </div>

                <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-xl overflow-hidden rounded-lg">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
