<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Scripts -->
    <script src="{{ asset('js/semantic.min.js') }}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/semantic.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

</head>

<body>
    <div id="app">
        <div class="ui fixed menu big">

            @guest
            <a class="pl-4 item @if(Request::route()->getName() == 'home') active @endif" title="Início" href="{{ route('home') }}"><i class="home icon"></i>Início</a>

            <div class="right menu">
                <a class="item @if(Request::route()->getName() == 'login') active @endif" title="{{ __('Login') }}" href="{{ route('login') }}">
                    <i class="sign-in icon"></i>
                </a>
            </div>


            @else
            <a class="pl-4 item @if(Request::route()->getName() == 'homeGerencial') active @endif" href="{{ route('homeGerencial') }}"><i class="home icon"></i>Início</a>

            <div class="right menu">
                <a class="ui dropdown item">
                    <i class="user icon"></i>
                    <span class="text">{{ Auth::user()->name }}</span>
                    <i class="dropdown icon"></i>
                    <div class="menu">
                        <div class="item" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="sign-out icon"></i>
                            {{ __('Logout') }}</div>
                    </div>
                </a>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
        @endif

    </div>
    <div style="padding-top: 45px;">
        @yield('content')
    </div>
    </div>
</body>

<script>
    $(document).ready(function() {
        $('.ui .dropdown')
            .dropdown({
                action: 'hide',
                transition: 'drop',
                duration: 300,
  loading     : 'loading',
            });
    });
</script>

</html>