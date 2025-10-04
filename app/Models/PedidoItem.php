<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    //
   public $timestamps = false;

    protected $fillable = [
    'pedido_id',
    'product_id',
    'quantity',
    'subtotal',
    'tamano',
    'leche',
    'extras'
];

protected $casts = [
    'extras' => 'array'
];

    public function producto()
    {
        return $this->belongsTo(ProductsModel::class, 'product_id');
    }

    public function extras()
    {
        return $this->hasMany(PedidoItemExtra::class);
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
