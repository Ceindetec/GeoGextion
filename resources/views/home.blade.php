@extends('layouts.admin')

@section('antestyles')
    <link href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/clockpicker/css/bootstrap-clockpicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/leaflet/leaflet.css')}}" rel="stylesheet">
@endsection

@section('styles')
@endsection

@section('contenido')

    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box m-b-0">
                <h4 class="page-title">GeoPosicion</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="#">GeoGextion</a>
                    </li>
                    <li class="active">
                        GeoPosicion
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- end row -->


    <div class="row m-b-1">
        <div class="col-sm-12 ">
            <div class="card-box table-responsive m-b-1">
                <h4 class="header-title m-t-0 m-b-20">Filtros</h4>
                <div class="form-group col-md-4">
                    <label>Asesor:</label>
                    {{Form::select('asesor', $asesores, 'Selecione..', ['class'=>'form-control', 'id'=>'asesor','placeholder'=>'Seleccione un asesor'])}}
                </div>
                <div class="form-group col-md-4">
                    <label>Fecha:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="yyyy-mm-dd" id="datepicker">
                        <span class="input-group-addon bg-custom b-0"><i class="mdi mdi-calendar text-white"></i></span>
                    </div><!-- input-group -->
                </div>
                <div class="form-group col-md-4">
                    <label style="display: block">&nbsp;</label>
                    <button class="btn btn-success" id="ruta">Ruta</button>
                </div>
            </div>
        </div>
    </div>
    <div class="gmaps-full">
        <div id="otromap" class="gmaps-full1"></div>
    </div>

@endsection

@section('scripts')

    <!-- google maps api -->
    {{Html::script('http://maps.google.com/maps/api/js?key=AIzaSyAk3sbqJFg6zDsnVlJ4p1VR8b6PQwcQobU')}}

    <!-- main file -->
    {{Html::script('plugins/gmaps/gmaps.min.js')}}

    <script src="{{asset('plugins/moment/moment.js')}}"></script>
    <script src="{{asset('plugins/timepicker/bootstrap-timepicker.js')}}"></script>
    <script src="{{asset('plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
    <script src="{{asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('plugins/clockpicker/js/bootstrap-clockpicker.min.js')}}"></script>
    <script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{asset('plugins/leaflet/leaflet.js')}}"></script>

    <script>
        var map;

        var ruta = false;
        $(function () {

            initmapa();

            $('#datepicker').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: "yyyy-mm-dd",
            });

            $('#asesor').change(function () {
                ruta = false;
                if ($(this).val() != '') {
                    marketIndividual($(this).val())
                } else {
                    ultimaposcicion();
                }
            });

            $('#ruta').click(function () {
                if ($('#asesor').val() != '' && $('#datepicker').val() != '') {
                    ruta = true;
                    $.get('{{route('rutaasesor')}}', {
                        identificacion: $("#asesor").val(),
                        fecha: $('#datepicker').val()
                    }, function (data) {
                        map.removeMarkers();
                        map.removePolylines();
                        map.cleanRoute();
                        var puntos = [];
                        var punto = [];
                        var punto2 = [];
                        for (i = 0; i < data.length; i++) {
                            punto = {
                                location: {lat: parseFloat(data[i].latitud), lng: parseFloat(data[i].longitud)},
                                stopover: true
                            }
                            puntos.push(punto);
                        }

                        map.setCenter(
                            puntos[0].location.lat,
                            puntos[0].location.lng
                        );

                        map.setZoom(16);

                        map.drawRoute({
                            origin: [puntos[0].location.lat, puntos[0].location.lng],
                            destination: [puntos[puntos.length - 1].location.lat, puntos[puntos.length - 1].location.lng],
                            waypoints: puntos,
                            travelMode: 'walking',
                            strokeColor: '#131540',
                            strokeOpacity: 0.6,
                            strokeWeight: 6
                        });


                    });
                }
            });
            setTimeout(ultimaposcicion, 300);
        });

        function ultimaposcicion() {
            $.get('{{route('geoposicionfinal')}}', {}, function (data) {
                contrucionutimomarke(data)
            });
        }


        function marketIndividual(id) {
            $.get('{{route('ubicarasesor')}}', {identificacion: id}, function (data) {
                map.eachLayer(function (layer) {
                    if(layer.options.attribution == null){
                        map.removeLayer(layer);
                    }
                });
                map.setZoom(16);
                var marker = L.marker([data[0].ultimaposiciones.latitud, data[0].ultimaposiciones.longitud]).addTo(map);
                marker.bindPopup(`${data[0].nombres} ${data[0].apellidos}`);
                map.panTo((new L.LatLng(data[0].ultimaposiciones.latitud, data[0].ultimaposiciones.longitud)));

            });
        }

        function contrucionutimomarke(data) {
            map.eachLayer(function (layer) {
                if(layer.options.attribution == null){
                    map.removeLayer(layer);
                }
            });
            map.setZoom(7);
            for (i = 0; i < data.length; i++) {
                var marker = L.marker([data[i].ultimaposiciones.latitud, data[i].ultimaposiciones.longitud]).addTo(map);
                marker.bindPopup(`${data[i].nombres} ${data[i].apellidos}`);
            }
        }


        function initmapa() {
            map = L.map('otromap');

            map.setView([4.612853, -74.0728357], 7);

            L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
                maxZoom: 19
            }).addTo(map);
        }

        /*var source = new EventSource("{{--{{route('updatemarketgeneral')}}--}}");
        source.onmessage = function (event) {
            if (!ruta) {
                if ($('#asesor').val() == '') {
                    contrucionutimomarke(JSON.parse(event.data))
                } else {
                    marketIndividual($('#asesor').val());
                }
            }
        };*/

    </script>

@endsection

