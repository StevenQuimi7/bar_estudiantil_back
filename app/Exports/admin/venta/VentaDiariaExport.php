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
        $this->ventas_diarias = collect($ventas);
        $this->detalles = $this->setDetalles($ventas);

    }

    private function setDetalles($ventas_diarias)
    {
        return collect($ventas_diarias)->flatMap(function ($venta) {
            return collect($venta->detalles_venta)->map(function ($detalle) use ($venta) {
                return (object)[
                    'numero_identificacion' => $venta->cliente->numero_identificacion,
                    'cliente'               => $venta->cliente->nombre_completo,
                    'producto'              => $detalle->producto->nombre,
                    'precio'                => $detalle->producto->precio,
                    'cantidad'              => $detalle->cantidad,
                    'subtotal'              => $detalle->subtotal,
                    'fecha'                 => ($venta->created_at)->format('Y-m-d'),
                    'estado_gestion'        => $venta->estado_gestion,
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
