<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Yrkesbenamning as YrkesbenamningResource;

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
            'yrkesomrade_id' => $this->yrkesomraden()->first()->id,
            'ssyk' => $this->ssyk,
            'yrkesprognoser' => Yrkesprognos::collection($this->yrkesprognoser ?? $this->getYrkesprognoser()),
            'yrkesprognosgrupp' => BristindexGroupingOnlyNameAndYrkesgrupperCollection::make($this->whenLoaded('bristindexGroupings')),
            'alternative_ssyk' => $this->alternative_ssyk,
            'name' => $this->name,
            'description' => $this->description,
            'sunkoder' => SunkodCollection::make($this->whenLoaded('sunkoder')),
            'kurser' => SusanavetCourse::make($this->susanavetCourses()->latest()->first()),
            'search_similarity' => $this->when(isset($this->similarity), $this->similarity),
            'yrkesbenamningar' => YrkesbenamningResource::collection($this->whenLoaded('yrkesbenamningar')),
            'loner' => $this->aggregated_statistics['lon'],
            'anstallda' => $this->aggregated_statistics['anstallda'],
            'sektorer' => $this->aggregated_statistics['sektorer'],
            'bristindex' => $this->aggregated_statistics['bristindex'],
            'ledigaJobb' => $this->aggregated_statistics['ledigaJobb'],
            'regioner' => $this->aggregated_statistics['regioner'],
            'yrkesgrupper' => self::collection($this->when(isset($this->siblings), $this->siblings)),
            'old_yrkesinfo' => $this->when($request->input('withOldYrkesinfo'), $this->extras['old_yrkesinfo'] ?? []),
        ];
    }
}
