<?php

namespace App\Exports\admin\venta;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DetalleVentaDiariaExport implements FromView, ShouldAutoSize,WithStyles
{
    private $detalles_ventas_generadas;
    public function __construct($ventas)
    {
        $this->detalles_ventas_generadas = collect($ventas)->map(function ($venta) {
            return is_array($venta) ? (object) $venta : $venta;
        });
    }
    public function view(): View
    {
        return view('exports.ventas.DetalleVentaDiariaExport', [
            'results' => $this->detalles_ventas_generadas
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        //titulo de hoja
        $sheet->setTitle('Detalles Ventas Diarias');

        //Estilo para la primera fila
        $sheet->getStyle('1')->applyFromArray([
            'alignment' => [
                'wrapText' => true,
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],

        ]);
        //Altura a la filas
        $sheet->getRowDimension(1)->setRowHeight(25);


        return $sheet;
    }
}
