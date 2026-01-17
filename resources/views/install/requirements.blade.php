@extends('install.layout', ['currentStep' => 'requirements'])

@section('content')
    <h2 class="text-xl font-semibold text-gray-900 mb-4">Server Requirements</h2>
    <p class="text-gray-600 mb-6">Please ensure your server meets the following requirements before continuing.</p>

    <div class="space-y-3">
        @foreach($requirements as $requirement)
            <div class="flex items-center justify-between p-3 rounded-lg {{ $requirement['passed'] ? 'bg-green-50' : 'bg-red-50' }}">
                <div>
                    <div class="font-medium {{ $requirement['passed'] ? 'text-green-800' : 'text-red-800' }}">
                        {{ $requirement['name'] }}
                    </div>
                    <div class="text-sm {{ $requirement['passed'] ? 'text-green-600' : 'text-red-600' }}">
                        Required: {{ $requirement['required'] }} | Current: {{ $requirement['current'] }}
                    </div>
                </div>
                <div>
                    @if($requirement['passed'])
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8 flex justify-end">
        @if($allPassed)
            <a href="{{ route('install.database') }}" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 font-medium">
                Continue
            </a>
        @else
            <button disabled class="px-6 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed font-medium">
                Fix Issues to Continue
            </button>
        @endif
    </div>
@endsection
