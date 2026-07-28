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
    @if($themeColor && $themeColor !== '#dc2626')
    <script>
        (function() {
            var hex = '{{ $themeColor }}';
            function hexToHSL(h) {
                var r = parseInt(h.slice(1,3),16)/255, g = parseInt(h.slice(3,5),16)/255, b = parseInt(h.slice(5,7),16)/255;
                var max = Math.max(r,g,b), min = Math.min(r,g,b), d = max - min, hue, s, l = (max+min)/2;
                if(d === 0) { hue = 0; s = 0; } else {
                    s = l > 0.5 ? d/(2-max-min) : d/(max+min);
                    switch(max) {
                        case r: hue = ((g-b)/d + (g<b?6:0))*60; break;
                        case g: hue = ((b-r)/d + 2)*60; break;
                        case b: hue = ((r-g)/d + 4)*60; break;
                    }
                }
                return {h: Math.round(hue), s: Math.round(s*100), l: Math.round(l*100)};
            }
            var hsl = hexToHSL(hex);
            var H = hsl.h, S = hsl.s;
            var palette = {
                '50':  'hsl('+H+','+Math.min(S+10,100)+'%,97%)',
                '100': 'hsl('+H+','+Math.min(S+8,100)+'%,93%)',
                '200': 'hsl('+H+','+Math.min(S+5,100)+'%,86%)',
                '300': 'hsl('+H+','+S+'%,76%)',
                '400': 'hsl('+H+','+S+'%,62%)',
                '500': 'hsl('+H+','+S+'%,52%)',
                '600': 'hsl('+H+','+S+'%,45%)',
                '700': 'hsl('+H+','+Math.max(S-5,0)+'%,38%)',
                '800': 'hsl('+H+','+Math.max(S-8,0)+'%,30%)',
                '900': 'hsl('+H+','+Math.max(S-10,0)+'%,24%)',
                '950': 'hsl('+H+','+Math.max(S-12,0)+'%,14%)'
            };
            var root = document.documentElement;
            for (var k in palette) { root.style.setProperty('--primary-'+k, palette[k]); }
            root.style.setProperty('--bg-sidebar', 'linear-gradient(180deg, '+palette['800']+' 0%, '+palette['900']+' 50%, '+palette['950']+' 100%)');
        })();
    </script>
    @endif
    @stack('styles')
</head>
<body>
    @yield('content')

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
