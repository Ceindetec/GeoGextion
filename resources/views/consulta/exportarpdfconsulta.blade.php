<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        #logo {
            float: left;
            width: 48%;
            display: inline-block;
            margin-bottom: 20px;
        }

        #info {
            float: right;
            width: 50%;
            display: inline-block;
            margin-bottom: 20px;
            text-align: right;
        }

        .table {
            clear: both;
        }

        ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .izquierda, .derecha {
            display: inline-block;
            width: 50%;
        }

        .izquierda {
            text-align: left;
        }

        .derecha {
            text-align: right;
        }
    </style>

</head>
<body>
<div class="row">

    <div id="logo">
        <img src="{{url('images/logo1.png')}}" width="220px">
    </div>
    <div id="info">
        <ul>
            <li><label>Nombre del reporte: </label><strong>Geoposiciones del asesor</strong></li>
            <li><label>Identificación: </label><strong>{{$geposiciones[0]->getAsesor->identificacion}}</strong></li>
            <li><label>Nombre del asesor: </label><strong>{{$geposiciones[0]->getAsesor->nombres}} {{$geposiciones[0]->getAsesor->apellidos}}</strong></li>
            <li><label>Fecha de generación: </label><strong>{{\Carbon\Carbon::now()->format('d/m/Y')}}</strong></li>
        </ul>

    </div>
    <br>

    <div class="table-responsive m-b-20">
        <table id="datatable" class="table table-striped table-bordered datatable"
               width="100%">
            <thead>
            <tr>
                <th>Fecha</th>
                <th>Coordenadas</th>
                <th>Dirección</th>
            </tr>
            </thead>
            <tbody>
            @foreach($geposiciones as $geposicion)
                <tr>
                    <td>{{$geposicion->fecha}}</td>
                    <td>{{$geposicion->latitud}},{{$geposicion->longitud}} </td>
                    <td>{{$geposicion->direccion}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
</body>
</html>



