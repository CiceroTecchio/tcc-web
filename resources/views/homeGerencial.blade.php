@extends('layouts.app')
@section('title', 'Início')
@section('content')
<style>
    #googleMap {
        padding-left: 3%;
    }

    .alerta {
        position: absolute;
        z-index: 999;
        width: 100%;
        top: 90px;
        pointer-events: none;
    }

    #searchBox {
        position: absolute;
        z-index: 1;
        width: 100%;
        pointer-events: none;
    }

    @media screen and (max-width: 767px) {
        .raised.segment {
            width: 97% !important;
        }

        #searchBox {
            display: none;
            top: 90px;
        }
    }

    .pointer {
        pointer-events: all;
    }
</style>

<div class="ui two column centered grid msgAlerta alerta">
    @if (session('error'))
    <div class="ui negative message w-50 pointer">
        <i class="close icon"></i>
        <h2 class="ui header">
            {{ session('error') }}
        </h2>
    </div>
    @endif

    @if (session('success'))
    <div class="ui success message w-50 msgAlerta pointer">
        <i class="close icon"></i>
        <h2 class="ui header">
            {{ session('success') }}
        </h2>
    </div>
    @endif
</div>

<div id="searchBox" class="ui two column centered grid" style="margin-left: 0% !important;">
    <div class="ui raised secondary segment w-25 pointer">
        <select id="selectLinha" class="ui fluid search dropdown">
            <option value="">Todas as Linhas</option>
        </select>
    </div>
</div>

<div>
    <div id="googleMap"></div>
</div>

