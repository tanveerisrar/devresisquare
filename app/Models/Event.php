<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'sub_type',
        'office',
        'status',
        'diary_owner',
        'on_behalf_of',
        'start_datetime',
        'end_datetime',
        'description',
        'location',
        'reminder',
        'repeat',
        'repeat_until',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    // in app/Models/Event.php

    public function type()
    {
        return $this->belongsTo(EventType::class, 'type_id');
    }

    public function subType()
    {
        return $this->belongsTo(EventSubType::class, 'sub_type_id');
    }

}
