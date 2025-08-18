
<p>
    <strong>{{ translate('Name') }}:</strong> {{ $name }}<br>
    @if ($email != null || $email != '')
    <strong>{{ translate('Email') }}:</strong> {{ $email }}
    <br>
    @endif
    @if ($phone != null || $phone != '')
    <strong>{{ translate('Phone') }}:</strong> {{ $phone }}
    @endif 
</p>
<p>{!! $content !!}</p>
<p><strong>{{ translate('Page URL') }}:</strong> <a href="{{ env('APP_URL') }}">{{ translate('Go to the website') }}</a></p>

