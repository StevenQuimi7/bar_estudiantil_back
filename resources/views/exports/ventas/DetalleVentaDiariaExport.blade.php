<table>
    <thead>
    <tr>
        <th style="font-weight: bold; background-color:#0a1725; color:white;">#</th>
        <th style="font-weight: bold; background-color:#0a1725; color:white;">CLIENTE</th>
        <th style="font-weight: bold; background-color:#0a1725; color:white;">PRODUCTO</th>
        <th style="font-weight: bold; background-color:#0a1725; color:white;">PRECIO</th>
        <th style="font-weight: bold; background-color:#0a1725; color:white;">CANTIDAD</th>
        <th style="font-weight: bold; background-color:#0a1725; color:white;">SUBTOTAL</th>
        <th style="font-weight: bold; background-color:#0a1725; color:white;">FECHA</th>
        <th style="font-weight: bold; background-color:#0a1725; color:white;">ESTADO</th>
    </tr>
    </thead>
    <tbody>
    @forelse($results as $key=>$resultado)
        <tr>
            <td>{{ $key+1 }}</td>
            <td>{{ $resultado->cliente }}</td>
            <td>{{ $resultado->producto }}</td>
            <td>{{ number_format(doubleval($resultado->precio),2,".","") }}</td>
            <td>{{ $resultado->cantidad }}</td>
            <td>{{ number_format(doubleval($resultado->subtotal),2,".","") }}</td>
            <td>{{ date('d/m/Y',strtotime($resultado->fecha)) }}</td>
            <td>{{ $resultado->estado_gestion }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="8">No hay información</td>
        </tr>
    @endforelse
    </tbody>
</table>
