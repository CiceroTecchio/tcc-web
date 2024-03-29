@extends('layouts.app')
@section('title', 'Início')
@section('content')
<style>
    #googleMap {
        padding-left: 3%;
    }

    #buttons {
        position: absolute;
        z-index: 1;
        top: 120px;
        pointer-events: none;
    }

    #aviso {
        position: absolute;
        width: 80%;
        margin-left: 7%;
        pointer-events: none;
    }

    #alerta {
        position: absolute;
        z-index: 999;
        width: 100%;
        top: 120px;
        pointer-events: none;
    }
    .pointer{
        pointer-events: all;
    }

    @media screen and (max-width: 767px) {
        #form{
            margin-left: -30px;
        }
        #style-selector-control{
            margin-top: -5px !important;
        }
    }
</style>

<div id="alerta" class="ui two column centered grid msgAlerta">
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

<form id="form" action="{{ route('pontos.store') }}" method="post">
    @csrf
    <input id="markers" type="hidden" name="markers">
    <div id="buttons" class="ui two column centered grid w-100">
        <button type="button" onclick="submitForm()" data-tooltip="Salvar Pontos" class="ui large green labeled icon button pointer">
            <i class="save icon"></i> Salvar
        </button>
    </div>
</form>

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
    <div id="googleMap" style="padding-left: 3%;"></div>
</div>

<div id="buttons" class="ui left aligned grid ml-3">
    <button data-position="right center" data-tooltip="Mostrar Dicas" class="ui teal icon button pointer" onclick="$('.ui.message').addClass('visible')">
        <i class="question icon"></i>
    </button>
</div>


<div id="aviso" class="ui center aligned grid aviso">

    <div class="ui teal message pointer">
        <i class="close icon"></i>
        <div class="header">
            Como usar?
        </div>
        <ul class="list">
            <li>Clique esquerdo para colocar ponto.</li>
            <li>Clique direito para remover ponto.</li>
            <li>Arraste para alterar o local.</li>
        </ul>
    </div>
</div>

</div>
<script>
    var map, listener, icon;
    var markers = [];
    var marcadores = <?php echo $pontos ?>;
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

    function submitForm() {
        $('#markers').val(null);
        var input = [];
        for (var x = 0; x < markers.length; x++) {
            if (markers[x] != null) {
                input.push(markers[x].position);
            }
        }
        $('#markers').val(JSON.stringify(input));
        $('#form').submit();
    }


    function initMap() {
        map = new google.maps.Map(document.getElementById("googleMap"), {
            zoom: 14,
            draggableCursor: "crosshair",
            center: {
                lat: -25.745,
                lng: -53.060
            },
            styles: styles["hide"]
        });

        // Add controls to the map, allowing users to hide/show features.
        const styleControl = document.getElementById("style-selector-control");
        map.controls[google.maps.ControlPosition.RIGHT_TOP].push(styleControl);
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


        icon = {
            url: "/img/bus-stop.png", // url
            scaledSize: new google.maps.Size(80, 80), // scaled size
        };

        for (var x = 0; x < marcadores.length; x++) {
            markers.push(new google.maps.Marker({
                position: {
                    lat: parseFloat(marcadores[x].latitude),
                    lng: parseFloat(marcadores[x].longitude)
                },

                icon: icon,
                map: map,
                draggable: true,
                title: 'Ponto de Parada - ' + (markers.length).toString()
            }));
            deleteMarker(x);
        }

        map.addListener('click', function(mapsMouseEvent) {
            markers.push(new google.maps.Marker({
                position: mapsMouseEvent.latLng,
                icon: icon,
                map: map,
                draggable: true,
                title: 'Ponto de Parada - ' + (markers.length).toString()
            }));
            var index = markers.length - 1;

            markers[index].addListener("rightclick", function(point) {
                markers[index].setMap(null);
                markers[index] = null;
            });

            google.maps.event.removeListener(listener);
        });

    }

    function deleteMarker(id) {
        markers[id].addListener("rightclick", function(point) {
            markers[id].setMap(null);
            markers[id] = null;
        });
    }
    
    $('#style-selector-control').show();
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzCKnFntWPYLZPMiR6Ayf-grtw5SP_0Pc&callback=initMap&libraries=&v=weekly"></script>

@endsection