<script>
    var map, listener, icon, timer;
    var markers = <?php echo $linhas ?>;
    var colors = [];
    var markersPontos = [];
    var markersVeiculos = [];
    var directionsService = [];
    var infoWindow = [];
    var directionsRenderer = [];
    var polylineOptionsActual = [];
    var pontos = <?php echo $pontos ?>;
    var veiculos = [];

    //redimensiona as divs
    redimensionarDivs();

    //ao alterar tamanho da tela, redimensiona as divs
    $(window).resize(function() {
        redimensionarDivs();
    });

    function redimensionarDivs() {
        document.getElementById('googleMap').style.height = (window.innerHeight - 48) + 'px';
    }

    function initMap() {
        map = new google.maps.Map(document.getElementById("googleMap"), {
            zoom: 14,
            center: {
                lat: -25.745,
                lng: -53.060
            }
        });
        displayPontos();
        displayRoute();
    }

    function displayPontos() {
        icon = {
            url: "/img/bus-stop.png",
            scaledSize: new google.maps.Size(40, 40),
        };

        for (var x = 0; x < pontos.length; x++) {
            markersPontos.push(new google.maps.Marker({
                position: {
                    lat: parseFloat(pontos[x].latitude),
                    lng: parseFloat(pontos[x].longitude)
                },

                icon: icon,
                map: map,
                title: 'Ponto de Parada - ' + (markersPontos.length + 1).toString()
            }));
        }
    }


    function displayRoute() {
        var directionsService = [];
        var directionsRenderer = [];
        var colors = [];
        for (var x = 0; x < markers.length; x++) {
            mostrarNoMapa(x);
        }
    }

    function mostrarNoMapa(id) {
        colors[id] = "#" + ((1 << 24) * Math.random() | 0).toString(16);
        var wps = JSON.parse(markers[id].waypoints);
        directionsRenderer[id] = new google.maps.DirectionsRenderer({
            map,
            polylineOptions: {
                strokeColor: colors[id]
            },
            suppressMarkers: true,
            preserveViewport: true
        });
        directionsService[id] = new google.maps.DirectionsService();

        if (wps.length > 0) {
            var w = [];
            for (var y = 0; y < wps.length; y++) {
                w.push({
                    location: wps[y].location.lat + ',' + wps[y].location.lng,
                    stopover: false,
                });
            }
            var options = {
                origin: JSON.parse(markers[id].origin),
                destination: JSON.parse(markers[id].destination),
                waypoints: w,
                travelMode: google.maps.TravelMode.DRIVING
            };
        } else {
            var options = {
                origin: JSON.parse(markers[id].origin),
                destination: JSON.parse(markers[id].destination),
                travelMode: google.maps.TravelMode.DRIVING
            };
        }
        directionsService[id].route(options,
            (result, status) => {
                if (status === "OK") {
                    directionsRenderer[id].setDirections(result);
                    directionsRenderer[id].setMap(map);
                } else {
                    alert("Falha ao mostrar roteiro, tente novamente");
                }
            }
        );
    }

    function getVeiculos(clean) {
        var value = $('#selectLinha').dropdown('get value');
        if (value == null) {
            value = '';
        }
        $.get('/gerencial/busca/localizacao/' + value, function(data) {
            veiculos = data['localizacao'];
            displayVeiculos(clean);
        });
    }

    function displayVeiculos(clean) {
        icon = {
            url: "/img/marker-veiculo.png",
            scaledSize: new google.maps.Size(30, 40),
        };

        if (clean == true) {
            for (var x = 0; x < markersVeiculos.length; x++) {
                markersVeiculos[x].setMap(null);
            }
            markersVeiculos = [];
            for (var x = 0; x < veiculos.length; x++) {
                if (veiculos[x].latitude != null) {

                    markersVeiculos.push(new google.maps.Marker({
                        position: {
                            lat: parseFloat(veiculos[x].latitude),
                            lng: parseFloat(veiculos[x].longitude)
                        },

                        icon: icon,
                        map: map,
                        title: 'Veículo - ' + (markersVeiculos.length + 1).toString()
                    }));
                    mostrarInfo(x);
                }

            }
        } else {
            for (var x = 0; x < markersVeiculos.length; x++) {
                markersVeiculos[x].setPosition(new google.maps.LatLng(parseFloat(veiculos[x].latitude), parseFloat(veiculos[x].longitude)));
            }
        }
    }

    function mostrarInfo(x) {
        infoWindow = new google.maps.InfoWindow;
        var content = '<div class="ui message">\
                <div class="header">\
                    ' + veiculos[x].linha + '\
                </div>\
                <ul class="list">\
                    <li><b>Colaborador:</b> ' + veiculos[x].colaborador + '</li>\
                    <li><b>Veículo:</b> ' + veiculos[x].veiculo + '</li>\
                </ul>\
            </div>';

        infoWindow.setContent(content);
        markersVeiculos[x].addListener('click', function() {
            infoWindow.open(map, markersVeiculos[x]);
        });
    }

    $(document).ready(function() {
        getVeiculos(true);
        timer = setInterval(getVeiculos, 3000);

        $('.ui.dropdown')
            .dropdown({
                message: {
                    noResults: 'Nenhuma linha encontrada.'
                },
                placeholder: "Todas as Linhas",
                clearable: true,
                ignoreCase: true,
                forceSelection: false,
                fullTextSearch: true,
                saveRemoteData: false,
                filterRemoteData: true,
                fields: {
                    remoteValues: 'linhas',
                    name: 'nome',
                    value: 'id',
                },
                apiSettings: {
                    url: '/gerencial/busca/linhas',
                    cache: false,
                },

            });

        var onChangeActive = true;

        $('.ui.dropdown').dropdown('setting', 'onChange', function(value, text, $selectedItem) {
            if (onChangeActive) {
                onChangeActive = false;
                if (value.length > 0) {
                    $('.ui.dropdown').dropdown('set value', value);
                    $('.ui.dropdown').dropdown('set text', text);
                } else {
                    $('.ui.dropdown').dropdown('clear');
                }
                for (var x = 0; x < directionsRenderer.length; x++) {
                    directionsRenderer[x].setMap(null);
                }

                $.get('/gerencial/busca/rota/' + value, function(data) {
                    markers = [];
                    markers = data['linha'];

                    displayRoute();
                });

                onChangeActive = true;
                getVeiculos(true);

            }
        });

    });
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzCKnFntWPYLZPMiR6Ayf-grtw5SP_0Pc&callback=initMap&libraries=&v=weekly"></script>

@endsection