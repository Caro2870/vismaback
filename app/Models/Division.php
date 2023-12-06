<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'division_superior_id', 'colaboradores', 'nivel', 'embajador_nombre'
    ];

    // Relación con la división superior
    public function divisionSuperior()
    {
        return $this->belongsTo(Division::class, 'division_superior_id');
    }

    // Relación con las subdivisiones
    public function subdivisiones()
    {
        return $this->hasMany(Division::class, 'division_superior_id');
    }

    // Validaciones
    public static function rules($id = null)
    {
        return [
            'nombre' => 'required|unique:divisions,nombre,' . $id . '|max:45',
            'division_superior_id' => 'nullable|exists:divisions,id',
            'colaboradores' => 'required|integer|min:0',
            'nivel' => 'required|integer|min:0',
            'embajador_nombre' => 'nullable|string|max:255',
        ];
    }

}