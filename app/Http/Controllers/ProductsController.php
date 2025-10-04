<?php

namespace App\Http\Controllers;
use App\Models\ProductsModel as Products;
use App\Models\CategoryModel as Category;


use Illuminate\Http\Request;

class ProductsController extends Controller
{
    //
    public function index($id)
    {
      $categoria = Category::findOrFail($id);
       $productos = Products::where('id_cat', $id)->get();
       return view('Products.productsIndex', compact('productos', 'categoria'));
    }
}
