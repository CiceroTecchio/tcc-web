@extends('layouts.app')
@section('title', 'Usuários')
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


<div class="ui container segment mt-5">
    <div class="ui mobile reversed stackable grid container pb-3">
        <div class="four wide column">
            <a class="ui green labeled icon button" href="{{ route('colaboradores.create') }}">
                <i class="plus icon"></i>
                Adicionar
            </a>
        </div>
        <div class="eight wide column">
            <h2 class="ui teal aligned center header">
                <i style="font-size: 28px;" class="users icon"></i> Colaborador
            </h2>
        </div>
    </div>

    <div class="table-responsive-lg">
        <table class="ui table unstackable celled" style="width: 100%;">
            <thead>
                <tr>
                    <th class="collapsing">ID</th>
                    <th>Nome</th>
                    <th>E-Mail</th>
                    <th>CPF</th>
                    <th class="collapsing center aligned">Admin</th>
                    <th class="collapsing center aligned">Editar</th>
                    <th class="collapsing center aligned">Ativo</th>
                </tr>
            </thead>
            <tbody class="scrolling content">
                @foreach($usuarios as $usuario)
                <tr>
                    <td class="middle aligned">{{$usuario->id}}</td>
                    <td class="middle aligned">{{$usuario->name}}</td>
                    <td class="middle aligned">{{$usuario->email}}</td>
                    <td class="middle aligned">{{$usuario->cpf}}</td>
                    <form id="formAdmin{{$usuario->id}}" action="{{ route('destroyAdmin', $usuario->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <td class="selectable center aligned">
                            <a class="linkIcon" href="#" onclick="document.getElementById('formAdmin{{$usuario->id}}').submit(); return false;">
                                <i class="large inverted orange star @if($usuario->fg_admin == false) outline @endif icon"></i>
                            </a>
                        </td>
                    </form>
                    <td class="selectable center aligned">
                        <a class="linkIcon" href="{{ route('colaboradores.edit', $usuario->id) }}">
                            <i class="inverted blue edit icon"></i>
                        </a>
                    </td>
                    <form id="formAtivar{{$usuario->id}}" action="{{ route('colaboradores.destroy', $usuario->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <td class="selectable center aligned">
                            <a class="linkIcon" href="#" onclick="document.getElementById('formAtivar{{$usuario->id}}').submit(); return false;">
                                <i class="large inverted @if($usuario->fg_ativo == true) green toggle on @else red toggle off @endif icon"></i>
                            </a>
                        </td>
                    </form>
                </tr>

                @endforeach

            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {

        var table = $('.ui.table').DataTable({
            autoWidth: true,
            "dom": "<'ui stackable grid container'" +
                "<'row'" +
                "<'four wide column'l>" +
                "<'seven wide column'B>" +
                "<'right aligned five wide column'f>" +
                ">" +
                "<'row dt-table'" +
                "<'sixteen wide column'tr>" +
                ">" +
                "<'row'" +
                "<'seven wide column'i>" +
                "<'right aligned nine wide column'p>" +
                ">" +
                ">",
            buttons: [{
                    extend: 'colvis',
                    text: '<i class="inverted brown eye icon"></i> Colunas',
                    titleAttr: 'Visibilidade das Colunas',
                },
                {
                    extend: 'copyHtml5',
                    text: '<i class="inverted black copy icon"></i>',
                    titleAttr: 'Copiar Dados',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="inverted red file pdf icon"></i>',
                    titleAttr: 'Gerar PDF',
                    customize: function(doc) {
                        doc.defaultStyle.alignment = 'left';
                        doc.styles.tableHeader.alignment = 'left';
                        doc.content[1].table.widths = ['10%', '35%', '20%', '35%'];
                    },
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
            ],
            "columnDefs": [{
                "targets": [4, 5, 6],
                "orderable": false
            }],
            "language": {
                "sEmptyTable": "Nenhum registro encontrado",
                "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                "sInfoPostFix": "",
                "sInfoThousands": ".",
                "sLengthMenu": "_MENU_ resultados por página",
                "sLoadingRecords": "Carregando...",
                "sProcessing": "Processando...",
                "sZeroRecords": "Nenhum registro encontrado",
                "sSearch": "Pesquisar",
                "oPaginate": {
                    "sNext": "Próximo",
                    "sPrevious": "Anterior",
                    "sFirst": "Primeiro",
                    "sLast": "Último"
                },
                "oAria": {
                    "sSortAscending": ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                },
                "select": {
                    "rows": {
                        "_": "Selecionado %d linhas",
                        "0": "Nenhuma linha selecionada",
                        "1": "Selecionado 1 linha"
                    }
                },
                "buttons": {
                    "copy": "Copiar",
                    "copyTitle": "Cópia bem sucedida",
                    "copySuccess": {
                        "1": "Uma linha copiada com sucesso",
                        "_": "%d linhas copiadas com sucesso"
                    }
                }
            },
            "scrollY": "50vh",
            scrollX: true,
            scroller: true
        });
        $(window).on('resize', function() {
            $('.ui.table').DataTable().columns.adjust();
        });

    });
</script>
@endsection