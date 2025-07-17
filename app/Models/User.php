<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    
    use HasRoles;

    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];

    /*public function role()
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
    }*/

    // If you still want a helper to fetch users of a given role:
    public function scopeRoleName($query, string $roleName)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', $roleName));
    }

    public function getDisplayLabelAttribute(): string
    {
        return "{$this->name} — {$this->email}";
    }

    public static function optionsForSelect(): array
    {
        // If you want to filter by role, you can now do:
        // return self::roleName('Landlord')->pluck('display_label', 'id')->toArray();

        return self::all()->pluck('display_label', 'id')->toArray();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }
}
