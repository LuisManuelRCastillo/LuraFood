<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class sales extends Component
{
    public $pedidos;
    public $totalVentas;
    public $productosMasVendidos;
    public $ventasPorDia;
    public $productoMasVendido;

    public function __construct($pedidos, $totalVentas, $productosMasVendidos, $ventasPorDia, $productoMasVendido)
    {
        $this->pedidos = $pedidos;
        $this->totalVentas = $totalVentas;
        $this->productosMasVendidos = $productosMasVendidos;
        $this->ventasPorDia = $ventasPorDia;
        $this->productoMasVendido = $productoMasVendido;
    }

    public function render()
    {
        return view('components.sales');
    }
}
