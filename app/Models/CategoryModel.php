<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model
{
    protected $table = 'categorias';

    protected $fillable = ['descripcion', 'tipo'];

    public function productos()
    {
        return $this->hasMany(ProductsModel::class, 'id_cat');
    }
}
