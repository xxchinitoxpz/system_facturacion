<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear cliente para tickets (DNI 00000000)
        Client::firstOrCreate(
            ['nro_documento' => '00000000'],
            [
                'nombre_completo' => 'CLIENTE GENERAL',
                'tipo_documento' => 'DNI',
                'nro_documento' => '00000000',
                'telefono' => null,
                'email' => null,
                'direccion' => null,
                'activo' => true
            ]
        );
    }
}
