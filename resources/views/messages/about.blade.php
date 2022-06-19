<b>🤖 Bot</b><br>
<b>Name:</b> {{config('app.name')}}<br>
<b>Username:</b> {{'@'.config('bot.username')}}<br>
<b>Version:</b> {{config('app.version')}}<br>

@if(config('bot.source'))
<b>Source code</b>: <a href="{{config('bot.source')}}">Open url</a><br>
@endif

@if(config('bot.changelog'))
<b>Changelog:</b> <a href="{{config('bot.changelog')}}">Open url</a><br>
@endif

<br>

<b>👤 Owner</b><br>
<b>Channel:</b> {{config('owner.channel')}}<br>
<b>Support:</b> {{config('owner.support')}}<br>
