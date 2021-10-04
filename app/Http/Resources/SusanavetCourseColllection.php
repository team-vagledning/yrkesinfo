<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SusanavetCourseColllection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($susanavetCourses) {
            dd($susanavetCourses->data);
            return [
                'title' => $susanavetCourse->content->title->string[0].content
            ];
        })->toArray();
    }
}
