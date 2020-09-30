@extends('layouts.app')
@section('title', 'Editar Roteiro')
@section('content')

<style>
    #googleMap {
        padding-left: 3%;
    }

    #aviso {
        position: absolute;
        z-index: 1;
        width: 80%;
        margin-left: 7%;
    }

    #infoMessage {
        position: absolute;
        pointer-events: none;
        z-index: 1;
        width: 100%;
        top: 40px;
    }

    #buttons {
        position: absolute;
        z-index: 1;
        top: 120px;
    }

    .pointer {
        pointer-events: all;
    }

    @media screen and (max-width: 700px) {
        #infoMessage {
            top: 90px;
            padding-left: 45%;
        }
        #style-selector-control{
            margin-top: 40px !important;
        }
    }
</style>

<div id="msgAlerta" class="ui two column centered grid mt-5" style="display:none;position: absolute;z-index: 999; width:100%;">
    <div class="ui negative message" style="width:50%;">
        <i class="close icon"></i>
        <h2 class="ui header">
            Falha ao salvar rota, tente novamente
        </h2>
    </div>
</div>

<div id="style-selector-control" style="display: none;position:absolute;" class="ui compact segment form mt-5 p-0 pl-1 pr-1">
    <label>Mostrar Comércios?</label>
    <div class="inline fields m-0">
        <div class="field">
            <div class="ui radio checkbox">
                <input id="hide-poi" type="radio" name="frequency" checked="checked">
                <label>Não</label>
            </div>
        </div>
        <div class="field">
            <div class="ui radio checkbox">
                <input id="show-poi" type="radio" name="frequency">
                <label>Sim</label>
            </div>
        </div>
    </div>
</div>

<div>
    <div id="googleMap"></div>
</div>

<form id="formEdit" class="ui form" action="{{ route('linha_update_mapa', $linha->id) }}" method="post">
    @csrf
    @method('PUT')
    <input id="origin" value="{{$linha->origin}}" name="origin" type="hidden">
    <input id="destination" value="{{$linha->destination}}" name="destination" type="hidden">
    <input id="waypoints" value="{{$linha->waypoints}}" name="waypoints" type="hidden">
</form>

<div id="buttons" class="ui left aligned grid ml-3">
    <a href="{{ route('linhas.index') }}" data-position="right center" data-tooltip="Voltar" class="ui teal icon button">
        <i class="arrow left icon"></i>
    </a>
    <button data-position="right center" data-tooltip="Limpar o mapa" class="ui yellow icon button" onclick="limparRotas()">
        <i class="eraser icon"></i>
    </button>
    <button data-position="right center" data-tooltip="Remover último ponto" class="ui green icon button" onclick="removerUltimo()">
        <i class="undo icon"></i>
    </button>
</div>

<div id="infoMessage" class="ui center aligned grid">
    <div id="info" class="ui center aligned warning large message w-auto pointer">
    </div>
</div>

<div id="aviso" class="ui center aligned grid">
    <div class="ui center aligned teal icon large message w-auto">
        <i id="mapMarker" style="display: none;" class="map marker alternate icon"></i>
        <div class="content">
            <div id="mensagem" class="header">
                Ajuste o percurso da linha
            </div>
        </div>
        <button id="buttonTerminei" data-tooltip="Adicionar Fim da Rota" class="ui green button ml-3" style="display: none;" onclick="pontoFinal()">
            <i class="check icon"></i> Terminei
        </button>
        <button id="buttonFinalizar" data-tooltip="Finalizar Rota" class="ui green button ml-3" onclick="finalizarRota()">
            <i class="flag checkered icon"></i> Finalizar
        </button>
    </div>
</div>


