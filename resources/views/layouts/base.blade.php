<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? $header ?? '' }}</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="{{ $bodyClass ?? '' }}">
@yield('body')

<script src="{{ asset('js/app.js') }}" defer></script>

@yield('footer')
</body>
</html>
