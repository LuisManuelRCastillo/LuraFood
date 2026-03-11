<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryModel;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = CategoryModel::withCount('productos')->orderBy('id')->get();
        return view('Admin.categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('Admin.categorias.form', ['categoria' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:100',
            'tipo'        => 'required|in:bebida,alimento',
        ]);

        CategoryModel::create($request->only('descripcion', 'tipo'));

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function edit($id)
    {
        $categoria = CategoryModel::findOrFail($id);
        return view('Admin.categorias.form', compact('categoria'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'descripcion' => 'required|string|max:100',
            'tipo'        => 'required|in:bebida,alimento',
        ]);

        $categoria = CategoryModel::findOrFail($id);
        $categoria->update($request->only('descripcion', 'tipo'));

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy($id)
    {
        $categoria = CategoryModel::withCount('productos')->findOrFail($id);

        if ($categoria->productos_count > 0) {
            return back()->withErrors(['error' => "No se puede eliminar: la categoría tiene {$categoria->productos_count} producto(s). Elimina los productos primero."]);
        }

        $categoria->delete();

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
