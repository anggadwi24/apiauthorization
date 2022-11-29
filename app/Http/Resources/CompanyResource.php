<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'id'=>$this->id,
            'fitur'=>$this->fitur->name,
            'fitur_slug'=>$this->fitur->slug,
            'price'=>$this->price->name,
            'price_slug'=>$this->price->slug,
            'referal_code'=>$this->referal_code,
            'icon'=>$this->icon,
            'name'=>$this->name,
            'slug'=>$this->slug,
            'category'=>$this->category->name,
            'province'=>$this->province->name,
            'province_id'=>$this->province_id,
            'city'=>$this->city->name,
            'city_id'=>$this->city_id,
            'address'=>$this->address,
            'kode_pos'=>$this->kode_pos,
            'referal'=>$this->referal,
            'active'=>$this->active,
            'expiry_on'=>$this->expiry_on,
            'phone'=>$this->phone,
            'email'=>$this->email,
            
           
            

        ];
    }
}
