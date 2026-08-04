<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'paciente_id', 'centro_id',
        'fecha', 'hora', 'estado', 'notas',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function centro(): BelongsTo
    {
        return $this->belongsTo(CentroAgenda::class, 'centro_id');
    }
}