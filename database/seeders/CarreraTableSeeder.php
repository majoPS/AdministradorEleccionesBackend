<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Carrera;

class CarreraTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Carrera::create([
            'COD_CARRERA' => 8,
            'COD_FACULTAD' => 1,
            'NOMBRE_CARRERA' => 'Lic en Informatica',
            'DESCRIPCION' => '',
        ]);

        Carrera::create([
            'COD_CARRERA' => 7,
            'COD_FACULTAD' => 1,
            'NOMBRE_CARRERA' => 'Ing de Sistemas',
            'DESCRIPCION' => '',
        ]);
    }
}
