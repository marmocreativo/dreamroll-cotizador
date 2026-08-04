<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 32px; }
        h1 { color: #002745; font-size: 18px; }
        .fecha { color: #666; font-size: 11px; margin-bottom: 24px; }
        .intro { margin-bottom: 20px; color: #444; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        thead tr { background-color: #002745; color: #fff; }
        thead th { padding: 8px 10px; text-align: left; font-size: 11px; text-transform: uppercase; }
        tbody tr { border-bottom: 1px solid #e5e7eb; }
        tbody td { padding: 8px 10px; font-size: 13px; }
        .label { color: #888; font-size: 11px; text-transform: uppercase; }
        .notas { margin-top: 20px; padding: 10px; background: #f9fafb; border-left: 3px solid #002745; font-size: 11px; }
        .footer { margin-top: 40px; font-size: 11px; color: #888; border-top: 1px solid #e5e7eb; padding-top: 12px; }
    </style>
</head>
<body>

    <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('logo_azul.png'))) }}"
     style="height: 45px; width: auto; margin-bottom: 8px;" />
    <div class="fecha">Ciudad de México a {{ now()->isoFormat('D [de] MMMM [de] YYYY') }}</div>

    <p class="intro">
        Estimado/a <strong>{{ $cita->paciente->nombre_display }}</strong>,<br>
        le confirmamos su cita con los siguientes datos:
    </p>

    <table>
        <thead>
            <tr>
                <th>Detalle</th>
                <th>Información</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="label">Fecha</td>
                <td><strong>{{ $cita->fecha->translatedFormat('l d \d\e F \d\e Y') }}</strong></td>
            </tr>
            <tr>
                <td class="label">Hora</td>
                <td><strong>{{ $cita->hora }}</strong></td>
            </tr>
            <tr>
                <td class="label">Centro</td>
                <td>
                    <strong>{{ $cita->centro->nombre }}</strong>
                    @if($cita->centro->direccion)
                        <br><span style="font-size:11px; color:#666;">{{ $cita->centro->direccion }}</span>
                    @endif
                </td>
            </tr>
            @if($cita->paciente->edad || $cita->paciente->sexo)
            <tr>
                <td class="label">Paciente</td>
                <td>
                    {{ $cita->paciente->nombre_display }}
                    @if($cita->paciente->edad)
                        &nbsp;·&nbsp; {{ $cita->paciente->edad }} años
                    @endif
                    @if($cita->paciente->sexo)
                        &nbsp;·&nbsp; {{ match($cita->paciente->sexo) { 'M' => 'Masculino', 'F' => 'Femenino', default => 'Otro' } }}
                    @endif
                </td>
            </tr>
            @endif
            <tr>
                <td class="label">Estado</td>
                <td>{{ ucfirst($cita->estado) }}</td>
            </tr>
        </tbody>
    </table>

    @if($cita->notas)
    <div class="notas">
        <strong>Notas:</strong><br>
        {{ $cita->notas }}
    </div>
    @endif

    <div class="footer">
        Si tiene alguna duda, no dude en contactarnos.<br><br>
        Cordialmente<br>
        <strong>GeneticLab</strong>
    </div>

</body>
</html>