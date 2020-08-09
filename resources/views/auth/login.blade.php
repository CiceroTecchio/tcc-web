@extends('layouts.app')
@section('title', 'Login')
@section('content')

<style type="text/css">
    body {
        background-color: #00b5ad;
    }

    body>.grid {
        height: 100%;
    }

    .ui.grid {
        margin: 0rem;
    }

    .column {
        max-width: 450px;
    }
</style>

<!-- <body style="background-image: url('https://thumbs.dreamstime.com/z/city-bus-map-application-mobile-phone-flat-cartoon-vector-illustration-puplic-transport-route-around-town-urban-150468065.jpg'); 
  background-repeat: no-repeat;
  background-attachment: fixed;
  background-size: 100% 100%;"> -->
  <body class="pt-5">
    <div class="ui middle aligned center aligned grid">
        <div class="column">
            <h2 class="ui teal image header">
                <img src="{{ asset('/img/onibus.gif') }}" style="height: 100px; width: 100px; border: 2px solid white;" class="ui medium circular image"><br>
                <div class="content mt-4" style="color: white;">
                    Entre em sua conta
                </div>
            </h2>
            <form id="submitForm" class="ui large form" onsubmit="$('#submitButton').addClass('loading')" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="ui stacked segment">
                    <div class="field @error('email') error @enderror" title="Endereço de E-Mail">
                        <div class="ui left icon input ">
                            <i class="user icon"></i>
                            <input id="email" type="text" name="email" placeholder="Endereço de E-Mail" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        </div>
                    </div>
                    <div class="field @error('password') error @enderror" title="Senha">
                        <div class="ui left icon right action input">
                            <i class="lock icon"></i>
                            <input id="password" type="password" name="password" placeholder="Senha" required autocomplete="current-password">

                            <button type="button" class="ui compact icon button" onclick="visibilidadeSenha()">
                                <i id="iconPassword" class="eye icon"></i>
                            </button>
                        </div>
                    </div>
                    <button id="submitButton" type="submit" class="ui fluid large teal submit button"> {{ __('Login') }}</button>
                </div>

                @error('email')
                <div class="ui red message">
                    <strong>{{ $message }}</strong>
                </div>
                @enderror

            </form>
            @if (Route::has('password.request'))
            <div class="ui message">
                <strong><a href="{{ route('password.request') }}">Esqueceu sua senha?</a></strong>
            </div>
            @endif

        </div>
    </div>
</body>

@endsection

<script>
    function visibilidadeSenha() {
        var input = $('#password');
        var icon = $('#iconPassword');
        if (input.attr('type') == 'password') {
            input.attr('type', 'text');
            icon.addClass('slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('slash');
        }
    }
</script>