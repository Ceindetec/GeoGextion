@extends('layouts.admin')

@section('styles')
@endsection

@section('contenido')

    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">Perfil de Usuario</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="#">GeoGextion</a>
                    </li>
                    <li class="active">
                        Perfil de Usuario
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-sm-offset-2 col-sm-8">
            <div class="card-box">
                <h4 class="header-title m-t-0 m-b-20">Perfil usuario</h4>

                @if (session('status'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <strong>{{ session('status') }}</strong>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <strong>{{ session('error') }}</strong>
                    </div>
                @endif


                {{Form::model(Auth::User(),['route'=>['perfil'], 'class'=>'form-horizontal', 'id'=>'actualizarusuario'])}}
                <div class="form-group">
                    <label class="control-label">Usuario</label>
                    {{Form::text('name', null ,['class'=>'form-control', "required", "maxlength"=>"10", "data-parsley-pattern"=>"^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+(\s*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]*)*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+$"])}}
                </div>

                <div class="form-group">
                    <label class="control-label">E-mail</label>
                    {{Form::email('email', null ,['class'=>'form-control', "required"])}}
                </div>

                <div class="form-group">
                    <label class="control-label">Contraseña</label>
                    {{Form::password('password',['class'=>'form-control', "maxlength"=>"12",'id'=>'password'])}}
                </div>
                <div class="form-group">
                    <label class="control-label">Confirmar contraseña</label>
                    {{Form::password('password_confirmation',['class'=>'form-control',  "maxlength"=>"12", "data-parsley-equalto"=>"#password"])}}
                </div>
                <button type="submit" class="btn btn-success waves-effect waves-light">Guardar</button>
                {{Form::close()}}

            </div>
        </div>
    </div>

@endsection

@section('scripts')

    <script>
        $(function () {
            $('#actualizarusuario').parsley();
        })
    </script>
@endsection

