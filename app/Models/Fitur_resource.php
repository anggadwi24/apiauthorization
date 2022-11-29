<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fitur_resource extends Model
{
    use HasFactory;
   
    protected $table = 'fitur_resource';
    protected $fillable = [
        'fitur_id',
        'resource_id',
        'value',
      
        'created_by',
        'updated_at',
        'created_at',
       
        

    ];
   

    public function sumber(){
        return $this->belongsTo(Resource::class,'resource_id');
    }
}
