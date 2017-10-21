<div class="slimscroll-menu" id="remove-scroll">

    <!--- Sidemenu -->
    <div id="sidebar-menu">
        <!-- Left Menu Start -->
        <ul class="metisMenu nav" id="side-menu">
            <li class="menu-title">Navigation</li>
            <li><a href="{{route('home')}}"><i class="fi-map text-muted"></i><span> Geoposicion </span></a></li>
            <li><a href="{{route('listaacesores')}}"><i class="fi-help"></i><span> Asesores </span></a></li>
            @role('admin')
            <li><a href="{{route('listasupervisores')}}"><i class="fi-help"></i><span> Supervisores </span></a></li>
            @endrole
            <li><a href="{{route('consulta')}}"><i class="fa fa-database"></i><span> Consulta </span></a></li>


        </ul>

    </div>
    <!-- Sidebar -->
    <div class="clearfix"></div>

</div>

<!-- Sidebar -left -->