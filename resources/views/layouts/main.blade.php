<!DOCTYPE html>
<html>
@include('layouts.header')
</head>

<body>

    <div class="container">
        @yield('content')
    </div>

</body>

@include('layouts.footer')
@yield(section: 'scripts')
</html>