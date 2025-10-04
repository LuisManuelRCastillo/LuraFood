<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    // public $timestamps = false;
     const UPDATED_AT = null;

    protected $fillable = ['customer_name', 'customer_email', 'status', 'total'];

    public function items()
    {
        return $this->hasMany(PedidoItem::class);
    }
}
