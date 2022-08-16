<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Yrkesbenamning as YrkesbenamningResource;

class YrkesgruppSimple extends JsonResource
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
            'yrkesomrade_id' => $this->yrkesomraden()->first()->id,
            'ssyk' => $this->ssyk,
            'alternative_ssyk' => $this->alternative_ssyk,
            'name' => $this->name,
            'search_similarity' => $this->when(isset($this->similarity), $this->similarity),
            'old_yrkesinfo' => $this->when($request->input('withOldYrkesinfo'), $this->extras['old_yrkesinfo'] ?? []),
        ];
    }
}
