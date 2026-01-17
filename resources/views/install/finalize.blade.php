@extends('install.layout', ['currentStep' => 'finalize'])

@section('content')
    <h2 class="text-xl font-semibold text-gray-900 mb-4">Finalize Installation</h2>
    <p class="text-gray-600 mb-6">Almost done! Click the button below to complete the installation.</p>

    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <div class="text-gray-700 space-y-2">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Create storage symlink
            </div>
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Generate application key (if needed)
            </div>
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enable database-backed sessions and cache
            </div>
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Disable debug mode for production
            </div>
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Cache configuration for performance
            </div>
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Mark installation as complete
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('install.complete') }}">
        @csrf

        <div class="mt-8 flex justify-between">
            <a href="{{ route('install.admin') }}" class="px-6 py-2 text-gray-600 hover:text-gray-800 font-medium">
                &larr; Back
            </a>
            <button type="submit" class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 font-medium">
                Complete Installation
            </button>
        </div>
    </form>
@endsection
