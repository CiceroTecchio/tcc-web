@extends('layouts.app')
@section('title', 'Resetar Senha')
@section('content')

<style type="text/css">
    body {
        background-color: #DADADA;
    }

    body>.grid {
        height: 100%;
    }

    .ui.grid {
        margin: 0rem;
    }

    .column {
        max-width: 450px;
        min-width: 450px;
    }
</style>

<body class="pt-5">
    <div class="ui middle aligned center aligned grid">
        <div class="column">
            <h2 class="ui teal image header">
                <div class="content">
                    {{ __('Reset Password') }}
                </div>
            </h2>

            <form id="form" onsubmit="$('#submitResetButton').addClass('loading')" class="ui large form" method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="ui stacked segment">
                    <div class="field @error('email') error @enderror" title="Endereço de E-Mail">
                        <div class="ui left icon input ">
                            <i class="user icon"></i>
                            <input id="email" type="text" name="email" placeholder="Endereço de E-Mail" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        </div>
                    </div>

                    <button id="submitResetButton" type="submit" class="ui fluid large teal submit button">{{ __('Send Password Reset Link') }}</button>

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
        </div>
    </div>
</body>
@endsection