@extends('install.layout', ['currentStep' => 'admin'])

@section('content')
    <h2 class="text-xl font-semibold text-gray-900 mb-4">Create Admin User</h2>

    @if(session('migration_output'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="text-green-800 font-medium">Migrations completed successfully!</div>
        </div>
    @endif

    @if($hasUsers)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="text-yellow-800 font-medium">Users already exist in the database.</div>
            <div class="text-yellow-600 text-sm mt-1">You can skip this step if you already have an admin user.</div>
        </div>

        <div class="mt-8 flex justify-between">
            <a href="{{ route('install.migrate') }}" class="px-6 py-2 text-gray-600 hover:text-gray-800 font-medium">
                &larr; Back
            </a>
            <a href="{{ route('install.finalize') }}" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 font-medium">
                Skip & Continue
            </a>
        </div>
    @else
        <p class="text-gray-600 mb-6">Create your administrator account. This will be the first super admin user.</p>

        <form method="POST" action="{{ route('install.admin.save') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                       placeholder="Your name" required>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                       placeholder="admin@example.com" required>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                       placeholder="Minimum 8 characters" required>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                       placeholder="Repeat password" required>
            </div>

            <div class="mt-8 flex justify-between">
                <a href="{{ route('install.migrate') }}" class="px-6 py-2 text-gray-600 hover:text-gray-800 font-medium">
                    &larr; Back
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 font-medium">
                    Create Admin & Continue
                </button>
            </div>
        </form>
    @endif
@endsection
