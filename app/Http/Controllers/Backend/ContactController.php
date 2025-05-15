<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Models\Notes;
use App\Models\Contact;
use App\Models\Property;
use App\Models\Nationality;
use Illuminate\Http\Request;
use App\Models\ContactCategory;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ContactController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Fetch categories for your filter dropdown
        $categories = ContactCategory::all();

        // Build base contacts query, eager‑loading all relationships
        $contactsQuery = Contact::with([
            'category',
            'details',
            'tenancies',
            'repairIssues',
            'tenantMembers',
        ]);

        // Apply a category filter if provided
        if ($request->filled('category')) {
            $contactsQuery->where('category_id', $request->category);
        }

        // Fetch all contacts (newest first)
        $contacts = $contactsQuery->orderBy('id', 'desc')->get();

        // If no contacts at all, redirect to quick-create
        if ($contacts->isEmpty()) {
            flash("You don't have any contacts yet!")->error();
            return redirect()->route('admin.contacts.quick');
        }

        // Decide which contact/tab to show
        $contactId = $request->query('contact_id');
        $tabName   = $request->query('tabname', 'contact');

        // Try to find the requested contact or fall back to the most recent
        $contact = $contactId
            ? $contacts->firstWhere('id', $contactId)
            : null;

        if (! $contact) {
            $contact = $contacts->first();
            $contactId = $contact->id;
        }

        // Define your tab list
        $tabs = [
            ['name' => 'Contact'],
            ['name' => 'Appointments'],
            ['name' => 'Link'],
            ['name' => 'Bank'],
            ['name' => 'Contact Owner'],
            ['name' => 'Letters'],
            ['name' => 'Compliance'],
            ['name' => 'Documents'],
            ['name' => 'Notes'],
        ];

        $content = $this->getTabContent($tabName, $contactId, $contact); // Dynamically get content for the tab and property

        // Check if the request is via AJAX (this handles dynamic content loading)
        if ($request->ajax()) {
            return response()->json(['content' => $content, 'tabName' => $tabName]);
        }

        return view('backend.contacts.index', compact('contacts', 'categories','tabs', 'tabName', 'contactId', 'contact', 'content'));
    }
    private function getTabContent($tabname, $contactId, $contact)
    {
        switch (strtolower($tabname)) {
            case 'contact':
                return view('backend.contacts.tabs.contact_details', compact('contactId', 'contact'))->render();
            
            case 'appointments':
                return view('backend.contacts.tabs.appointments', compact('contactId', 'contact'))->render();                
            
            case 'link':
                $propertyIds = [];

                if (!empty($contact->selected_properties)) {
                    $decoded = is_array($contact->selected_properties)
                        ? $contact->selected_properties
                        : json_decode($contact->selected_properties, true);

                    if (is_array($decoded)) {
                        $propertyIds = $decoded;
                    }
                }

                $properties = !empty($propertyIds)
                    ? Property::whereIn('id', $propertyIds)->get()
                    : collect(); // empty collection if no IDs
                    
                return view('backend.contacts.tabs.linked', compact('contactId', 'contact', 'properties'))->render();

            case 'bank':
                // Fetch bank details related to the specific contact by contact ID
                $bankDetails = $contact->bankDetails()->orderBy('updated_at', 'desc')->get();
                // Ensure it's an empty collection if no bank details are found
                if ($bankDetails->isEmpty()) {
                    $bankDetails = collect();  // Make sure it's an empty collection, not null
                }
                return view('backend.contacts.tabs.bank_details', compact('contactId', 'contact', 'bankDetails'))->render();
            
            case 'contact owner':
                $contact->load('creator.role'); // Eager load role
                return view('backend.contacts.tabs.contact_owner', compact('contactId', 'contact'))->render();

            case 'letters':
                return view('backend.contacts.tabs.letters', compact('contactId', 'contact'))->render();
    
            case 'compliance':
                // load all nationalities keyed by id→name
                $nationalities = Nationality::orderBy('name')->pluck('name', 'id');
                // load all users for the “checked by” dropdown
                $users = User::orderBy('name')->pluck('name', 'id');
                
                return view('backend.contacts.tabs.compliance', compact('contactId', 'contact', 'users', 'nationalities'))->render();
    
            case 'documents':
                return view('backend.contacts.tabs.documents', compact('contactId', 'contact'))->render();
    
            case 'notes':
                // Fetch the notes related to the specific contact by contact ID
                $notes = Notes::where('contact_id', $contactId)->orderBy('updated_at', 'desc')->get();
                
                // Ensure it's an empty collection if no notes are found
                if ($notes->isEmpty()) {
                    $notes = collect();  // Make sure it's an empty collection, not null
                }
                
                return view('backend.contacts.tabs.notes', compact('contactId', 'contact', 'notes'))->render();
    
            default:
                return 'Tab content not found';
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Contact $contact)
    {
        $categories = ContactCategory::all();
        return view('backend.contacts.create', compact('contact', 'categories'));
        // return view('backend.contacts.create'); // Return the create contact view
    }

    public function contactStore(Request $request)
    {
        // Validate data based on the current step
        if ($request->has('step')) {
            // Validate the request data
            $validatedData = $request->validate($this->getValidationRulesQuick($request->step));

            // Get contact_id from the request
            $contact_id = $request->contact_id;

            // Check if first name, middle name, and last name are present
            $fullName = trim($request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name);

            // Store full name if it's not empty
            if (!empty($fullName)) {
                $validatedData['full_name'] = $fullName;
            }


            // Check if contact_id is provided in the request
            if ($contact_id) {
                $contact = Contact::find($contact_id);
                if ($contact) {
                    // Log the data before updating
                    Log::info('Updating contact with ID ' . $contact_id, $validatedData);

                    // Merge new selected properties if provided
                    if ($request->has('selected_properties')) {
                        $validatedData['selected_properties'] = $request->selected_properties;
                    }
                    // Add a condition to prevent updating the step if it's the final step
                    if ($request->step < $this->getTotalQuickSteps()) {
                        $validatedData['quick_step'] = $request->step; // Update step only if it's not the last step
                    }

                    $contact->update($validatedData);
                }
            } else {
                // Create new contact only empty contact id
                if (empty($contact_id)) {
                    $validatedData['quick_step'] = $request->step;
                    Log::info('Creating new contact', $validatedData);

                    $contact = Contact::create(array_merge($validatedData, ['added_by' => Auth::id()]));

                }
            }
            // Get total number of steps
            $totalSteps = $this->getTotalQuickSteps();

            // Check if the current step is the last one
            if ($request->step >= $totalSteps) {

                // Final submission handling
                flash("Contact Added/Updated successfully!")->success();
                return view('backend.contacts.contact_form.thankyou');
            }

            return view('backend.contacts.contact_form.step' . ($request->step + 1), compact('contact'));
        } else {
            // If no step is present, return a message (optional)
            return response()->json(['message' => 'Invalid step from quick store.']);
        }
    }

    private function getValidationRulesQuick($step)
    {
        switch ($step) {
            case 1:
                return [
                    'category_id' => 'required',
                ];
            case 2:
                return [
                    'first_name' => 'required|string|max:55',
                    'middle_name' => 'nullable|string|max:55',
                    'last_name' => 'required|string|max:55',
                    'phone' => 'required|string|max:20',
                    'email' => 'required|email|max:55',
                    'address_line_1' => 'required|string|max:255',
                    'address_line_2' => 'nullable|string|max:255',
                    'postcode' => 'required|string|max:15',
                    'city' => 'required|string|max:55',
                    'country' => 'required|string|max:55',
                ];
            case 3:
                return [
                    'selected_properties' => 'nullable',
                ];

            default:
                return [];
        }
    }

    private function getTotalQuickSteps()
    {
        // Specify the directory where your Blade files for steps are located
        $stepsDirectory = resource_path('views/backend/contacts/contact_form');

        // Get all Blade files in the directory that start with 'step' and count them
        return count(glob($stepsDirectory . '/step*.blade.php'));
    }

    public function searchProperties(Request $request)
    {
        // Check if we are passing specific property IDs
        $ids = $request->input('ids');

        // If IDs are provided, fetch properties by IDs
        if ($ids) {
            $properties = Property::whereIn('id', $ids)
                ->get(['id', 'prop_ref_no', 'prop_name', 'line_1', 'line_2', 'city', 'country', 'postcode', 'specific_property_type', 'available_from']);  // Return only necessary fields
        } else {
            // If no IDs are passed, search properties based on the query (default behavior)
            $query = $request->input('query');
            $properties = Property::where('prop_ref_no', 'LIKE', '%' . $query . '%')
                ->orWhere('prop_name', 'LIKE', '%' . $query . '%')
                ->orWhere('line_1', 'LIKE', '%' . $query . '%')
                ->orWhere('line_2', 'LIKE', '%' . $query . '%')
                ->orWhere('city', 'LIKE', '%' . $query . '%')
                ->orWhere('country', 'LIKE', '%' . $query . '%')
                ->orWhere('postcode', 'LIKE', '%' . $query . '%')
                ->limit(10)
                ->get(['id', 'prop_ref_no', 'prop_name', 'line_1', 'line_2', 'city', 'country', 'postcode', 'specific_property_type', 'available_from']);
        }

        // Return the properties as JSON response
        return response()->json($properties->map(function($property) {
            return [
                'id' => $property->id,
                'address' => trim($property->line_1 . ' ' . $property->line_2 . ', ' . $property->city . ', ' . $property->postcode) ?: 'N/A',
                'type' => trim($property->specific_property_type) ?: 'N/A',
                'availability' => trim($property->available_from) ?: 'N/A',
                'prop_ref_no' => trim($property->prop_ref_no) ?: 'N/A',
                'prop_name' => trim($property->prop_name) ?: 'N/A',
            ];
        }));
    }



    // public function searchProperties(Request $request)
    // {
    //     // Get the search query from the request
    //     $query = $request->input('query');
    //     $properties = null;

    //     if($query){
    //         // Search for properties based on multiple fields
    //         $properties = Property::where('prop_ref_no', 'LIKE', '%' . $query . '%')
    //             ->orWhere('prop_name', 'LIKE', '%' . $query . '%')
    //             ->orWhere('line_1', 'LIKE', '%' . $query . '%')
    //             ->orWhere('line_2', 'LIKE', '%' . $query . '%')
    //             ->orWhere('city', 'LIKE', '%' . $query . '%')
    //             ->orWhere('country', 'LIKE', '%' . $query . '%')
    //             ->orWhere('postcode', 'LIKE', '%' . $query . '%')
    //             ->limit(10)  // Limit the results to 10
    //             ->get(['id', 'prop_ref_no', 'prop_name', 'line_1', 'line_2', 'city', 'country', 'postcode', 'specific_property_type', 'available_from', 'property_type', 'price', 'letting_price']);
    //     }

    //     // Return the properties as JSON response
    //     return view('backend.contacts.contact_form.property_search_results', compact('properties'));
    // }


    public function getQuickStepView($step, Request $request)
    {
        // Get contact_id from the session or request
        $contact_id = $request->contact_id;
        $contact = Contact::find($contact_id);
        $categories = ContactCategory::all();
        $selectedProperties = $selectedProperties = json_decode($contact->selected_properties, true);
        // Get the total number of steps dynamically
        $totalSteps = $this->getTotalQuickSteps();

        // Check if the step is valid
        if ($step > 0 && $step <= $totalSteps) {
            return view('backend.contacts.contact_form.step' . $step, compact('contact','categories', 'selectedProperties')); // Return the corresponding Blade view
        } else {
            // Return a view with an error message if the step is invalid
            return view('backend.contacts.contact_form.error', ['message' => 'Invalid step.']);
        }
    }

    public function quicklyStoreContact(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email',
            'phone' => 'required|string|max:20',
        ]);

        // Create a new contact
        $contact = Contact::create([
            'category_id' => $request->category_id ?? 9,
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'added_by' => Auth::id(),
        ]);

        // Return the contact data as a JSON response
        return response()->json([
            'success' => true,
            'contact' => $contact
        ]);

    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            // 'category_id' => 'required|exists:contact_categories,id',
            'first_name' => 'required|string|max:55',
            'middle_name' => 'nullable|string|max:55',
            'last_name' => 'required|string|max:55',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:55',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'postcode' => 'required|string|max:15',
            'city' => 'required|string|max:55',
            'country' => 'required|string|max:55',
            'status' => 'required|in:0,1',
        ]);

        // Concatenate first, middle, and last names to create full_name
        $fullName = trim($request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name);
        $Category_id = $request->category_id;
        // Store the contact
        Contact::create([
            'category_id' => $Category_id,
            'first_name' => $validatedData['first_name'],
            'middle_name' => $validatedData['middle_name'],
            'last_name' => $validatedData['last_name'],
            'full_name' => $fullName,
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'address_line_1' => $validatedData['address_line_1'],
            'address_line_2' => $validatedData['address_line_2'],
            'postcode' => $validatedData['postcode'],
            'city' => $validatedData['city'],
            'country' => $validatedData['country'],
            'status' => $validatedData['status'],
            'updated_by' => Auth::user()->id,
        ]);

        // Redirect or return a response
        flash("Contact Added Successfully!")->success();
        return redirect()->route('admin.contacts.index');
        // return redirect()->route('contacts.index')->with('success', 'Contact created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */

     public function edit($id)
     {
         $contact = Contact::findOrFail($id); // Fetch the contact by ID
         $categories = ContactCategory::all(); // Fetch all categories
         $selectedProperties = json_decode($contact->selected_properties, true);
         return view('backend.contacts.edit', compact('contact', 'categories', 'selectedProperties'));
     }



    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            // 'category_id' => 'required|exists:contact_categories,id',
            'first_name' => 'required|string|max:55',
            'middle_name' => 'nullable|string|max:55',
            'last_name' => 'required|string|max:55',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:55',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'postcode' => 'required|string|max:15',
            'city' => 'required|string|max:55',
            'country' => 'required|string|max:55',
            'status' => 'required|in:0,1',
        ]);

        // Find the contact to be updated
        $contact = Contact::findOrFail($id);

        // Concatenate first, middle, and last names to create full_name
        $fullName = trim($request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name);
        $category_id = $request->category_id;
        // Update the contact
        $contact->update([
            'category_id' => $category_id,
            'first_name' => $validatedData['first_name'],
            'middle_name' => $validatedData['middle_name'],
            'last_name' => $validatedData['last_name'],
            'full_name' => $fullName,
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'address_line_1' => $validatedData['address_line_1'],
            'address_line_2' => $validatedData['address_line_2'],
            'postcode' => $validatedData['postcode'],
            'city' => $validatedData['city'],
            'country' => $validatedData['country'],
            'status' => $validatedData['status'],
            'updated_by' => Auth::user()->id,
        ]);

        // Redirect or return a response
        flash("Contact Updated Successfully!")->success();
        return redirect()->route('admin.contacts.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        // Find the contact to be deleted
        $contact = Contact::findOrFail($id);

        // Delete the contact
        $contact->delete();
        $response = [
            'status' => true,
            'notification' => 'Contact Deleted successfully!',
        ];

        return response()->json($response);

        // flash("Contact deleted successfully!")->success();
        // return redirect()->route('admin.contacts.index');
    }
}
