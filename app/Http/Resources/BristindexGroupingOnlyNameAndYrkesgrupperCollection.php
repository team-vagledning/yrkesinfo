<?php

namespace App\Http\Resources;

use App\Http\Resources\Yrkesgrupp as YrkesgruppResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BristindexGroupingOnlyNameAndYrkesgrupperCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->map(function ($grouping) {
            return [
                'id' => $grouping->id,
                'name' => $grouping->name,
                'yrkesgrupper' => YrkesgruppSimple::collection($grouping->yrkesgrupper),
            ];
        })->toArray();
    }
}
