<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'amount',
        'receipt_date',
        'reference_number',
        'mode_of_payment',
        'send_email',
        'send_sms',
        'send_whatsapp',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'send_email' => 'boolean',
        'send_sms' => 'boolean',
        'send_whatsapp' => 'boolean',
        'receipt_date' => 'date',
    ];

    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoice::class);
    }
}
