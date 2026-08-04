<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CentroAgenda extends Model
{
    use SoftDeletes;

    protected $table = 'centros_agenda';

    protected $fillable = ['nombre', 'direccion', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'centro_id');
    }
}