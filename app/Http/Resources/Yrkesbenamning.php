<?php

namespace App\Http\Resources;

use App\Http\Resources\YrkesgruppSimple as YrkesgruppSimpleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class Yrkesbenamning extends JsonResource
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
            'name' => $this->name,
            'external_id' => $this->external_id,
            'search_similarity' => $this->when(isset($this->similarity), $this->similarity),
            'yrkesgrupper' => YrkesgruppSimpleResource::collection($this->whenLoaded('yrkesgrupper')),
        ];
    }
}
