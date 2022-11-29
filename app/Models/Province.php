<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;
    protected $table = 'province';
    protected $fillable = [
        'name',
       
       
        

    ];
    public function company(){
        return $this->hasOne(Company::class);
    }
    public function city(){
        return $this->hasMany(City::class);
    }
}
