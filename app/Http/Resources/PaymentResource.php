<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
          
            'invoice'=>$this->invoice_no,
            'date'=>$this->date,
            'status'=>$this->status,
            'amount'=>$this->amount,
            'method'=>$this->method,
            'method_by'=>$this->method_by,
           
            'price'=>$this->price->name,
            'price_slug'=>$this->price->slug,
            'feature'=>$this->fitur->name,
            'feature_slug'=>$this->fitur->slug,
            

        ];
    }
}
