<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Yrkesgrupp extends JsonResource
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
            'ssyk' => $this->ssyk,
            'alternative_ssyk' => $this->alternative_ssyk,
            'name' => $this->name,
            'description' => $this->description,
            'anstallda' => $this->aggregated_statistics['anstallda'],
            'sektorer' => $this->aggregated_statistics['sektorer'],
            'bristindex' => $this->aggregated_statistics['bristindex'],
            'ledigaJobb' => $this->aggregated_statistics['ledigaJobb'],
            'regioner' => $this->aggregated_statistics['regioner'],
            'yrkesgrupper' => self::collection($this->when(isset($this->siblings), $this->siblings)),
        ];
    }
}