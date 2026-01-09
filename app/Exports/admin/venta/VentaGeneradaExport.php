<?php

namespace App\Exports\admin\venta;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\Log;

class VentaGeneradaExport implements WithMultipleSheets
{
 


    private $ventas_generadas;
    private $detalles;
    public function __construct($ventas)
    {
        $this->ventas_generadas = collect($ventas);
        $this->detalles = $this->setDetalles($ventas);

    }

    private function setDetalles($ventas_generadas)
    {
        return collect($ventas_generadas)->flatMap(function ($venta) {
            return collect($venta->detalles)->map(function ($detalle) use ($venta) {
                return [
                    'cliente'         => $venta->cliente,
                    'producto'        => $detalle->producto,
                    'precio'          => $detalle->precio,
                    'cantidad'        => $detalle->cantidad,
                    'subtotal'        => $detalle->subtotal,
                    'fecha'           => $detalle->venta_created_at,
                    'estado_gestion'  => $venta->estado_gestion,
                ];
            });
        })->values();
    }

    

     public function sheets(): array
    {
        return [
            new CabeceraVentaGeneradaExport($this->ventas_generadas),
            new DetallesVentaGeneradaExport($this->detalles),
        ];
    }
}
