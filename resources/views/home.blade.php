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

<div id="style-selector-control" style="display: none;position:absolute;" class="ui compact segment form mt-2 p-0 pl-1 pr-1">
    <label>Mostrar Comércios?</label>
    <div class="inline fields p-0 m-0">
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

<script>
    var directionsService = [];
    var directionsRenderer = [];
    var colors = [];
    var markersPontos = [];
    var markersVeiculos = [];
    var markers = <?php echo $linhas ?>;
    var map;
    var pontos = <?php echo $pontos ?>;
    var rota;
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
    }

    function initMap() {
        map = new google.maps.Map(document.getElementById("googleMap"), {
            zoom: 14,
            center: {
                lat: -25.745,
                lng: -53.060
            },
            styles: styles["hide"]
        });

        // Add controls to the map, allowing users to hide/show features.
        const styleControl = document.getElementById("style-selector-control");
        map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(styleControl);
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
        infoWindow = new google.maps.InfoWindow;

        // Try HTML5 geolocation.
        if (navigator.geolocation) {


            icon = {
                url: "/img/marker-user.png",
                scaledSize: new google.maps.Size(40, 40),
            };

            navigator.geolocation.getCurrentPosition(function(position) {
                    var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    userMarker = new google.maps.Marker({
                        position: pos,
                        icon: icon,
                        map: map,
                        title: 'Sua localização!'
                    });
                },
                function() {
                    handleLocationError(true, infoWindow, map.getCenter());
                });
            navigator.geolocation.watchPosition(function(position) {
                    var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    userMarker.setPosition(new google.maps.LatLng(pos));
                },
                function() {
                    handleLocationError(true, infoWindow, map.getCenter());
                });
        } else {
            // Browser doesn't support Geolocation
            handleLocationError(false, infoWindow, map.getCenter());
        }
        displayPontos();
        displayRoute();
    }

    function handleLocationError(browserHasGeolocation, infoWindow, pos) {}

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
        $.get('/busca/localizacao/' + value, function(data) {
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
                    url: '/busca/linhas',
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

                $.get('/busca/rota/' + value, function(data) {
                    markers = [];
                    markers = data['linha'];

                    displayRoute();
                });

                onChangeActive = true;
                getVeiculos(true);

            }
        });
        $('#style-selector-control').show();
    });
</script>
<!-- https://roads.googleapis.com/v1/snapToRoads?path=&key=AIzaSyAzCKnFntWPYLZPMiR6Ayf-grtw5SP_0Pc -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzCKnFntWPYLZPMiR6Ayf-grtw5SP_0Pc&callback=initMap&libraries=&v=weekly"></script>

@endsection