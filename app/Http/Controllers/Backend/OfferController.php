<?php

namespace App\Http\Controllers\Backend;

use App\Models\Offer;
use App\Models\Property;
use App\Models\Tenancy;
use App\Models\TenantMember;
use App\Models\Contact;
use App\Models\ContactDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferController
{
    public function index()
    {
        $offers = Offer::with('property')->get();
        return view('backend.offers.index', compact('offers'));
    }

    public function create()
    {
        $properties = Property::all();
        return view('backend.offers.create', compact('properties'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'property_id' => 'required|exists:properties,id',
    //         'price' => 'required|numeric',
    //         'deposit' => 'required|numeric',
    //         'term' => 'required|string|max:255',
    //         'move_in_date' => 'required|date',
    //     ]);

    //     Offer::create($request->all());
    //     return redirect()->route('offers.index')->with('success', 'Offer created successfully.');
    // }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'property_id' => 'required|exists:properties,id',  // Ensure property exists in database
            'price' => 'required|numeric',                      // Validate the price to be a number
            'deposit' => 'required|numeric',                    // Validate the deposit to be a number
            'term' => 'required|string|max:255',                 // Validate the term as a string
            'moveInDate' => 'required|date',                    // Validate move-in date
        ]);

        // Collect tenant details from the request
        $tenantDetails = [];
        $tenantIndex = 1;

        while ($request->has("tenantName_{$tenantIndex}")) {
            // Create a new contact for each tenant
            $contact = Contact::create([
                'category_id' => 3,
                'full_name' => $request->input("tenantName_{$tenantIndex}"),
                'phone' => $request->input("tenantPhone_{$tenantIndex}"),
                'email' => $request->input("tenantEmail_{$tenantIndex}"),
                'status' => 1,  // You can adjust the status accordingly
                'updated_by' => Auth::id(),  // Assuming authenticated user updates the record
                'added_by' => Auth::id(),    // Assuming authenticated user adds the record
            ]);

            // Create corresponding contact details (tenancy related information)
            ContactDetail::create([
                'contact_id' => $contact->id,
                'employment_status' => $request->input("employmentStatus_{$tenantIndex}"),
                'business_name' => $request->input("businessName_{$tenantIndex}"),
                'guarantee' => convert_to_boolean($request->input("guarantee_{$tenantIndex}")),
                'previously_rented' => convert_to_boolean($request->input("previouslyRented_{$tenantIndex}")),
                'poor_credit' => convert_to_boolean($request->input("poorCredit_{$tenantIndex}")),
            ]);

            // Add the contact ID to the $contactIds array, with mainPerson flag as true/false
            $isMainPerson = $request->input("mainPerson_{$tenantIndex}") == 'on' ? true : false;
            $contactIds[$contact->id] = $isMainPerson;

            // $tenantDetails[] = [
            //     'tenantName' => $request->input("tenantName_{$tenantIndex}"),
            //     'tenantPhone' => $request->input("tenantPhone_{$tenantIndex}"),
            //     'tenantEmail' => $request->input("tenantEmail_{$tenantIndex}"),
            //     'employmentStatus' => $request->input("employmentStatus_{$tenantIndex}"),
            //     'businessName' => $request->input("businessName_{$tenantIndex}"),
            //     'guarantee' => $request->input("guarantee_{$tenantIndex}"),
            //     'previouslyRented' => $request->input("previouslyRented_{$tenantIndex}"),
            //     'poorCredit' => $request->input("poorCredit_{$tenantIndex}"),
            //     'mainPerson' => $request->input("mainPerson_{$tenantIndex}") == 'on' ? true : false,  // Main person flag
            // ];

            $tenantIndex++;
        }

        // Create the offer in the database
        $offer = Offer::create([
            'property_id' => $request->input('property_id'),
            'price' => $request->input('price'),
            'deposit' => $request->input('deposit'),
            'term' => $request->input('term'),
            'move_in_date' => $request->input('moveInDate'),
            'tenant_details' => json_encode($contactIds),  // Store tenant details as JSON
            // 'tenant_details' => json_encode($tenantDetails),  // Store tenant details as JSON
            'status' => 'Pending',  // Default status for the offer
        ]);

        $response = [
            'status' => true,
            'message' => 'Offer Added successfully!',
        ];

        return response()->json($response);

        // Redirect back with a success message
        // return redirect()->route('offers.index')->with('success', 'Offer created successfully.');
    }

    public function edit($id)
    {
        $offer = Offer::findOrFail($id);
        $properties = Property::all();
        return view('backend.offers.edit', compact('offer', 'properties'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'price' => 'required|numeric',
            'deposit' => 'required|numeric',
            'term' => 'required|string|max:255',
            'move_in_date' => 'required|date',
        ]);

        $offer = Offer::findOrFail($id);
        $offer->update($request->all());
        return redirect()->route('offers.index')->with('success', 'Offer updated successfully.');
    }

    public function destroy($id)
    {
        $offer = Offer::findOrFail($id);
        $offer->delete();
        return redirect()->route('offers.index')->with('success', 'Offer deleted successfully.');
    }

    public function setMainPerson(Request $request, $id)
    {
        // Retrieve the offer by ID
        $offer = Offer::findOrFail($id);

        // Decode tenant details stored as JSON in the 'tenant_details' field
        $tenantDetails = collect(json_decode($offer->tenant_details, true));

        // Ensure that the 'member' key is provided in the request and contains the 'contactId'
        if (!$request->has('contactId')) {
            return response()->json(['status' => false, 'message' => 'Contact ID is missing in the request.']);
        }

        // The provided 'contactId' will be used to identify the tenant
        $contactId = $request->contactId;

        // Update main person flag based on the provided contact ID
        foreach ($tenantDetails as $key => $isMain) {
            // Reset all mainPerson flags to false first
            $tenantDetails[$key] = false;

            // Set mainPerson flag to true for the tenant whose contact ID matches
            if ($key == $contactId) {
                $tenantDetails[$key] = true;
            }
        }

        // Save the updated tenant details back to the offer
        $offer->tenant_details = json_encode($tenantDetails);
        $offer->save();

        // Respond with a success message
        return response()->json(['status' => true, 'message' => 'Main person updated successfully.']);
    }
    // public function updateStatus(Request $request, $id)
    // {
    //     // Retrieve the offer by ID
    //     $offer = Offer::findOrFail($id);
    //     $offer->status = $request->status;

    //     // Check if the status is 'Accepted'
    //     if ($offer->status === 'Accepted') {
    //         // Decode tenant details from JSON
    //         $tenantDetails = json_decode($offer->tenant_details, true);

    //         // Create the tenancy record
    //         $tenancy = Tenancy::create([
    //             'offer_id' => $offer->id,  // Link the tenancy to the offer
    //             'property_id' => $offer->property_id,  // Store property_id if needed
    //             'move_in' => $offer->move_in_date,  // Move-in date from offer
    //             'move_out' => null,
    //             'price' => $offer->price,  // Price from offer
    //             'deposit' => $offer->deposit,  // Deposit from offer
    //             'frequency' => $tenantDetails['frequency'] ?? 'Monthly',  // Default to 'Monthly'
    //             'status' => 'Active',  // Default status for the tenancy
    //         ]);

    //         // Generate a unique group ID (e.g., GROUP_1, GROUP_2)
    //         $groupId = 'GROUP_' . $tenancy->id;

    //         // Now insert tenant members from tenantDetails
    //         foreach ($tenantDetails as $contactId => $isMainPerson) {
    //             // Retrieve the contact using contact_id stored in tenantDetails
    //             $contact = Contact::findOrFail($contactId);

    //             // Create the TenantMember record for each tenant
    //             TenantMember::create([
    //                 'tenancy_id' => $tenancy->id,  // Link the tenant to the created tenancy
    //                 'contact_id' => $contact->id,  // Link to the correct Contact model
    //                 'is_main_person' => $isMainPerson ? 1 : 0,  // Set the main person flag (1 for true, 0 for false)
    //                 'group_id' => $groupId,  // Link tenant to the group ID
    //             ]);
    //         }

    //         // Save the offer to update the status
    //         $offer->save();
    //     } else {
    //         // If the status is not 'Accepted', simply save the offer status without updating tenant details
    //         $offer->save();
    //     }

    //     return response()->json(['status' => true, 'message' => 'Offer status updated successfully.']);
    // }

    public function updateStatus(Request $request, $id)
    {
        // Retrieve the offer by ID
        $offer = Offer::findOrFail($id);
        $offer->status = $request->status;

        // Check if the status is 'Accepted'
        if ($offer->status === 'Accepted') {

            // Call the helper function to reject other offers
            Offer::rejectOtherOffers($offer->property_id, $offer->id);

            // Decode tenant details from JSON
            $tenantDetails = json_decode($offer->tenant_details, true);

            // Check if there is any active tenancy for the same property
            $existingTenancy = Tenancy::where('property_id', $offer->property_id)
                                    ->where('status', 'Active')
                                    ->first();

            // If there's an active tenancy for the same property, set this new tenancy's status to 'Inactive'
            $status = $existingTenancy ? 'Inactive' : 'Active';

            // Check if the tenancy record already exists
            $tenancy = Tenancy::where('offer_id', $offer->id)->first();

            if (!$tenancy) {
                // If the tenancy doesn't exist, create a new one
                $tenancy = Tenancy::create([
                    'offer_id' => $offer->id,  // Link the tenancy to the offer
                    'property_id' => $offer->property_id,  // Store property_id if needed
                    'move_in' => $offer->move_in_date,  // Move-in date from offer
                    'move_out' => null,
                    'rent' => $offer->price,  // Price from offer
                    'deposit' => $offer->deposit,  // Deposit from offer
                    'frequency' => $tenantDetails['frequency'] ?? 'Monthly',  // Default to 'Monthly'
                    'status' => $status,  // Default status for the tenancy
                ]);
            } else {
                // Update the tenancy record if it already exists
                $tenancy->move_in = $offer->move_in_date;
                $tenancy->rent = $offer->price;
                $tenancy->deposit = $offer->deposit;
                $tenancy->frequency = $tenantDetails['frequency'] ?? 'Monthly';
                $tenancy->status = $status;
                $tenancy->save();
            }

            // Generate a unique group ID (e.g., GROUP_1, GROUP_2)
            $groupId = 'GROUP_' . $tenancy->id;

            // Now insert/update tenant members from tenantDetails
            foreach ($tenantDetails as $contactId => $isMainPerson) {
                // Retrieve the contact using contact_id stored in tenantDetails
                $contact = Contact::findOrFail($contactId);

                // Check if the TenantMember record already exists
                $tenantMember = TenantMember::where('tenancy_id', $tenancy->id)
                                            ->where('contact_id', $contact->id)
                                            ->first();

                if (!$tenantMember) {
                    // If the tenant member doesn't exist, create a new one
                    TenantMember::create([
                        'tenancy_id' => $tenancy->id,  // Link the tenant to the created tenancy
                        'contact_id' => $contact->id,  // Link to the correct Contact model
                        'is_main_person' => $isMainPerson ? 1 : 0,  // Set the main person flag (1 for true, 0 for false)
                        'group_id' => $groupId,  // Link tenant to the group ID
                    ]);
                } else {
                    // If the tenant member exists, update the is_main_person flag
                    $tenantMember->is_main_person = $isMainPerson ? 1 : 0;
                    $tenantMember->group_id = $groupId;
                    $tenantMember->save();
                }
            }

            // Save the offer to update the status
            $offer->save();
        } else {
            // If the status is not 'Accepted', simply save the offer status without updating tenant details
            $offer->save();
        }

        return response()->json(['status' => true, 'message' => 'Offer status updated successfully.']);
    }


    // public function setMainPerson(Request $request, $id)
    // {
    //     $offer = Offer::findOrFail($id);
    //     $tenantDetails = collect(json_decode($offer->tenant_details, true));

    //     // Update main person
    //     $tenantDetails = $tenantDetails->map(function ($tenant) use ($request) {
    //         $tenant['mainPerson'] = $tenant['tenantName'] === $request->member['tenantName'];
    //         return $tenant;
    //     });

    //     $offer->tenant_details = json_encode($tenantDetails);
    //     $offer->save();

    //     return response()->json(['status' => true, 'message' => 'Main person updated successfully.']);
    // }

    // public function updateStatus(Request $request, $id)
    // {
    //     $offer = Offer::findOrFail($id);
    //     $offer->status = $request->status;
    //     // Check if the status is 'Accepted'
    //     if ($offer->status === 'Accepted') {
    //         // Assuming $tenantDetails is stored in JSON format in offer->tenant_details
    //         $tenantDetails = json_decode($offer->tenant_details, true);

    //         // Create the tenancy record
    //         $tenancy = Tenancy::create([
    //             'offer_id' => $offer->id, // Link the tenancy to the offer
    //             'property_id' => $offer->property_id, // You can also store property_id here if needed
    //             'move_in' => $offer->move_in_date, // Example: Extract move-in date from tenant details
    //             'move_out' => null,
    //             'price' => $offer->price, // Assuming price is the same for tenancy
    //             'deposit' => $offer->deposit, // Assuming deposit is the same for tenancy
    //             'frequency' => $tenantDetails['frequency'] ?? 'Monthly', // Default to 'Monthly'
    //             'status' => 'Active', // Default status can be 'Active'
    //         ]);

    //         // Generate a unique group ID (e.g., GROUP_1, GROUP_2)
    //         $groupId = 'GROUP_' . $tenancy->id;

    //         // Now insert tenant members
    //         if (isset($tenantDetails)) {
    //             // Insert tenant members
    //             foreach ($tenantDetails as $tenantMember) {
    //                 TenantMember::create([
    //                     'tenancy_id' => $tenancy->id, // Link to the created tenancy
    //                     'name' => $tenantMember['tenantName'],
    //                     'email' => $tenantMember['tenantEmail'],
    //                     'phone' => $tenantMember['tenantPhone'],
    //                     'employment_status' => $tenantMember['employmentStatus'] ?? null,
    //                     'business_name' => $tenantMember['businessName'] ?? null,
    //                     'guarantee' => $tenantMember['guarantee'] ?? null,
    //                     'previously_rented' => $tenantMember['previouslyRented'] ?? null,
    //                     'poor_credit' => $tenantMember['poorCredit'] ?? null,
    //                     'is_main_person' => ($tenantMember['mainPerson'] == 'Yes') ? 1 : 0, // Default to 0 if not specified
    //                     'group_id' => $groupId,
    //                 ]);
    //             }
    //         }

    //         // Save the offer to update status
    //         $offer->save();
    //     } else {
    //         // If the status is not 'Accepted', simply save the offer status without updating tenant details
    //         $offer->save();
    //     }

    //     return response()->json(['status' => true, 'message' => 'Offer status updated successfully.']);
    // }

    // public function updateStatus(Request $request, $id)
    // {
    //     $offer = Offer::findOrFail($id);
    //     $offer->status = $request->status;
    //     $offer->save();

    //     return response()->json(['status' => true, 'message' => 'Offer status updated successfully.']);
    // }


}
