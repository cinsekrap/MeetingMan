<x-mail::message>
Hi {{ $person->name }},

@if ($reminderType === 'overdue')
I hope you're doing well. I wanted to check in on an action item from our previous meeting that's now past its due date:

> **{{ $action->description }}**
> Due: {{ $action->due_date->format('j F Y') }}

Could you give me a quick update on where this stands when we next meet? If there's anything I can do to help move this forward, just let me know.
@else
I hope you're doing well. Just a friendly heads-up that we have an action item coming up soon:

> **{{ $action->description }}**
> Due: {{ $action->due_date->format('j F Y') }}

Could you update me on how this is progressing at our next meeting? If you need any support or if the timeline needs adjusting, I'm happy to discuss.
@endif

Thanks,<br>
{{ $sender->name }}
</x-mail::message>
