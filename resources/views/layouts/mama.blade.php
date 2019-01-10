<html>
<head>
    <title>Lening-@yield('title')</title>
</head>
<body>
@section('header')
    <p style="color: blue">This is the mama header.</p>
@show

<div class="container">
    @yield('content')

</div>

@section('footer')
    <p style="color: blue">This is the mama footer.</p>
@show
</body>
</html>