@extends('layouts.admin')

@section('antestyles')
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
                <h4 class="page-title">Asesores</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="#">GeoGextion</a>
                    </li>
                    <li class="active">
                        <a href="#">Asesores</a>
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- end row -->


    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <h4 class="header-title m-t-0 m-b-20">Lista de asesores</h4>


                <table id="table-asesores" class="table table-striped table-bordered" width="100%">
                    <thead>
                    <tr>
                        <th>Identificacion</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Telfono</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>
        </div>
    </div>


@endsection

@section('scripts')
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
            table = $('#table-asesores').DataTable({
                processing: true,
                serverSide: true,
                "language": {
                    "url": "{!!route('datatable_es')!!}"
                },
                ajax: {
                    url: "{!!route('gridasesores')!!}",
                    "type": "get"
                },
                columns: [
                    {data: 'identificacion', name: 'identificacion'},
                    {data: 'nombres', name: 'nombres'},
                    {data: 'apellidos', name: 'apellidos'},
                    {data: 'telefono', name: 'telefono'},
                    {data: 'email', name: 'email'},
                    {
                        data: 'estado',
                        name: 'estado',
                        render: function (data) {
                            if (data == 'A')
                                return 'Activo';
                            else
                                return 'Inactivo';

                        }
                    },
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                dom: "Bfrtip",
                buttons: [
                    {
                        text: 'Agregar asesor',
                        action: function ( e, dt, node, config ) {
                            modalBsContent.load('{{route('asesor.crear')}}', function (response, status, xhr) {
                                switch (status) {
                                    case "success":
                                        modalBs.modal({ backdrop: 'static', keyboard: false }, 'show');

                                        /*if (dataModalValue == "modal-lg") {
                                            modalBs.find(".modal-dialog").addClass("modal-lg");
                                        } else {
                                            modalBs.find(".modal-dialog").removeClass("modal-lg");
                                        }*/

                                        break;

                                    case "error":
                                        var message = "Error de ejecución: " + xhr.status + " " + xhr.statusText;
                                        if (xhr.status == 403) {$.msgbox(response, {type: 'error'});}
                                        else {
                                            swal(
                                                'Error!!',
                                                message,
                                                'error'
                                            )
                                        }
                                        break;
                                }

                            });
                        },
                        className:'btn-sm btn-success'
                    }
                ],
                order: [[1, 'asc']]
            });

            $('#datatable').DataTable();
        });
    </script>
@endsection

