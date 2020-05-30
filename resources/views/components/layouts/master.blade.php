<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'iNsomnia') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app">
        <header class="">
            <div class="hidden lg:block bg-blue-light">
                <a href="{{route('home')}}" class="pt-3 px-5 text-5xl font-bold text-blue-dark">iNsomnia</a>
            </div>
            <div class="bg-blue-dark text-white px-5 flex justify-between items-center">
                <div class="phone:flex phone:justify-between phone:items-center lg:hidden">
                    <hamburger class="mr-3"></hamburger>
                    <a class="font-bold text-4xl text-white" href="{{route('home')}}">iNsomnia</a>
                </div>
                <div class="hidden lg:flex items-center">
                    <x-tab-item name="Front Page" destination="home"></x-tab-item>
                    <x-tab-item name="Guides" destination="home"></x-tab-item>
                    <x-tab-item name="How Tos" destination="home"></x-tab-item>
                    <x-tab-item name="Reviews" destination="home"></x-tab-item>
                    <x-tab-item name="Ads" destination="home"></x-tab-item>
                    <x-tab-item name="Forums" destination="forum"></x-tab-item>
                </div>
                <div>
                    {{ $navRight }}
                </div>
            </div>


        </header>

        <main>{{$main}}</main>

    </div>
</body>

</html>