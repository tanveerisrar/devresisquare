<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{    
    protected $fillable = ['property_id', 'content', 'type'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
