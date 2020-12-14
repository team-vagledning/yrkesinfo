<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Yrkesgrupp as YrkesgruppResource;
use App\Http\Resources\Text as TextResource;

class Yrkesomrade extends JsonResource
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
            'old_yrkesinfo_id' => $this->extras['old_yrkesinfo_id'],
            'name' => $this->name,
            'description' => $this->description,
            'texter' => TextResource::collection($this->whenLoaded('texts')),
            'loner' => $this->aggregated_statistics['lon'],
            'ledigaJobb' => $this->aggregated_statistics['ledigaJobb'],
            'anstallda' => $this->aggregated_statistics['anstallda'],
            'sektorer' => $this->aggregated_statistics['sektorer'],
            'regioner' => $this->aggregated_statistics['regioner'],
            'utbildningsstege' => $this->aggregated_statistics['utbildningsstege'],
            'bristindex' => $this->getBristindexes(),
            'yrkesgrupper'=> YrkesgruppResource::collection($this->whenLoaded('yrkesgrupper')),
        ];
    }
}
