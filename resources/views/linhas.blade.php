@extends('layouts.app')
@section('title', 'Linhas')
@section('content')

<h6 class="ui center teal aligned icon header mt-3">
    <i class="map signs small icon"> Linhas</i>
</h6>

<div class="ui container segment table-responsive-lg pl-4 pr-0">

    <table class="ui celled unstackable table w-100">
        <thead>
            <tr>
                <th class="collapsing">Linha</th>
                <th class="collapsing">Percurso</th>
                <th class="collapsing">Dias</th>
                <th class="collapsing">Horário</th>
                <th class="collapsing">Frequência</th>
                <th class="collapsing">Empresa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($linhas as $linha)
            <tr>
                <td class="collapsing">{{$linha->nome}}</td>
                <td class="collapsing selectable center aligned">
                    <a class="linkIcon" href="#" onclick="openMapaModal({{$linha->id}},'{{$linha->nome}}')">
                        <i class="map icon"></i>
                    </a>
                </td>
                <?php
                $datas = [];
                if ($linha->fg_segunda) {
                    array_push($datas, "Segunda-Feira");
                }
                if ($linha->fg_terca) {
                    array_push($datas, "Terça-Feira");
                }
                if ($linha->fg_quarta) {
                    array_push($datas, "Quarta-Feira");
                }
                if ($linha->fg_quinta) {
                    array_push($datas, "Quinta-Feira");
                }
                if ($linha->fg_sexta) {
                    array_push($datas, "Sexta-Feira");
                }
                if ($linha->fg_sabado) {
                    array_push($datas, "Sábado");
                }
                if ($linha->fg_domingo) {
                    array_push($datas, "Domingo");
                }
                $data = '';
                if (count($datas) == 0) {
                    $data = 'Nenhum dia Cadastrado';
                } else if (count($datas) == 1) {
                    $data = $datas[0];
                } else {
                    for ($x = 0; $x < count($datas); $x++) {
                        if ($x == 0) {
                            $data = $data . $datas[$x];
                        } else if ($x == count($datas) - 1) {
                            $data = $data . ' e ' . $datas[$x];
                        } else {
                            $data = $data . ', ' . $datas[$x];
                        }
                    }
                }

                ?>
                <td class="collapsing">{{$data}}</td>

                <td class="collapsing">{{ date('H:i', strtotime($linha->horario_inicio)) }} até {{ date('H:i', strtotime($linha->horario_fim)) }}</td>
                <td class="collapsing">{{$linha->frequencia}} minutos</td>
                <td class="collapsing">{{$linha->empresa}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="ui large modal">
    <i class="close icon"></i>
    <div id="linhaTitulo" class="header"></div>
    <div class="content">
        <div id="dimmerRota" class="ui dimmer">
            <div class="ui text loader">Carregando Rota</div>
        </div>
        <div id="googleMap"></div>
    </div>
</div>

<script>
    var directionsRenderer;
    var directionsService;
    //Negócios não serão mostrados no mapa
    const styles = {
        default: [],
        hide: [{
            featureType: "poi.business",
            stylers: [{
                visibility: "off"
            }]
        }]
    };

    function openMapaModal(id, nome) {
        $('#dimmerRota').addClass('active');
        redimensionarDivs();
        $('#linhaTitulo').text(nome);
        buscarRota(id);
        $('.ui.modal')
            .modal({
                blurring: true
            })
            .modal('show');
    }

    function buscarRota(id) {
        $.get('/rotas/' + id, function(data) {
            mostrarNoMapa(data);
        });
    }


    //Mostra as rotas no mapa
    function mostrarNoMapa(data) {
        var linha = data['linha'];
        var wps = JSON.parse(linha.waypoints);
        if (directionsRenderer != null) {
            directionsRenderer.setMap(null);
        }
        directionsRenderer = new google.maps.DirectionsRenderer({
            map,
            polylineOptions: {
                strokeWeight: 2,
                strokeOpacity: 1,
                geodesic: true,
                icons: [{
                    icon: {
                        path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW
                    },
                    offset: '100%',
                    repeat: '150px'
                }]
            },
            suppressMarkers: true,
        });

        directionsService = new google.maps.DirectionsService();

        if (wps.length > 0) {
            var w = [];
            for (var y = 0; y < wps.length; y++) {
                w.push({
                    location: wps[y].location.lat + ',' + wps[y].location.lng,
                    stopover: true,
                });
            }
            var options = {
                origin: JSON.parse(linha.origin),
                destination: JSON.parse(linha.destination),
                waypoints: w,
                travelMode: google.maps.TravelMode.DRIVING
            };
        } else {
            var options = {
                origin: JSON.parse(linha.origin),
                destination: JSON.parse(linha.destination),
                travelMode: google.maps.TravelMode.DRIVING
            };
        }

        directionsService.route(options,
            (result, status) => {
                if (status === "OK") {
                    directionsRenderer.setDirections(result);
                    directionsRenderer.setMap(map);
                    $('#dimmerRota').removeClass('active');

                } else {
                    alert("Falha ao mostrar roteiro, tente novamente");
                }
            }
        );
    }

    //ao alterar tamanho da tela, redimensiona as divs
    $(window).resize(function() {
        redimensionarDivs();
    });

    //redimensiona as divs
    function redimensionarDivs() {
        document.getElementById('googleMap').style.height = (window.innerHeight * 0.75) + 'px';
    }


    //Inicia o Mapa
    function initMap() {
        map = new google.maps.Map(document.getElementById("googleMap"), {
            zoom: 14,
            center: {
                lat: -25.745,
                lng: -53.060
            },
            styles: styles["hide"]
        });
    }

    $(document).ready(function() {

        var table = $('.ui.table').DataTable({
            autoWidth: true,
            "dom": "<'ui unstackable grid container'" +
                "<'row'" +
                "<'six wide column'l>" +
                "<'right aligned ten wide column'f>" +
                ">" +
                "<'row dt-table'" +
                "<'sixteen wide column'tr>" +
                ">" +
                "<'row'" +
                "<'right aligned sixteen wide column'p>" +
                ">" +
                ">",
            "columnDefs": [{
                "targets": [1],
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
                }
            },
            "scrollY": "50vh",
            scrollX: true,
            scroller: true
        });
        setTimeout(function() {
            $('.ui.table').DataTable().columns.adjust();
        }, 200);
        $(window).on('resize', function() {
            $('.ui.table').DataTable().columns.adjust();
        });

    });
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzCKnFntWPYLZPMiR6Ayf-grtw5SP_0Pc&callback=initMap&libraries=&v=weekly"></script>
@endsection