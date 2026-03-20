<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany as MorphManyRelation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\GlJournal;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invoiceHeader(): BelongsTo
    {
        return $this->belongsTo(SysInvoiceHeader::class, 'invoice_header_id');
    }

    public function getCustomerAvailableCreditAttribute(): float
    {
        return $this->user?->available_credit ?? 0;
    }

    public function journals(): MorphMany
    {
        return $this->morphMany(GlJournal::class, 'source');
    }

    public function activeJournal(): ?GlJournal
    {
        // Prefer the newer dedicated issue type
        $journal = GlJournal::activeFor('sale_invoice_issue', $this->id);
        if ($journal) {
            return $journal;
        }
        // Fallback for legacy journals stored with generic sale_invoice type
        return GlJournal::where('source_type', 'sale_invoice')
            ->where('source_id', $this->id)
            ->whereNull('reversal_of_id')
            ->whereDoesntHave('reversal')
            ->where('memo', 'like', 'Issue invoice%')
            ->orderByDesc('id')
            ->first();
    }

    public function hasActiveJournal(): bool
    {
        return (bool) $this->activeJournal();
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
