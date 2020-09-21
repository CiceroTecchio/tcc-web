@extends('layouts.app')
@section('title', 'Início')
@section('content')

<div>
    <div id="googleMap" style="padding-left: 3%;"></div>
</div>


<script>
    var directionsRenderer;
    var directionsService;
    var map;
    var pontos = <?php echo $pontos ?>;
    var markersPontos = [];
    var rota;

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

        directionsRenderer = new google.maps.DirectionsRenderer({
            suppressMarkers: true,
            map
        });
        directionsService = new google.maps.DirectionsService();
        directionsRenderer.setMap(map);
        displayPontos();

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
</script>
<!-- https://roads.googleapis.com/v1/snapToRoads?path=&key=AIzaSyAzCKnFntWPYLZPMiR6Ayf-grtw5SP_0Pc -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzCKnFntWPYLZPMiR6Ayf-grtw5SP_0Pc&callback=initMap&libraries=&v=weekly"></script>

@endsection