@extends('layouts.admin')

@section('antestyles')
    <link href="{{asset('plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}" rel="stylesheet" />
    <link href="{{asset('plugins/clockpicker/css/bootstrap-clockpicker.min.css')}}" rel="stylesheet" />
    <link href="{{asset('plugins/bootstrap-fileupload/bootstrap-fileupload.css')}}" rel="stylesheet" />

@endsection

@section('contenido')

    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">Configuración</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="#">GeoGextion</a>
                    </li>
                    <li class="active">
                        <a href="#">Configuración</a>
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
                <h4 class="header-title m-t-0 m-b-20">
                    Formulario de configuración
                </h4>
                <br>

                <form class="form-horizontal" action="{{route('configuracion')}}" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="email">Nit:</label>
                        <div class="col-sm-10">
                            <input type="text" name="nit" value="{{$empresa->nit}}" readonly="true" class="form-control"
                                   id="email" placeholder="Enter email">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="pwd">Razon social:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{$empresa->razon}}" readonly="true"
                                   name="razon" id="razon" placeholder="Enter password">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="pwd">Abreviatura:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{$empresa->abreviatura}}" readonly="true"
                                   name="abreviatura" id="abreviatura" placeholder="Enter password">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="pwd">Telefono:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{$empresa->telefono}}" readonly="true"
                                   name="telefono" id="telefono" placeholder="Enter password">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="pwd">Dirección:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{$empresa->direccion}}" readonly="true"
                                   name="direccion" id="direccion" placeholder="Enter password">
                        </div>
                    </div>

                    <div class="form-group">

                        <label class="control-label col-sm-2">Color superior:</label>
                        <div class="col-sm-10">
                            <input type="text" name="color" class="colorpicker-default form-control" value="{{$empresa->color}}">
                        </div>

                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2">Logo:</label>
                        <div class="col-md-9">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-new thumbnail">
                                    <img src="{{$empresa->logo != null?url($empresa->logo):url('images/logo1.png')}}" alt="image" />
                                </div>
                                <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                <div>
                                    <button type="button" class="btn btn-default btn-file">
                                        <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Selecionar imagen</span>
                                        <span class="fileupload-exists"><i class="fa fa-undo"></i> Cambiar</span>
                                        <input type="file" name="logo" class="btn-default" />
                                    </button>
                                    <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload"><i class="fa fa-trash"></i> Remove</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-success">aplicar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script src="{{asset('plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
    <script src="{{asset('plugins/bootstrap-fileupload/bootstrap-fileupload.js')}}"></script>

    <script>
        var table;

        $(function () {

            $('.colorpicker-default').colorpicker({
                format: 'hex'
            });

        });


    </script>
@endsection

