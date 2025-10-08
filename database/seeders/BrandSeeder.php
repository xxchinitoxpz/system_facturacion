<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            // Bebidas
            'Coca-Cola',
            'Pepsi',
            'Inca Kola',
            'Fanta',
            'Sprite',
            'San Mateo',
            'San Luis',
            'Cifrut',
            'Pulp',
            'Gloria',
            
            // Lácteos
            'Gloria',
            'Laive',
            'Nestlé',
            'Danone',
            'Yoplait',
            'Alpina',
            'Pura Vida',
            'Lácteos San Fernando',
            
            // Snacks y Golosinas
            'Doritos',
            'Cheetos',
            'Ruffles',
            'Lays',
            'Frito Lay',
            'Kraft',
            'Nestlé',
            'Ferrero',
            'Cadbury',
            'Hershey\'s',
            'M&M\'s',
            'Snickers',
            'Twix',
            'Milky Way',
            
            // Cereales
            'Kellogg\'s',
            'Nestlé',
            'Quaker',
            'Kraft',
            'General Mills',
            
            // Conservas
            'La Campiña',
            'San Fernando',
            'Gloria',
            'Laive',
            'Nestlé',
            'Maggi',
            'Knorr',
            
            // Condimentos
            'Maggi',
            'Knorr',
            'Ajinomoto',
            'Salsa Inglesa',
            'Ketchup Heinz',
            'Mayonesa Hellmann\'s',
            'Mostaza Heinz',
            
            // Limpieza
            'Ace',
            'Clorox',
            'Ajax',
            'Fabuloso',
            'Downy',
            'Ariel',
            'Tide',
            'Omo',
            'Rexona',
            'Dove',
            
            // Cuidado Personal
            'Colgate',
            'Crest',
            'Oral-B',
            'Head & Shoulders',
            'Pantene',
            'Dove',
            'Nivea',
            'Ponds',
            'Vaseline',
            
            // Cigarrillos
            'Marlboro',
            'Lucky Strike',
            'Kent',
            'Benson & Hedges',
            'Philip Morris',
            
            // Congelados
            'San Fernando',
            'Gloria',
            'Laive',
            'Nestlé',
            'D\'Onofrio',
            
            // Aceites
            'Primor',
            'Laive',
            'Gloria',
            'Cocinero',
            'Capullo',
            
            // Harinas y Pastas
            'Molitalia',
            'San Fernando',
            'Gloria',
            'Barilla',
            'Don Vittorio',
            
            // Sopas
            'Maggi',
            'Knorr',
            'Gloria',
            'Laive',
            
            // Bebidas Alcohólicas
            'Cristal',
            'Pilsen',
            'Cusqueña',
            'Corona',
            'Heineken',
            'Budweiser',
            'Johnnie Walker',
            'Jack Daniel\'s',
            'Bacardi',
            'Smirnoff',
            
            // Marca Genérica
            'Genérico',
            'Sin Marca',
            'Otros'
        ];

        foreach ($brands as $brandName) {
            Brand::create([
                'nombre' => $brandName
            ]);
        }
    }
}
