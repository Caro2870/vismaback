<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Division;

class DivisionController extends Controller
{
    public function listarTodas(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $query = Division::query();

            // Búsqueda de texto
            $search = $request->input('search');
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%$search%")
                      ->orWhere('division_superior', 'like', "%$search%")
                      ->orWhere('colaboradores', 'like', "%$search%")
                      ->orWhere('nivel', 'like', "%$search%");
                });
            }

            // Ordenamiento
            $orderColumn = $request->input('order_column', 'nombre');
            $orderDirection = $request->input('order_direction', 'asc');
            $query->orderBy($orderColumn, $orderDirection);

            // Filtrado por columna
            $filterColumn = $request->input('filter_column');
            $filterValue = $request->input('filter_value');
            if ($filterColumn && $filterValue) {
                $query->where($filterColumn, $filterValue);
            }

            // Paginación
            $divisiones = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json(['divisiones' => $divisiones]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al recuperar las divisiones.'], 500);
        }
    }

    public function listarSubdivisiones($divisionId)
    {
        $division = Division::findOrFail($divisionId);
        $subdivisiones = $division->subdivisiones;
        return view('division.listar_subdivisiones', ['division' => $division, 'subdivisiones' => $subdivisiones]);
    }
}
