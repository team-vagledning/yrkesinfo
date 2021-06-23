<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class YrkesomradeCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($yrkesomrade) {
           return [
               'id' => $yrkesomrade->id,
               'name' => $yrkesomrade->name,
               'external_id' => $yrkesomrade->external_id,
               'description' => $yrkesomrade->description,
               'bristindex' => $yrkesomrade->getBristindexes(),
               'yrkesprognoser' => $yrkesomrade->getYrkesprognoser(),
               'anstallda' => $yrkesomrade->aggregated_statistics['anstallda'],
           ];
        })->toArray();
    }
}
