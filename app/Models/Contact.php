<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    // protected $casts = [
    //     'selected_properties' => 'array', // Automatically casts JSON to an array
    // ];

    public function events()
    {
        return $this->morphToMany(Event::class, 'eventable');
    }

}
