<div id="modalpunto">

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Ubicacion</h4>
    </div>
    <div class="modal-body">
        <div class="gmaps-full">
            <div id="gmaps-markers" style="width: 100%; height: 300px"></div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cerrar</button>

    </div>

</div>

<script>
    $(function () {
        initMap()
    });
    var map;
    function initMap() {
        setTimeout(function () {
            map = new GMaps({
                div: '#gmaps-markers',
                lat: "{{$geposicion->latitud}}",
                lng: "{{$geposicion->longitud}}",
                zoom: 16
            });
            map.addMarker({
                lat: "{{$geposicion->latitud}}",
                lng: "{{$geposicion->longitud}}",
            });
        },200);
    };
</script>