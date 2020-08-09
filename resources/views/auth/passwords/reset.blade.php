@extends('layouts.app')
@section('title', 'Modificar Senha')
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

<body class="pt-5">
    <div class="ui middle aligned center aligned grid">
    <div class="column">
        <h2 class="ui teal image header">
            <img src="{{ asset('/img/onibus.gif') }}" style="height: 100px; width: 100px; border: 2px solid white;" class="ui medium circular image"><br>
            <div class="content mt-4" style="color: white;">
                {{ __('Reset Password') }}
            </div>
        </h2>
            <form id="form" onsubmit="$('#submitResetButton').addClass('loading')" class="ui large form" method="POST" action="{{ route('password.update') }}">
                @csrf
                
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="ui stacked segment">
                    <div class="field @error('email') error @enderror" title="Endereço de E-Mail">
                        <div class="ui left icon input ">
                            <i class="user icon"></i>
                            <input id="email" type="text" name="email" placeholder="Endereço de E-Mail" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                        </div>
                    </div>

                    <div class="field @error('password') error @enderror" title="Senha">
                        <div class="ui left icon input ">
                            <i class="lock icon"></i>
                            <input id="password" type="password" name="password" placeholder="Senha" required autocomplete="password" autofocus>
                        </div>
                    </div>

                    <div class="field @error('password') error @enderror" title="Senha">
                        <div class="ui left icon input ">
                            <i class="lock icon"></i>
                            <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('Confirm Password') }}" autofocus>
                        </div>
                    </div>

                    <button id="submitResetButton" type="submit" class="ui fluid large teal submit button">{{ __('Reset Password') }}</button>

                </div>
            </form>
            @if (session('status'))
            <div class="ui green message" role="alert">
                <strong>{{ session('status') }}</strong>
            </div>
            @endif

            @error('email')
            <div class="ui red message">
                <strong>{{ $message }}</strong>
            </div>
            @enderror

            @error('password')
            <div class="ui red message">
                <strong>{{ $message }}</strong>
            </div>
            @enderror
        </div>
    </div>
</body>

@endsection
