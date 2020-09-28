<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script data-ad-client="ca-pub-7359984401631359" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="{{ asset('js/semantic.min.js') }}" defer></script>
    <script src="{{ asset('js/jquery.mask.js') }}" defer></script>

    <!-- Scripts Datatables -->
    <script src="{{ asset('js/datatables/datatables.min.js') }}" defer></script>
    <script src="{{ asset('js/datatables/datatables-semantic.min.js') }}" defer></script>
    <script src="{{ asset('js/datatables/datatables-buttons.js') }}" defer></script>
    <script src="{{ asset('js/datatables/datatables-semantic-buttons.js') }}" defer></script>
    <script src="{{ asset('js/datatables/datatables-semantic.min.js') }}" defer></script>
    <script src="{{ asset('js/datatables/pdfmake.min.js') }}" defer></script>
    <script src="{{ asset('js/datatables/pdfmake-vfs_fonts.js') }}" defer></script>
    <script src="{{ asset('js/datatables/export.js') }}" defer></script>
    <script src="{{ asset('js/datatables/col-visibility.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/semantic.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/datatables-semantic.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/datatables-buttons.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('img/onibus.gif') }}">

    <style>
        .linkIcon {
            text-decoration: none !important;
        }

        @media screen and (min-width: 768px) {
            #divSearchMenu {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light">
            <div class="container">
                <div class="ui fixed menu big">

                    <button class="navbar-toggler" type="button" onclick="toggleClick()">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div id="divSearchMenu" class="w-100">
                        <select id="searchMenu" class="ui fluid search dropdown" style="display: none;">

                        </select>
                    </div>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto">
                            @guest
                            <li class="nav-item">
                                <a class="item @if(Request::route()->getName() == 'home') active @endif" title="Mapa" href="{{ route('home') }}"><i class="map icon"></i>Mapa</a>
                            </li>

                            <li class="nav-item">
                                <a class="item @if(Request::route()->getName() == 'linhasPublic') active @endif" title="Linhas" href="{{ route('linhasPublic') }}"><i class="map signs icon"></i>Linhas</a>
                            </li>

                            @else
                            <li class="nav-item">
                                <a class="item @if(Request::route()->getName() == 'homeGerencial') active @endif" href="{{ route('homeGerencial') }}"><i class="home icon"></i>Início</a>
                            </li>
                            <li class="nav-item">
                                <a class="item @if(Request::route()->getName() == 'pontos.index') active @endif" href="{{ route('pontos.index') }}"><i class="map pin icon"></i>Pontos</a>
                            </li>
                            <div class="ui dropdown item menuDropdown">
                                <i class="map signs icon"></i>Linhas
                                <i class="dropdown icon"></i>
                                <div class="menu">
                                    <a class="item @if(Request::route()->getName() == 'linhas.index') active @endif" href="{{ route('linhas.index') }}">
                                        <i class="list alternate icon"></i> Listar
                                    </a>
                                    <a class="item @if(Request::route()->getName() == 'linha_create_mapa') active @endif" href="{{ route('linha_create_mapa') }}">
                                        <i class="plus icon"></i> Adicionar
                                    </a>
                                </div>
                            </div>
                            <div class="ui dropdown item menuDropdown">
                                <i class="bus icon"></i>Veículos
                                <i class="dropdown icon"></i>
                                <div class="menu">
                                    <a class="item @if(Request::route()->getName() == 'veiculos.index') active @endif" href="{{ route('veiculos.index') }}">
                                        <i class="list alternate icon"></i> Listar
                                    </a>
                                    <a class="item @if(Request::route()->getName() == 'veiculos.create') active @endif" href="{{ route('veiculos.create') }}">
                                        <i class="plus icon"></i> Adicionar
                                    </a>
                                </div>
                            </div>
                            <div class="ui dropdown item menuDropdown">
                                <i class="users icon"></i> Colaboradores
                                <i class="dropdown icon"></i>
                                <div class="menu">
                                    <a class="item @if(Request::route()->getName() == 'colaboradores.index') active @endif" href="{{ route('colaboradores.index') }}">
                                        <i class="list alternate icon"></i> Listar
                                    </a>
                                    <a class="item @if(Request::route()->getName() == 'colaboradores.create') active @endif" href="{{ route('colaboradores.create') }}">
                                        <i class="plus icon"></i> Adicionar
                                    </a>
                                </div>
                            </div>
                            <div class="ui dropdown item menuDropdown">
                                <i class="pdf file icon"></i> Relatórios
                                <i class="dropdown icon"></i>
                                <div class="menu">
                                    <a class="item @if(Request::route()->getName() == 'registro.index') active @endif" href="{{ route('registro.index') }}">
                                        <i class="road icon"></i> Roteiros
                                    </a>
                                </div>
                            </div>
                            @endguest

                        </ul>
                        <ul class="navbar-nav ml-auto">
                            @guest
                            <li class="nav-item">
                                <a class="item @if(Request::route()->getName() == 'login') active @endif" title="{{ __('Login') }}" href="{{ route('login') }}">
                                    <i class="sign-in icon"></i> Entrar
                                </a>
                            </li>
                            @else
                            <div class="ui pointing dropdown link item menuDropdown">
                                <i class="user icon"></i>{{ Auth::user()->name }}
                                <i class="dropdown icon"></i>
                                <div class="menu">
                                    <a class="item" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                        <i class="sign-out icon"></i> {{ __('Logout') }}
                                    </a>
                                </div>
                            </div>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            @endguest
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <main style="padding-top: 34px;">
        @yield('content')
    </main>
</body>

<script>
    function toggleClick() {
        $("#divSearchMenu").toggle();
        $('#navbarSupportedContent').toggleClass('show');
    }
    $(document).ready(function() {

        $('.menuDropdown')
            .dropdown({
                action: 'hide',
                transition: 'drop',
                duration: 300,
                loading: 'loading',
            });

        $('.message .close')
            .on('click', function() {
                $(this)
                    .closest('.message')
                    .transition('fade');
            });

        $('.msgAlerta')
            .delay(5000)
            .queue(function() {
                $(this).closest('.msgAlerta').transition('fade down');
            });
    });
</script>

</html>
