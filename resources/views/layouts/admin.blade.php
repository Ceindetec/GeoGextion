<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('images/logosmnormal.png')}}">

    <!-- C3 charts css -->
    @yield('antestyles')

    {{Html::style('plugins/sweet-alert2/sweetalert2.min.css')}}


    <!-- App css -->
    {{Html::style('css/bootstrap.min.css')}}
    {{Html::style('css/core.css')}}
    {{Html::style('css/components.css')}}
    {{Html::style('css/icons.css')}}
    {{Html::style('css/menu.css')}}
    {{Html::style('css/responsive.css')}}
    {{Html::style('css/pages.css')}}
    {{Html::script('js/modernizr.min.js')}}

    @yield('estyles')

    <style>

        #contecarga{
            display: none;
            position: absolute;
            top:0;
            width: 98%;
            height: 100%;
            z-index: 5000;
        }

        #fondo{
            position: fixed;
            top:0;
            width: 100%;
            height: 100%;
            display: block;
            background-color: #fff;
            opacity: .5;
        }

        #imacarga{
            position: fixed;
            top:0;
            opacity: 1;
        }

        td.details-control {
            /*background: url('../images/details_open.png') no-repeat center center;*/
            color: green;
            text-align: center;
            cursor: pointer;
            width: 25px;
        }
        tr.shown td.details-control {
            color:red;
            /* background: url('../images/details_close.png') no-repeat center center;*/
        }

    </style>

</head>


<body>

<!-- Begin page -->
<div id="wrapper">

    <!-- Top Bar Start -->
    <div class="topbar">

        <!-- LOGO -->
        <div class="topbar-left">
            <!--<a href="index.html" class="logo"><span>Code<span>Fox</span></span><i class="mdi mdi-layers"></i></a>-->
            <!-- Image logo -->
            <a href="{{route('home')}}" class="logo">
                        <span>
                            <img src="{{url('images/logoblanco.png')}}" alt="" height="50">
                        </span>
                <i>
                    <img src="{{url('images/logosmblanco.png')}}" alt="" height="38">
                </i>
            </a>
        </div>

        <!-- Button mobile view to collapse sidebar menu -->
        <div class="navbar navbar-default" role="navigation">
            <div class="container">

                <!-- Navbar-left -->
                <ul class="nav navbar-nav navbar-left nav-menu-left">
                    <li>
                        <button type="button" class="button-menu-mobile open-left waves-effect">
                            <i class="dripicons-menu"></i>
                        </button>
                    </li>

                </ul>

                <!-- Right(Notification) -->
                <ul class="nav navbar-nav navbar-right">

                    <li class="dropdown user-box">
                        <a href="" class="dropdown-toggle waves-effect user-link" data-toggle="dropdown" aria-expanded="true">
                            <img src="{{url('images/avatar2.png')}}" alt="user-img" class="img-circle user-img">
                        </a>

                        <ul class="dropdown-menu dropdown-menu-right arrow-dropdown-menu arrow-menu-right user-list notify-list">
                            <li><a href="{{route('perfil')}}">Perfil</a></li>
                            <li class="divider"></li>
                            <li><a href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">Logout</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>

                </ul> <!-- end navbar-right -->

            </div><!-- end container -->
        </div><!-- end navbar -->
    </div>
    <!-- Top Bar End -->


    <!-- ========== Left Sidebar Start ========== -->
    <div class="left side-menu">

        @include('layouts.menulateral');


    </div>
    <!-- Left Sidebar End -->



    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="content-page">
        <!-- Start content -->
        <div class="content">
            <div class="container">
                @yield('contenido')

            </div> <!-- container -->

        </div> <!-- content -->

        <!-- Modal Bootstrap-->


        <footer class="footer text-right">
            2017 © GeoGextion. - Ceindetec.org.co
        </footer>

    </div>

    <div id='modalBs' class='modal fade' tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
        </div>
    </div>

    <div id="contecarga">
        <div id="fondo"></div>
        <img src="{{asset('images/62157.gif')}}" id="imacarga">
    </div>


    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->


</div>
<!-- END wrapper -->



<!-- jQuery  -->
{{Html::script('js/jquery.min.js')}}
{{Html::script('js/bootstrap.min.js')}}
{{Html::script('js/metisMenu.min.js')}}
{{Html::script('js/waves.js')}}
{{Html::script('js/jquery.slimscroll.js')}}

<!-- Counter js  -->
{{Html::script('plugins/waypoints/jquery.waypoints.min.js')}}
{{Html::script('plugins/counterup/jquery.counterup.min.js')}}
{{Html::script('plugins/sweet-alert2/sweetalert2.min.js')}}

<script src="{{asset('plugins/parsleyjs/parsley.min.js')}}"></script>
<script src="{{asset('plugins/parsleyjs/idioma/es.js')}}"></script>

<!-- App js -->
{{Html::script('js/jquery.core.js')}}
{{Html::script('js/jquery.app.js')}}
{{Html::script('js/inicio.js')}}

@yield('scripts')

</body>
</html>