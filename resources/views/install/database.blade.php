@extends('install.layout', ['currentStep' => 'database'])

@section('content')
    <h2 class="text-xl font-semibold text-gray-900 mb-4">Environment Configuration</h2>
    <p class="text-gray-600 mb-6">Configure your site URL and database connection details.</p>

    <form method="POST" action="{{ route('install.database.save') }}" class="space-y-4">
        @csrf

        <div class="pb-4 mb-4 border-b border-gray-200">
            <label for="app_url" class="block text-sm font-medium text-gray-700 mb-1">Site URL</label>
            <input type="url" name="app_url" id="app_url" value="{{ old('app_url', $currentConfig['app_url']) }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                   placeholder="https://www.example.com">
            <p class="mt-1 text-sm text-gray-500">The full URL where MeetingMan will be accessed (include https://)</p>
        </div>

        <div>
            <label for="db_host" class="block text-sm font-medium text-gray-700 mb-1">Database Host</label>
            <input type="text" name="db_host" id="db_host" value="{{ old('db_host', $currentConfig['host']) }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                   placeholder="localhost or 127.0.0.1">
        </div>

        <div>
            <label for="db_port" class="block text-sm font-medium text-gray-700 mb-1">Database Port</label>
            <input type="text" name="db_port" id="db_port" value="{{ old('db_port', $currentConfig['port']) }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                   placeholder="3306">
        </div>

        <div>
            <label for="db_database" class="block text-sm font-medium text-gray-700 mb-1">Database Name</label>
            <input type="text" name="db_database" id="db_database" value="{{ old('db_database', $currentConfig['database']) }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                   placeholder="meetingman">
        </div>

        <div>
            <label for="db_username" class="block text-sm font-medium text-gray-700 mb-1">Database Username</label>
            <input type="text" name="db_username" id="db_username" value="{{ old('db_username', $currentConfig['username']) }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                   placeholder="root">
        </div>

        <div>
            <label for="db_password" class="block text-sm font-medium text-gray-700 mb-1">Database Password</label>
            <input type="password" name="db_password" id="db_password"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                   placeholder="Leave blank if no password">
        </div>

        <div class="mt-8 flex justify-between">
            <a href="{{ route('install.requirements') }}" class="px-6 py-2 text-gray-600 hover:text-gray-800 font-medium">
                &larr; Back
            </a>
            <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 font-medium">
                Test Connection & Continue
            </button>
        </div>
    </form>
@endsection
