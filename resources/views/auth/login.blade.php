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

<body class="pt-4">
    <div class="ui middle aligned center aligned grid">
        <div class="column">
            <h2 class="ui teal image header">
                <img src="{{ asset('/img/onibus.gif') }}" style="height: 100px; width: 100px; border: 2px solid white;" class="ui medium circular image"><br>
                <div class="content mt-4" style="color: white;">
                    Entre em sua conta
                </div>
            </h2>
            <form class="ui large form" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="ui stacked segment">
                    <div class="field @error('email') error @enderror" title="Endereço de E-Mail">
                        <div class="ui left icon input ">
                            <i class="user icon"></i>
                            <input id="email" type="text" name="email" placeholder="Endereço de E-Mail" value="{{ old('email') }}" autocomplete="email" autofocus>
                        </div>
                    </div>
                    <div class="field @error('password') error @enderror" title="Senha">
                        <div class="ui left icon input">
                            <i class="lock icon"></i>
                            <input id="password" type="password" name="password" placeholder="Senha" autocomplete="current-password">
                        </div>
                    </div>
                    <button id="submitButton" type="button" onclick="submitForm()" class="ui fluid large teal button"> {{ __('Login') }}</button>
                    <div class="ui error message"></div>
                </div>

                <div id="messages">
                @if (session('error'))
                <div class="ui red message">
                    <strong>{{ session('error') }}</strong>
                </div>
                @endif
                @error('email')
                <div class="ui red message">
                    <strong>{{ $message }}</strong>
                </div>
                @enderror
                </div>
            </form>
            @if (Route::has('password.request'))
            <div class="ui  message">
                <strong><a class="disabled" href="{{ route('password.request') }}">Esqueceu sua senha?</a></strong>
            </div>
            @endif

        </div>
    </div>
</body>

<script>
    $(document).ready(function() {
        $('.ui.form').form({
            fields: {
                email: {
                    identifier: 'email',
                    rules: [{
                        type: 'email',
                        prompt: 'Digite o E-Mail do Colaborador'
                    }]
                },
                password: {
                    identifier: 'password',
                    rules: [{
                            type: 'empty',
                            prompt: 'Digite a senha do Colaborador'
                        },
                        {
                            type: 'minLength[8]',
                            prompt: 'A senha deve ter no mínimo {ruleValue} caracteres'
                        }
                    ]
                },
            },
        });
    });

    function submitForm() {
        $('#messages').hide();
        if ($('.ui.form').form('validate form')) {
            $('.ui.form').addClass('loading');
            $('.ui.form').submit();
        }
    }
</script>
@endsection