<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountHeader extends Model
{
    use HasFactory;

    protected $fillable = [
        'header_type',
        'name',
        'description',
        'status',
        'reference_number',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
