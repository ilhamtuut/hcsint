<tr>
<td class="header">
<a href="https://hcsint.network" style="display: inline-block;">
@if (trim($slot) === 'HCS International')
<img src="{{asset('images/logo/hcs.png')}}" class="logo" alt="HCS International">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
