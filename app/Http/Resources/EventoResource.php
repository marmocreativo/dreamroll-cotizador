<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'titulo'      => $this->titulo,
            'descripcion' => $this->descripcion,
            'fecha'       => $this->fecha?->format('Y-m-d'),
            'lugar'       => $this->lugar,
            'cliente'     => $this->cliente,
            'portada_url' => $this->portada_url,
            'galeria'     => GaleriaEventoResource::collection($this->whenLoaded('galeria')),
        ];
    }
}