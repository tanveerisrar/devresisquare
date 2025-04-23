<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSubType extends Model
{
    protected $fillable = ['event_type_id','name'];

    public function type()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }
}
