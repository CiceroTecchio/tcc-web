@extends('layouts.app')
@section('title', 'Início')
@section('content')

<style>
    body {
        background-color: #00b5ad;
    }
</style>
@if (session('error'))
<div class="ui two column centered grid mt-5 msgAlerta w-100" style="position: absolute;z-index: 999;">
    <div class="ui negative message w-50">
        <i class="close icon"></i>
        <h2 class="ui header">
            {{ session('error') }}
        </h2>
    </div>
</div>
@endif

@if (session('success'))
<div class="ui two column centered grid mt-5 msgAlerta w-100" style="position: absolute;z-index: 999;">
    <div class="ui success message w-50">
        <i class="close icon"></i>
        <h2 class="ui header">
            {{ session('success') }}
        </h2>
    </div>
</div>
@endif

@if (session('info'))
<div class="ui two column centered grid mt-5 msgAlerta w-100" style="position: absolute;z-index: 999;">
    <div class="ui info message w-50">
        <i class="close icon"></i>
        <h2 class="ui header">
            {{ session('info') }}
        </h2>
    </div>
</div>
@endif

<div class="ui raised very padded container segment mt-5">

    <div class="ui mobile reversed stackable grid container pb-3">
        <div class="four wide column">
            <a href="{{ route('colaboradores.index') }}" data-tooltip="Cancelar" class="ui mini labeled icon negative button">
                <i class="times icon"></i>
                Cancelar
            </a>
        </div>
        <div class="eight wide column">
            <h2 class="ui aligned center header">
                Cadastro de Colaborador
            </h2>
        </div>
    </div>

    <div class="ui divider"> </div>

    <form class="ui form" action="{{ route('colaboradores.store') }}" method="POST">
        @csrf

        <div class="two fields">
            <div class="field">
                <label>Nome</label>
                <input type="text" name="name" placeholder="Nome do Colaborador..." autocomplete="false">
            </div>
            <div class="field">
                <label>CPF</label>
                <input id="cpf" type="text" name="cpf" placeholder="Número do CPF..." autocomplete="false">
            </div>
        </div>
        <div class="two fields">
            <div class="field">
                <label>E-Mail</label>
                <input type="text" name="email" placeholder="Endereço de E-Mail..." autocomplete="false">
            </div>
            <div class="field">
                <label>Senha</label>
                <input id="password" type="password" name="password" placeholder="Senha" autocomplete="false">
            </div>
        </div>
        <div class="two fields">
            <div class="field">
                <div class="ui slider checkbox">
                    <input type="checkbox" checked="checked" name="fg_ativo">
                    <label>Ativo</label>
                </div>
            </div>
            <div class="field">
                <div class="ui toggle checkbox">
                    <input type="checkbox" name="fg_admin">
                    <label>Administrador</label>
                </div>
            </div>
        </div>
        <div class="ui centered grid">
            <div class="row">
                <button type="submit" class="ui labeled icon positive button">
                    <i class="save icon"></i>
                    <b style="font-size: 18px;"> Salvar </b>
                </button>
            </div>
            @if (session('incorrectFields'))
            <div class="ui red message">
                <i class="close icon"></i>
                <div class="header">
                    Erro ao Salvar Colaborador
                </div>
                <ul class="list">
                    @foreach(session('incorrectFields') as $message)
                    <li>{{$message}}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('.ui.checkbox').checkbox();
        $('#cpf').mask('000.000.000-00');
        $('.ui.form').form({
            inline: true,
            on: 'blur',
            fields: {
                name: {
                    identifier: 'name',
                    rules: [{
                        type: 'empty',
                        prompt: 'Digite o nome do Colaborador'
                    }]
                },
                email: {
                    identifier: 'email',
                    rules: [{
                        type: 'email',
                        prompt: 'Digite o E-Mail do Colaborador'
                    }]
                },
                cpf: {
                    identifier: 'cpf',
                    rules: [{
                        type: 'exactLength[14]',
                        prompt: 'Digite o CPF do Colaborador'
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
            }
        });
    });
</script>
@endsection