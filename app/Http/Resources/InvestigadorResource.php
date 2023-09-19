<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvestigadorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'orcid' => $this->orcid,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'palabras_clave' => json_decode($this->palabras_clave),
            'correo_principal' => $this->correo_principal,
        ];
    }
}
