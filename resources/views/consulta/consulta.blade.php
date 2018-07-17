@extends('layouts.admin')

@section('antestyles')
    <link href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/clockpicker/css/bootstrap-clockpicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet">


    <link href="{{asset('plugins/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/buttons.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/fixedHeader.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/responsive.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/scroller.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/dataTables.colVis.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/dataTables.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/fixedColumns.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
@endsection

@section('contenido')

    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">Consulta</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="#">GeoGextion</a>
                    </li>
                    <li class="active">
                        Consulta
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <div class="row m-b-1">
        <div class="col-sm-12 col-md-6">
            <form method="post" class="form-horizontal" id="consultar">
                <div class="card-box table-responsive m-b-1">
                    <h4 class="header-title m-t-0 m-b-20">Filtros</h4>
                    <div class="form-group">
                        <label>Asesor:</label>
                        {{Form::select('asesor', $asesores, 'Selecione..', ['class'=>'form-control', 'id'=>'asesor','placeholder'=>'seleccione asesor'])}}
                    </div>
                    <div class="form-group">
                        <label>Fecha:</label>
                        <div class="input-group">
                            <input type="text" name="fecha" class="form-control" id="datepicker">
                            <span class="input-group-addon bg-custom b-0"><i
                                        class="mdi mdi-calendar text-white"></i></span>
                        </div><!-- input-group -->
                    </div>
                    <div class="form-group">
                        <label>Hora inicial</label>
                        <div class="input-group">
                            <input type="text" name="hora1" id="hora1" class="form-control timepicker">
                            <span class="input-group-addon bg-custom"><i class="mdi mdi-clock text-white"></i></span>
                        </div><!-- input-group -->
                    </div>
                    <div class="form-group">
                        <label>Hora final</label>
                        <div class="input-group">
                            <input type="text" name="hora2" id="hora2" class="form-control timepicker">
                            <span class="input-group-addon bg-custom"><i class="mdi mdi-clock text-white"></i></span>
                        </div><!-- input-group -->
                    </div>
                    <div class="form-group">
                        <label style="display: block">&nbsp;</label>
                        <button class="btn btn-success" id="ruta">Consultar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row" id="resultado">

    </div>

@endsection

@section('scripts')
    <!-- google maps api -->
    {{Html::script('http://maps.google.com/maps/api/js?key=AIzaSyB58pcny_fD7Anmb1CUtdXT1cGPzPyhw3I')}}

    <!-- main file -->
    {{Html::script('plugins/gmaps/gmaps.min.js')}}

    <script src="{{asset('plugins/moment/moment.js')}}"></script>
    <script src="{{asset('plugins/timepicker/bootstrap-timepicker.js')}}"></script>
    <script src="{{asset('plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
    <script src="{{asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{asset('plugins/clockpicker/js/bootstrap-clockpicker.min.js')}}"></script>

    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.js')}}"></script>

    <script src="{{asset('plugins/datatables/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/buttons.bootstrap.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/jszip.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/pdfmake.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/vfs_fonts.js')}}"></script>
    <script src="{{asset('plugins/datatables/buttons.html5.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/buttons.print.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.fixedHeader.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.keyTable.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/responsive.bootstrap.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.scroller.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.colVis.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.fixedColumns.min.js')}}"></script>

    <script>
        $(function () {
            var end = moment();
            var hora1 = moment().subtract(30, 'minute');

            console.log(hora1);

            $('#hora1').timepicker({
                showMeridian: false,
                defaultTime: hora1.format("h:mm:ss")
            });

            $('#hora2').timepicker({
                showMeridian: false,
                defaultTime: end.format("h:mm:ss")
            });

            // $('#datepicker').datepicker({
            //     autoclose: true,
            //     todayHighlight: true,
            //     format: "yyyy-mm-dd",
            //     endDate: moment().format('YYYY-MM-DD')
            // });




            $('#datepicker').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                },
                maxDate: end
            });


            $('#consultar').submit(function (e) {
                e.preventDefault();

                if ($('#datepicker').val() != '') {
                    var hora1 = $('#hora1').val();
                    var corrige = hora1.split(":");
                    if(corrige[0]<=9){
                        hora1 ="0"+hora1;
                    }
                    var hora2 = $('#hora2').val();
                    var corrige = hora2.split(":");
                    if(corrige[0]<=9){
                        hora2 ="0"+hora2;
                    }
                    console.log(hora1, hora2);
                    if (hora1 < hora2) {
                        var form = $(this);
                        $("#resultado").load("{{route('resultadoconsulta')}}",form.serialize()+"&hora1="+hora1+"&hora2="+hora2);
                    }
                }
            })
        })
    </script>
@endsection

