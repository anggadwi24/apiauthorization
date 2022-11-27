<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fitur extends Model
{
    use HasFactory;
    use Sluggable;
    protected $table = 'fitur';
    protected $fillable = [
        'name',
        'slug',
        'description',
       
        'best',
    
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

    public function fitur_resource(){
        return $this->hasMany(Fitur_resource::class);
    }
    public function fitur_price(){
        return $this->hasMany(Fitur_price::class);
    }
    
}
