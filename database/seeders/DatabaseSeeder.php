<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar seeders en orden
        $this->call([
            PermissionSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ClientSeeder::class,
            // MeasurementUnitSeeder::class, // Comentado temporalmente - requiere modelo MeasurementUnit
        ]);
    }
}
