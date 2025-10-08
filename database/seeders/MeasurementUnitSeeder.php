<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MeasurementUnit;

class MeasurementUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['abreviatura' => '4A', 'nombre' => 'BOBINAS'],
            ['abreviatura' => 'BJ', 'nombre' => 'BALDE'],
            ['abreviatura' => 'BLL', 'nombre' => 'BARRILES'],
            ['abreviatura' => 'BG', 'nombre' => 'BOLSA'],
            ['abreviatura' => 'BO', 'nombre' => 'BOTELLAS'],
            ['abreviatura' => 'BX', 'nombre' => 'CAJA'],
            ['abreviatura' => 'CT', 'nombre' => 'CARTONES'],
            ['abreviatura' => 'CMK', 'nombre' => 'CENTIMETRO CUADRADO'],
            ['abreviatura' => 'CMQ', 'nombre' => 'CENTIMETRO CUBICO'],
            ['abreviatura' => 'CMT', 'nombre' => 'CENTIMETRO LINEAL'],
            ['abreviatura' => 'CEN', 'nombre' => 'CIENTO DE UNIDADES'],
            ['abreviatura' => 'CY', 'nombre' => 'CILINDRO'],
            ['abreviatura' => 'CJ', 'nombre' => 'CONOS'],
            ['abreviatura' => 'DZN', 'nombre' => 'DOCENA'],
            ['abreviatura' => 'DZP', 'nombre' => 'DOCENA POR 10**6'],
            ['abreviatura' => 'BE', 'nombre' => 'FARDO'],
            ['abreviatura' => 'GLI', 'nombre' => 'GALON INGLES (4,545956L)'],
            ['abreviatura' => 'GRM', 'nombre' => 'GRAMO'],
            ['abreviatura' => 'GRO', 'nombre' => 'GRUESA'],
            ['abreviatura' => 'HLT', 'nombre' => 'HECTOLITRO'],
            ['abreviatura' => 'LEF', 'nombre' => 'HOJA'],
            ['abreviatura' => 'SET', 'nombre' => 'JUEGO'],
            ['abreviatura' => 'KGM', 'nombre' => 'KILOGRAMO'],
            ['abreviatura' => 'KTM', 'nombre' => 'KILOMETRO'],
            ['abreviatura' => 'KWH', 'nombre' => 'KILOVATIO HORA'],
            ['abreviatura' => 'KT', 'nombre' => 'KIT'],
            ['abreviatura' => 'CA', 'nombre' => 'LATAS'],
            ['abreviatura' => 'LBR', 'nombre' => 'LIBRAS'],
            ['abreviatura' => 'LTR', 'nombre' => 'LITRO'],
            ['abreviatura' => 'MWH', 'nombre' => 'MEGAWATT HORA'],
            ['abreviatura' => 'MTR', 'nombre' => 'METRO'],
            ['abreviatura' => 'MTK', 'nombre' => 'METRO CUADRADO'],
            ['abreviatura' => 'MTQ', 'nombre' => 'METRO CUBICO'],
            ['abreviatura' => 'MGM', 'nombre' => 'MILIGRAMOS'],
            ['abreviatura' => 'MLT', 'nombre' => 'MILILITRO'],
            ['abreviatura' => 'MMT', 'nombre' => 'MILIMETRO'],
            ['abreviatura' => 'MMK', 'nombre' => 'MILIMETRO CUADRADO'],
            ['abreviatura' => 'MMQ', 'nombre' => 'MILIMETRO CUBICO'],
            ['abreviatura' => 'MIL', 'nombre' => 'MILLARES'],
            ['abreviatura' => 'UM', 'nombre' => 'MILLON DE UNIDADES'],
            ['abreviatura' => 'ONZ', 'nombre' => 'ONZAS'],
            ['abreviatura' => 'PF', 'nombre' => 'PALETAS'],
            ['abreviatura' => 'PK', 'nombre' => 'PAQUETE'],
            ['abreviatura' => 'PR', 'nombre' => 'PAR'],
            ['abreviatura' => 'FOT', 'nombre' => 'PIES'],
            ['abreviatura' => 'FTK', 'nombre' => 'PIES CUADRADOS'],
            ['abreviatura' => 'FTQ', 'nombre' => 'PIES CUBICOS'],
            ['abreviatura' => 'C62', 'nombre' => 'PIEZAS'],
            ['abreviatura' => 'PG', 'nombre' => 'PLACAS'],
            ['abreviatura' => 'ST', 'nombre' => 'PLIEGO'],
            ['abreviatura' => 'INH', 'nombre' => 'PULGADAS'],
            ['abreviatura' => 'RM', 'nombre' => 'RESMA'],
            ['abreviatura' => 'DR', 'nombre' => 'TAMBOR'],
            ['abreviatura' => 'STN', 'nombre' => 'TONELADA CORTA'],
            ['abreviatura' => 'LTN', 'nombre' => 'TONELADA LARGA'],
            ['abreviatura' => 'TNE', 'nombre' => 'TONELADAS'],
            ['abreviatura' => 'TU', 'nombre' => 'TUBOS'],
            ['abreviatura' => 'NIU', 'nombre' => 'UNIDAD (BIENES)'],
            ['abreviatura' => 'ZZ', 'nombre' => 'UNIDAD (SERVICIOS)'],
            ['abreviatura' => 'GLL', 'nombre' => 'US GALON (3,7843 L)'],
            ['abreviatura' => 'YRD', 'nombre' => 'YARDA'],
            ['abreviatura' => 'YDK', 'nombre' => 'YARDA CUADRADA'],
        ];

        foreach ($units as $unit) {
            MeasurementUnit::create($unit);
        }
    }
}
