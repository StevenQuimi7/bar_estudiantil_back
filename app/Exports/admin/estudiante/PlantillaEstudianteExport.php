<?php

namespace App\Exports\admin\estudiante;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Illuminate\Support\Facades\Log;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use PhpOffice\PhpSpreadsheet\Style\Style;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;

use Carbon\Carbon;

use Exception;

class PlantillaEstudianteExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithDefaultStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        try {
            $data = [
                [
                    '0923599657',
                    'PEREZ CASTRO',
                    'MARIA BELEN',
                    'BACHILLERATO',
                    '3',
                    'TECNICO',
                    'A'
                ]
            ];
            return collect($data);
        } catch (Exception $e) {
            Log::error("El error está en PlantillaEstudianteExport en la línea: " . $e->getLine());
            Log::error("El error es: " . $e->getMessage());
        }
    }

    public function headings(): array
    {
        return [
            'numero_identificacion', 'apellidos', 'nombres', 'nivel', 'grado', 'especialidad', 'seccion'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Título de la hoja
        $sheet->setTitle('Estudiantes');

        // Encabezado en negrita y centrado
        $sheet->getStyle('1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'wrapText' => true,
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Altura de la fila del encabezado
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Ajuste de alineación de la columna C (Numero Identificación) a la izquierda
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        return $sheet;
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return [
            'font' => [
                'size' => 10,
            ],
        ];
    }
}
