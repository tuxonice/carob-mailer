<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Carob Mailer') }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ mix('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ mix('dist/css/adminlte.min.css') }}">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <x-top-menu/><x-main-menu/>
    <div class="content-wrapper">
        {{ $slot }}
    </div>
    <x-footer/>
</div>
<script src="{{ mix('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ mix('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ mix('dist/js/adminlte.min.js') }}/"></script>
</body>
</html>
