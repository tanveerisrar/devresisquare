<?php

namespace App\Models;

use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'work_order_id',
        'property_id',
        'user_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'total_amount',
        'status_id',
        'invoiced_date_time',
        'notes',
        'created_by',
        'updated_by',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItems::class);
    }

    public function payments()
    {
        return $this->hasMany(Transaction::class);
    }

    public function status()
    {
        return $this->belongsTo(InvoiceStatuses::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}