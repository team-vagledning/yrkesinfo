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
                'identifierare' => self::getContent(self::info($course), 'identifier'),
                'beskrivning' => self::getContent(self::info($course), 'description.string.0.content'),
                'lank' => self::buildLink(self::getContent(self::info($course), 'identifier')),
                'utbildningstyp' => self::getContent(self::info($course), 'form.code'),
            ];
        })->filter(function ($course) {
            return $course['utbildningstyp'] === 'yrkeshögskoleutbildning';
        })->toArray();
    }

    public static function buildLink($identifier)
    {
        $id = last(explode('.', $identifier));

        if (is_numeric($id) === false) {
            return '';
        }

        return "https://www.yrkeshogskolan.se/hitta-utbildning/sok/utbildning/?id={$id}";
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
