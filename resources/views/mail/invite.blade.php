subject = "Invitation to access {{ $site_name }}"
==
{{ $staff_name }}, you've been invited to access {{ $site_name }}

Click the link below to accept this invitation to gain access to {{ $site_name }} Admin.

{{ $invite_url }}
==
{{ $staff_name }}, you've been invited to access {{ $site_name }}

Accept this invitation to gain access to {{ $site_name }} Admin.

@partial('button', ['url' => $invite_url, 'type' => 'primary'])
Accept Invitation
@endpartial
