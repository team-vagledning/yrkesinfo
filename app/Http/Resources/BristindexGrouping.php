<?php

namespace App\Http\Resources;

use App\Http\Resources\Yrkesgrupp as YrkesgruppResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BristindexGrouping extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'yrkesprognoser' => Yrkesprognos::collection(
                $this->yrkesgrupper()->has('bristindex')->first()->getYrkesprognoser()
            ),
            'yrkesgrupper' => YrkesgruppResource::collection($this->whenLoaded('yrkesgrupper')),
        ];
    }
}
