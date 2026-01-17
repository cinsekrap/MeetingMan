<x-mail::message>
Hi {{ $personName }},

I hope you're doing well. I wanted to check in on a few action items from our meetings.

@if ($overdueActions->isNotEmpty())
**Past due date:**

@foreach ($overdueActions as $action)
> **{{ $action->description }}**
> Due: {{ $action->due_date->format('j F Y') }}

@endforeach
@endif

@if ($dueSoonActions->isNotEmpty())
**Coming up soon:**

@foreach ($dueSoonActions as $action)
> **{{ $action->description }}**
> Due: {{ $action->due_date->format('j F Y') }}

@endforeach
@endif

Could you give me a quick update on where these stand when we next meet? If there's anything I can do to help move things forward, just let me know.

Thanks,<br>
{{ $sender->name }}
</x-mail::message>
