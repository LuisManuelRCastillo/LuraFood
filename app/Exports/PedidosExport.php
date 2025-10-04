<?php

namespace App\Exports;

use App\Models\Pedido;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PedidosExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Pedido::with('items.producto')
            ->where('status', 'delivered')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Pedido',
            'Cliente',
            'Productos',
            'Total'
        ];
    }

    public function map($pedido): array
    {
        $productos = $pedido->items->map(function($item) {
            return $item->quantity . 'x ' . ($item->producto->nombre ?? 'Producto');
        })->implode(', ');

        return [
            $pedido->id,
            $pedido->customer_name,
            $productos,
            '$' . number_format($pedido->total, 2)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
