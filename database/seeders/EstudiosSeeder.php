<?php

namespace Database\Seeders;

use App\Models\Estudio;
use Illuminate\Database\Seeder;

class EstudiosSeeder extends Seeder
{
    public function run(): void
    {
        $estudios = [
            [
                'nombre'           => 'Idylla™ Panel KRAS-NRAS-BRAF Mutation Test (CE-IVD)',
                'especimen'        => 'Biopsia sólida — Tejido FFPE de adenocarcinoma colorrectal',
                'area_terapeutica' => 'Oncología colorrectal',
                'tiempo_respuesta' => '72-96 horas',
                'precio_unitario'  => 12000.00,
                'activo'           => true,
            ],
            [
                'nombre'           => 'Idylla™ KRAS Mutation Test (CE-IVD)',
                'especimen'        => 'Biopsia sólida — Tejido FFPE de adenocarcinoma colorrectal',
                'area_terapeutica' => 'Oncología colorrectal',
                'tiempo_respuesta' => '48 horas',
                'precio_unitario'  => 7000.00,
                'activo'           => true,
            ],
            [
                'nombre'           => 'Idylla™ MSI Test (CE-IVD)',
                'especimen'        => 'Biopsia sólida — Tejido FFPE de adenocarcinoma colorrectal',
                'area_terapeutica' => 'Oncología colorrectal',
                'tiempo_respuesta' => '72-96 horas',
                'precio_unitario'  => 7000.00,
                'activo'           => true,
            ],
            [
                'nombre'           => 'Idylla™ EGFR Mutation Test (CE-IVD)',
                'especimen'        => 'Biopsia sólida — Tejido humano (FFPE)',
                'area_terapeutica' => 'Oncología pulmonar (NSCLC)',
                'tiempo_respuesta' => '72-96 horas',
                'precio_unitario'  => 7000.00,
                'activo'           => true,
            ],
            [
                'nombre'           => 'Idylla™ GeneFusion Panel (CE-IVD)',
                'especimen'        => 'Biopsia sólida — Tejido humano (FFPE)',
                'area_terapeutica' => 'Oncología pulmonar (NSCLC)',
                'tiempo_respuesta' => '72-96 horas',
                'precio_unitario'  => 10900.00,
                'activo'           => true,
            ],
            [
                'nombre'           => 'Idylla™ Panel RAS BL — ctKRAS + ctNRAS-BRAF Mutation Test (CE-IVD)',
                'especimen'        => 'Biopsia líquida — 10 ml de plasma en tubos PAXgene Blood DNA Tube',
                'area_terapeutica' => 'Oncología colorrectal',
                'tiempo_respuesta' => '24-48 horas',
                'precio_unitario'  => 12250.00,
                'activo'           => true,
            ],
            [
                'nombre'           => 'Idylla™ ctKRAS Mutation Test (CE-IVD)',
                'especimen'        => 'Biopsia líquida — 10 ml de plasma en tubos PAXgene Blood DNA Tube',
                'area_terapeutica' => 'Oncología colorrectal',
                'tiempo_respuesta' => '24-48 horas',
                'precio_unitario'  => 7250.00,
                'activo'           => true,
            ],
            [
                'nombre'           => 'Idylla™ ctEGFR Mutation Test (CE-IVD)',
                'especimen'        => 'Biopsia líquida — 10 ml de plasma en tubos PAXgene Blood DNA Tube',
                'area_terapeutica' => 'Oncología pulmonar (NSCLC)',
                'tiempo_respuesta' => '72-96 horas',
                'precio_unitario'  => 7250.00,
                'activo'           => true,
            ],
            [
                'nombre'           => 'Idylla™ BRAF Mutation Test (CE-IVD)',
                'especimen'        => 'Biopsia sólida — Tejido humano (FFPE)',
                'area_terapeutica' => 'Oncología — Melanoma / NSCLC / Colorrectal',
                'tiempo_respuesta' => '72-96 horas',
                'precio_unitario'  => 7000.00,
                'activo'           => true,
            ],
            [
                'nombre'           => 'Idylla™ POLE-POLD1 Mutation Assay (CE-IVD)',
                'especimen'        => 'Biopsia sólida — Sección FFPE 5µm (50-600 mm²) o 10µm (25-300 mm²)',
                'area_terapeutica' => 'Oncología ginecológica — Endometrio',
                'tiempo_respuesta' => '5-7 días hábiles',
                'precio_unitario'  => 9950.00,
                'activo'           => true,
            ],
            [
                'nombre'           => 'Idylla™ PIK3CA-AKT1 Mutation Assay (CE-IVD)',
                'especimen'        => 'Biopsia sólida — Tejido FFPE (mínimo 20% células tumorales)',
                'area_terapeutica' => 'Oncología — Mama HR+ HER2-',
                'tiempo_respuesta' => '72-96 horas',
                'precio_unitario'  => 11000.00,
                'activo'           => true,
            ],
            [
                'nombre'           => 'Idylla™ ThyroidPrint® Assay (CE-IVD)',
                'especimen'        => 'Biopsia por aspiración con aguja fina (FNA/AAF) — Nódulo tiroideo Bethesda III/IV',
                'area_terapeutica' => 'Oncología — Tiroides',
                'tiempo_respuesta' => '72-96 horas',
                'precio_unitario'  => 39200.00,
                'activo'           => true,
            ],
            [
                'nombre'           => 'PDL-1 (IHQ)',
                'especimen'        => 'Biopsia sólida — Tejido humano (FFPE)',
                'area_terapeutica' => 'Oncología — NSCLC / Gástrico / Vejiga / Cervical',
                'tiempo_respuesta' => '5-7 días hábiles',
                'precio_unitario'  => 7500.00,
                'activo'           => true,
            ],
            [
                'nombre'           => 'FISH HER2',
                'especimen'        => 'Biopsia sólida — Bloque de parafina / Laminillas HER2 para hibridación',
                'area_terapeutica' => 'Oncología — Mama / Gástrico / Colorrectal',
                'tiempo_respuesta' => '10 días hábiles',
                'precio_unitario'  => 6500.00,
                'activo'           => true,
            ],
            [
                'nombre'           => 'Detección y Genotipificación de VPH (Panel Amplio)',
                'especimen'        => 'Biopsia sólida o vial base líquida',
                'area_terapeutica' => 'Oncología — Cervical',
                'tiempo_respuesta' => '5-7 días hábiles',
                'precio_unitario'  => 4400.00,
                'activo'           => true,
            ],
            [
                'nombre'           => 'Secuenciación NGS — Genes BRCA1 y BRCA2',
                'especimen'        => 'Biopsia sólida — Tejido FFPE (mínimo 20% células tumorales)',
                'area_terapeutica' => 'Oncología — Mama / Ovario',
                'tiempo_respuesta' => '10 días hábiles',
                'precio_unitario'  => 17000.00,
                'activo'           => true,
            ],
        ];

        foreach ($estudios as $estudio) {
            Estudio::updateOrCreate(
                ['nombre' => $estudio['nombre']],
                $estudio
            );
        }

        $this->command->info('✓ ' . count($estudios) . ' estudios cargados correctamente.');
    }
}