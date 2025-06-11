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
        'type_id',       // foreign key
        'sub_type_id',   // foreign key
        'office',
        'status',
        'diary_owner',
        'on_behalf_of',
        // 'start_datetime',
        // 'end_datetime',
        'description',
        'location',
        'reminder',
        'repeat',
        'repeat_interval',
        // 'repeat_until_count',
        'repeat_until_date',
        'rrule',
        'exdates'
    ];

    // protected $casts = [
    //     'start_datetime' => 'datetime',
    //     'end_datetime' => 'datetime',
    // ];

    // in app/Models/Event.php

    public function type()
    {
        return $this->belongsTo(EventType::class, 'type_id');
    }

    public function subType()
    {
        return $this->belongsTo(EventSubType::class, 'sub_type_id');
    }

       /**
     * Each master event has many instances.
     */
    public function instances()
    {
        return $this->hasMany(EventInstance::class);
    }

    /**
     * Color helper from status (unchanged).
     */
    public function getColorAttribute()
    {
        return match ($this->status) {
            'Cancelled' => '#dc3545',
            'Confirmed' => '#28a745',
            'Rescheduled'=> '#ffc107',
            default      => '#007bff',
        };
    }
}