<script>
    var markers = [],
        waypts = [];
    var colors = ["green", "red", "blue", "purple", "yellow"];
    var listener, directionsRenderer, directionsService, map;
    const styles = {
        default: [],
        hide: [{
            featureType: "poi.business",
            stylers: [{
                visibility: "off"
            }]
        }]
    };

    //redimensiona as divs
    redimensionarDivs();

    //ao alterar tamanho da tela, redimensiona as divs
    $(window).resize(function() {
        redimensionarDivs();
    });


    function redimensionarDivs() {
        document.getElementById('googleMap').style.height = (window.innerHeight - 48) + 'px';
        document.getElementById('aviso').style.bottom = (window.innerHeight * 0.06) + 'px';
    }

    function finalizarRota() {
        var fields = $('#formEdit').form('get values', ['waypoints', 'origin', 'destination']);
        console.log(fields)
        if (fields['waypoints'].length > 0 && fields['origin'].length > 0 && fields['destination'].length > 0) {
            $('#formEdit').submit();
        } else {
            $('#msgAlerta').show();
            $('#msgAlerta')
                .delay(5000)
                .queue(function() {
                    $('#msgAlerta').transition('fade down');
                });
        }
    }

    function pontoFinal() {
        map.setOptions({
            draggableCursor: "crosshair"
        });
        $("#mensagem").text("Selecione o final da rota");
        colocarListener();
    }

    function limparRotas() {
        for (let i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
        }
        markers = [];
        waypts = [];

        if (directionsRenderer != null) {
            directionsRenderer.setMap(null);
        }

        google.maps.event.removeListener(listener);
        colocarListener();
        map.setOptions({
            draggableCursor: "crosshair"
        });
        $("#mensagem").text("Selecione o início da rota");
        $("#buttonTerminei").hide();
        $("#buttonFinalizar").hide();
        $('#infoMessage').hide();
        $("#mapMarker").show();

    }

    function initMap() {

        var wpts = <?php echo $linha->waypoints ?>;
        for (var x = 0; x < wpts.length; x++) {
            waypts.push({
                location: wpts[x].location.lat + ',' + wpts[x].location.lng,
                stopover: false,
            });
        }

        map = new google.maps.Map(document.getElementById("googleMap"), {
            zoom: 15,
            center: {
                lat: -25.745,
                lng: -53.060
            },
            styles: styles["hide"]
        });

        // Add controls to the map, allowing users to hide/show features.
        const styleControl = document.getElementById("style-selector-control");
        map.controls[google.maps.ControlPosition.LEFT_TOP].push(styleControl);
        document.getElementById("hide-poi").addEventListener("click", () => {
            map.setOptions({
                styles: styles["hide"]
            });
        });
        document.getElementById("show-poi").addEventListener("click", () => {
            map.setOptions({
                styles: styles["default"]
            });
        });

        var origem = <?php echo $linha->origin ?>;
        markers.push(new google.maps.Marker({
            icon: "http://maps.google.com/mapfiles/ms/icons/" + colors[markers.length] + "-dot.png",
            position: new google.maps.LatLng({
                lat: origem.lat,
                lng: origem.lng
            }),
            map: map,
            stopover: false,
            title: (markers.length).toString()
        }));
        var destino = <?php echo $linha->destination ?>;
        markers.push(new google.maps.Marker({
            icon: "http://maps.google.com/mapfiles/ms/icons/" + colors[markers.length] + "-dot.png",
            position: new google.maps.LatLng({
                lat: destino.lat,
                lng: destino.lng
            }),
            map: map,
            stopover: false,
            title: (markers.length).toString()
        }));

        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            draggable: true,
            map,
            panel: document.getElementById("right-panel")
        });
        displayRoute();
        colocarDirectionlistener();
        colocarListener();
    }

    function colocarDirectionlistener() {
        directionsRenderer.addListener('directions_changed', function() {
            var result = directionsRenderer.getDirections();
            rota = result;
            atualizarPainel(result);
            if (rota.request.origin.location == null) {
                $('#origin').val(JSON.stringify(rota.request.origin));
            } else {
                $('#origin').val(JSON.stringify(rota.request.origin.location));
            }
            if (rota.request.destination.location == null) {
                $('#destination').val(JSON.stringify(rota.request.destination));
            } else {
                $('#destination').val(JSON.stringify(rota.request.destination.location));
            }
            $('#waypoints').val(JSON.stringify(rota.routes[0].legs[0].via_waypoint));
            waypts = [];
            for (var x = 0; x < result.routes[0].legs[0].via_waypoint.length; x++) {
                waypts.push({
                    location: result.routes[0].legs[0].via_waypoint[x].location.lat() + ', ' + result.routes[0].legs[0].via_waypoint[x].location.lng(),
                    stopover: false,
                });
            }
        });
    }


    function colocarListener() {
        listener = map.addListener('click', function(mapsMouseEvent) {
            map.setOptions({
                draggableCursor: "crosshair"
            });
            if (markers.length == 0) {
                markers.push(new google.maps.Marker({
                    icon: "http://maps.google.com/mapfiles/ms/icons/" + colors[markers.length] + "-dot.png",
                    position: mapsMouseEvent.latLng,
                    map: map,
                    stopover: false,
                    title: (markers.length).toString()
                }));

                $("#mensagem").text("Selecione o ponto mais distante da rota");
            } else if (markers.length == 1) {
                google.maps.event.removeListener(listener);

                markers.push(new google.maps.Marker({
                    icon: "http://maps.google.com/mapfiles/ms/icons/" + colors[markers.length] + "-dot.png",
                    position: mapsMouseEvent.latLng,
                    map: map,
                    stopover: false,
                    title: (markers.length).toString()
                }));

                $("#mensagem").text("Ajuste o percurso realizado nessa parte do trajeto");
                $("#mapMarker").hide();
                $("#buttonTerminei").show();
                map.setOptions({
                    draggableCursor: ""
                });
                displayRoute();
            } else if (markers.length == 2) {
                google.maps.event.removeListener(listener);
                waypts.push({
                    location: markers[1].position.lat() + ',' + markers[1].position.lng(),
                    stopover: false,
                });
                markers[1].setMap(null);
                markers.pop();

                markers.push(new google.maps.Marker({
                    icon: "http://maps.google.com/mapfiles/ms/icons/" + colors[markers.length] + "-dot.png",
                    position: mapsMouseEvent.latLng,
                    map: map,
                    stopover: false,
                    title: (markers.length).toString()
                }));
                $("#mensagem").text("Ajuste o percurso da linha");
                $("#buttonTerminei").hide();
                $("#buttonFinalizar").show();
                map.setOptions({
                    draggableCursor: ""
                });

                displayRoute();
            }

        });

    }

    function displayRoute() {
        directionsRenderer.setMap(null);
        directionsRenderer = null;
        directionsRenderer = new google.maps.DirectionsRenderer({
            draggable: true,
            map,
            panel: document.getElementById("right-panel")
        });
        colocarDirectionlistener();

        if (waypts.length > 0) {
            var options = {
                origin: markers[0].position,
                destination: markers[markers.length - 1].position,
                waypoints: waypts,
                travelMode: google.maps.TravelMode.DRIVING
            };
        } else {
            var options = {
                origin: markers[0].position,
                destination: markers[markers.length - 1].position,
                travelMode: google.maps.TravelMode.DRIVING
            };
        }
        directionsService.route(options,
            (result, status) => {
                if (status === "OK") {
                    directionsRenderer.setDirections(result);
                    directionsRenderer.setMap(map);
                    for (var x = 0; x < markers.length; x++) {
                        markers[x].setMap(null);
                    }
                    atualizarPainel(result);
                } else {
                    alert("Falha ao mostrar roteiro, tente novamente");
                }
            }
        );
    }

    function atualizarPainel(result) {

        $('#infoMessage').show();
        var route = result.routes[0];
        document.getElementById('info').innerHTML = '<p class="m-0 p-0"><i class="icon road"></i>Percurso de ' + route.legs[0].distance.text + '</p>';
        document.getElementById('info').innerHTML += '<p class="m-0 p-0"><i class="icon stopwatch"></i>Aproximadamente ' + parseInt((route.legs[0].duration.value * 1.4) / 60) + ' minutos';
    }

    function removerUltimo() {

        if (waypts.length > 0) {
            waypts.pop();

            displayRoute();

        } else {
            $('#infoMessage').hide();
            if (markers.length - 1 == 0) {
                markers[0].setMap(null);
                $("#mensagem").text("Selecione o início da rota");

            } else if (markers.length - 1 == 1) {
                directionsRenderer.setMap(null);
                $("#mensagem").text("Selecione o ponto mais distante da rota");
                $("#buttonTerminei").hide();
                $("#buttonFinalizar").hide();
                $("#mapMarker").show();
            } else if (markers.length - 1 == 2) {
                directionsRenderer.setMap(null);
                $("#mensagem").text("Selecione o final da rota");
                $("#buttonFinalizar").hide();
                $("#buttonTerminei").show();
            }

            markers.pop();

            if (markers.length < 2) {
                map.setOptions({
                    draggableCursor: "crosshair"
                });
            }
            for (var x = 0; x < markers.length; x++) {
                markers[x].setMap(map);
            }
        }

    }

    // Handles click events on a map, and adds a new point to the Polyline.
    function addLatLng(event) {

        var latitude = event.latLng.lat().toString();
        var longitude = event.latLng.lng().toString();

        locais.push({
            location: latitude + ', ' + longitude,
        });

        setRoutes();
    }

    function setRoutes() {
        service.route({
            origin: locais[0].location,
            destination: locais[locais.length - 1].location,
            waypoints: locais.slice(1, -1),
            travelMode: google.maps.TravelMode.DRIVING,
        }, function(response, status) {
            if (status === 'OK') {
                directionsRenderer.setDirections(response);
            }
        });
    }
    
    $('#style-selector-control').show();
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzCKnFntWPYLZPMiR6Ayf-grtw5SP_0Pc&callback=initMap&libraries=&v=weekly"></script>
@endsection