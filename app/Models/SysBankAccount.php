<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysBankAccount extends Model
{
    use HasFactory;

    protected $table = 'sys_bank_accounts';
    protected $guarded = [];
}
