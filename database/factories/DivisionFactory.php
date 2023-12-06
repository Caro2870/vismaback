<?php

// database/factories/DivisionFactory.php

namespace Database\Factories;

use App\Models\Division;
use Illuminate\Database\Eloquent\Factories\Factory;

class DivisionFactory extends Factory
{
    protected $model = Division::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->unique()->sentence(2),
            'division_superior_id' => null,
            'colaboradores' => $this->faker->numberBetween(1, 100),
            'nivel' => $this->faker->numberBetween(1, 10),
            'embajador_nombre' => $this->faker->boolean(30) ? $this->faker->name : null,
        ];
    }
}
