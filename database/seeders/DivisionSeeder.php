<?php


// database/seeders/DivisionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division;

class DivisionSeeder extends Seeder
{
    public function run()
    {
        \App\Models\Division::factory(10)->create()->each(function ($division) {
            $this->createRandomHierarchy($division, 3);
        });
    }

    protected function createRandomHierarchy(Division $parentDivision, $remainingLevels)
    {
        if ($remainingLevels <= 0) {
            return;
        }

        \App\Models\Division::factory(rand(0, 1))->create([
            'nombre' => \Faker\Factory::create()->unique()->sentence(2),
            'division_superior_id' => $parentDivision->id,
            'colaboradores' => rand(5, 20),
            'nivel' => $parentDivision->nivel + 1,
            'embajador_nombre' => $this->generateRandomName(),
        ])->each(function ($division) use ($remainingLevels) {
            $this->createRandomHierarchy($division, $remainingLevels - 1);
        });
        \App\Models\Division::factory(rand(0, 1))->create([
            'nombre' => \Faker\Factory::create()->unique()->sentence(2),
            'division_superior_id' => $parentDivision->id,
            'colaboradores' => rand(5, 20),
            'nivel' => $parentDivision->nivel + 1,
            'embajador_nombre' => $this->generateRandomName(),
        ])->each(function ($division) use ($remainingLevels) {
            $this->createRandomHierarchy($division, $remainingLevels - 1);
        });
    }

    protected function generateRandomName()
    {
        return rand(0, 1) ? \Faker\Factory::create()->name : null;
    }
}

