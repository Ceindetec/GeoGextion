<div class="slimscroll-menu" id="remove-scroll">

    <!--- Sidemenu -->
    <div id="sidebar-menu">
        <!-- Left Menu Start -->
        <ul class="metisMenu nav" id="side-menu">
            <li class="menu-title">Navigation</li>

            @role('superadmin')
            <li><a href="{{route('listaEmpresas')}}"><i class="fi-help"></i><span> Crear una Empresa </span></a></li>
            @endrole

            @if(Auth::user()->isRole("sadminempresa") || Auth::user()->isRole("admin") || Auth::user()->isRole("super"))

                <li><a href="{{route('home')}}"><i class="fi-map text-muted"></i><span> Geoposicion </span></a></li>

                @role('sadminempresa')
                <li><a href="{{route('listaAdministradores')}}"><i
                                class="fa fa-user-circle"></i><span> Administradores </span></a></li>
                @endrole
                <li>
                    <a href="javascript: void(0);" aria-expanded="true"><i class="fi-target"></i>
                        <span> Comercial </span>
                        <span class="menu-arrow"></span></a>
                    <ul class="nav-second-level nav" aria-expanded="true">

                        @if(Auth::user()->isRole("sadminempresa") || Auth::user()->isRole("admin"))

                            <li><a href="{{route('listasupervisores')}}"></i><span> Supervisores </span></a>
                            </li>
                        @endif
                        <li><a href="{{route('listaacesores')}}"></i><span> Asesores </span></a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" aria-expanded="true"><i class="fa fa-car"></i>
                        <span> Transporte </span>
                        <span class="menu-arrow"></span></a>
                    <ul class="nav-second-level nav" aria-expanded="true">

                        @if(Auth::user()->isRole("sadminempresa") || Auth::user()->isRole("admin"))

                            <li><a href="{{route('listasupervisorestransporte')}}"></i><span> Supervisores </span></a>
                            </li>
                        @endif
                        <li><a href="{{route('listatrasportador')}}"></i><span> Transportadores </span></a></li>
                        <li><a href="{{route('listarvehiculo')}}"></i><span> Vehiculos </span></a></li>
                    </ul>
                </li>

                <li><a href="{{route('consulta')}}"><i class="fa fa-database"></i><span> Consulta </span></a></li>


            @endif


        </ul>

    </div>
    <!-- Sidebar -->
    <div class="clearfix"></div>

</div>

<!-- Sidebar -left -->