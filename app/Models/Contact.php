<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contacts';

    protected $fillable = [
        'category_id',
        'selected_properties',
        'first_name',
        'middle_name',
        'last_name',
        'full_name',
        'phone',
        'email',
        'address_line_1',
        'address_line_2',
        'postcode',
        'city',
        'country',
        'status',
        'quick_step',
        'updated_by',
        'added_by',
    ];

    // Relationship with ContactCategory model
    public function category()
    {
        return $this->belongsTo(ContactCategory::class, 'category_id');
    }

    // Relationship with ContactAttribute model
    public function details()
    {
        return $this->hasOne(ContactDetail::class);
    }

    // Define the many-to-many relationship with Tenancy
    public function tenancies()
    {
        return $this->belongsToMany(Tenancy::class, 'property_manager_tenancy', 'property_manager_id', 'tenancy_id');
    }

    public function repairIssues()
    {
        return $this->hasMany(RepairIssue::class, 'final_contractor_id');
    }

    public function tenantMembers()
    {
        return $this->hasMany(TenantMember::class, 'contact_id');
    }

    public function creator() 
    { 
        return $this->belongsTo(User::class,'added_by');
    }

    // protected $casts = [
    //     'selected_properties' => 'array', // Automatically casts JSON to an array
    // ];

    // whenever first_name is set, rebuild full_name
    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = $value;
        $this->rebuildFullName();
    }

    // same for middle name
    public function setMiddleNameAttribute($value)
    {
        $this->attributes['middle_name'] = $value;
        $this->rebuildFullName();
    }

    // and last name
    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = $value;
        $this->rebuildFullName();
    }

    // helper to trim and set full_name
    protected function rebuildFullName()
    {
        $parts = [
            $this->attributes['first_name'] ?? '',
            $this->attributes['middle_name'] ?? '',
            $this->attributes['last_name'] ?? '',
        ];
        $this->attributes['full_name'] = trim(implode(' ', array_filter($parts)));
    }

    public function bankDetails()
    {
        return $this->hasMany(BankDetails::class);
    }
    
    // public function notes()
    // {
    //     return $this->hasMany(Notes::class);
    // }

    public function notes()
    {
        return $this->morphMany(Notes::class, 'noteable');
    }
}
