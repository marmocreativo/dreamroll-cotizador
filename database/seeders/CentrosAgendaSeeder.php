<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CentrosAgendaSeeder extends Seeder
{
    public function run(): void
    {
        $centros = [
            ['nombre' => 'Laboratorios del Carmen',                          'direccion' => 'Gobernador Rico 9981, Gabilondo, Tijuana, Baja California C.P. 22044'],
            ['nombre' => 'Laboratorio Vives',                                'direccion' => 'Avenida 1 Norte Ote. 1117, Col. Hidalgo, Tuxtla Gutiérrez, Chiapas C.P. 29000'],
            ['nombre' => 'Laboratorio de análisis clínicos y microbiológicos CAM', 'direccion' => 'Av. Prol. Teófilo Borunda 1418, Cuauhtémoc, Zona Centro II, Chihuahua C.P. 31020'],
            ['nombre' => 'Laboratorio Diagnova',                             'direccion' => 'Calle 23 de abril 54, San Pedro Xalpa, Azcapotzalco, CDMX C.P. 02719'],
            ['nombre' => 'Laboratorio Hidalgo Maldonado',                    'direccion' => 'Xicoténcatl 213, Zona Centro, Saltillo, Coahuila C.P. 25000'],
            ['nombre' => 'Laboratorio Crystal',                              'direccion' => 'Escobedo 1036, Primero de Cobián Centro, Torreón, Coahuila C.P. 27000'],
            ['nombre' => 'Naive Laboratorios',                               'direccion' => 'Dr. Miguel Galindo 224, Colima Centro, Colima C.P. 28000'],
            ['nombre' => 'Laboratorio Arquímedes & Dorsch',                  'direccion' => 'Mariano Matamoros 127, San Jose Guadalupe Otzacatipan, Toluca, Estado de México C.P. 50230'],
            ['nombre' => 'ProMédica Laboratorio de Análisis Clínicos',       'direccion' => 'Avenida Solidaridad 218, Parque de Poblamiento 1a. Secc., Pachuca, Hidalgo C.P. 42032'],
            ['nombre' => 'Laboratorios Quezada',                             'direccion' => 'Avenida Prisciliano Sánchez Sur 120, Centro, Tepic, Nayarit C.P. 63000'],
            ['nombre' => 'Oncocharité',                                      'direccion' => '1a Avenida 1495, Colonia Las Cumbres, Monterrey, Nuevo León C.P. 64610'],
            ['nombre' => 'CLADI',                                            'direccion' => 'Manuel Doblado 1330, Treviño, Monterrey, Nuevo León C.P. 64580'],
            ['nombre' => 'Hidalgo Maldonado (Centro AVE)',                   'direccion' => 'Dr. Fernando Guajardo 155, Los Doctores, Monterrey, Nuevo León C.P. 64710'],
            ['nombre' => 'Laboratorios LAB Sucursal Juárez',                 'direccion' => 'Benito Juárez 285 Ote., Primer Cuadro, Culiacán, Sinaloa C.P. 80000'],
            ['nombre' => 'Laboratorio de Análisis Clínicos Los Arcos',       'direccion' => 'Olivares 2, Los Arcos, Hermosillo, Sonora C.P. 83250'],
            ['nombre' => 'Laboratorio Pasteur',                              'direccion' => 'Ignacio Allende 105 B, Centro, Ciudad Obregón, Sonora C.P. 85000'],
            ['nombre' => 'López Laboratorio',                                'direccion' => 'Hermosillo 425-2, Colonia Granja, Nogales, Sonora C.P. 84065'],
            ['nombre' => 'Bioquim Laboratorio Clínico Zamarripa',            'direccion' => 'Herón Ramirez 786, Colonia Rodriguez, Reynosa, Tamaulipas C.P. 88630'],
            ['nombre' => 'Laboratorio Clínico Pasteur',                      'direccion' => 'Necaxa 300 esq. con Quintero, El Llano, Ciudad Madero, Tamaulipas C.P. 89570'],
            ['nombre' => 'Laboratorio ABG',                                  'direccion' => 'Avenida Valentín Gómez Farías 1849, Col. Supermanzana Ver., Veracruz C.P. 91900'],
            ['nombre' => 'Laboratorio Cornu',                                'direccion' => 'Poniente 4, 26, Centro, Orizaba, Veracruz C.P. 94300'],
            ['nombre' => 'Laboratorios CYALAB',                              'direccion' => 'Calle 20 E por 25, Limones, Mérida, Yucatán C.P. 97219'],
            ['nombre' => 'Laboratorio de Análisis Santa Fé',                 'direccion' => 'Plaza Siglo 21, Blvd. Manuel Talamas Camandari 761, Lote Bravo, Ciudad Juárez, Chihuahua C.P. 32695'],
            ['nombre' => 'Laboratorio Juárez',                               'direccion' => 'Sauces 512, Reforma, Oaxaca C.P. 68050'],
            ['nombre' => 'Clínicos Eureka Laboratorios',                     'direccion' => 'Avenida Fray Luis de León 3071-local 14, Centro Sur, Querétaro C.P. 76093'],
        ];

        foreach ($centros as $centro) {
            DB::table('centros_agenda')->insert([
                'nombre'     => $centro['nombre'],
                'direccion'  => $centro['direccion'],
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::statement('ALTER TABLE centros_agenda AUTO_INCREMENT = 100');
    }
}