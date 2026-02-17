<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Relations\MorphMany as MorphManyRelation;

class SysSaleInvoice extends Model
{
    use HasFactory;

    protected $table = 'sys_sale_invoices';
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

    public function items(): HasMany
    {
        return $this->hasMany(SysSaleInvoiceItem::class, 'sale_invoice_id');
    }

    protected static function booted(): void
    {
        static::deleting(function (SysSaleInvoice $invoice) {
            $invoice->items()->delete();
        });
    }
}
