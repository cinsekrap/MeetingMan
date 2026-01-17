<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create New Company
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-gray-600 mb-6">
                    Create a new company to manage a separate team or organization.
                </p>

                <form method="POST" action="{{ route('company.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Company Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus placeholder="e.g. Acme Corp" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end space-x-4 pt-4">
                        <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                        <x-primary-button>Create Company</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
