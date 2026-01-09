<table>
    <thead>
        <tr>
            <th style="font-weight: bold; background-color:#0a1725; color:white;">#</th>
            @foreach($data["columnas"] as $columna)
                <th style="font-weight: bold; background-color:#0a1725; color:white;">
                    {{ $columna }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        {{-- Corregido: Un solo $ y acceso correcto --}}
        @forelse($data["result"] as $key => $result)
            <tr>
                <td>{{ $key + 1 }}</td>
                @foreach ($result as $col)
                    <td>{{ $col }}</td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="{{ count($data['columnas']) + 1 }}">No hay información</td>
            </tr>
        @endforelse
    </tbody>
</table>