<table>
    <thead>
    <tr>
        <th style="font-weight: bold; background-color:#0a1725; color:white;">#</th>
        <th style="font-weight: bold; background-color:#0a1725; color:white;">CLIENTE</th>
        <th style="font-weight: bold; background-color:#0a1725; color:white;">TOTAL VENTA</th>
        <th style="font-weight: bold; background-color:#0a1725; color:white;">DESCUENTO CREDITO</th>
        <th style="font-weight: bold; background-color:#0a1725; color:white;">TOTAL A PAGAR</th>
        <th style="font-weight: bold; background-color:#0a1725; color:white;">FECHA</th>
        <th style="font-weight: bold; background-color:#0a1725; color:white;">ESTADO</th>
    </tr>
    </thead>
    <tbody>
    @forelse($results as $key=>$resultado)
        <tr>
            <td>{{ $key+1 }}</td>
            <td>{{ $resultado->cliente }}</td>
            <td>{{ number_format(doubleval($resultado->total_venta),2,".","") }}</td>
            <td>{{ number_format(doubleval($resultado->descuento_credito),2,".","") }}</td>
            <td>{{ number_format(doubleval($resultado->total_consolidado),2,".","") }}</td>
            <td>{{ date('d/m/Y',strtotime($resultado->created_at)) }}</td>
            <td>{{ $resultado->estado_gestion }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="7">No hay información</td>
        </tr>
    @endforelse
    </tbody>
</table>
