<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Default Settings</h3>

                    <form method="POST" action="{{ route('settings.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <x-input-label for="default_meeting_frequency_days" :value="__('Default Meeting Frequency')" />
                            <p class="text-sm text-gray-500 mb-2">How often should you meet with new people by default? This is used when creating new people and determines when a meeting is considered overdue.</p>

                            <div class="flex items-center space-x-3">
                                <x-text-input
                                    id="default_meeting_frequency_days"
                                    name="default_meeting_frequency_days"
                                    type="number"
                                    min="1"
                                    max="365"
                                    class="w-24"
                                    :value="old('default_meeting_frequency_days', $settings->default_meeting_frequency_days)"
                                    required
                                />
                                <span class="text-gray-700">days</span>
                            </div>

                            <div class="mt-2 text-sm text-gray-500">
                                Common frequencies:
                                <button type="button" onclick="setFrequency(7)" class="text-primary-600 hover:text-primary-800">Weekly (7)</button> |
                                <button type="button" onclick="setFrequency(14)" class="text-primary-600 hover:text-primary-800">Fortnightly (14)</button> |
                                <button type="button" onclick="setFrequency(30)" class="text-primary-600 hover:text-primary-800">Monthly (30)</button>
                            </div>

                            <x-input-error :messages="$errors->get('default_meeting_frequency_days')" class="mt-2" />
                        </div>

                        <div class="flex justify-end">
                            <x-primary-button>Save Settings</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Two-Factor Authentication --}}
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Two-Factor Authentication</h3>
                    <p class="text-sm text-gray-500 mb-6">Add additional security to your account using two-factor authentication.</p>

                    @if (! auth()->user()->two_factor_secret)
                        {{-- 2FA not enabled --}}
                        <div class="text-sm text-gray-600 mb-4">
                            You have not enabled two-factor authentication. When enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's authenticator application.
                        </div>

                        <form method="POST" action="{{ route('two-factor.enable') }}">
                            @csrf
                            <x-primary-button>Enable Two-Factor Authentication</x-primary-button>
                        </form>
                    @else
                        @if (auth()->user()->two_factor_confirmed_at)
                            {{-- 2FA enabled and confirmed --}}
                            <div class="text-sm text-green-600 font-medium mb-4">
                                Two-factor authentication is enabled and confirmed.
                            </div>

                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Recovery Codes</h4>
                                <p class="text-sm text-gray-500 mb-3">Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two-factor authentication device is lost.</p>
                                <div class="bg-gray-100 rounded-lg p-4 font-mono text-sm">
                                    @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
                                        <div class="mb-1">{{ $code }}</div>
                                    @endforeach
                                </div>

                                <form method="POST" action="{{ route('two-factor.recovery-codes') }}" class="mt-3">
                                    @csrf
                                    <button type="submit" class="text-sm text-primary-600 hover:text-primary-800">Regenerate Recovery Codes</button>
                                </form>
                            </div>

                            <form method="POST" action="{{ route('two-factor.disable') }}">
                                @csrf
                                @method('DELETE')
                                <x-danger-button onclick="return confirm('Are you sure you want to disable two-factor authentication?')">
                                    Disable Two-Factor Authentication
                                </x-danger-button>
                            </form>
                        @else
                            {{-- 2FA enabled but not confirmed --}}
                            <div class="text-sm text-yellow-600 font-medium mb-4">
                                Finish enabling two-factor authentication by scanning the QR code below.
                            </div>

                            <div class="mb-6">
                                <div class="mb-4">
                                    {!! auth()->user()->twoFactorQrCodeSvg() !!}
                                </div>

                                <p class="text-sm text-gray-500 mb-2">Or enter this setup key manually:</p>
                                <code class="bg-gray-100 px-2 py-1 rounded text-sm">{{ decrypt(auth()->user()->two_factor_secret) }}</code>
                            </div>

                            <form method="POST" action="{{ route('two-factor.confirm') }}">
                                @csrf
                                <div class="mb-4">
                                    <x-input-label for="code" :value="__('Code')" />
                                    <x-text-input id="code" name="code" type="text" class="mt-1 block w-full max-w-xs" inputmode="numeric" autofocus autocomplete="one-time-code" />
                                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                                </div>
                                <x-primary-button>Confirm</x-primary-button>
                            </form>

                            <form method="POST" action="{{ route('two-factor.disable') }}" class="mt-4">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">Cancel</button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function setFrequency(days) {
            document.getElementById('default_meeting_frequency_days').value = days;
        }
    </script>
</x-app-layout>
