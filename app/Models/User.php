<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role_id'];
    protected $hidden = ['password', 'remember_token'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // In User.php
    public function scopeOfRole($query, $roleName)
    {
        return $query->whereHas('role', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    public function getDisplayLabelAttribute(): string
    {
        return "{$this->name} — {$this->email}";
    }

    public static function optionsForSelect(): array
    {
        return self::all()->pluck('display_label', 'id')->toArray();
    }

}
