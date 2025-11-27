<?php

namespace App\Exports\admin\venta;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CabeceraVentaGeneradaExport implements FromView, ShouldAutoSize,WithStyles
{
   private $ventas_generadas;
    public function __construct($ventas)
    {
        //log::alert(count(collect($reubicaciones)));
        $this->ventas_generadas = collect($ventas);
        // log::alert("parametro desde CabeceraVentaGeneradaExport");
        // log::alert(collect($ventas));
        // log::alert("----");

    }
    public function view(): View
    {
        return view('admin.exports.ventas.VentaGeneradaExport', [
            'results' => $this->ventas_generadas
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        //titulo de hoja
        $sheet->setTitle('Ventas Generadas');

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
