<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Installation Complete - MeetingMan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            500: '#a855f7',
                            600: '#9333ea',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4">
        <div class="w-full max-w-md text-center">
            <div class="bg-white shadow-lg rounded-lg p-8">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-gray-900 mb-2">Installation Complete!</h1>
                <p class="text-gray-600 mb-8">MeetingMan has been successfully installed and is ready to use.</p>

                <a href="{{ url('/') }}" class="inline-block px-8 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 font-medium">
                    Go to MeetingMan
                </a>

                <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-left">
                    <div class="text-yellow-800 font-medium text-sm">Security Reminder</div>
                    <div class="text-yellow-600 text-sm mt-1">
                        For security, you may want to delete or rename the installer files after confirming everything works.
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
