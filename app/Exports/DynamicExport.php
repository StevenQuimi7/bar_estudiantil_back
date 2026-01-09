<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DynamicExport implements FromView, ShouldAutoSize
{
    protected $result;
    protected $columnas;
    protected $titulo;

    public function __construct($result, $titulo = null, $columnas = null)
    {
        // Si result es una colección, convertimos a array. 
        // Agregamos una validación simple por si viene vacío.
        $this->result = is_object($result) ? $result->toArray() : $result;

        $this->titulo = substr($titulo, 0, 31);

        // Lógica de Columnas
        if (!empty($columnas)) {
            $rawColumnas = $columnas;
        } elseif (!empty($this->result)) {
            // Extraemos las llaves del primer registro
            $firstRecord = (array) $this->result[0];
            $rawColumnas = array_keys($firstRecord);
        } else {
            $rawColumnas = [];
        }

        // Aplicar formato: Primera mayúscula, resto minúscula
        $this->columnas = array_map(function($col) {
            return ucfirst(strtolower($col));
        }, $rawColumnas);
    }

    public function view(): View
    {
        return view('exports.DynamicExport', [
            'data' => [
                'columnas' => $this->columnas,
                'result'   => $this->result
            ]
        ]);
    }

    public function title(): string
    {
        return $this->titulo;
    }
}