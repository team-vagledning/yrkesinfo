<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Yrkesprognos extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'omfang' => (int) $this->omfang,
            'artal' => $this->artal,
            'varde' => (float) $this->bristindex,
            'ingress' => $this->meta['ingress'],
            'stycke1' => $this->meta['stycke1'],
            'stycke2' => $this->meta['stycke2'],
            'stycke3' => $this->meta['stycke3'],
        ];
    }
}
