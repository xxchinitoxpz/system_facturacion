<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Bebidas',
            'Lácteos',
            'Panadería',
            'Snacks',
            'Dulces y Chocolates',
            'Cereales y Desayunos',
            'Conservas',
            'Condimentos y Salsas',
            'Limpieza del Hogar',
            'Cuidado Personal',
            'Cigarrillos',
            'Congelados',
            'Frutas y Verduras',
            'Carnes y Embutidos',
            'Pescados y Mariscos',
            'Granos y Legumbres',
            'Aceites y Vinagres',
            'Harinas y Pastas',
            'Sopas y Caldos',
            'Golosinas',
            'Bebidas Alcohólicas',
            'Artículos de Papelería',
            'Productos para Bebés',
            'Mascotas',
            'Otros'
        ];

        foreach ($categories as $categoryName) {
            Category::create([
                'nombre' => $categoryName
            ]);
        }
    }
}
