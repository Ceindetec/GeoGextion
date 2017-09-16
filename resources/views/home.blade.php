@extends('layouts.admin')

@section('styles')
@endsection

@section('contenido')

    <div class="row">
        <div class="col-xs-12">
            <div class="page-title-box">
                <h4 class="page-title">Dashboard</h4>
                <ol class="breadcrumb p-0 m-0">
                    <li>
                        <a href="#">Adminox</a>
                    </li>
                    <li>
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="active">
                        Dashboard 1
                    </li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <div class="gmaps-full">
        <div id="gmaps-markers" class="gmaps-full1"></div>
    </div>

@endsection

@section('scripts')

    <!-- google maps api -->
    {{Html::script('http://maps.google.com/maps/api/js?key=AIzaSyDAHZxs8NRsHDfR2Zse_9P0mdZFs3rvASQ')}}

    <!-- main file -->
    {{Html::script('plugins/gmaps/gmaps.min.js')}}

    <script>

        $(function () {
           initMap();
        });


        function initMap() {
            setTimeout(function () {
                map = new GMaps({
                    div: '#gmaps-markers',
                    lat: 4.6097100,
                    lng: -74.0817500
                });
            },200);
        };
    </script>

@endsection

