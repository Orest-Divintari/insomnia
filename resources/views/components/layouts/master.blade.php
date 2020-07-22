<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'iNsomnia') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>r

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.core.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('scripts')
    @stack('styles')
    <script>
    window.App = @json([
        'user' => Auth::user(),
        'signedIn' => Auth::check()
    ]);
    </script>

    <style>
    [v-cloak] {
        display: none
    }
    </style>


</head>

<body>

    <div id="app">

        <header>
            <div class="hidden lg:block bg-blue-light">
                <a href="{{route('home')}}" class="pt-3 px-5 text-5xl font-bold text-blue-dark">iNsomnia</a>
            </div>
            <div class="bg-blue-dark text-white px-5 flex justify-between items-stretch">
                <div class="xs:flex xs:justify-between xs:items-center lg:hidden">
                    <hamburger class="mr-3"></hamburger>
                    <a class="font-bold text-4xl text-white" href="{{route('home')}}">iNsomnia</a>
                </div>
                <div class="hidden lg:flex items-stretch">
                    <x-head_tab_item name="Front Page" destination="home"></x-head_tab_item>
                    <x-head_tab_item name="Guides" destination="home"></x-head_tab_item>
                    <x-head_tab_item name="How Tos" destination="home"></x-head_tab_item>
                    <x-head_tab_item name="Reviews" destination="home"></x-head_tab_item>
                    <x-head_tab_item name="Ads" destination="home"></x-head_tab_item>
                    <x-head_tab_item name="Forums" destination="forum"></x-head_tab_item>
                </div>
                <div>
                    {{ $navRight }}
                </div>
            </div>


            <div>{{$subHeader}}</div>
        </header>

        <main class="p-5 h-screen bg-semi-white">{{$main}}</main>

    </div>



</body>

</html>