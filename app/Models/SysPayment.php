<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SysPayment extends Model
{
    use HasFactory;

    protected $table = 'sys_payments';
    protected $guarded = [];
    const UPDATED_AT = null;

    public function reference(): MorphTo
    {
        // Polymorphic link to sale or purchase invoice
        return $this->morphTo(__FUNCTION__, 'reference_type', 'reference_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(SysBankAccount::class, 'sys_bank_account_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
