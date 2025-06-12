<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MovimientosExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $movimientos;

    public function __construct($movimientos)
    {
        $this->movimientos = $movimientos;
    }

    public function collection()
    {
        return $this->movimientos;
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Hora',
            'Tipo',
            'Producto',
            'Código',
            'Cantidad',
            'Unidad',
            'Proveedor',
            'Proyecto',
            'Usuario',
            'Observaciones'
        ];
    }

    public function map($movimiento): array
    {
        return [
            $movimiento->fecha_hora->format('d/m/Y'),
            $movimiento->fecha_hora->format('H:i:s'),
            ucfirst($movimiento->tipo),
            $movimiento->producto->nombre ?? 'N/A',
            $movimiento->producto->codigo ?? 'N/A',
            $movimiento->cantidad,
            $movimiento->producto->unidad ?? '',
            $movimiento->proveedor->nombre ?? '',
            $movimiento->proyecto->nombre ?? '',
            $movimiento->usuario->nombre ?? '',
            $movimiento->observaciones ?? ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}