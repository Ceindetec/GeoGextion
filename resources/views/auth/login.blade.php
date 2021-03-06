<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('images/logosmnormal.png')}}">

    <!-- App css -->
    <link href="{{asset('plugins/bootstrap-select/css/bootstrap-select.min.css')}}" rel="stylesheet" />
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('css/core.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('css/components.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('css/icons.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('css/pages.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('css/menu.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('css/responsive.css')}}" rel="stylesheet" type="text/css" />

    <script src="{{asset('js/modernizr.min.js')}}"></script>

</head>


<body class="bg-accpunt-pages">

<!-- HOME -->
<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <div class="wrapper-page">

                    <div class="account-pages">
                        <div class="account-box">
                            <div class="account-logo-box">
                                <h2 class="text-uppercase text-center">
                                    <a href="{{route('login')}}" class="text-success">
                                        <span><img src="{{url('images/logo1.png')}}" alt="" height="70"></span>
                                    </a>
                                </h2>
                                <h5 class="text-uppercase font-bold m-b-5 m-t-50">Ingrese</h5>
                                <p class="m-b-0"> con su cuenta de administrador</p>
                                <p>
                                    @if (session('estado'))
                                        <span class="text-center" style="color: red">
                                            <strong class="text-center">{{ session('estado') }}</strong>
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div class="account-content">
                                <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                                    {{ csrf_field() }}
                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} m-b-20">
                                        <div class="col-xs-12">
                                            <label for="emailaddress">Correo electronico</label>
                                            <input class="form-control" type="email" name="email" id="emailaddress" required value="{{ old('email') }}" autofocus placeholder="micorreo@host.com">
                                            @if ($errors->has('email'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} m-b-20">
                                        <div class="col-xs-12">
                                            {{--<a href="{{ route('password.request') }}" class="text-muted pull-right"><small>¿Olvido su contraseña?</small></a>--}}
                                            <label for="password">Contraseña</label>
                                            <input class="form-control" type="password" name="password" required="" id="password" placeholder="Ingrese su contraseña">
                                            @if ($errors->has('password'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group m-b-20">
                                        <div class="col-xs-12">

                                            <div class="checkbox checkbox-success">
                                                <input id="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                                                <label for="remember">
                                                    Recordarme
                                                </label>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="form-group text-center m-t-10">
                                        <div class="col-xs-12">
                                            <button class="btn btn-md btn-block btn-primary waves-effect waves-light" type="submit">Ingresar</button>
                                        </div>
                                    </div>

                                </form>

                                <div class="row m-t-50">
                                    <div class="col-sm-12 text-center">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end card-box-->


                </div>
                <!-- end wrapper -->

            </div>
        </div>
    </div>
</section>
<!-- END HOME -->



<script>
    var resizefunc = [];
</script>

<!-- jQuery  -->
{{Html::script('js/jquery.min.js')}}
<script src="{{asset('js/bootstrap.min.js')}}"></script>
<script src="{{asset('js/metisMenu.min.js')}}"></script>
<script src="{{asset('js/waves.js')}}"></script>
<script src="{{asset('js/jquery.slimscroll.js')}}"></script>
<script src="{{asset('plugins/bootstrap-select/js/bootstrap-select.min.js')}}" type="text/javascript"></script>

<!-- App js -->
<script src="{{asset('js/jquery.core.js')}}"></script>
<script src="{{asset('js/jquery.app.js')}}"></script>

</body>
</html>
