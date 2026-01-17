<x-mail::message>
Hi {{ $meeting->person->name }},

Thank you for meeting today. Here's a summary of our discussion:

@if ($meeting->topics->isNotEmpty())
@foreach ($meeting->topics as $topic)
**TOPIC {{ $topic->position_word }}**

{{ $topic->content }}

@endforeach
@endif

@if ($meeting->actions->isNotEmpty())
**ACTION ITEMS**

@foreach ($meeting->actions as $action)
- {{ $action->description }} *(Assigned: {{ $action->assigned_to_name }}, Due: {{ $action->due_date->format('j M Y') }})*
@endforeach

@endif

@if ($overdueActions->isNotEmpty())
**OVERDUE TASKS**

@foreach ($overdueActions as $action)
- {{ $action->description }} *(Due: {{ $action->due_date->format('j M Y') }})*
@endforeach

@endif

@if ($dueSoonActions->isNotEmpty())
**DUE SOON**

@foreach ($dueSoonActions as $action)
- {{ $action->description }} *(Due: {{ $action->due_date->format('j M Y') }})*
@endforeach

@endif

Best regards,<br>
{{ $sender->name }}
</x-mail::message>
