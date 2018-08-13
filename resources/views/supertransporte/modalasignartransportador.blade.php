<div id="modalcrearsupervisor">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Asociar asesores</h4>
    </div>
    <div class="modal-body">
        <h4 class="modal-title">Transportadores sin asignar</h4>
        <table id="noasesores" class="table table-striped table-bordered" width="100%">
            <thead>
            <tr>
                <th>Identificacion</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <h4 class="modal-title">Transportadores asignados</h4>
        <table id="asesores" class="table table-striped table-bordered" width="100%">
            <thead>
            <tr>
                <th>Identificacion</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cerrar</button>
    </div>
</div>

<script>
    var table2, table3;
    $(function () {
        table2 = $('#noasesores').DataTable({
            processing: true,
            serverSide: true,
            "language": {
                "url": "{!!route('datatable_es')!!}"
            },
            ajax: {
                url: "{!!route('gridnoasesorestrans', $supervisor->id)!!}",
                "type": "get"
            },
            columns: [
                {data: 'identificacion', name: 'identificacion'},
                {data: 'nombres', name: 'nombres'},
                {data: 'apellidos', name: 'apellidos'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
        });

        table3 = $('#asesores').DataTable({
            processing: true,
            serverSide: true,
            "language": {
                "url": "{!!route('datatable_es')!!}"
            },
            ajax: {
                url: "{!!route('gridsiasesorestrans', $supervisor->id)!!}",
                "type": "get"
            },
            columns: [
                {data: 'identificacion', name: 'identificacion'},
                {data: 'nombres', name: 'nombres'},
                {data: 'apellidos', name: 'apellidos'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
        });
    });

    function quitar(id) {
        $.ajax({
            url: '{{route('supervisor.quitartransportador')}}',
            data: {id: id,idsuper:'{{$supervisor->id}}'},
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                cargando();
            },
            success: function (result) {
                if (result.estado) {

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
                table2.ajax.reload();
                table3.ajax.reload();
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
    }

    function agregar(id) {
        $.ajax({
            url: '{{route('supervisor.agregatransportador')}}',
            data: {id: id,idsuper:'{{$supervisor->id}}'},
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                cargando();
            },
            success: function (result) {
                if (result.estado) {

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
                table2.ajax.reload();
                table3.ajax.reload();
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
    }
</script>