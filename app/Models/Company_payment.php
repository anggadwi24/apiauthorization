<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company_payment extends Model
{
    use HasFactory;
    protected $table = 'company_payment';
    protected $fillable = [
        'fitur_id',
        'fitur_price_id',
        'company_id',
        'invoice_no',
        'date',
        'method',
        'method_by',
    
        'amount',
        'status',
        'payment_url',
       
        'updated_at',
        'created_at',
       
        

    ];
   
    public function province(){
        return $this->belongsTo(Province::class);
    }
    public function city(){
        return $this->belongsTo(City::class);
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function fitur(){
        return $this->belongsTo(Fitur::class);
    }
    public function price(){
        return $this->belongsTo(Fitur_price::class);
    }
}
