<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCompanyResource extends JsonResource
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
            'level'=>$this->level,
            'email'=>$this->email,
            'phone'=>$this->phone,
            'nickname'=>$this->nickname,
            'photo'=>$this->photo,
            'verify'=>$this->email_verified_at,
            'company'=>CompanyResource::collection($this->company),

        ];
    }
}
