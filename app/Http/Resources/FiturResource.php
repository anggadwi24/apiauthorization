<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FiturResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
           
            'name'=>$this->name,
            'slug'=>$this->slug,
            'description'=>$this->description,
            'resource'=>FiturSourceResource::collection($this->fitur_resource),
            'price'=>FiturPriceResource::collection($this->fitur_price),
            

        ];
    }
}
