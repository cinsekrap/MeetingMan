@extends('install.layout', ['currentStep' => 'migrate'])

@section('content')
    <h2 class="text-xl font-semibold text-gray-900 mb-4">Database Migration</h2>
    <p class="text-gray-600 mb-6">The database connection was successful. Now we'll create the necessary tables.</p>

    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <div class="flex items-center text-gray-700">
            <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            This will create all required database tables for MeetingMan.
        </div>
    </div>

    <form method="POST" action="{{ route('install.migrate.run') }}">
        @csrf

        <div class="mt-8 flex justify-between">
            <a href="{{ route('install.database') }}" class="px-6 py-2 text-gray-600 hover:text-gray-800 font-medium">
                &larr; Back
            </a>
            <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 font-medium">
                Run Migrations
            </button>
        </div>
    </form>
@endsection
