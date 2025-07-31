<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\TracksUser;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use TracksUser;
    
    protected $guarded = []; // or define fillables

    protected $fillable = [
        'company_id',
        'branch_id',
        'designation_id',
        'user_type',
        'name',
        'email',
        'password',
        'category_id',
        'selected_properties',
        'first_name',
        'middle_name',
        'last_name',
        'phone',
        'email',
        'address_line_1',
        'address_line_2',
        'postcode',
        'city',
        'country',
        'status',
        'quick_step',
        'company_id',
        'branch_id',
        'designation_id',
        'can_login',
        'created_by',
        'updated_by',
    ];

    protected $hidden = ['password', 'remember_token'];
    
    // Optional: declare guard explicitly if needed
    protected $guard_name = 'web';

    // Optional: define the roles() relationship manually (if needed elsewhere)
    public function roles()
    {
        return $this->belongsToMany(
            \Spatie\Permission\Models\Role::class,
            'model_has_roles',
            'model_id',
            'role_id'
        );
    }


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

    // public function parent()
    // {
    //     return $this->belongsTo(User::class, 'parent_id');
    // }

    // Relationship with UserCategory model
    public function category()
    {
        return $this->belongsTo(UserCategory::class, 'category_id');
    }

    // Relationship with UserAttribute model
    public function details()
    {
        return $this->hasOne(UserDetail::class);
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
        return $this->hasMany(TenantMember::class, 'user_id');
    }

    // whenever first_name is set, rebuild name
    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = $value;
        $this->rebuildName();
    }

    // same for middle name
    public function setMiddleNameAttribute($value)
    {
        $this->attributes['middle_name'] = $value;
        $this->rebuildName();
    }

    // and last name
    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = $value;
        $this->rebuildName();
    }

    // helper to trim and set name
    protected function rebuildName()
    {
        $parts = [
            $this->attributes['first_name'] ?? '',
            $this->attributes['middle_name'] ?? '',
            $this->attributes['last_name'] ?? '',
        ];
        $this->attributes['name'] = trim(implode(' ', array_filter($parts)));
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

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function events()
    {
        return $this->morphToMany(Event::class, 'eventable');
    }
}
