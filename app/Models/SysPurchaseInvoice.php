<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany as MorphManyRelation;

class SysPurchaseInvoice extends Model
{
    use HasFactory;

    protected $table = 'sys_purchase_invoices';
    protected $guarded = [];
    const UPDATED_AT = null;

    public function receipts(): MorphMany
    {
        return $this->morphMany(SysReceipt::class, 'receiptable');
    }

    public function payments(): MorphManyRelation
    {
        return $this->morphMany(SysPayment::class, 'reference', 'reference_type', 'reference_id')->latest('id');
    }
}
