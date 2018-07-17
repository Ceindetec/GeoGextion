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
                <h4 class="header-title m-t-0 m-b-20">Lista de asesores
                    @if(Auth::user()->isRole("sadminempresa") || Auth::user()->isRole("admin"))
                        <span class="pull-right">
                        <a href="{{route('exportasesor')}}" class="btn btn-success">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        </a>
                    </span>
                    @endif
                </h4>
                <br>

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
        var table;

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
                @if(Auth::user()->isRole("sadminempresa") || Auth::user()->isRole("admin"))
                dom: "Bfrtip",
                buttons: [
                    {
                        text: 'Agregar asesor',
                        action: function (e, dt, node, config) {
                            modalBsContent.load('{{route('asesor.crear')}}', function (response, status, xhr) {
                                switch (status) {
                                    case "success":
                                        modalBs.modal({backdrop: 'static', keyboard: false}, 'show');

                                        break;

                                    case "error":
                                        var message = "Error de ejecución: " + xhr.status + " " + xhr.statusText;
                                        if (xhr.status == 403) {
                                            $.msgbox(response, {type: 'error'});
                                        }
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
                        className: 'btn-sm btn-success'
                    }
                ],
                @endif
                order: [[1, 'asc']]
            });


        });

        function cambiarestado(id) {
            swal({
                title: '¿Estas seguro?',
                text: "¡¡Desea cambiar estado del asesor!!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Si',
                cancelButtonText: 'No',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger m-l-10',
                buttonsStyling: false
            }).then(function () {
                $.ajax({
                    url: '{{route('asesor.cambiarestado')}}',
                    data: {id: id},
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function () {
                        cargando();
                    },
                    success: function (result) {
                        if (result.estado) {
                            swal(
                                {
                                    title: 'Bien!!',
                                    text: result.mensaje,
                                    type: 'success',
                                    confirmButtonColor: '#4fa7f3'
                                }
                            )
                            modalBs.modal('hide');
                        } else if (result.estado == false) {
                            swal(
                                'Error!!',
                                result.mensaje,
                                'error'
                            )
                        } else {
                            html = '';
                            for (i = 0; i < result.length; i++) {
                                html += result[i] + '\n\r';
                            }
                            swal(
                                'Error!!',
                                html,
                                'error'
                            )
                        }
                        table.ajax.reload();
                    },
                    error: function (xhr, status) {
                        var message = "Error de ejecución: " + xhr.status + " " + xhr.statusText;
                        swal(
                            'Error!!',
                            message,
                            'error'
                        )
                    },
                    // código a ejecutar sin importar si la petición falló o no
                    complete: function (xhr, status) {
                        fincarga();
                    }
                });
            });


        }


    </script>
@endsection

