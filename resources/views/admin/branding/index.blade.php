<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Branding Settings
            </h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-purple-600 hover:text-purple-800">&larr; Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.branding.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Logo --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Logo</h3>

                    @if($logoPath)
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-2">Current logo:</p>
                            <div class="flex items-center space-x-4">
                                <div class="bg-gray-800 p-3 rounded-lg">
                                    <img src="{{ asset($logoPath) }}" alt="Current logo" class="h-10 max-w-[200px] object-contain">
                                </div>
                                <button type="button" onclick="document.getElementById('remove-logo-form').submit()" class="text-sm text-red-600 hover:text-red-800">
                                    Remove logo
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">No logo uploaded. The site name will be displayed as text.</p>
                        </div>
                    @endif

                    <div>
                        <x-input-label for="logo" :value="__('Upload new logo')" />
                        <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/jpg,image/svg+xml" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100" />
                        <p class="mt-1 text-sm text-gray-500">PNG, JPG, or SVG. Max 2MB. Recommended: 200x40px (or similar aspect ratio).</p>
                        <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                    </div>

                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm font-medium text-gray-700 mb-2">Preview:</p>
                        <div class="bg-gradient-to-r from-purple-600 to-purple-800 p-4 rounded-lg">
                            @if($logoPath)
                                <img src="{{ asset($logoPath) }}" alt="Logo preview" class="h-8 object-contain">
                            @else
                                <span class="text-white font-bold text-xl">{{ $siteName }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Site Name --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Site Name</h3>

                    <div>
                        <x-input-label for="site_name" :value="__('Site Name')" />
                        <x-text-input id="site_name" name="site_name" type="text" class="mt-1 block w-full" :value="old('site_name', $siteName)" placeholder="MeetingMan" />
                        <p class="mt-1 text-sm text-gray-500">Displayed when no logo is uploaded, and used in emails.</p>
                        <x-input-error :messages="$errors->get('site_name')" class="mt-2" />
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end">
                    <x-primary-button>Save Changes</x-primary-button>
                </div>
            </form>

            {{-- Separate form for removing logo (outside main form to avoid nesting) --}}
            @if($logoPath)
                <form id="remove-logo-form" method="POST" action="{{ route('admin.branding.remove-logo') }}" class="hidden" onsubmit="return confirm('Remove the logo?')">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
