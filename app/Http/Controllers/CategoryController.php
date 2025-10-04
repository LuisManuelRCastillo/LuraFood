<?php

namespace App\Http\Controllers;
use App\Models\CategoryModel as Category;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //
    public function index()
    {
        $categorias = Category::all();

        // dd( $categorias);
        return view('Categories.categoriesIndex', compact('categorias'));
    }
}
