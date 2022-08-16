<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Yrkesbenamning as YrkesbenamningResource;

class Kommun extends JsonResource
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
            'id' => $this->id,
            'external_id' => $this->external_id,
            'name' => $this->name,
            'region' => $this->when(isset($this->region), [
                'id' => $this->region->id,
                'external_id' => $this->region->external_id,
                'name' => $this->region->name,
            ]),
            'fa_region' => $this->when(isset($this->faRegion), [
                'id' => $this->faRegion->id,
                'external_id' => $this->faRegion->external_id,
                'name' => $this->faRegion->name,
            ]),
        ];
    }
}
