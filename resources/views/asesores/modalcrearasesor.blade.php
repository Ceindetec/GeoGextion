<div id="modalcrearasesores">
    {{Form::open(['route'=>['asesor.crearp'], 'class'=>'form-horizontal', 'id'=>'crearasesor'])}}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Agregar asesor</h4>
    </div>
    <div class="modal-body">
            <div class="form-group">
                <label class="control-label">Identificacion</label>
                {{Form::text('identificacion', null ,['class'=>'form-control', "required", "maxlength"=>"10", "data-parsley-type"=>"number"])}}
            </div>

            <div class="form-group">
                <label class="control-label">Nombres</label>
                {{Form::text('nombres', null ,['class'=>'form-control toupercase', "required", "maxlength"=>"40", "data-parsley-pattern"=>"^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+(\s*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]*)*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+$"])}}
            </div>

            <div class="form-group">
                <label class="control-label">Apellidos</label>
                {{Form::text('apellidos', null ,['class'=>'form-control toupercase', "required", "maxlength"=>"40", "data-parsley-pattern"=>"^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+(\s*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]*)*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+$"])}}
            </div>

            <div class="form-group">
                <label class="control-label">E-mail</label>
                {{Form::email('email', null ,['class'=>'form-control', "required"])}}
            </div>

            <div class="form-group">
                <label class="control-label">Telefono</label>
                {{Form::text('telefono', null ,['class'=>'form-control', "required", "data-parsley-type"=>"number", "maxlength"=>"10"])}}
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
        $("#crearasesor").parsley();
        $("#crearasesor").submit(function (e) {
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
                    var message = "Error de ejecución: " + xhr.status + " " + xhr.statusText;
                    swal(
                        'Error!!',
                        message,
                        'error'
                    )
                },
                // código a ejecutar sin importar si la petición falló o no
                complete : function(xhr, status) {
                    fincarga();
                }
            });
        })


    })


</script>