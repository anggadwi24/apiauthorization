<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;
    use Sluggable;
    protected $table = 'company';
    protected $fillable = [
        'fitur_id',
        'fitur_price_id',
        'referal_code',
        'name',
        'slug',
        'icon',
        'province_id',
        'category_id',
        'city_id',
        'kode_pos',
        'referal',
        'active',
        'category_id',
        'expiry_on',
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
    public function province(){
        return $this->belongsTo(Province::class);
    }
    public function city(){
        return $this->belongsTo(City::class);
    }

    public function payment(){
        return $this->belongsTo(Company_payment::class);
    }

    public function referal(){
        return $this->belongsTo(Company_referal::class);
    }
    public function fitur(){
        return $this->belongsTo(Fitur::class);
    }
    public function price(){
        return $this->belongsTo(Fitur_price::class,'fitur_price_id');
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }
}
