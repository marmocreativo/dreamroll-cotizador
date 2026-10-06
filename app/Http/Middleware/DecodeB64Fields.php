<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Decodifica campos enviados en Base64 con sufijo "__b64"
 * (workaround para ModSecurity que bloquea HTML en el POST).
 * Ej.: "condiciones__b64" => se restaura como "condiciones".
 */
class DecodeB64Fields
{
    public function handle(Request $request, Closure $next)
    {
        $decodificados = [];

        foreach ($request->input() as $campo => $valor) {
            if (is_string($campo) && str_ends_with($campo, '__b64')) {
                $real = substr($campo, 0, -5);
                $texto = base64_decode((string) $valor, true);
                $decodificados[$real] = $texto === false ? '' : $texto;
            }
        }

        if ($decodificados) {
            $request->merge($decodificados);
        }

        return $next($request);
    }
}