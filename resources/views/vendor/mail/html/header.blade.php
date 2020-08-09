<tr>
<td class="header">
    
<img src="{{asset('/img/onibus.gif')}}" style="height: 80px; width: 80px;" alt="Logo"/><br>
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
