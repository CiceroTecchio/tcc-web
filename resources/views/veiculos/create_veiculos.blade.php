@extends('layouts.app')
@section('title', 'Início')
@section('content')

<style>
    body {
        background-color: #00b5ad;
    }
</style>

<div class="ui raised very padded container segment mt-5">

    <div class="ui mobile reversed stackable grid container pb-3">
        <div class="four wide column">
            <a href="{{ route('veiculos.index') }}" data-tooltip="Cancelar" class="ui mini labeled icon negative button">
                <i class="times icon"></i>
                Cancelar
            </a>
        </div>
        <div class="eight wide column">
            <h2 class="ui aligned center header">
                Cadastro de Veículo
            </h2>
        </div>
    </div>

    <div class="ui divider"> </div>

    <form class="ui form" action="{{ route('veiculos.store') }}" method="POST">
        @csrf

        <div class="field">
            <label>Nome Identificador</label>
            <input type="text" name="identificador" placeholder="Identificação do Veículo...">
        </div>
        <div class="two fields">
            <div class="field">
                <label>Marca</label>
                <select name="cod_marca" class="ui fluid search dropdown">
                    <option value="">Marca do Veículo...</option>
                    @foreach($marcas as $marca)
                    <option value="{{$marca->id}}">{{$marca->descricao_marca}}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Placa</label>
                <input type="text" name="placa" placeholder="Placa do Veículo...">
            </div>
        </div>

        <div class="field">
            <div class="ui slider checkbox">
                <input type="checkbox" checked="checked" text name="fg_ativo">
                <label>Veículo Ativo?</label>
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
        $('.ui.dropdown').dropdown({
            fullTextSearch: true,
            ignoreCase: true,
            message: {
                noResults: 'Nenhuma marca encontrada.'
            }
        });
        $('.ui.form').form({
            inline: true,
            on: 'blur',
            fields: {
                identificador: {
                    identifier: 'identificador',
                    rules: [{
                        type: 'empty',
                        prompt: 'Digite a Identificação do veículo'
                    }]
                },
                cod_marca: {
                    identifier: 'cod_marca',
                    rules: [{
                        type: 'empty',
                        prompt: 'Digite a Marca do veículo'
                    }]
                },
                placa: {
                    identifier: 'placa',
                    rules: [{
                        type: 'minLength[7]',
                        prompt: 'Digite a Placa do veículo'
                    }]
                }
            }
        });
    });
</script>
@endsection