<?php

namespace App\Models;

use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\TracksUser;

class Invoice extends Model
{
    use HasFactory, TracksUser;

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
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Sum of completed payments applied to this invoice.
     * Uses Transaction::total_amount for payment amounts and only counts transactions with status 'completed'.
     */
    public function paidAmount(): float
    {
        return (float) $this->payments()
            ->where('status', 'completed')
            ->sum('total_amount');
    }

    /**
     * Outstanding amount left on the invoice.
     */
    public function outstandingAmount(): float
    {
        return max(0, (float)$this->total_amount - $this->paidAmount());
    }

    /**
     * Apply a payment amount to this invoice.
     * Marks invoice as fully paid if amount settles or exceeds outstanding.
     * 
     * NOTE: set the 'PAID' status id in the config or replace `config('invoices.status.paid')` with your actual status id.
     */
    public function applyPayment(float $amount): void
    {
        // after transaction created we recalc paid amount
        $paid = $this->paidAmount();

        // if fully paid now, set status_id if you use status ids
        if ($paid >= (float)$this->total_amount) {
            // recommended: put mapping in config/invoices.php like ['status' => ['paid' => 3]]
            $paidStatusId = config('invoices.status.paid') ?? null;

            if ($paidStatusId) {
                $this->status_id = $paidStatusId;
                $this->invoiced_date_time = $this->invoiced_date_time ?? now();
                $this->save();
            } else {
                // optional fallback: still save updated invoiced_date_time if fully paid
                $this->invoiced_date_time = $this->invoiced_date_time ?? now();
                $this->save();
            }
        }
    }

}