<div class="col-md-12">
    <div class="card-box">
        <h4 class="header-title m-t-0">Resultado</h4>

        <div class="card-box table-responsive">
            <div class="col-md-12 m-b-5 p-0 text-right">
                <strong>Exportar:</strong>
                <a href="{{route('exportarpdf',['asesor'=>$request->asesor,'fecha'=>$request->fecha,'hora1'=>$request->hora1,'hora2'=>$request->hora2])}}" class="btn btn-success " data-placement="bottom" data-toggle="tooltip" title="PDF">
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                </a>
                <a href="{{route('exportarexcel',['asesor'=>$request->asesor,'fecha'=>$request->fecha,'hora1'=>$request->hora1,'hora2'=>$request->hora2])}}" class="btn btn-success " data-placement="bottom" data-toggle="tooltip" title="EXCEL">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                </a>
            </div>
            <table id="table-posiciones" class="table table-striped table-bordered" width="100%">
                <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Coordenadas</th>
                    <th>Direccion</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($geposiciones as $geposicione)
                    <tr>
                        <td>{{$geposicione->fecha}}</td>
                        <td>{{$geposicione->latitud}},{{$geposicione->longitud}}</td>
                        <td>
                            {{$geposicione->direccion}}
                        </td>
                        <td>
                            <a href="{{route('modalpunto',["id"=>$geposicione->id])}}" data-modal class="btn btn-custom waves-effect waves-light" data-toggle="modal" data-target="#modalrol">Ver mapa</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>

    </div> <!-- end card-box -->
</div>

<script>
    $('[data-toggle="tooltip"]').tooltip();
    /* var geocoder;
     var elemento;
     var direcciones = new Array();
     var contador = 0;
     var time = 1000
     $(function () {
         $(".cambio").each(function (index) {
            direcciones[index] = $(this).text();
         });
         setTimeout(prueba,time)

     });

     function prueba() {
         console.log(direcciones.length, contador);
         if(contador<direcciones.length){
             getDireccion(direcciones[contador])
             setTimeout(prueba,time)
         }
     }

     function getDireccion(coordenadas) {
         geocoder = new google.maps.Geocoder();
         var corrdenadas = coordenadas.split(",");
         var latlng = new google.maps.LatLng(corrdenadas[0], corrdenadas[1]);
         geocoder.geocode({'latLng': latlng}, function (results, status) {
             if (status == google.maps.GeocoderStatus.OK) {
                 if (results[0]) {
                     //console.log(results[0]);
                     $(".elemento"+(contador+1)).text(results[0].formatted_address);
                     contador++;
                     time=100;
                 }
             } else if(status == google.maps.GeocoderStatus.OVER_QUERY_LIMIT){
                 console.log('Geocoder failed due to: ' + status);
                 time=1500;
             }
         })
     }*/
</script>