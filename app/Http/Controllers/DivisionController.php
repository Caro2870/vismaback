<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Division;
use Illuminate\Support\Facades\DB;
class DivisionController extends Controller
{
   

    public function crearDivisones(Request $request)
    {
        try {
            // Validar y guardar la nueva división
            $request->validate(Division::rules());
    
            $division = Division::create($request->all());
    
            return response()->json(['message' => 'División creada correctamente', 'division' => $division]);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            // Capturar excepción de validación y obtener mensajes de error
            $errors = $validationException->errors();
    
            return response()->json(['error' => 'Error de validación', 'messages' => $errors], 422);
        } catch (\Exception $e) {
            // Capturar otras excepciones
            return response()->json(['error' => 'Error al procesar la solicitud. Detalles: ' . $e->getMessage()], 500);
        }
    }
    

    public function actualizarDivisiones(Request $request)
    {
        try {
            // Validar y actualizar la división
            $request->validate(Division::rules($request->id));
    
            $division = Division::findOrFail($request->id);
            $division->update($request->all());
    
            return response()->json(['message' => 'División actualizada correctamente', 'division' => $division]);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            // Capturar excepción de validación y obtener mensajes de error
            $errors = $validationException->errors();
    
            return response()->json(['error' => 'Error de validación', 'messages' => $errors], 422);
        } catch (\Exception $e) {
            // Capturar otras excepciones
            return response()->json(['error' => 'Error al procesar la solicitud. Detalles: ' . $e->getMessage()], 500);
        }
    }
    

    public function listarDivisiones(Request $request)
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
                        $query->where('contador.subdivisiones', '=', $search) ;
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
                                $query->where('divisions.nivel', '=', $search); 
                                break;
                            case 'embajador_nombre':
                                $query->where('divisions.embajador_nombre', 'like', "%$search%");
                                break;
                    
                    default:
                        $query->where(function ($q) use ($search) {
                            $q->where('car.nombre', 'like', "%$search%")
                                ->orWhere('divisions.nombre', 'like', "%$search%")
                                ->orWhere('divisions.colaboradores', 'like', "%$search%")
                                ->orWhere('divisions.nivel', '=', $search) 
                                ->orWhere('contador.subdivisiones', '=', $search) 
                                ->orWhere('divisions.embajador_nombre', 'like', "%$search%");
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

   

    public function listarSubdivisionesPorId(Request $request)
    {
        try {
            // Utiliza el Query Builder para obtener las subdivisiones de una división específica
            $subdivisiones = DB::table('divisions')
                ->select('divisions.*', 'car.nombre as division_superior_nombre')
                ->leftJoin('divisions as car', 'car.id', '=', 'divisions.division_superior_id')
                ->where('divisions.division_superior_id', $request->query('division_id'))
                ->get();

            return response()->json(['subdivisiones' => $subdivisiones]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al recuperar las subdivisiones.'], 500);
        }
    }

    public function eliminarDivision(Request $request)
    {
        try {
            // Eliminar la división
            $division = Division::findOrFail( $request->id);
            $division->delete();
    
            return response()->json(['message' => 'División eliminada correctamente']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $notFoundException) {
            // Capturar excepción si el ID no existe
            return response()->json(['error' => 'División no encontrada.'], 404);
        } catch (\Exception $e) {
            // Capturar otras excepciones
            return response()->json(['error' => 'Error al procesar la solicitud. Detalles: ' . $e->getMessage()], 500);
        }
    }
    
    
    }
