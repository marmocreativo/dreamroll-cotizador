<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CotizacionExcelImporter
{
    /**
     * Nombres de hoja que se ignoran siempre (no son grupos de productos).
     */
    private const HOJAS_IGNORADAS = ['resumen', 'resúmen', 'summary'];

    /**
     * Resultado de la importación:
     * [
     *   'productos'   => [ ['grupo' => ..., 'dia' => ..., 'concepto' => ..., ...], ... ],
     *   'hospedajes'  => [ ['nombre' => ..., 'checkin' => ..., ...], ... ],
     *   'advertencias'=> [ 'Hoja X: no se detectó columna concepto', ... ],
     * ]
     */
    public function importar(string $rutaArchivo): array
    {
        $spreadsheet = IOFactory::load($rutaArchivo);

        $productos    = [];
        $hospedajes   = [];
        $advertencias = [];

        foreach ($spreadsheet->getWorksheetIterator() as $hoja) {
            $nombreHoja = trim($hoja->getTitle());

            if (in_array(mb_strtolower($nombreHoja), self::HOJAS_IGNORADAS, true)) {
                continue;
            }

            $resultado = $this->parsearHoja($hoja, $nombreHoja);

            if ($resultado === null) {
                $advertencias[] = "Hoja \"{$nombreHoja}\": no se encontró un encabezado reconocible, se omitió.";
                continue;
            }

            if ($resultado['tipo'] === 'hospedaje') {
                $hospedajes = array_merge($hospedajes, $resultado['filas']);
            } else {
                $productos = array_merge($productos, $resultado['filas']);
            }
        }

        return compact('productos', 'hospedajes', 'advertencias');
    }

    /**
     * Recorre una hoja completa. Puede contener uno o varios sub-bloques
     * (el header se re-detecta cada vez que reaparece, como en "Alimentos y Extras").
     */
    private function parsearHoja(Worksheet $hoja, string $nombreHoja): ?array
    {
        $filasCrudas = $hoja->toArray(null, true, true, false);

        $columnas = null; // mapa campo => índice, se define al detectar cada header
        $esHospedaje = null; // se decide con el primer header válido de la hoja
        $filasResultado = [];

        foreach ($filasCrudas as $fila) {
            if ($this->esFilaHeader($fila)) {
                $columnas = $this->mapearColumnas($fila);

                if ($esHospedaje === null) {
                    $esHospedaje = $this->esHeaderDeHospedaje($columnas);
                }

                continue;
            }

            if ($columnas === null) {
                continue; // aún no vimos ningún header (título de hoja, etc.)
            }

            if ($this->esFilaVacia($fila) || $this->esFilaTotal($fila)) {
                continue;
            }

            $fila = $this->normalizarFila($fila);

            $filasResultado[] = $esHospedaje
                ? $this->extraerHospedaje($fila, $columnas)
                : $this->extraerProducto($fila, $columnas, $nombreHoja);
        }

        if ($columnas === null) {
            return null; // hoja sin ningún header reconocible en toda su extensión
        }

        return [
            'tipo'  => $esHospedaje ? 'hospedaje' : 'producto',
            'filas' => $filasResultado,
        ];
    }

    // ──────────────────────────────────────────────
    // Detección de filas
    // ──────────────────────────────────────────────

    private function esFilaHeader(array $fila): bool
    {
        $texto = mb_strtolower(implode('|', array_map(fn($v) => (string) $v, $fila)));

        $esHeaderProducto = str_contains($texto, 'concepto')
            && (str_contains($texto, 'precio') || str_contains($texto, 'costo'));

        $esHeaderHospedaje = str_contains($texto, 'check in')
            && str_contains($texto, 'noches');

        return $esHeaderProducto || $esHeaderHospedaje;
    }

    private function esFilaTotal(array $fila): bool
    {
        $primera = mb_strtolower(trim((string) ($fila[0] ?? '')));

        return str_starts_with($primera, 'total') || str_contains($primera, 'gran total');
    }

    private function esFilaVacia(array $fila): bool
    {
        foreach ($fila as $valor) {
            if (trim((string) $valor) !== '') {
                return false;
            }
        }

        return true;
    }

    // ──────────────────────────────────────────────
    // Mapeo dinámico de columnas por nombre
    // ──────────────────────────────────────────────

    private function mapearColumnas(array $fila): array
    {
        $mapa = [];
        $headersOriginales = [];

        foreach ($fila as $idx => $valor) {
            $texto = trim((string) $valor);

            if ($texto === '') {
                continue;
            }

            $headersOriginales[$idx] = $texto;

            $campo = $this->normalizarNombreColumna($texto);

            if ($campo !== null) {
                $mapa[$campo] = $idx;
            }
        }

        $mapa['_headers_originales'] = $headersOriginales;

        return $mapa;
    }

    private function normalizarNombreColumna(string $texto): ?string
    {
        $texto = mb_strtolower(trim($texto));

        return match (true) {
            $texto === '' => null,
            str_contains($texto, 'check in') => 'checkin',
            str_contains($texto, 'check out') => 'checkout',
            str_contains($texto, 'noches') => 'noches',
            str_contains($texto, 'habs') || str_contains($texto, 'habitaciones') => 'habitaciones',
            str_contains($texto, 'resort fee') => 'resort_fee',
            str_contains($texto, 'bell boys') => 'bell_boys',
            str_contains($texto, 'camaristas') => 'camaristas',
            str_contains($texto, 'ish') => 'ish_porcentaje',
            str_contains($texto, 'día') || str_contains($texto, 'dia') => 'dia',
            str_contains($texto, 'horario') => 'horario',
            str_contains($texto, 'lugar') || str_contains($texto, 'nombre') => 'nombre_o_lugar',
            str_contains($texto, 'concepto') => 'concepto',
            str_contains($texto, 'precio') || str_contains($texto, 'costo unit') || str_contains($texto, 'costo') => 'precio_unitario',
            str_contains($texto, 'cantidad') => 'cantidad',
            str_contains($texto, 'sub-total') || str_contains($texto, 'subtotal') => 'subtotal_excel',
            str_contains($texto, 'iva') => 'iva_porcentaje',
            str_contains($texto, 'serv') => 'servicio_porcentaje',
            default => null,
        };
    }

    private function esHeaderDeHospedaje(array $columnas): bool
    {
        return isset($columnas['checkin']) && isset($columnas['noches']);
    }

    // ──────────────────────────────────────────────
    // Extracción de filas ya identificadas
    // ──────────────────────────────────────────────

        private function extraerProducto(array $fila, array $columnas, string $grupo): array
    {
        return [
            'grupo'    => $grupo,
            'dia'      => $this->valor($fila, $columnas, 'dia'),
            'lugar'    => $this->valor($fila, $columnas, 'nombre_o_lugar'),
            'concepto' => (string) $this->valor($fila, $columnas, 'concepto'),
            'precio_unitario' => $this->numero($fila, $columnas, 'precio_unitario'),
            'cantidad'        => $this->numero($fila, $columnas, 'cantidad') ?? 1,
            // NOTA: las columnas de IVA/Servicio del Excel traen el monto ya
            // calculado, no el %, así que se ignoran deliberadamente. El
            // sistema calcula el IVA sobre el subtotal de productos en
            // Cotizacion::recalcular().
        ];
    }

    private function extraerHospedaje(array $fila, array $columnas): array
    {
        // Índices ya cubiertos por un campo conocido (excluye la entrada auxiliar _headers_originales)
        $indicesConocidos = array_values(array_filter(
            $columnas,
            fn($v, $k) => $k !== '_headers_originales',
            ARRAY_FILTER_USE_BOTH
        ));

        $headersOriginales = $columnas['_headers_originales'] ?? [];

        // Cualquier columna con header no reconocido se guarda tal cual en cargos_adicionales
        $cargosAdicionales = [];
        foreach ($headersOriginales as $idx => $textoHeader) {
            if (in_array($idx, $indicesConocidos, true)) {
                continue; // ya mapeada a un campo conocido (checkin, noches, etc.)
            }

            $valor = $fila[$idx] ?? null;

            if ($valor === null || $valor === '') {
                continue;
            }

            $cargosAdicionales[$textoHeader] = is_numeric($valor) ? (float) $valor : $valor;
        }

        return [
            'nombre'             => (string) $this->valor($fila, $columnas, 'nombre_o_lugar'),
            'checkin'            => $this->valor($fila, $columnas, 'checkin'),
            'checkout'           => $this->valor($fila, $columnas, 'checkout'),
            'noches'             => (int) ($this->numero($fila, $columnas, 'noches') ?? 0),
            'habitaciones'       => (int) ($this->numero($fila, $columnas, 'habitaciones') ?? 0),
            'costo_unitario'     => $this->numero($fila, $columnas, 'precio_unitario'),
            // NOTA: ISH/IVA no se importan del Excel (esas columnas traen
            // montos, no porcentajes) -- el usuario los captura manualmente
            // en el wizard, y bell_boys/resort_fee/camaristas sí son montos
            // reales tal cual vienen en el Excel.
            'ish_porcentaje'     => null,
            'iva_porcentaje'     => null,
            'resort_fee'         => $this->numero($fila, $columnas, 'resort_fee'),
            'bell_boys'          => $this->numero($fila, $columnas, 'bell_boys'),
            'camaristas'         => $this->numero($fila, $columnas, 'camaristas'),
            'cargos_adicionales' => $cargosAdicionales ?: null,
        ];
    }

    // ──────────────────────────────────────────────
    // Helpers de lectura de valores
    // ──────────────────────────────────────────────

    private function valor(array $fila, array $columnas, string $campo): mixed
    {
        if (!isset($columnas[$campo])) {
            return null;
        }

        return $fila[$columnas[$campo]] ?? null;
    }

    private function numero(array $fila, array $columnas, string $campo): ?float
    {
        $valor = $this->valor($fila, $columnas, $campo);

        if ($valor === null || $valor === '') {
            return null;
        }

        if (is_numeric($valor)) {
            return (float) $valor;
        }

        // Limpia formatos tipo "$1,770.50", "$ 482.23", "1,066.72 USD"
        $limpio = preg_replace('/[^0-9.\-]/', '', (string) $valor);

        return is_numeric($limpio) && $limpio !== '' ? (float) $limpio : null;
    }

    /**
     * Normaliza celdas de fecha (datetime, string, número) sin forzar
     * conversión a un objeto Carbon/Date -- se guarda como texto tal cual
     * llegó, ya que el formato es inconsistente incluso dentro del mismo
     * archivo (ver hoja "Alimentos y Extras" del Excel de referencia).
     */
    private function normalizarFila(array $fila): array
    {
        return array_map(function ($valor) {
            if ($valor instanceof \DateTimeInterface) {
                return $valor->format('d-M');
            }

            return $valor;
        }, $fila);
    }
}