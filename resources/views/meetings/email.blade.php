<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('meetings.show', $meeting) }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to meeting</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Email Summary
                </h2>
            </div>
            <button onclick="copyToClipboard()" class="inline-flex items-center px-4 py-2 bg-primary-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-600">
                Copy to Clipboard
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Instructions --}}
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    <strong>Instructions:</strong> Click "Copy to Clipboard" to copy the email content below, then paste it into your email client.
                </p>
            </div>

            {{-- Email preview --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    {{-- Subject line --}}
                    <div class="mb-4 pb-4 border-b">
                        <span class="text-sm text-gray-500">Subject:</span>
                        <div class="font-medium" id="email-subject">1:1 Meeting Summary - {{ $meeting->meeting_date->format('j F Y') }}</div>
                    </div>

                    {{-- Email body --}}
                    <div id="email-content" class="prose prose-sm max-w-none">
                        <p>Hi {{ $meeting->person->name }},</p>

                        <p>Thank you for meeting today. Here's a summary of our discussion:</p>

                        @if ($meeting->topics->isNotEmpty())
                            @foreach ($meeting->topics as $topic)
                                <p><strong>TOPIC {{ $topic->position_word }}</strong></p>
                                <p>{!! nl2br(e($topic->content)) !!}</p>
                            @endforeach
                        @endif

                        @if ($meeting->actions->isNotEmpty())
                            <p><strong>ACTION ITEMS</strong></p>
                            <ul>
                                @foreach ($meeting->actions as $action)
                                    <li>{{ $action->description }} (Assigned: {{ $action->assigned_to_name }}, Due: {{ $action->due_date->format('j M Y') }})</li>
                                @endforeach
                            </ul>
                        @endif

                        @if ($overdueActions->isNotEmpty())
                            <p><strong>OVERDUE TASKS</strong></p>
                            <ul>
                                @foreach ($overdueActions as $action)
                                    <li>{{ $action->description }} (Due: {{ $action->due_date->format('j M Y') }})</li>
                                @endforeach
                            </ul>
                        @endif

                        @if ($dueSoonActions->isNotEmpty())
                            <p><strong>DUE SOON</strong></p>
                            <ul>
                                @foreach ($dueSoonActions as $action)
                                    <li>{{ $action->description }} (Due: {{ $action->due_date->format('j M Y') }})</li>
                                @endforeach
                            </ul>
                        @endif

                        <p>Best regards,<br>{{ auth()->user()->name }}</p>
                    </div>
                </div>
            </div>

            {{-- Plain text version for copying --}}
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Plain Text Version (for copying)</h3>
                    <pre id="plain-text-content" class="whitespace-pre-wrap text-sm bg-gray-50 p-4 rounded-lg border font-mono">Subject: 1:1 Meeting Summary - {{ $meeting->meeting_date->format('j F Y') }}

Hi {{ $meeting->person->name }},

Thank you for meeting today. Here's a summary of our discussion:
@if ($meeting->topics->isNotEmpty())
@foreach ($meeting->topics as $topic)

TOPIC {{ $topic->position_word }}
{{ $topic->content }}
@endforeach
@endif
@if ($meeting->actions->isNotEmpty())

ACTION ITEMS
@foreach ($meeting->actions as $action)
- {{ $action->description }} (Assigned: {{ $action->assigned_to_name }}, Due: {{ $action->due_date->format('j M Y') }})
@endforeach
@endif
@if ($overdueActions->isNotEmpty())

OVERDUE TASKS
@foreach ($overdueActions as $action)
- {{ $action->description }} (Due: {{ $action->due_date->format('j M Y') }})
@endforeach
@endif
@if ($dueSoonActions->isNotEmpty())

DUE SOON
@foreach ($dueSoonActions as $action)
- {{ $action->description }} (Due: {{ $action->due_date->format('j M Y') }})
@endforeach
@endif

Best regards,
{{ auth()->user()->name }}</pre>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard() {
            const text = document.getElementById('plain-text-content').innerText;
            navigator.clipboard.writeText(text).then(() => {
                alert('Copied to clipboard!');
            }).catch(err => {
                // Fallback for older browsers
                const textarea = document.createElement('textarea');
                textarea.value = text;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                alert('Copied to clipboard!');
            });
        }
    </script>
</x-app-layout>
