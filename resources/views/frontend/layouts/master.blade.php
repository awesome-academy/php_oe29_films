<!DOCTYPE html>
<html lang="en">
<head>
    @include('frontend.layouts.header')
</head>
<body class="body">
    @include('frontend.layouts.main_header')
    <!-- home -->
    @yield('header_content')
    <!-- end home -->

    @yield('filter')

    <!-- content -->
    @yield('content')
    <!-- end content -->

    <!-- expected premiere -->
    @yield('expected_premiere')
    <!-- end expected premiere -->

    <!-- partners -->
    @yield('partners')
    <!-- end partners -->

    <!-- footer -->
    @include('frontend.layouts.main_footer')
    <!-- end footer -->
    @include('frontend.layouts.footer')
</body>
</html>
