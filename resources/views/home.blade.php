@extends('layouts.app')
@section('title', 'Início')
@section('content')
<style>
    @media screen and (max-width: 700px) {
        div.example {
            padding-right: 3%;
        }
    }
</style>


<div>
    <div id="googleMap" style="padding-left: 3%;"></div>
</div>


<div class="ui left aligned grid ml-3" style="position:absolute; z-index: 1; top: 120px">
    <button data-position="right center" data-tooltip="Limpar o mapa" class="ui yellow icon button" onclick="limparRotas()">
        <i class="eraser icon"></i>
    </button>
    <button data-position="right center" data-tooltip="Remover último ponto" class="ui green icon button" onclick="removerUltimo()">
        <i class="undo icon"></i>
    </button>

</div>


<div class="ui center aligned grid" style="position:absolute; z-index: 1; bottom: 50px; width: 80%; margin-left: 10%;">

    <div class="ui center aligned teal icon message w-auto">
        <i class="map marker alternate icon"></i>
        <div class="content">
            <div id="mensagem" class="header">
                Selecione o início da rota
            </div>
        </div>
    </div>

</div>


<script>
    document.getElementById('googleMap').style.height = (window.innerHeight - 45) + 'px';

    var markers = [];
    var waypts = [];
    var ultimoWaypts = [];
    var colors = ["green", "red", "blue", "purple", "yellow"];
    var listener;
    var directionsRenderer;
    var directionsService;
    var map;
    var rota;

    function limparRotas() {
        for (let i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
        }
        markers = [];
        if (directionsRenderer != null) {
            directionsRenderer.setMap(null);
        }
        map.setOptions({
            draggableCursor: "crosshair"
        });
        $("#mensagem").text("Selecione o início da rota");

    }

    function initMap() {
        // map.setOptions({draggableCursor: myNewDraggableCursor});

        map = new google.maps.Map(document.getElementById("googleMap"), {
            zoom: 15,
            draggableCursor: 'crosshair',
            center: {
                lat: -25.745,
                lng: -53.060
            }
        });
        // const centerControlDiv = document.getElementById("menu");
        // centerControlDiv.index = 1;
        // map.controls[google.maps.ControlPosition.TOP_CENTER].push(centerControlDiv);

        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            draggable: true,
            map,
            // panel: document.getElementById("right-panel")
        });

        // displayRoute(
        //     "-25.731228, -53.075230",
        //     "-25.731228, -53.075230",
        //     directionsService,
        //     directionsRenderer
        // );

        colocarDirectionlistener();
        colocarListener();
    }

    function colocarDirectionlistener() {
        directionsRenderer.addListener('directions_changed', function() {
            var result = directionsRenderer.getDirections();
            rota = result;
            console.log(rota);
            ultimoWaypts.push(waypts);
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

        listener = map.addListener('rightclick', function(mapsMouseEvent) {
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

                markers.push(new google.maps.Marker({
                    icon: "http://maps.google.com/mapfiles/ms/icons/" + colors[markers.length] + "-dot.png",
                    position: mapsMouseEvent.latLng,
                    map: map,
                    stopover: false,
                    title: (markers.length).toString()
                }));

                $("#mensagem").text("Ajuste o percurso realizado nessa parte do trajeto");
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
            // panel: document.getElementById("right-panel")
        });
        colocarDirectionlistener();
        directionsService.route({
                origin: markers[0].position,
                destination: markers[markers.length - 1].position,
                waypoints: waypts,
                travelMode: google.maps.TravelMode.DRIVING,
                // avoidTolls: true
            },
            (result, status) => {
                if (status === "OK") {
                    directionsRenderer.setDirections(result);
                    directionsRenderer.setMap(map);
                    for (var x = 0; x < markers.length; x++) {
                        markers[x].setMap(null);
                    }

                } else {
                    alert("Could not display directions due to: " + status);
                }
            }
        );
    }


    function removerUltimo() {

        if (waypts.length > 0) {
            waypts = ultimoWaypts.pop();

            displayRoute();

        } else {
            if (markers.length - 1 == 0) {
                markers[0].setMap(null);
                $("#mensagem").text("Selecione o início da rota");

            } else if (markers.length - 1 == 1) {
                directionsRenderer.setMap(null);
                $("#mensagem").text("Selecione o ponto mais distante da rota");
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
            console.log(response, status);
            if (status === 'OK') {
                directionsRenderer.setDirections(response);
            }
        });
    }
</script>
<!-- https://roads.googleapis.com/v1/snapToRoads?path=&key=AIzaSyAzCKnFntWPYLZPMiR6Ayf-grtw5SP_0Pc -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzCKnFntWPYLZPMiR6Ayf-grtw5SP_0Pc&callback=initMap&libraries=&v=weekly"></script>

@endsection