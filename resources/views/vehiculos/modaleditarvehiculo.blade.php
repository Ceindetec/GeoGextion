<div id="modalcrearvehiculo">
    {{Form::model($vehiculo,['route'=>['vehiculo.editar',$vehiculo->id], 'class'=>'form-horizontal', 'id'=>'crearvehiculo'])}}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Agregar vehiculo</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label class="control-label">Placa</label>
            {{Form::text('placa', null ,['class'=>'form-control toupercase', "required", "maxlength"=>"10"])}}
        </div>

        <div class="form-group">
            <label class="control-label">Marca</label>
            {{Form::select('marca_id', $marcas,null,['class'=>'form-control toupercase', "required", "placeholder"=>"selecciones una marca"])}}
        </div>

        <div class="form-group">
            <label class="control-label">Modelo</label>
            {{Form::text('modelo', null ,['class'=>'form-control toupercase', "required", "maxlength"=>"4", "data-parsley-type"=>"number"])}}
        </div>


        <div class="form-group">
            <label class="control-label">Capacidad</label>
            {{Form::text('capacidad', null ,['class'=>'form-control',  "data-parsley-pattern"=>"^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+(\s*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]*)*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+$", "maxlength"=>"100"])}}
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-success waves-effect waves-light">Guardar</button>
    </div>
    {{Form::close()}}
</div>

<script>
    $(function () {
        $("#crearvehiculo").parsley();
        $("#crearvehiculo").submit(function (e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url : form.attr('action'),
                data : form.serialize(),
                type : 'POST',
                dataType : 'json',
                beforeSend: function () {
                    cargando();
                },
                success : function(result) {
                    if(result.estado){
                        swal(
                            {
                                title: 'Bien!!',
                                text: result.mensaje,
                                type: 'success',
                                confirmButtonColor: '#4fa7f3'
                            }
                        )
                        modalBs.modal('hide');
                    }else if(result.estado == false){
                        swal(
                            'Error!!',
                            result.mensaje,
                            'error'
                        )
                    }else{
                        html='';
                        for(i=0; i<result.length;i++){
                            html+=result[i]+'\n\r';
                        }
                        swal(
                            'Error!!',
                            html,
                            'error'
                        )
                    }
                    table.ajax.reload();
                },
                error : function(xhr, status) {
                    if(xhr.status === 400){
                        var message = "Error de ejecución: " + xhr.status + " " + xhr.responseJSON.mensaje;
                        swal(
                            'Error!!',
                            message,
                            'error'
                        )
                    }else{
                        var message = "Error de ejecución: " + xhr.status + " " + xhr.statusText;
                        swal(
                            'Error!!',
                            message,
                            'error'
                        )
                    }

                },
                // código a ejecutar sin importar si la petición falló o no
                complete : function(xhr, status) {
                    fincarga();
                }
            });
        })


    })


</script>