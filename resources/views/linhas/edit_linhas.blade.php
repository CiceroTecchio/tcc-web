@extends('layouts.app')
@section('title', 'Início')
@section('content')

<style>
    body {
        background-color: #00b5ad;
    }
</style>
<div class="ui raised padded container segment mt-5">

    <div class="ui mobile reversed stackable grid container pb-3">
        <div class="four wide column">
            <a href="{{ route('linhas.index') }}" data-tooltip="Cancelar" class="ui mini labeled icon negative button">
                <i class="times icon"></i>
                Cancelar
            </a>
        </div>
        <div class="eight wide column">
            <h2 class="ui aligned center header">
                Informações da Linha
            </h2>
        </div>
    </div>

    <div class="ui divider"> </div>
    <form class="ui form" action="{{ route('linhas.update', $linha->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="field">
            <label>Nome da Linha</label>
            <input type="text" name="nome" value="{{$linha->nome}}" placeholder="Nome da linha...">
        </div>
        <div class="three fields">
            <div class="field">
                <label>Frequência (minutos)</label>
                <input type="number" max="999" value="{{$linha->frequencia}}" min="1" name="frequencia" placeholder="Frequência da linha...">
            </div>
            <div class="field">
                <label>Hora de Início</label>
                <input type="time" name="horario_inicio" value="{{$linha->horario_inicio}}" placeholder="Início da linha...">
            </div>
            <div class="field">
                <label>Hora de Fim</label>
                <input type="time" name="horario_fim" value="{{$linha->horario_fim}}" placeholder="Final da linha...">
            </div>
        </div>
        <div class="four fields">
            <div class="field">
                <div class="ui slider checkbox">
                    <input type="checkbox" @if($linha->fg_ativo == true) checked="checked" @endif name="fg_ativo">
                    <label>Linha Ativa?</label>
                </div>
            </div>
            <div class="field">
                <div class="ui checkbox">
                    <input type="checkbox" @if($linha->fg_domingo == true) checked="checked" @endif name="fg_domingo">
                    <label>Domingo</label>
                </div>
            </div>
            <div class="field">
                <div class="ui checkbox">
                    <input type="checkbox" @if($linha->fg_segunda == true) checked="checked" @endif name="fg_segunda">
                    <label>Segunda-Feira</label>
                </div>
            </div>

            <div class="field">
                <div class="ui checkbox">
                    <input type="checkbox" @if($linha->fg_terca == true) checked="checked" @endif name="fg_terca">
                    <label>Terça-Feira</label>
                </div>
            </div>
        </div>
        <div class="four fields">
            <div class="field">
                <div class="ui checkbox">
                    <input type="checkbox" @if($linha->fg_quarta == true) checked="checked" @endif name="fg_quarta">
                    <label>Quarta-Feira</label>
                </div>
            </div>
            <div class="field">
                <div class="ui checkbox">
                    <input type="checkbox" @if($linha->fg_quinta == true) checked="checked" @endif name="fg_quinta">
                    <label>Quinta-Feira</label>
                </div>
            </div>
            <div class="field">
                <div class="ui checkbox">
                    <input type="checkbox" @if($linha->fg_sexta == true) checked="checked" @endif name="fg_sexta">
                    <label>Sexta-Feira</label>
                </div>
            </div>
            <div class="field">
                <div class="ui checkbox">
                    <input type="checkbox" @if($linha->fg_sabado == true) checked="checked" @endif name="fg_sabado">
                    <label>Sábado</label>
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
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('.ui.checkbox').checkbox();
        $('.ui.form').form({
            inline: true,
            on: 'blur',
            fields: {
                nome: {
                    identifier: 'nome',
                    rules: [{
                        type: 'empty',
                        prompt: 'Digite o nome da linha'
                    }]
                },
                frequencia: {
                    identifier: 'frequencia',
                    rules: [{
                        type: 'integer[1..1000]',
                        prompt: 'Digite a frequência da linha'
                    }]
                },
                horario_inicio: {
                    identifier: 'horario_inicio',
                    rules: [{
                        type: 'minLength[5]',
                        prompt: 'Digite o início da linha'
                    }]
                },
                horario_fim: {
                    identifier: 'horario_fim',
                    rules: [{
                        type: 'minLength[5]',
                        prompt: 'Digite o fim da linha'
                    }]
                }
            }
        });
    });
</script>
@endsection