<?php
/**
 * setup-1310.php — Herramienta de despliegue 1310 Studio
 * ⚠️  Eliminar después de cada sesión de mantenimiento.
 *
 * Coloca este archivo en:
 * /home/n779715/public_html/1310studio.mx/setup-1310.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ── Rutas absolutas del proyecto ──────────────────────────────────────────────
define('APP_ROOT',    dirname(__DIR__, 2) . '/1310studio');
define('PUBLIC_ROOT', __DIR__);

// ── Cargar Laravel ────────────────────────────────────────────────────────────
$laravelOk = false;
$laravelError = null;

try {
    require APP_ROOT . '/vendor/autoload.php';
    $app    = require_once APP_ROOT . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle(Illuminate\Http\Request::capture());
    $laravelOk = true;
} catch (Throwable $e) {
    $laravelError = $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine();
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function artisan(string $cmd, array $params = []): string {
    Illuminate\Support\Facades\Artisan::call($cmd, $params);
    return trim(Illuminate\Support\Facades\Artisan::output());
}

function ok(string $msg): string  { return "<span class='ok'>✅ {$msg}</span>"; }
function err(string $msg): string { return "<span class='err'>❌ {$msg}</span>"; }
function warn(string $msg): string{ return "<span class='warn'>⚠️  {$msg}</span>"; }
function info(string $msg): string{ return "<span class='info'>ℹ️  {$msg}</span>"; }

// ── Procesar acción POST ──────────────────────────────────────────────────────
$result = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $laravelOk) {
    $action = $_POST['action'];
    ob_start();

    try {
        switch ($action) {

            // ── ENV ───────────────────────────────────────────────────────────
            case 'env':
                echo "<strong>APLICACIÓN</strong>\n";
                echo sprintf("  %-28s %s\n", 'APP_NAME',  config('app.name'));
                echo sprintf("  %-28s %s\n", 'APP_ENV',   config('app.env'));
                echo sprintf("  %-28s %s\n", 'APP_DEBUG', config('app.debug') ? warn('true') : ok('false'));
                echo sprintf("  %-28s %s\n", 'APP_URL',   config('app.url'));
                echo sprintf("  %-28s %s\n", 'APP_KEY',   config('app.key') ? ok('Configurada') : err('NO CONFIGURADA'));

                echo "\n<strong>BASE DE DATOS</strong>\n";
                echo sprintf("  %-28s %s\n", 'DB_CONNECTION', config('database.default'));
                echo sprintf("  %-28s %s\n", 'DB_HOST',       config('database.connections.mysql.host'));
                echo sprintf("  %-28s %s\n", 'DB_PORT',       config('database.connections.mysql.port'));
                echo sprintf("  %-28s %s\n", 'DB_DATABASE',   config('database.connections.mysql.database'));
                echo sprintf("  %-28s %s\n", 'DB_USERNAME',   config('database.connections.mysql.username'));
                $pass = config('database.connections.mysql.password');
                echo sprintf("  %-28s %s\n", 'DB_PASSWORD',   $pass ? ok('Configurada (' . strlen($pass) . ' chars)') : err('vacía'));

                echo "\n<strong>STORAGE</strong>\n";
                echo sprintf("  %-28s %s\n", 'FILESYSTEM_DISK',        config('filesystems.default'));
                echo sprintf("  %-28s %s\n", 'FILESYSTEM_PUBLIC_ROOT', config('filesystems.disks.public.root'));
                echo sprintf("  %-28s %s\n", 'FILESYSTEM_PUBLIC_URL',  config('filesystems.disks.public.url'));

                echo "\n<strong>SERVICIOS</strong>\n";
                $mpKey = config('services.mercadopago.access_token') ?? env('MERCADOPAGO_ACCESS_TOKEN');
                echo sprintf("  %-28s %s\n", 'MERCADOPAGO_TOKEN',
                    $mpKey
                        ? (str_starts_with($mpKey, 'APP_USR-') ? ok('LIVE token configurado') : warn('TEST token configurado'))
                        : err('NO configurado'));

                echo sprintf("  %-28s %s\n", 'MAIL_MAILER', config('mail.default'));
                echo sprintf("  %-28s %s\n", 'MAIL_HOST',   config('mail.mailers.smtp.host') ?? 'N/A');
                echo sprintf("  %-28s %s\n", 'MAIL_FROM',   config('mail.from.address') ?? 'N/A');
                break;

            // ── TEST DB ───────────────────────────────────────────────────────
            case 'db':
                try {
                    $pdo    = Illuminate\Support\Facades\DB::connection()->getPdo();
                    $dbname = Illuminate\Support\Facades\DB::connection()->getDatabaseName();
                    echo ok("Conexión exitosa → {$dbname}") . "\n\n";

                    $tables = Illuminate\Support\Facades\DB::select('SHOW TABLES');
                    $count  = count($tables);
                    echo ok("{$count} tablas encontradas") . "\n";

                    $names = array_map(fn($t) => array_values((array)$t)[0], $tables);
                    sort($names);
                    foreach ($names as $name) {
                        echo "  · {$name}\n";
                    }
                } catch (Throwable $e) {
                    echo err('No se pudo conectar') . "\n";
                    echo "  Mensaje: " . $e->getMessage() . "\n";
                    echo "\n" . warn('Verifica DB_HOST, DB_DATABASE, DB_USERNAME y DB_PASSWORD en tu .env') . "\n";
                }
                break;

            // ── CLEAR CACHE ───────────────────────────────────────────────────
            case 'clear':
                $cmds = ['config:clear', 'route:clear', 'view:clear', 'cache:clear', 'event:clear'];
                foreach ($cmds as $cmd) {
                    try {
                        $out = artisan($cmd);
                        echo ok($cmd) . ($out ? " → {$out}" : '') . "\n";
                    } catch (Throwable $e) {
                        echo err($cmd) . " → " . $e->getMessage() . "\n";
                    }
                }

                // Limpieza manual de archivos compilados (por si Artisan falla)
                echo "\n<strong>Limpieza manual de bootstrap/cache</strong>\n";
                $cacheDir = APP_ROOT . '/bootstrap/cache';
                $files = glob($cacheDir . '/*.php') ?: [];
                if (empty($files)) {
                    echo info('bootstrap/cache ya estaba vacío') . "\n";
                } else {
                    foreach ($files as $f) {
                        @unlink($f) ? print(ok(basename($f) . ' eliminado') . "\n") : print(warn('No se pudo eliminar ' . basename($f)) . "\n");
                    }
                }

                echo "\n<strong>Limpieza manual de storage/framework/views</strong>\n";
                $viewsDir = APP_ROOT . '/storage/framework/views';
                $views = glob($viewsDir . '/*.php') ?: [];
                echo count($views) > 0
                    ? ok(count($views) . ' archivos de vistas compiladas eliminados') . "\n"
                    : info('Sin vistas compiladas') . "\n";
                foreach ($views as $v) { @unlink($v); }
                break;

            // ── OPTIMIZE ──────────────────────────────────────────────────────
            case 'optimize':
                try {
                    $out = artisan('optimize');
                    echo ok('optimize completado') . "\n{$out}\n";
                } catch (Throwable $e) {
                    echo err('optimize falló') . "\n" . $e->getMessage() . "\n";
                    echo info('Intenta correr clear primero') . "\n";
                }
                break;

            // ── STORAGE INFO ──────────────────────────────────────────────────
            case 'storage':
                $diskRoot = config('filesystems.disks.public.root');
                $diskUrl  = config('filesystems.disks.public.url');

                echo "<strong>Configuración del disco public</strong>\n";
                echo sprintf("  %-20s %s\n", 'root (física):', $diskRoot);
                echo sprintf("  %-20s %s\n", 'url (pública):', $diskUrl);

                echo "\n<strong>¿Existe la carpeta root?</strong>\n";
                if (is_dir($diskRoot)) {
                    echo ok($diskRoot . ' existe') . "\n";
                    echo sprintf("  %-20s %s\n", 'Permisos:', substr(sprintf('%o', fileperms($diskRoot)), -4));
                    echo sprintf("  %-20s %s\n", 'Escribible:', is_writable($diskRoot) ? ok('Sí') : err('No'));
                } else {
                    echo err($diskRoot . ' NO existe') . "\n";
                    echo info('Crear la carpeta manualmente vía FTP o cPanel') . "\n";
                }

                echo "\n<strong>Subcarpetas de media</strong>\n";
                $folders = ['categorias', 'productos/galeria', 'directorio-floral', 'directorio-floral/galeria', 'talleres', 'slides/fondos', 'slides/logos', 'slides/overlays', 'slides/videos'];
                foreach ($folders as $folder) {
                    $path = $diskRoot . '/' . $folder;
                    if (is_dir($path)) {
                        $files = count(array_diff(scandir($path), ['.', '..']));
                        echo ok($folder) . " ({$files} archivos)\n";
                    } else {
                        echo warn($folder . ' no existe aún') . "\n";
                    }
                }

                echo "\n<strong>Test de escritura</strong>\n";
                $testFile = '_test_write_' . time() . '.txt';
                try {
                    Illuminate\Support\Facades\Storage::disk('public')->put($testFile, 'test-' . date('c'));
                    echo ok("Storage::disk('public')->put() funciona") . "\n";
                    $publicPath = $diskRoot . '/' . $testFile;
                    echo is_file($publicPath)
                        ? ok('Archivo accesible en ruta física') . "\n"
                        : warn('Archivo no encontrado en ruta física (¿ruta incorrecta?)') . "\n";
                    Illuminate\Support\Facades\Storage::disk('public')->delete($testFile);
                    echo info('Archivo de prueba eliminado') . "\n";
                } catch (Throwable $e) {
                    echo err('Error de escritura: ' . $e->getMessage()) . "\n";
                }

                echo "\n<strong>URL de ejemplo</strong>\n";
                echo "  " . $diskUrl . "/categorias/ejemplo.webp\n";
                break;

            default:
                echo warn('Acción desconocida') . "\n";
        }
    } catch (Throwable $e) {
        echo err('Error inesperado') . "\n";
        echo $e->getMessage() . "\n";
        echo "En " . $e->getFile() . ':' . $e->getLine() . "\n";
    }

    $result = ob_get_clean();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Setup — 1310 Studio</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Courier New', monospace; background: #0f0f0f; color: #d4d4d4; min-height: 100vh; padding: 2rem; }
        .wrap { max-width: 860px; margin: 0 auto; }

        header { border-bottom: 1px solid #333; padding-bottom: 1.5rem; margin-bottom: 2rem; }
        header h1 { font-family: Georgia, serif; font-size: 1.6rem; color: #c9a96e; font-weight: normal; letter-spacing: .05em; }
        header p  { font-size: .75rem; color: #666; margin-top: .4rem; }

        .warning { background: #3a1a00; border: 1px solid #c9603e; border-radius: 4px; padding: .75rem 1rem; margin-bottom: 2rem; font-size: .8rem; color: #f0a070; }

        .status { background: #1a1a1a; border: 1px solid #333; border-radius: 4px; padding: .75rem 1rem; margin-bottom: 2rem; font-size: .8rem; }
        .status .ok  { color: #6dbf67; }
        .status .err { color: #e06c6c; }

        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: .75rem; margin-bottom: 2rem; }

        form.action-form { display: contents; }

        button {
            width: 100%;
            background: #1e1e1e;
            border: 1px solid #444;
            color: #c9a96e;
            padding: .75rem 1rem;
            font-family: 'Courier New', monospace;
            font-size: .8rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            cursor: pointer;
            border-radius: 4px;
            transition: background .2s, border-color .2s;
            text-align: left;
        }
        button:hover { background: #2a2a2a; border-color: #c9a96e; }
        button .icon { display: block; font-size: 1.2rem; margin-bottom: .35rem; }
        button .label { display: block; }
        button .desc { display: block; font-size: .65rem; color: #666; margin-top: .2rem; text-transform: none; letter-spacing: 0; }

        .output-wrap { background: #111; border: 1px solid #333; border-radius: 4px; padding: 1.25rem 1.5rem; }
        .output-wrap h2 { font-size: .8rem; color: #888; text-transform: uppercase; letter-spacing: .1em; margin-bottom: 1rem; border-bottom: 1px solid #222; padding-bottom: .5rem; }
        .output-wrap pre { font-size: .8rem; line-height: 1.7; white-space: pre-wrap; word-break: break-all; }

        .ok   { color: #6dbf67; }
        .err  { color: #e06c6c; }
        .warn { color: #e0b96c; }
        .info { color: #6cb4e0; }

        footer { margin-top: 3rem; padding-top: 1rem; border-top: 1px solid #222; font-size: .7rem; color: #444; }
    </style>
</head>
<body>
<div class="wrap">

    <header>
        <h1>1310 Studio — Setup</h1>
        <p>Herramienta de despliegue y diagnóstico · <?= date('Y-m-d H:i:s') ?> · PHP <?= PHP_VERSION ?></p>
    </header>

    <div class="warning">
        ⚠️ &nbsp;Este archivo debe eliminarse después de cada sesión de mantenimiento. No dejes este archivo en producción.
    </div>

    <div class="status">
        Laravel:
        <?php if ($laravelOk): ?>
            <span class="ok">✅ cargado correctamente</span> · <?= config('app.name') ?> · <?= config('app.env') ?>
        <?php else: ?>
            <span class="err">❌ error al cargar: <?= htmlspecialchars($laravelError) ?></span>
        <?php endif; ?>
    </div>

    <?php if ($laravelOk): ?>
    <div class="grid">

        <form class="action-form" method="POST">
            <button type="submit" name="action" value="env">
                <span class="icon">🔍</span>
                <span class="label">Variables ENV</span>
                <span class="desc">App, DB, Storage, Servicios</span>
            </button>
        </form>

        <form class="action-form" method="POST">
            <button type="submit" name="action" value="db">
                <span class="icon">🗄️</span>
                <span class="label">Test Base de Datos</span>
                <span class="desc">Conexión + listado de tablas</span>
            </button>
        </form>

        <form class="action-form" method="POST">
            <button type="submit" name="action" value="clear">
                <span class="icon">🧹</span>
                <span class="label">Limpiar Caché</span>
                <span class="desc">Config, rutas, vistas, eventos</span>
            </button>
        </form>

        <form class="action-form" method="POST">
            <button type="submit" name="action" value="optimize">
                <span class="icon">⚡</span>
                <span class="label">Optimize</span>
                <span class="desc">php artisan optimize</span>
            </button>
        </form>

        <form class="action-form" method="POST">
            <button type="submit" name="action" value="storage">
                <span class="icon">📁</span>
                <span class="label">Storage</span>
                <span class="desc">Rutas, permisos, test escritura</span>
            </button>
        </form>

    </div>
    <?php endif; ?>

    <?php if ($result): ?>
    <div class="output-wrap">
        <h2>Resultado</h2>
        <pre><?= $result ?></pre>
    </div>
    <?php endif; ?>

    <footer>
        APP_ROOT: <?= APP_ROOT ?> &nbsp;·&nbsp;
        PUBLIC_ROOT: <?= PUBLIC_ROOT ?> &nbsp;·&nbsp;
        Memory: <?= round(memory_get_usage()/1024/1024, 1) ?> MB
    </footer>

</div>
</body>
</html>