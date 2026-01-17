<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Install MeetingMan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#faf5ff',
                            100: '#f3e8ff',
                            200: '#e9d5ff',
                            300: '#d8b4fe',
                            400: '#c084fc',
                            500: '#a855f7',
                            600: '#9333ea',
                            700: '#7e22ce',
                            800: '#6b21a8',
                            900: '#581c87',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4">
        <div class="w-full max-w-xl">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-primary-600">MeetingMan</h1>
                <p class="text-gray-600 mt-2">Installation Wizard</p>
            </div>

            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex justify-between">
                    @php
                        $steps = [
                            'requirements' => 'Requirements',
                            'database' => 'Configure',
                            'migrate' => 'Migrate',
                            'admin' => 'Admin User',
                            'finalize' => 'Finalize',
                        ];
                        $currentStep = $currentStep ?? 'requirements';
                        $stepKeys = array_keys($steps);
                        $currentIndex = array_search($currentStep, $stepKeys);
                    @endphp
                    @foreach($steps as $key => $label)
                        @php
                            $stepIndex = array_search($key, $stepKeys);
                            $isComplete = $stepIndex < $currentIndex;
                            $isCurrent = $key === $currentStep;
                        @endphp
                        <div class="flex flex-col items-center flex-1">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                {{ $isComplete ? 'bg-green-500 text-white' : ($isCurrent ? 'bg-primary-500 text-white' : 'bg-gray-300 text-gray-600') }}">
                                @if($isComplete)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @else
                                    {{ $stepIndex + 1 }}
                                @endif
                            </div>
                            <span class="text-xs mt-1 {{ $isCurrent ? 'text-primary-600 font-medium' : 'text-gray-500' }}">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Content Card -->
            <div class="bg-white shadow-lg rounded-lg p-8">
                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="text-red-800 font-medium">Error</div>
                        @foreach($errors->all() as $error)
                            <div class="text-red-600 text-sm mt-1">{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
