<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'employment_status',
        'business_name',
        'registered_address',
        'guarantee',
        'previously_rented',
        'poor_credit',
        'correspondence_address',
        'allow_email',
        'allow_post',
        'allow_text',
        'allow_call',
        'occupation',
        'vat_number',
        'emails',
        'phones',
        'budget',
        'area',
        'tentative_move_in',
        'no_of_beds',
        'no_of_tenants',
        'specialisations',
        'cover_areas',
        'pi_insurance',
        'pi_reference_number',
        'pi_certificate',
    ];

    // cast these fields to/from arrays automatically
    protected $casts = [
        'emails' => 'array',
        'phones' => 'array',
        'allow_email' => 'boolean',
        'allow_post'  => 'boolean',
        'allow_text'  => 'boolean',
        'allow_call'  => 'boolean',
    ];

    /**
     * Define relationship with Contact model.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
