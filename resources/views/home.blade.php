@extends('layouts.app')
@section('title', 'Início')
@section('content')

<style>
    #googleMap {
        padding-left: 3%;
    }

    #aviso {
        position: absolute;
        z-index: 1;
        display: none;
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

        #aviso {
            left: -30px !important;
        }

        #style-selector-control {
            display: none !important;
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

<div id="googleMap"></div>

<div id="aviso" class="ui two column centered grid w-100">
    <div class="column w-auto ml-5">
        <div class="ui teal icon message">
            <i class="bus icon"></i>
            <div class="content">
                <h4 id="mensagem" class="ui header">

                </h4>
            </div>
        </div>
    </div>
</div>

<script>
    var directionsService = [];
    var directionsRenderer = [];
    var colors = [];
    var markersPontos = [];
    var markersVeiculos = [];
    var distancias = [];
    var resultado = [];
    var duracaoDistancia;
    var markers = <?php echo $linhas ?>;
    var map;
    var resultado;
    var pontos = <?php echo $pontos ?>;
    var rota;
    var userMarker;
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

    redimensionarDivs();

    //ao alterar tamanho da tela, redimensiona as divs
    $(window).resize(function() {
        redimensionarDivs();
    });

    //redimensiona as divs
    function redimensionarDivs() {
        document.getElementById('googleMap').style.height = (window.innerHeight - 48) + 'px';
        document.getElementById('aviso').style.bottom = (window.innerHeight * 0.05) + 'px';
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

        // Add a opção de habilitar/desabilitar negócios no mapa
        const styleControl = document.getElementById("style-selector-control");
        map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(styleControl);
        // Oculta os negócios do mapa
        document.getElementById("hide-poi").addEventListener("click", () => {
            map.setOptions({
                styles: styles["hide"]
            });
        });
        // Exibe os negócios do mapa
        document.getElementById("show-poi").addEventListener("click", () => {
            map.setOptions({
                styles: styles["default"]
            });
        });

        infoWindow = new google.maps.InfoWindow;

        // Se conseguir acesso a localização do usuário
        if (navigator.geolocation) {
            var userIcon = {
                url: "/img/marker-user.png",
                scaledSize: new google.maps.Size(40, 40),
            };

            // Colocar o marcador do usuário na localização dele
            navigator.geolocation.getCurrentPosition(function(position) {
                    var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    userMarker = new google.maps.Marker({
                        position: pos,
                        icon: userIcon,
                        map: map,
                        title: 'Sua localização!'
                    });
                },
                function() {
                    erroLocalizacaoUsuario(true, infoWindow, map.getCenter());
                }, {
                    enableHighAccuracy: true,
                    maximumAge: 30000,
                    timeout: 27000
                });

            // Função que atualiza a localização do usuário conforme ele se move
            navigator.geolocation.watchPosition(function(position) {
                    var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    userMarker.setPosition(new google.maps.LatLng(pos));
                },
                function() {
                    erroLocalizacaoUsuario(true, infoWindow, map.getCenter());
                }, {
                    enableHighAccuracy: true,
                    maximumAge: 3000,
                    timeout: 2700
                }
            );
        } else {
            //Falha ao pegar localização do usuário
            erroLocalizacaoUsuario(false, infoWindow, map.getCenter());
        }
        displayRoute();
        displayPontos();
    }

    //Caso a localização do usuário não possa ser acessada
    function erroLocalizacaoUsuario(browserHasGeolocation, infoWindow, pos) {
        var userIcon = {
            url: "/img/marker-user.png",
            scaledSize: new google.maps.Size(40, 40),
        };

        userMarker = new google.maps.Marker({
            position: pos,
            icon: userIcon,
            map: map,
            title: 'Sua localização!'
        });
        var pos = {
            lat: -25.744782,
            lng: -53.068239
        };
        userMarker.setPosition(new google.maps.LatLng(pos));
    }

    //Mostra os pontos de parada no mapa
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

    //Para cada rota, chama a função para exibir no mapa
    function displayRoute() {
        var directionsService = [];
        var directionsRenderer = [];
        var colors = [];
        for (var x = 0; x < markers.length; x++) {
            mostrarNoMapa(x);
        }

    }

    //Mostra as rotas no mapa
    function mostrarNoMapa(id) {
        colors[id] = "#" + ((1 << 24) * Math.random() | 0).toString(16);
        if (markers[id] == null) {
            $(".ui.dropdown").dropdown('change values');
            return null;
        }
        var wps = JSON.parse(markers[id].waypoints);
        directionsRenderer[id] = new google.maps.DirectionsRenderer({
            map,
            polylineOptions: {
                strokeColor: colors[id],
                strokeWeight: 2,
                strokeOpacity: 1,
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
                    stopover: true,
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
                resultado[id] = result;
                if (status === "OK") {
                    if (markers.length == 1) {
                        distancia(id);
                    }
                    directionsRenderer[id].setDirections(result);
                    directionsRenderer[id].setMap(map);
                } else {
                    alert("Falha ao mostrar roteiro, tente novamente");
                }
            }
        );
    }

    //Define a distância entre o veículo e o usuário
    function distancia(id) {
        if (resultado[id] == null) {
            return false;
        } else if (markersVeiculos[id] == null) {
            return false;
        }
        var veiculo = [];
        var user = [];

        //Define os pontos da linha mais próximos do veículo
        for (var x = 0; x < resultado[id].routes[0].legs.length; x++) {
            var distanciaAtual = google.maps.geometry.spherical.computeDistanceBetween(resultado[id].routes[0].legs[x].start_location, markersVeiculos[id].position);
            if (x == 0) {
                distancias[0] = {
                    distancia: distanciaAtual,
                    id: 0
                };
                veiculo['x'] = 0;
                veiculo['y'] = null;
            } else if (distancias[0].distancia > distanciaAtual) {
                distancias[0] = {
                    distancia: distanciaAtual,
                    id: x
                };
                veiculo['x'] = x;
                veiculo['y'] = null;
            }
            for (var y = 0; y < resultado[id].routes[0].legs[x].steps.length; y++) {
                var distanciaAtual = google.maps.geometry.spherical.computeDistanceBetween(resultado[id].routes[0].legs[x].steps[y].start_location, markersVeiculos[id].position);

                if (x == 0 && y == 0) {
                    distancias[0] = {
                        distancia: distanciaAtual,
                        id: 0
                    };
                    veiculo['x'] = 0;
                    veiculo['y'] = 0;
                } else if (distancias[0].distancia > distanciaAtual) {
                    distancias[0] = {
                        distancia: distanciaAtual,
                        id: x
                    };
                    veiculo['x'] = x;
                    veiculo['y'] = x;
                }
            }
        }

        //Define os pontos da linha mais próximos do veículo
        for (var x = 0; x < resultado[id].routes[0].legs.length; x++) {
            var distanciaAtual = google.maps.geometry.spherical.computeDistanceBetween(resultado[id].routes[0].legs[x].start_location, userMarker.position);
            if (x == 0) {
                distancias[1] = {
                    distancia: distanciaAtual,
                    id: 0
                };
                user['x'] = 0;
                user['y'] = 0;
            } else if (distancias[1].distancia > distanciaAtual) {
                distancias[1] = {
                    distancia: distanciaAtual,
                    id: x
                };
                user['x'] = x;
                user['y'] = 0;
            }
            for (var y = 0; y < resultado[id].routes[0].legs[x].steps.length; y++) {
                var distanciaAtual = google.maps.geometry.spherical.computeDistanceBetween(resultado[id].routes[0].legs[x].steps[y].start_location, userMarker.position);

                if (x == 0 && y == 0) {
                    distancias[1] = {
                        distancia: distanciaAtual,
                        id: 0
                    };
                    user['x'] = 0;
                    user['y'] = 0;
                } else if (distancias[1].distancia > distanciaAtual) {
                    distancias[1] = {
                        distancia: distanciaAtual,
                        id: x
                    };
                    user['x'] = x;
                    user['y'] = x;
                }
            }
        }

        var z = 0;
        duracaoDistancia = 0;

        //Se o veículo estiver a frente do usuário
        //Vai ver a distancia até o final da linha, e depois do inicio até o usuário
        if (veiculo['x'] > user['x']) {
            for (var x = veiculo['x']; x < resultado[id].routes[0].legs.length; x++) {
                if (veiculo['y'] == null) {
                    duracaoDistancia += resultado[id].routes[0].legs[x].duration.value;
                } else {
                    for (var y = 0; y < resultado[id].routes[0].legs[x].steps.length; y++) {
                        duracaoDistancia += resultado[id].routes[0].legs[x].steps[y].duration.value;
                    }
                }
            }
            z = 0;

            for (var x = 0; x <= user['x']; x++) {
                if (veiculo['y'] == null) {
                    duracaoDistancia += resultado[id].routes[0].legs[x].duration.value;
                } else {
                    if (veiculo['y'] == null) {
                        duracaoDistancia += resultado[id].routes[0].legs[x].duration.value;
                        for (var y = 0; y <= user['y']; y++) {
                            duracaoDistancia += resultado[id].routes[0].legs[x].steps[y].duration.value;
                        }
                    } else {
                        for (var y = 0; y < resultado[id].routes[0].legs[x].steps.length; y++) {
                            duracaoDistancia += resultado[id].routes[0].legs[x].steps[y].duration.value;
                        }
                    }
                }
            }

            //Se o usuário estiver a frente do veículo
        } else {
            for (var x = veiculo['x']; x <= user['x']; x++) {
                if (veiculo['y'] == null) {
                    duracaoDistancia += resultado[id].routes[0].legs[x].duration.value;
                } else {
                    if (x == user['x']) {
                        for (var y = 0; y <= user['y']; y++) {
                            if (resultado[id].routes[0].legs[x].steps[y] != null) {
                                duracaoDistancia += resultado[id].routes[0].legs[x].steps[y].duration.value;
                            }
                        }
                    } else if (z == 0) {
                        for (var y = veiculo['y']; y < resultado[id].routes[0].legs[x].steps.length; y++) {
                            duracaoDistancia += resultado[id].routes[0].legs[x].steps[y].duration.value;
                        }
                        z++;
                    } else {
                        for (var y = 0; y < resultado[id].routes[0].legs[x].steps.length; y++) {
                            duracaoDistancia += resultado[id].routes[0].legs[x].steps[y].duration.value;
                        }
                    }
                }
            }
        }

        //transforma os segundos de distância em minutos, e adiciona uma porcentagem a mais.
        duracaoDistancia = parseInt((duracaoDistancia / 60) * 1.4);

        //Mostra a informação ao usuário
        if (duracaoDistancia <= 2) {
            $('#mensagem').text('O ônibus está perto do seu local!');
        } else {
            $('#mensagem').text('O ônibus está a menos de ' + duracaoDistancia + ' minutos de distancia!');
        }
        $('#aviso').show();
    }

    //Requisição para buscar os veículos que estão percorrendo alguma linha
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

    //Mostra os veículos no mapa
    function displayVeiculos(clean) {
        icon = {
            url: "/img/marker-veiculo.png",
            scaledSize: new google.maps.Size(45, 60),
        };

        //Se o parâmetro "clean" for true, vai limpar todos veículos atuais
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
                    if ($('#selectLinha').dropdown('get value') != '') {
                        distancia(x);
                    }
                }

            }

            //Se for false, apenas atualiza as localizações dos veículos atuais
        } else {
            for (var x = 0; x < markersVeiculos.length; x++) {
                if (veiculos[x].latitude == null) {
                    markersVeiculos[x].setPosition(new google.maps.LatLng(parseFloat(veiculos[x].latitude), parseFloat(veiculos[x].longitude)));
                    directionsRenderer[x].setMap(null);
                    $(".ui.dropdown").dropdown('change values');

                } else {
                    markersVeiculos[x].setPosition(new google.maps.LatLng(parseFloat(veiculos[x].latitude), parseFloat(veiculos[x].longitude)));

                    if ($('#selectLinha').dropdown('get value') != '') {
                        distancia(x);
                    }
                }

            }
        }
    }

    //Mostra uma infoWindow no marker do veículo
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
        //Timer para atualizar constantemente a localização do veículo
        getVeiculos(true);
        timer = setInterval(getVeiculos, 1500);

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
                allowReselection: true,
                fields: {
                    remoteValues: 'linhas',
                    name: 'nome',
                    value: 'id',
                },
                apiSettings: {
                    url: '/busca/linhas',
                    cache: false
                },
            });

        var onChangeActive = true;
        //Quando mudar o valor do dropdown
        //Faz requisição para atualizar as rotas e buscar os veículos
        $('.ui.dropdown').dropdown('setting', 'onChange', function(value, text, $selectedItem) {
            if (onChangeActive) {
                $('.search').blur();
                $('#aviso').hide();
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
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzCKnFntWPYLZPMiR6Ayf-grtw5SP_0Pc&callback=initMap&libraries=&v=weekly"></script>

@endsection