<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{    
    protected $fillable = ['property_id', 'contact_id', 'content', 'type'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
