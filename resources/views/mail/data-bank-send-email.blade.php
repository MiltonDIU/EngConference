<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AI Connect Bangladesh Summit, 2023</title>
    <link rel="stylesheet" href="">
</head>
<body>
{{--<h2 style="text-align: center;">{!! $data['message']->subject !!}</h2>--}}
<p>Hello! Mr. {!! $data['dataBank']->name !!}</p>
{!! $data['message']->mail_body !!}
<br>

<a class="bdn btn-primary" href="{{ route('data-banks.unsubscribe',[$data['dataBank']->unsubscribe_link]) }}">Unsubscribe from the mail list.</a>

</body>
</html>
