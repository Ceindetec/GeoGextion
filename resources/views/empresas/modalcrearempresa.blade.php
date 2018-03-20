<div id="modalcrearempresa">
    {{Form::open(['route'=>['empresa.crear'], 'class'=>'form-horizontal', 'id'=>'crearempresa'])}}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Crear una Empresa</h4>
    </div>
    <div class="modal-body">
        <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="nit" class="col-sm-4 control-label">NIT</label>
                <div class="col-sm-8">
                    {{Form::text('nit', null ,['id'=>'nit','class'=>'form-control', "required", "maxlength"=>"15", "data-parsley-type"=>"number"])}}
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="nit" class="col-sm-4 control-label">Abreviatura</label>
                <div class="col-sm-8">
                    {{Form::text('abreviatura', null ,['id'=>'abreviatura','class'=>'form-control toupercase', "required", "maxlength"=>"40", "data-parsley-pattern"=>"^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+(\s*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]*)*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+$"])}}
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="form-group">
                <label for="razon" class="col-sm-2 control-label">Razon Social</label>
                <div class="col-sm-10">
                {{Form::text('razon', null ,['id'=>'razon','class'=>'form-control toupercase', "required", "maxlength"=>"100", "data-parsley-pattern"=>"^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+(\s*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]*)*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+$"])}}
                </div>
            </div>
        </div>



        <div class="col-sm-6">
            <div class="form-group">
                <label for="telefono" class="col-sm-4 control-label">Telefono</label>
                <div class="col-sm-8">
                {{Form::text('telefono', null ,['id'=>'telefono','class'=>'form-control', "required", "maxlength"=>"10", "data-parsley-type"=>"number"])}}
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label for="direccion" class="col-sm-4 control-label">Dirección</label>
                <div class="col-sm-8">
                {{Form::text('direccion', null ,['id'=>'direccion','class'=>'form-control', "required", "maxlength"=>"40", "data-parsley-pattern"=>"^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+(\s*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]*)*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+$"])}}
                </div>
            </div>
        </div>
        </div>
       <h5 style="margin: 15px 0;">Datos del Usuario Super administrador</h5>

            <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="identificacion" class="col-sm-4 control-label">Identificación</label>
                    <div class="col-sm-8">
                        {{Form::text('identificacion', null ,['id'=>'identificacion','class'=>'form-control', "required", "maxlength"=>"10", "data-parsley-type"=>"number"])}}
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="email" class="col-sm-4 control-label">Email</label>
                    <div class="col-sm-8">
                        {{Form::email('email', null ,['id'=>'email','class'=>'form-control', "required", "maxlength"=>"40"])}}
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="nombres" class="col-sm-4 control-label">Nombres</label>
                    <div class="col-sm-8">
                        {{Form::text('nombres', null ,['id'=>'nombres','class'=>'form-control', "required", "maxlength"=>"40", "data-parsley-pattern"=>"^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+(\s*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]*)*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+$"])}}
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="apellidos" class="col-sm-4 control-label">Apellidos</label>
                    <div class="col-sm-8">
                        {{Form::text('apellidos', null ,['id'=>'apellidos','class'=>'form-control', "required", "maxlength"=>"40", "data-parsley-pattern"=>"^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+(\s*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]*)*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+$"])}}
                    </div>
                </div>
            </div>
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
        $("#crearempresa").parsley();
        $("#crearempresa").submit(function (e) {
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
                        table.ajax.reload();
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