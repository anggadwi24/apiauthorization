<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fitur_price extends Model
{
    use HasFactory;
    use Sluggable;
    protected $table = 'fitur_price';
    protected $fillable = [
        'fitur_id',
        'name',
        'slug',
        'price',
        'discount',
        'discount_percent',
        'duration',
    
        'created_by',
        'updated_at',
        'created_at',
       
        

    ];
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
    public function fitur(){
        return $this->belongsTo(Fitur::class);
    }
    public function company(){
        return $this->hasOne(Company::class);
    }
    public function payment(){
        return $this->hasOne(Company_payment::class);
    }
}
