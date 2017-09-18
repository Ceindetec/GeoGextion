@extends('layouts.admin')

@section('antestyles')
    <link href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/clockpicker/css/bootstrap-clockpicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet">
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
                    {{Form::select('asesor', $asesores, 'Selecione..', ['class'=>'form-control', 'id'=>'asesor'])}}
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
        <div id="gmaps-markers" class="gmaps-full1"></div>
    </div>

@endsection

@section('scripts')

    <!-- google maps api -->
    {{Html::script('http://maps.google.com/maps/api/js?key=AIzaSyDAHZxs8NRsHDfR2Zse_9P0mdZFs3rvASQ')}}

    <!-- main file -->
    {{Html::script('plugins/gmaps/gmaps.min.js')}}

    <script src="{{asset('plugins/moment/moment.js')}}"></script>
    <script src="{{asset('plugins/timepicker/bootstrap-timepicker.js')}}"></script>
    <script src="{{asset('plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
    <script src="{{asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('plugins/clockpicker/js/bootstrap-clockpicker.min.js')}}"></script>
    <script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}"></script>

    <script>
        var ruta = false;
        $(function () {
           initMap();

            $('#datepicker').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: "yyyy-mm-dd",
            });

            $('#asesor').change(function () {
                ruta = false;
                if($(this).val()!=''){
                    marketIndividual($(this).val())
                }else{
                    ultimaposcicion();
                }
            });

            $('#ruta').click(function () {
                if($('#asesor').val() != '' && $('#datepicker').val() != ''){
                    ruta = true;
                    $.get('{{route('rutaasesor')}}',{identificacion:$("#asesor").val(),fecha:$('#datepicker').val()},function (data) {
                        map.removeMarkers();
                        map.removePolylines();
                        var puntos= [];
                        var punto = [];
                        for(i=0;i<data.length;i++){
                            punto = [data[i].latitud,data[i].longitud];
                            puntos.push(punto);
                        }
                        map.drawPolyline({
                            path: puntos,
                            strokeColor: '#131540',
                            strokeOpacity: 0.6,
                            strokeWeight: 6
                        });
                        map.setZoom(16);
                    });
                }
            });
            setTimeout(ultimaposcicion,300);
        });

        function ultimaposcicion() {
            $.get('{{route('geoposicionfinal')}}',{},function(data){
                contrucionutimomarke(data)
            });
        }

        function initMap() {
            setTimeout(function () {
                map = new GMaps({
                    div: '#gmaps-markers',
                    lat: 4.6097100,
                    lng: -74.0817500,
                    zoom: 9
                });
            },200);
        };

        function marketIndividual(id) {
            $.get('{{route('ubicarasesor')}}',{identificacion:id},function (data) {
                map.removeMarkers();
                map.removePolylines();
                map.addMarker({
                    lat: data.get_position[data.get_position.length -1].latitud,
                    lng: data.get_position[data.get_position.length -1].longitud,
                    title: 'Marker with InfoWindow',
                    infoWindow: {
                        content: '<p>'+data.nombres+' '+data.apellidos+'</p>'
                    }
                });

                map.setCenter(
                    data.get_position[data.get_position.length -1].latitud,
                    data.get_position[data.get_position.length -1].longitud
                );

                map.setZoom(16);
            });
        }

        function contrucionutimomarke(data) {
            map.removePolylines();
            map.removeMarkers();
            for(i=0;i<data.length;i++){
                map.addMarker({
                    lat: data[i].get_position[data[i].get_position.length -1].latitud,
                    lng: data[i].get_position[data[i].get_position.length -1].longitud,
                    title: 'Marker with InfoWindow',
                    infoWindow: {
                        content: '<p>'+data[i].nombres+' '+data[i].apellidos+'</p>'
                    }
                });
            }
        }

        var source = new EventSource("{{route('updatemarketgeneral')}}");
        source.onmessage = function(event) {
            if(!ruta){
                if($('#asesor').val() == ''){
                    contrucionutimomarke(JSON.parse(event.data))
                }else{
                    marketIndividual($('#asesor').val());
                }
            }
        };

    </script>

@endsection

