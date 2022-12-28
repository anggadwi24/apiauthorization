<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FeaturePriceResource extends JsonResource
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
            'price'=>$this->price,
            'discount'=>$this->discount,
            'discount_percent'=>$this->discount,
            'duration'=>$this->duration,

            

        ];
    }
}
