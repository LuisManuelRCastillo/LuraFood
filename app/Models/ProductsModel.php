<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsModel extends Model
{
    protected $table = 'productos';

    protected $fillable = ['nombre', 'descripcion', 'precio', 'qty', 'id_cat', 'activo'];

    public function categoria()
    {
        return $this->belongsTo(CategoryModel::class, 'id_cat');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
