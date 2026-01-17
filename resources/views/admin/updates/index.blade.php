<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                System Updates
            </h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-purple-600 hover:text-purple-800">&larr; Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Current Version --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Current Version</h3>
                <div class="flex items-center space-x-4">
                    <div class="text-3xl font-bold text-purple-600">v{{ $currentVersion }}</div>
                    <button
                        id="checkUpdatesBtn"
                        type="button"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <svg id="checkSpinner" class="hidden animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="checkBtnText">Check for Updates</span>
                    </button>
                </div>
            </div>

            {{-- Update Status (hidden by default) --}}
            <div id="updateStatus" class="hidden">
                {{-- Up to date --}}
                <div id="upToDateMsg" class="hidden bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>You are running the latest version.</span>
                    </div>
                </div>

                {{-- Update available --}}
                <div id="updateAvailableCard" class="hidden bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Update Available</h3>
                            <p class="text-sm text-gray-500 mt-1">A new version is available for download.</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            v<span id="newVersion"></span>
                        </span>
                    </div>

                    <div id="releaseInfo" class="mb-4">
                        <p class="text-sm text-gray-500">
                            Released: <span id="releaseDate" class="font-medium text-gray-700"></span>
                        </p>
                    </div>

                    {{-- Changelog --}}
                    <div id="changelogSection" class="mb-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Changelog</h4>
                        <div id="changelog" class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 prose prose-sm max-w-none overflow-auto max-h-64"></div>
                    </div>

                    {{-- Warning --}}
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <svg class="h-5 w-5 text-amber-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div class="text-sm text-amber-800">
                                <p class="font-medium">Before updating:</p>
                                <ul class="mt-1 list-disc list-inside space-y-1">
                                    <li>Ensure you have a recent backup of your database</li>
                                    <li>Your .env file and uploaded files will be preserved</li>
                                    <li>The update may take a few moments to complete</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Install button --}}
                    <div class="flex items-center justify-between">
                        <a id="releaseLink" href="#" target="_blank" class="text-sm text-purple-600 hover:text-purple-800">
                            View release on GitHub &rarr;
                        </a>
                        <button
                            id="installUpdateBtn"
                            type="button"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            <svg id="installSpinner" class="hidden animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span id="installBtnText">Install Update</span>
                        </button>
                    </div>
                </div>

                {{-- Error message --}}
                <div id="errorMsg" class="hidden bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-red-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span id="errorText"></span>
                    </div>
                </div>

                {{-- Success message --}}
                <div id="successMsg" class="hidden bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-green-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <div>
                            <span id="successText"></span>
                            <button onclick="window.location.reload()" class="ml-2 text-green-700 underline hover:no-underline">Refresh page</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info section --}}
            <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-medium text-gray-700 mb-2">About Updates</h3>
                <p class="text-sm text-gray-500">
                    Updates are fetched from the official GitHub repository. The update process will:
                </p>
                <ul class="mt-2 text-sm text-gray-500 list-disc list-inside space-y-1">
                    <li>Download the latest release from GitHub</li>
                    <li>Replace application files while preserving your configuration</li>
                    <li>Clear all application caches</li>
                    <li>Keep your .env, storage files, and logs intact</li>
                </ul>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkBtn = document.getElementById('checkUpdatesBtn');
            const checkSpinner = document.getElementById('checkSpinner');
            const checkBtnText = document.getElementById('checkBtnText');
            const installBtn = document.getElementById('installUpdateBtn');
            const installSpinner = document.getElementById('installSpinner');
            const installBtnText = document.getElementById('installBtnText');
            const updateStatus = document.getElementById('updateStatus');
            const upToDateMsg = document.getElementById('upToDateMsg');
            const updateAvailableCard = document.getElementById('updateAvailableCard');
            const errorMsg = document.getElementById('errorMsg');
            const errorText = document.getElementById('errorText');
            const successMsg = document.getElementById('successMsg');
            const successText = document.getElementById('successText');

            let latestVersion = null;

            function hideAllMessages() {
                upToDateMsg.classList.add('hidden');
                updateAvailableCard.classList.add('hidden');
                errorMsg.classList.add('hidden');
                successMsg.classList.add('hidden');
            }

            function showError(message) {
                hideAllMessages();
                updateStatus.classList.remove('hidden');
                errorMsg.classList.remove('hidden');
                errorText.textContent = message;
            }

            function showSuccess(message) {
                hideAllMessages();
                updateStatus.classList.remove('hidden');
                successMsg.classList.remove('hidden');
                successText.textContent = message;
            }

            // Check for updates
            checkBtn.addEventListener('click', async function() {
                checkBtn.disabled = true;
                checkSpinner.classList.remove('hidden');
                checkBtnText.textContent = 'Checking...';
                hideAllMessages();
                updateStatus.classList.remove('hidden');

                try {
                    const response = await fetch('{{ route("admin.updates.check") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        showError(data.message || 'Failed to check for updates.');
                        return;
                    }

                    if (data.update_available) {
                        latestVersion = data.latest_version;
                        document.getElementById('newVersion').textContent = data.latest_version;

                        if (data.published_at) {
                            const date = new Date(data.published_at);
                            document.getElementById('releaseDate').textContent = date.toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            });
                        }

                        if (data.changelog) {
                            document.getElementById('changelog').innerHTML = marked.parse(data.changelog);
                        } else {
                            document.getElementById('changelogSection').classList.add('hidden');
                        }

                        if (data.release_url) {
                            document.getElementById('releaseLink').href = data.release_url;
                        }

                        updateAvailableCard.classList.remove('hidden');
                    } else {
                        upToDateMsg.classList.remove('hidden');
                    }
                } catch (error) {
                    showError('Network error. Please check your connection and try again.');
                    console.error('Check update error:', error);
                } finally {
                    checkBtn.disabled = false;
                    checkSpinner.classList.add('hidden');
                    checkBtnText.textContent = 'Check for Updates';
                }
            });

            // Install update
            installBtn.addEventListener('click', async function() {
                if (!latestVersion) {
                    showError('No version selected for update.');
                    return;
                }

                if (!confirm('Are you sure you want to install this update? Make sure you have a backup of your database.')) {
                    return;
                }

                installBtn.disabled = true;
                installSpinner.classList.remove('hidden');
                installBtnText.textContent = 'Installing...';

                try {
                    const response = await fetch('{{ route("admin.updates.apply") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            version: latestVersion,
                        }),
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        showError(data.message || 'Failed to install update.');
                        return;
                    }

                    showSuccess(data.message || 'Update installed successfully!');
                    installBtn.classList.add('hidden');
                } catch (error) {
                    showError('Network error during update. Please check the logs and try again.');
                    console.error('Install update error:', error);
                } finally {
                    installBtn.disabled = false;
                    installSpinner.classList.add('hidden');
                    installBtnText.textContent = 'Install Update';
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    @endpush
</x-app-layout>
