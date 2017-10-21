<div id="modalcrearsupervisor">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Asociar asesores</h4>
    </div>
    <div class="modal-body">
        <h4 class="modal-title">Asesores sin asignar</h4>
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
        <h4 class="modal-title">Asesores asiganados</h4>
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
        <button type="submit" class="btn btn-success waves-effect waves-light">Guardar</button>
    </div>
</div>

<script>
    $(function () {
        table = $('#noasesores').DataTable({
            processing: true,
            serverSide: true,
            "language": {
                "url": "{!!route('datatable_es')!!}"
            },
            ajax: {
                url: "{!!route('gridnoasesores', $supervisor->id)!!}",
                "type": "get"
            },
            columns: [
                {data: 'identificacion', name: 'identificacion'},
                {data: 'nombres', name: 'nombres'},
                {data: 'apellidos', name: 'apellidos'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
        });
    })
</script>