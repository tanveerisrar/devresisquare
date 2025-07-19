<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'transaction_type',
        'invoice_id',
        'category_id',
        'property_id',
        'payer_id',
        'payee_id',
        'credit',
        'debit',
        'balance',
        'transaction_date',
        'amount',
        'tax_amount',
        'total_amount',
        'payment_method',
        'transaction_reference',
        'status',
        'notes',
        'transaction_category_id', // New field
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function payee()
    {
        return $this->belongsTo(User::class, 'payee_id');
    }

    public function category()
    {
        return $this->belongsTo(TransactionCategory::class, 'transaction_category_id');
    }
}
