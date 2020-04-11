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
<div id="googleMap" style="padding-left: 3%;display: none;"></div>

<script>
    document.getElementById('googleMap').style.height = (screen.height - 189) + 'px';
    document.getElementById('googleMap').style.display = 'block';
    var marker;
    var map;
    var myLatLng = {
        lat: -25.363,
        lng: 131.044
    };

    function myMap() {
        var mapProp = {
            center: new google.maps.LatLng(51.508742, -0.120850),
            zoom: 5,
        };
        map = new google.maps.Map(document.getElementById("googleMap"), mapProp);

        var icon = {
            url: '/img/teste.png', // url
            scaledSize: new google.maps.Size(35, 50), // scaled size
        };


        marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            icon: icon,
            title: 'Hello World!'
        });
    }
    window.setInterval(function() {
        console.log(myLatLng.lat + 1)
        moveBus(map, marker);
    }, 1000);

    function moveBus(map, marker) {
        myLatLng.lat += 0.0001;
        myLatLng.lng += 0.0001;
        map.panTo(myLatLng);
        marker.setPosition(myLatLng);

    };
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDxRK-cMcDgSvVfIzyEbdTrBE4jPS_dgZI&callback=myMap"></script>

@endsection