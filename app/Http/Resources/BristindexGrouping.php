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
        $yrkesgruppWithPotentialBristindex = $this->yrkesgrupper()->has('bristindex')->first();
        $yrkesprognoser = [];
        $yrkesprognoserPerRegion = [];

        if ($yrkesgruppWithPotentialBristindex) {
            $yrkesprognoser = $yrkesgruppWithPotentialBristindex->getYrkesprognoser();
            $yrkesprognoserPerRegion = $yrkesgruppWithPotentialBristindex->getYrkesprognoserWithFaRegioner();
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'search_similarity' => $this->when(isset($this->similarity), $this->similarity),
            'yrkesprognoser' => Yrkesprognos::collection(
                $yrkesprognoser
            ),
            'yrkesprognoser_per_fa_region' => YrkesprognosFaRegion::collection($yrkesprognoserPerRegion),
            'yrkesgrupper' => YrkesgruppResource::collection($this->whenLoaded('yrkesgrupper')),
        ];
    }
}
