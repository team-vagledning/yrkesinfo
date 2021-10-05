<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SusanavetCourse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return collect($this->data)->map(function ($course) {
            return [
                'namn' => self::getContent(self::info($course), 'title.string.0.content'),
                'stad' => self::getContent(self::event($course), 'location.0.town'),
                'kurskod' => self::getContent(self::info($course), 'code'),
                'beskrivning' => self::getContent(self::info($course), 'description.string.0.content'),
                'lank' => self::getContent(self::info($course), 'url.url.0.content'),
            ];
        })->toArray();
    }

    public static function info($course)
    {
        return $course['info']['educationInfo'];
    }

    public static function event($course)
    {
        return $course['events']['educationEvent'];
    }

    public static function getContent($course, $key)
    {
        return data_get($course, $key, '');
    }
}
