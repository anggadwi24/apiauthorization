<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Resource extends Model
{
    use HasFactory;

    use Sluggable;
    protected $table = 'resource';
    protected $fillable = [
        'name',
        'slug',
        'description',
    
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
        return $this->hasOne(Fitur_resource::class);
    }
}
