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
        return array_merge([
            'omfang' => (int) $this->omfang,
            'artal' => $this->artal,
            'varde' => (float) $this->bristindex,
            'fa_region_id' => $this->faRegion ? $this->faRegion->id : null,
        ], $this->meta);
    }
}
