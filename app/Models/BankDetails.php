<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankDetails extends Model
{
    protected $fillable = [
        'contact_id',
        'account_name',
        'account_no',
        'sort_code',
        'bank_name',
        'swift_code',
        'is_active',
        'is_primary',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

}
