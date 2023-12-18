<?php

namespace Database\Seeders;

use App\Models\Facultad;
use Illuminate\Database\Seeder;

class FacultadTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Facultad::create([
            'COD_FACULTAD' => 1,
            'NOMBRE_FACULTAD' => 'Facultad De Ciencias y Tecnología',
            'DESCRIPCION' => '',
        ]);

        
    }
}
