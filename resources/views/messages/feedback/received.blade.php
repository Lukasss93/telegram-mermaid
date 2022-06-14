New feedback!<br>
From: {{ $from }}
@isset($username)
 ({{ '@'.$username }})
@endisset
 [{{ $user_id }}]<br>
Message:<br>
{!! $message  !!}
