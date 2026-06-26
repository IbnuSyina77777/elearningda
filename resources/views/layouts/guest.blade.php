<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', setting('app_name', 'E-Learning SMK'))</title>
    <meta name="description" content="@yield('description', 'Sistem pembayaran digital untuk Sekolah Menengah Kejuruan')">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    @php $themeColor = setting('theme_color'); @endphp
    @if($themeColor)
    <style>
        :root {
            --primary-500: {{ $themeColor }};
            --primary-600: {{ $themeColor }};
        }
    </style>
    @endif
    @stack('styles')
</head>
<body>
    @yield('content')

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
