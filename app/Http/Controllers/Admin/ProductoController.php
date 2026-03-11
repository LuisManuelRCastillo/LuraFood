<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductsModel;
use App\Models\CategoryModel;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductsModel::with('categoria')->orderBy('id_cat')->orderBy('nombre');

        if ($request->filled('categoria')) {
            $query->where('id_cat', $request->categoria);
        }

        $productos = $query->get();
        $categorias = CategoryModel::orderBy('descripcion')->get();

        return view('Admin.productos.index', compact('productos', 'categorias'));
    }

    public function create()
    {
        $categorias = CategoryModel::orderBy('descripcion')->get();
        return view('Admin.productos.form', ['producto' => null, 'categorias' => $categorias]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'precio'      => 'required|numeric|min:0',
            'qty'         => 'nullable|integer|min:0',
            'id_cat'      => 'required|exists:categorias,id',
        ]);

        ProductsModel::create([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio'      => $request->precio,
            'qty'         => $request->qty ?? 0,
            'id_cat'      => $request->id_cat,
            'activo'      => $request->boolean('activo', true),
        ]);

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit($id)
    {
        $producto = ProductsModel::findOrFail($id);
        $categorias = CategoryModel::orderBy('descripcion')->get();
        return view('Admin.productos.form', compact('producto', 'categorias'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'precio'      => 'required|numeric|min:0',
            'qty'         => 'nullable|integer|min:0',
            'id_cat'      => 'required|exists:categorias,id',
        ]);

        $producto = ProductsModel::findOrFail($id);
        $producto->update([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio'      => $request->precio,
            'qty'         => $request->qty ?? 0,
            'id_cat'      => $request->id_cat,
            'activo'      => $request->boolean('activo'),
        ]);

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy($id)
    {
        $producto = ProductsModel::findOrFail($id);
        $producto->delete();

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}
