<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    use HasFactory;

    protected $fillable = [
        'payment_method_id',
        'bank_account_id',
        'transaction_number',
        'transaction_type',
        'invoice_id',
        'transaction_category_id',
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
        'transaction_reference',
        'credit',
        'debit',
        'balance',
        'status',
        'notes',
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

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }
}
