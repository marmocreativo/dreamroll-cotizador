<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductosSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'nombre'          => 'Playera Cuello Redondo Algodón',
                'descripcion'     => 'Playera 100% algodón, disponible en varias tallas y colores, ideal para bordado o serigrafía.',
                'precio_unitario' => 85.00,
                'activo'          => true,
            ],
            [
                'nombre'          => 'Taza Cerámica Blanca 11oz',
                'descripcion'     => 'Taza de cerámica clásica, apta para sublimación.',
                'precio_unitario' => 45.00,
                'activo'          => true,
            ],
            [
                'nombre'          => 'Termo Acero Inoxidable 500ml',
                'descripcion'     => 'Termo doble pared, mantiene temperatura hasta 6 horas, grabado láser disponible.',
                'precio_unitario' => 180.00,
                'activo'          => true,
            ],
            [
                'nombre'          => 'Bolígrafo Plástico Ecológico',
                'descripcion'     => 'Bolígrafo de tinta negra, cuerpo de plástico reciclado, personalizable con tampografía.',
                'precio_unitario' => 8.50,
                'activo'          => true,
            ],
            [
                'nombre'          => 'Libreta A5 Pasta Dura',
                'descripcion'     => 'Libreta de 80 hojas rayadas, pasta dura personalizable, incluye elástico y separador.',
                'precio_unitario' => 65.00,
                'activo'          => true,
            ],
            [
                'nombre'          => 'Mochila Ejecutiva Impermeable',
                'descripcion'     => 'Mochila con compartimento para laptop 15", material impermeable, bordado disponible.',
                'precio_unitario' => 420.00,
                'activo'          => true,
            ],
            [
                'nombre'          => 'Gorra Ajustable Bordada',
                'descripcion'     => 'Gorra de algodón con cierre ajustable, área frontal para bordado o parche.',
                'precio_unitario' => 95.00,
                'activo'          => true,
            ],
            [
                'nombre'          => 'Power Bank 10000mAh',
                'descripcion'     => 'Batería portátil con doble puerto USB, grabado láser en carcasa.',
                'precio_unitario' => 250.00,
                'activo'          => true,
            ],
            [
                'nombre'          => 'USB 16GB Personalizable',
                'descripcion'     => 'Memoria USB 2.0, cuerpo plástico o metálico, impresión a color o grabado láser.',
                'precio_unitario' => 110.00,
                'activo'          => true,
            ],
            [
                'nombre'          => 'Llavero Metálico Grabado',
                'descripcion'     => 'Llavero de aleación metálica, grabado láser de logo.',
                'precio_unitario' => 35.00,
                'activo'          => true,
            ],
            [
                'nombre'          => 'Vaso Térmico con Popote 700ml',
                'descripcion'     => 'Vaso térmico de plástico doble pared, popote reutilizable incluido, impresión UV.',
                'precio_unitario' => 130.00,
                'activo'          => true,
            ],
            [
                'nombre'          => 'Toalla Deportiva Microfibra',
                'descripcion'     => 'Toalla de secado rápido, ideal para gimnasios y eventos deportivos, bordado disponible.',
                'precio_unitario' => 75.00,
                'activo'          => true,
            ],
            [
                'nombre'          => 'Set de Escritorio Ejecutivo',
                'descripcion'     => 'Incluye pluma metálica, porta tarjetas y libreta pequeña, presentación en caja de regalo.',
                'precio_unitario' => 310.00,
                'activo'          => true,
            ],
            [
                'nombre'          => 'Bolsa Ecológica de Tela',
                'descripcion'     => 'Bolsa de manta de algodón, resistente, ideal para impresión serigráfica de gran formato.',
                'precio_unitario' => 40.00,
                'activo'          => true,
            ],
            [
                'nombre'          => 'Paraguas Automático Publicitario',
                'descripcion'     => 'Paraguas de apertura automática, tela resistente al viento, área de impresión en 4 paneles.',
                'precio_unitario' => 195.00,
                'activo'          => true,
            ],
            [
                'nombre'          => 'Cargador Inalámbrico 10W',
                'descripcion'     => 'Base de carga inalámbrica compatible con la mayoría de smartphones, superficie personalizable.',
                'precio_unitario' => 220.00,
                'activo'          => true,
            ],
            [
                'nombre'          => 'Chamarra Rompevientos',
                'descripcion'     => 'Chamarra ligera impermeable, ideal para uniformes corporativos, bordado en pecho y espalda.',
                'precio_unitario' => 380.00,
                'activo'          => true,
            ],
            [
                'nombre'          => 'Agenda Ejecutiva Anual',
                'descripcion'     => 'Agenda con pasta piel sintética, calendario anual, personalización en portada.',
                'precio_unitario' => 145.00,
                'activo'          => true,
            ],
            [
                'nombre'          => 'Cable USB Multipuerto 3 en 1',
                'descripcion'     => 'Cable de carga con conectores Lightning, USB-C y Micro USB, empaque personalizable.',
                'precio_unitario' => 60.00,
                'activo'          => true,
            ],
            [
                'nombre'          => 'Cojín Publicitario para Auto',
                'descripcion'     => 'Cojín para volante o asiento, tela lavable, impresión sublimada de logo.',
                'precio_unitario' => 90.00,
                'activo'          => true,
            ],
        ];

        foreach ($productos as $producto) {
            Producto::updateOrCreate(
                ['nombre' => $producto['nombre']],
                $producto
            );
        }

        $this->command->info('✓ ' . count($productos) . ' productos publicitarios cargados correctamente.');
    }
}