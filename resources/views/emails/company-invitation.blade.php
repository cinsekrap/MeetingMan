<x-mail::message>
# You're invited to join {{ $invite->company->name }}

{{ $invite->invitedBy->name }} has invited you to join **{{ $invite->company->name }}** on MeetingMan.

MeetingMan helps managers track 1:1 meetings, action items, and objectives with their team members.

<x-mail::button :url="$acceptUrl">
Accept Invitation
</x-mail::button>

This invitation will expire on {{ $invite->expires_at->format('F j, Y') }}.

If you weren't expecting this invitation, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
