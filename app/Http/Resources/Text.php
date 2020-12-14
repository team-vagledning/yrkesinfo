<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Text extends JsonResource
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
            'type' => $this->text_type,
            'text' => $this->content,
        ];
    }
}
