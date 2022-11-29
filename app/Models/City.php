<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $table = 'city';
    protected $fillable = [
        'name',
        'province_id'
       
       
        

    ];
    public function company(){
        return $this->hasOne(Company::class);
    }
    public function province(){
        return $this->belongsTo(City::class);
    }
}
