<table>
    <thead>
        <tr>
            <!-- <th>ID </th> -->
            <th>Detalles</th>
            <th>Fecha Creación </th>
            <th>Creador </th>
            <th>Cliente</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <tr class="{{ $cotizacion->active == 1 ? '' : 'text-muted' }}">
            <!-- <td>{{ $cotizacion->id }}</td> -->
            <td>{{count($cotizacion->detalles)}}</td>
            <td>{{ $cotizacion->created_at }}</td>
            <td class="text-truncate" title="{{$cotizacion->user->fullname}}" data-toggle="tooltip">{{$cotizacion->user->fullname}}</td>
            <td class="text-truncate" title="{{$cotizacion->client->nombreSap}}" data-toggle="tooltip">{{$cotizacion->client->nombreSap}}</td>
            <td>{{$cotizacion->estado->nombre}}
            </td>
            <td>
                <a href="{{route('cotizador.editar_cotizacion', $cotizacion->id)}}">
                    <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
                </a>
            </td>
        </tr>
</table>