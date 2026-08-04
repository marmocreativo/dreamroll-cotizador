<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'folio', 'anonimo',
        'iniciales', 'nombre', 'apellido_paterno', 'apellido_materno',
        'fecha_nacimiento', 'edad', 'sexo',
        'whatsapp', 'correo', 'notas',
    ];

    protected $casts = [
        'anonimo'          => 'boolean',
        'fecha_nacimiento' => 'date',
    ];

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    /**
     * Nombre para mostrar: iniciales, nombre completo o "Anónimo + folio"
     */
    public function getNombreDisplayAttribute(): string
    {
        if ($this->anonimo) {
            return 'Anónimo — ' . $this->folio;
        }
        /*
        if ($this->iniciales) {
            return $this->iniciales;
        }
        */

        return trim(implode(' ', array_filter([
            $this->nombre,
            $this->apellido_paterno,
            $this->apellido_materno,
        ]))) ?: $this->folio;
    }

    /**
     * Genera el folio automáticamente antes de crear
     */
    protected static function booted(): void
    {
        static::creating(function (Paciente $paciente) {
            if (empty($paciente->folio)) {
                $year    = now()->year;
                $ultimo  = static::whereYear('created_at', $year)->withTrashed()->count() + 1;
                $paciente->folio = sprintf('PAC-%d-%05d', $year, $ultimo);
            }
        });
    }
}