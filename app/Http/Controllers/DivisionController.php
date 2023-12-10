<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Division;
use Illuminate\Support\Facades\DB;
class DivisionController extends Controller
{
   

public function listarTodas(Request $request)
{
   
   
    try {
        $perPage = $request->query('per_page', 10);
        $page = $request->query('page', 1);

        $subquery = DB::table('divisions as car')
        ->select('car.division_superior_id', DB::raw('COUNT(*) as subdivisiones'))
        ->groupBy('car.division_superior_id');
    
    $query = DB::table('divisions')
        ->select('divisions.*', 'car.nombre as division_superior_nombre', 'contador.subdivisiones')
        ->leftJoin('divisions as car', 'car.id', '=', 'divisions.division_superior_id')
        ->leftJoinSub($subquery, 'contador', function ($join) {
            $join->on('divisions.id', '=', 'contador.division_superior_id');
        });
    
        // Búsqueda de texto
        $search = $request->query('search');
        $column = $request->query('column');
        if ($search) {
            switch ($column) {
                case 'subdivisiones':
                    $query->where('car.nombre', 'like', "%$search%");
                    break;
                case 'nombre':
                    $query->where('divisions.nombre', 'like', "%$search%");
                    break;
                    case 'division_superior_nombre':
                        $query->where('car.nombre', 'like', "%$search%");
                        break;
                    case 'colaboradores':
                        $query->where('divisions.colaboradores', 'like', "%$search%");
                        break;
                        case 'nivel':
                            $query->where('car.nivel', 'like', "%$search%");
                            break;
                        case 'embajador_nombre':
                            $query->where('divisions.embajador_nombre', 'like', "%$search%");
                            break;
                
                default:
                    $query->where(function ($q) use ($search) {
                        $q->where('car.nombre', 'like', "%$search%")
                            ->orWhere('divisions.nombre', 'like', "%$search%");
                    });
                    break;
            }

        }

        // Ordenamiento
        $orderColumn = $request->query('order_column', 'divisions.nombre');
        $orderDirection = $request->query('order_direction', 'asc');
        $query->orderBy($orderColumn, $orderDirection);

        // Filtrado por columna
        $filterColumn = $request->query('filter_column');
        $filterValue = $request->query('filter_value');
        if ($filterColumn && $filterValue) {
            $query->where($filterColumn, $filterValue);
        }

        // Paginación
        $result= $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json(['divisiones' => $result]);
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
