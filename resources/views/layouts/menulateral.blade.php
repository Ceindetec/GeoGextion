<div class="slimscroll-menu" id="remove-scroll">

    <!--- Sidemenu -->
    <div id="sidebar-menu">
        <!-- Left Menu Start -->
        <ul class="metisMenu nav" id="side-menu">
            <li class="menu-title">Navigation</li>
            @if(Auth::user()->isRole("sadminempresa") || Auth::user()->isRole("admin") || Auth::user()->isRole("super"))

            <li><a href="{{route('home')}}"><i class="fi-map text-muted"></i><span> Geoposicion </span></a></li>

            @role('sadminempresa')
            <li><a href="{{route('listaAdministradores')}}"><i class="fi-map text-muted"></i><span> Administradores </span></a></li>
            @endrole

            @if(Auth::user()->isRole("sadminempresa") || Auth::user()->isRole("admin"))

            <li><a href="{{route('listasupervisores')}}"><i class="fi-help"></i><span> Supervisores </span></a></li>

            @endif
            <li><a href="{{route('listaacesores')}}"><i class="fi-help"></i><span> Asesores </span></a></li>

            <li><a href="{{route('consulta')}}"><i class="fa fa-database"></i><span> Consulta </span></a></li>
            @endif

            @role('superadmin')
                <li><a href="{{route('listaEmpresas')}}"><i class="fi-help"></i><span> Crear una Empresa </span></a></li>
            @endrole

        </ul>

    </div>
    <!-- Sidebar -->
    <div class="clearfix"></div>

</div>

<!-- Sidebar -left -->