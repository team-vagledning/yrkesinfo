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
        return collect($this->data[0])->map(function ($course) {
            return [
                'namn' => self::getContent($course, 'title.string.0.content'),
                'kurskod' => self::getContent($course, 'code'),
                'beskrivning' => self::getContent($course, 'description.string.0.content'),
                'lank' => self::getContent($course, 'url.url.0.content'),
            ];
        })->toArray();
    }

    public static function shorter($course)
    {
        return $course['content']['educationInfo'];
    }

    public static function getContent($course, $key)
    {
        return data_get(self::shorter($course), $key, '');
    }
}
