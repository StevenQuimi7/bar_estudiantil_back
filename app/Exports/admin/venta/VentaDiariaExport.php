<?php

namespace App\Exports\admin\venta;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\Log;

class VentaDiariaExport implements WithMultipleSheets
{
 
    private $ventas_diarias;
    private $detalles;
    public function __construct($ventas)
    {
        //log::alert(count(collect($reubicaciones)));
        $this->ventas_diarias = collect($ventas);
        $this->detalles = $this->setDetalles($ventas);
        // log::alert("parametro desde VentaGeneradaExport");
        // log::alert(collect($ventas));
        // log::alert("----");

    }

    private function setDetalles($ventas_diarias)
    {
        return collect($ventas_diarias)->flatMap(function ($venta) {
            return collect($venta->detalles_venta)->map(function ($detalle) use ($venta) {
                return (object)[
                    'cliente'         => $venta->cliente->nombre_completo,
                    'producto'        => $detalle->producto->nombre,
                    'precio'          => $detalle->producto->precio,
                    'cantidad'        => $detalle->cantidad,
                    'subtotal'        => $detalle->subtotal,
                    'fecha'           => $venta->created_at,
                    'estado_gestion'  => $venta->estado_gestion,
                ];
            });
        })->values();
    }

    

     public function sheets(): array
    {
        return [
            new CabeceraVentaDiariaExport($this->ventas_diarias),
            new DetalleVentaDiariaExport($this->detalles),
        ];
    }
}
