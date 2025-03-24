<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\JobType;
use App\Models\Tenancy;
use App\Models\TaxRates;
use App\Models\WorkOrder;
use App\Models\RepairIssue;
use App\Models\RepairPhoto;
use App\Models\TenantMember;
use Illuminate\Http\Request;
use App\Models\RepairHistory;
use App\Models\RepairCategory;
use App\Models\RepairAssignment;
use App\Models\RepairIssueContact;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\RepairIssuePropertyManager;
use App\Models\RepairIssueContractorAssignment;

class PropertyRepairController
{

    public function repairRaise()
    {
        $categories = RepairCategory::with(['subCategories', 'parentCategory'])  // Pass as an array of relationships
            ->whereNull('parent_id')
            ->orderBy('level')
            ->orderBy('position')
            ->get();

        // Get the maximum level in the table
        $maxLevel = RepairCategory::max('level');

        return view('backend.repair.create_raise_issue', compact('categories', 'maxLevel'));
    }

    public function getSubCategories($categoryId)
    {
        // var_dump($categoryId);
        $subCategories = RepairCategory::where('parent_id', $categoryId)
            ->orderBy('level')
            ->orderBy('position')
            ->get();

        if ($subCategories->isEmpty()) {
            return response()->json(['message' => 'No subcategories found'], 200);
        }
        return response()->json($subCategories);
    }

    public function getCategories()
    {
        // Fetch all categories with id, name, parent_id, and level
        $categories = RepairCategory::all(['id', 'name', 'parent_id', 'level']);

        // Organize categories into a hierarchical structure (group by parent_id)
        $categoriesByParent = [];

        // Loop through the categories to group them by parent_id
        foreach ($categories as $category) {
            $categoriesByParent[$category->parent_id][] = $category;
        }

        // Return categories as a JSON response, including their hierarchical structure
        return response()->json($categoriesByParent);
    }



    public function checkLastStep(Request $request)
    {
        // Get selected categories from the request
        $selectedCategories = $request->input('selectedCategories');

        // Ensure the selectedCategories array is not empty
        if (empty($selectedCategories)) {
            return response()->json([
                'isLastStep' => false,
                'message' => 'No categories selected.'
            ]);
        }

        // Get the last selected category ID and its corresponding level
        $lastCategoryId = end($selectedCategories);
        $lastCategory = RepairCategory::find($lastCategoryId);

        if (!$lastCategory) {
            return response()->json([
                'isLastStep' => false,
                'message' => 'Invalid category selected.'
            ]);
        }

        $currentLevel = $lastCategory->level;

        // Check if there are any categories with this category as a parent (level + 1)
        $hasSubcategories = RepairCategory::where('parent_id', $lastCategoryId)
            ->where('level', $currentLevel + 1)
            ->exists();

        return response()->json([
            'isLastStep' => !$hasSubcategories, // If no subcategories exist, it's the last step
            'message' => $hasSubcategories ? 'Subcategories available.' : 'No further subcategories.'
        ]);
    }

    public function index(Request $request)
    {
        $query = RepairIssue::query();

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('property', function ($q) use ($search) {
                $q->where('prop_name', 'LIKE', "%$search%")
                  ->orWhere('prop_ref_no', 'LIKE', "%$search%");
            });
        }

        // Apply status filter
        if ($request->has('status') && in_array($request->status, [
            'Pending', 'Reported', 'Under Process', 'Work Completed', 'Invoice Received', 'Invoice Paid', 'Closed'
        ])) {
            $query->where('status', $request->status);
        }

        $repairIssues = $query->paginate(10);

        return view('backend.repair.index', compact('repairIssues'));
    }


/*
    public function index()
    {
        $repairIssues = RepairIssue::paginate(10);
        return view('backend.repair.index', compact('repairIssues'));
    }*/

    // Show a single repair issue
    public function show($id)
{
        // Load the repair issue with relationships if needed
        $repairIssue = RepairIssue::with([
            'repairAssignments',
            'repairHistories',
            'repairIssueContacts',
            'repairPhotos',
            'property' // Eager load the related property
        ])->findOrFail($id);
        return view('backend.repair.view_raise_issue', compact('repairIssue'));
    }

    // Show the form for editing a repair issue
    // public function edit($id)
    // {
    //     $repairIssue = RepairIssue::findOrFail($id);
    //     return view('backend.repair.edit_raise_issue', compact('repairIssue'));
    // }
    public function edit($id)
    {
        // Load the repair issue with relationships if needed
        $repairIssue = RepairIssue::with([
            'repairAssignments',
            'repairHistories',
            'repairIssueContacts',
            'repairPhotos',
            'property',
            'workOrder'
            // 'workOrders'
        ])->findOrFail($id);

        // Load additional data for the form:
        $categories = RepairCategory::all(); // or get only the top-level categories for step2
        // Get the maximum level in the table
        $maxLevel = RepairCategory::max('level');
        // $propertyManagers = User::ofRole('property_manager')->get();
        $propertyManagers = Contact::whereHas('category', callback: function ($query) {
            $query->where('id', 2);
        })->get();

        // dd($repairIssue->repairPhotos);

        $assignedManagers = RepairIssuePropertyManager::where('repair_issue_id', $id)->pluck('property_manager_id')->toArray();
        $contractorAssignments = RepairIssueContractorAssignment::where('repair_issue_id', $id)->get();
        $contractors = Contact::whereHas('category', callback: function ($query) {
            $query->where('name', 'Contractor');
        })->get();
        // $contractors = User::whereHas('role', function ($query) {
        //     $query->where('name', 'contractor');
        // })->get();

        $jobTypes = JobType::getHierarchy();
        
        return view('backend.repair.edit_raise_issue', data: compact(
            'repairIssue',
            'categories',
            'maxLevel',
            'propertyManagers',
            'assignedManagers',
            'contractorAssignments',
            'contractors',
            'jobTypes',
        ));
    }
    // Update the specified repair issue
    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'repair_category_id' => 'required',
    //         'description' => 'required',
    //     ]);

    //     $repairIssue = RepairIssue::findOrFail($id);
    //     $repairIssue->repair_category_id = $request->repair_category_id;
    //     $repairIssue->description = $request->description;
    //     $repairIssue->status = $request->status ?? $repairIssue->status; // Keep the current status if not updated
    //     $repairIssue->save();

    //     flash('Repair issue updated successfully')->success();
    //     return redirect()->route('admin.repairs.index');
    // }
    public function update(Request $request, $id)
    {
        // Use Validator::make() to validate input.
        $validator = Validator::make($request->all(), [
            'property_id'            => 'required',  // May come as an array; we'll extract a scalar below.
            'repair_navigation'      => 'nullable',  // Expected as a JSON string.
            'repair_category_id'     => 'nullable|integer|exists:repair_categories,id',
            'repair_navigation_old'  => 'nullable',  // Expected as a JSON string.
            'repair_category_id_old' => 'nullable|integer|exists:repair_categories,id',
            'description'            => 'required|string',
            'priority'               => 'required|in:low,medium,high,critical',
            'status'                 => 'required|in:Pending,Reported,Under Process,Work Completed,Invoice Received,Invoice Paid,Closed',
            'tenant_availability'    => 'nullable|date_format:Y-m-d\TH:i',
            'access_details'         => 'nullable|string',
            'estimated_price'        => 'required|numeric',
            'vat_type'               => 'required|in:inclusive,exclusive',
            'vat_percentage'         => 'required_if:vat_type,exclusive|numeric', // VAT percentage is required if VAT type is 'exclusive'
            'property_managers'      => 'required|array',
            'tenant_id'              => 'nullable',
            'repair_photos'          => 'nullable|string',  // The input is a string of IDs
            'repair_photos.*'        => 'nullable|integer|exists:uploads,id', // Validate each ID
            'final_contractor_id'    => 'nullable|integer|exists:contacts,id',
            // Note: Contractor assignments are validated via dynamic rules.
        ]);

        // Check for validation failure.
        // if ($validator->fails()) {
        //     return redirect()->back()
        //                     ->withErrors($validator)
        //                     ->withInput();
        // }
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all(); // Get all errors as an array

            foreach ($errorMessages as $error) {
                flash($error)->error(); // Flash each error separately
            }

            return back()->withInput();
        }
        // Get validated data.
        $validated = $validator->validated();

        // Retrieve the repair issue record.
        $repairIssue = RepairIssue::findOrFail($id);

        // Process property_id: If it's a JSON-encoded array, decode it first.
        $propertyId = $validated['property_id'];
        if (!is_array($propertyId)) {
            $decoded = json_decode($propertyId, true);
            if (is_array($decoded)) {
                $propertyId = $decoded;
            }
        }

        if (is_array($propertyId)) {
            $propertyId = (int) reset($propertyId);
        } else {
            $propertyId = (int) $propertyId;
        }

        // Assuming there's a pivot table for many-to-many relationship
        if ($request->has('repair_photos')) {
            // Get the comma-separated list of photo IDs
            $photoIds = $request->input('repair_photos');

            // Assuming you want to update the 'photos' column in the repair_photos table
            $repairIssue->repairPhotos()->update(['photos' => $photoIds]);
        }


        // dd($propertyId);
        // Capture the original status before updating.
        $oldStatus = $repairIssue->status;

        // Get new values from the validated request
        $repairNavigation = $validated['repair_navigation'] ?? null;
        $repairCategoryId = $validated['repair_category_id'] ?? null;
        $finalContractorId = $validated['final_contractor_id'] ?? null;

        // If new values are not provided, use the old values
        if (empty($repairNavigation) || $repairNavigation == '{}') {
            $repairNavigation = $request->input('repair_navigation_old');
        }

        if (empty($repairCategoryId)) {
            $repairCategoryId = $request->input('repair_category_id_old');
        }

        // Update the main repair issue record.
        $repairIssue->update([
            'property_id'            => $propertyId,
            'repair_navigation'      => $repairNavigation, // using new value if provided or original value
            'repair_category_id'     => $repairCategoryId, // using new value if provided or original value
            'description'            => $validated['description'],
            'priority'               => $validated['priority'],
            'status'                 => $validated['status'],
            'tenant_availability'    => $validated['tenant_availability'] ?? null,
            'access_details'         => $validated['access_details'] ?? null,
            'estimated_price'        => $validated['estimated_price'],
            'vat_type'               => $validated['vat_type'],
            'vat_percentage'               => $validated['vat_percentage'],
            'final_contractor_id'    => $finalContractorId,
        ]);
        // Update property manager assignments:
        RepairIssuePropertyManager::where('repair_issue_id', $id)->delete();
        if ($request->has('property_managers')) {
            $assignedBy = Auth::id();
            if (!$assignedBy) {
                abort(403, 'Unauthorized: No user logged in.');
            }
            foreach ($request->input('property_managers') as $managerId) {
                RepairIssuePropertyManager::create([
                    'repair_issue_id'     => $id,
                    'property_manager_id' => $managerId,
                    'assigned_by'         => $assignedBy,
                    'assigned_at'         => now(),
                ]);
            }
        }

        // Update contractor assignments:
        $submittedAssignments = $request->input('contractor_assignments', []);

        foreach ($submittedAssignments as $index => $assignmentData) {
            if (!isset($assignmentData['id']) || empty($assignmentData['id'])) {
                RepairIssueContractorAssignment::create([
                    'repair_issue_id' => $repairIssue->id,
                    'contractor_id'   => $assignmentData['contractor_id'],
                    'cost_price'      => $assignmentData['cost_price'],
                    'assigned_by'     => Auth::id(),
                    'quote_attachment' => $assignmentData['quote_attachment'] ?? null,
                    'contractor_preferred_availability' => $assignmentData['contractor_preferred_availability'] ?? null,
                    'status' => 'Proposed',
                ]);
            } else {
                RepairIssueContractorAssignment::where('id', $assignmentData['id'])
                    ->update([
                        'contractor_id'   => $assignmentData['contractor_id'],
                        'cost_price'      => $assignmentData['cost_price'],
                        'assigned_by'     => Auth::id(),
                        'quote_attachment' => $assignmentData['quote_attachment'] ?? null,
                        'contractor_preferred_availability' => $assignmentData['contractor_preferred_availability'] ?? null,
                    ]);
            }
        }

        // Update tenant selection if provided.
        if ($request->filled('tenant_id')) {
            $repairIssue->tenant_id = $request->input('tenant_id');
            $repairIssue->save();
        }

        if($oldStatus != $validated['status']){
            // --- Record a History Entry ---
            RepairHistory::create([
                'repair_issue_id' => $id,
                'action'        => 'Updated repair issue',
                'previous_status' => $oldStatus,
                'new_status'      => $validated['status'],
            ]);

                // // --- Send Notifications ---
                // // Assuming you have a Notification class: App\Notifications\RepairIssueUpdated
                // // Gather users to notify. For example, notify assigned property managers and admin.
                // $usersToNotify = [];
                // // Notify property managers assigned to this repair issue.
                // foreach ($repairIssue->repairIssuePropertyManagers as $assignment) {
                //     if ($assignment->propertyManager) {
                //         $usersToNotify[] = $assignment->propertyManager;
                //     }
                // }
                // // Optionally, add admin users. For example, if admin has id 1:
                // $adminUser = User::find(1);
                // if ($adminUser) {
                //     $usersToNotify[] = $adminUser;
                // }
                // // Remove duplicate users.
                // $usersToNotify = array_unique($usersToNotify);
                // // Send notification.
                // foreach ($usersToNotify as $user) {
                //     $user->notify(new \App\Notifications\RepairIssueUpdated([
                //         'repair_issue_id' => $id,
                //         'message'       => "Repair issue updated from {$oldStatus} to {$validated['status']}"
                //     ]));
                // }

        }

        return redirect()->route('admin.property_repairs.index')
                        ->with('success', 'Repair issue updated successfully.');
    }


    // Remove the specified repair issue
    public function destroy($id)
    {
        $repairIssue = RepairIssue::findOrFail($id);
        $repairIssue->delete();

        flash('Repair issue deleted successfully')->success();
        return redirect()->route('admin.property_repairs.index');
    }

    public function raiseIssueStore(Request $request)
    {
        $request->validate([
            'property_id' => 'required',  // Ensure it's an array with at least one item
            'repair_category_id' => 'required|integer|exists:repair_categories,id',
            'repair_navigation' => 'required|json',
            'description' => 'required|string',
        ]);

        // Decode JSON categories
        $categories = json_decode($request->repair_navigation, true);

        // dd([
        //     'original_categories' => $request->repair_navigation,
        //     'converted_categories' => $categories,
        // ]);

        // Decode `property_id` if it's a stringified array (e.g., "[5]")
        $propertyId = $request->property_id;

        if (is_string($propertyId) && str_starts_with($propertyId, '[') && str_ends_with($propertyId, ']')) {
            $propertyId = json_decode($propertyId, true); // Convert JSON string to PHP array
        }

        // If it's an array, extract the first value
        if (is_array($propertyId)) {
            $propertyId = reset($propertyId);
        }

        // Ensure it's a valid integer
        $propertyId = (int) $propertyId;

        // dd([
        //     'original_property_id' => $request->property_id,
        //     'converted_property_id' => $propertyId,
        // ]);

        // Generate Reference Number using a private function
        // $repairReference = $this->generateRepairReferenceNumber();
        $repairReference = generateReferenceNumber( RepairIssue::class, 'reference_number', 'RESISQRPR');

        // Store repair request
        $repair = RepairIssue::create([
            'property_id' => $propertyId,
            'repair_navigation' => json_encode($categories),
            'repair_category_id' => $request->repair_category_id,
            'description' => $request->description,
            'status' => 'Pending',
            'reference_number' => $repairReference, // Store the reference number
        ]);

        // Store repair photos
        if ($request->has('repair_photos')) {
            RepairPhoto::create([
                'photos' => $request->repair_photos,
                'repair_issue_id' => $repair->id,
                'photo_type' => 'jpg',
            ]);
        }

        flash('Repair request raised successfully')->success();
        return redirect()->route('admin.property_repairs.create');
    }


    // public function update(Request $request, $id)
    // {
    //     $repairIssue = RepairIssue::findOrFail($id);
    //     $repairIssue->update($request->all());
    //     return redirect()->route('repairs.index');
    // }

    public function assignRepair(Request $request, $repairIssueId)
    {
        $repairAssignment = new RepairAssignment();
        $repairAssignment->repair_issue_id = $repairIssueId;
        $repairAssignment->assigned_to = $request->assigned_to;
        $repairAssignment->assigned_at = now();
        $repairAssignment->status = 'assigned';
        $repairAssignment->save();

        return redirect()->route('repairs.index');
    }

    public function createHistory($repairIssueId, $action)
    {
        RepairHistory::create([
            'repair_issue_id' => $repairIssueId,
            'action' => $action,
            'previous_status' => 'pending', // Example
            'new_status' => 'in-progress', // Example
        ]);

        return redirect()->route('repairs.index');
    }

    public function getPropertyTenants(Request $request)
    {
        // Get the property_id from the request.
        // Note: The property ID may be passed as an array; if so, we take the first element.
        $propertyId = $request->input('property_id');
        if (is_array($propertyId)) {
            $propertyId = (int) reset($propertyId);
        } else {
            $propertyId = (int) $propertyId;
        }

        // Retrieve tenancy IDs for the given property.
        $tenancyIds = Tenancy::where('property_id', $propertyId)
            ->pluck('id')
            ->toArray();

        // Retrieve tenant members associated with those tenancies, with their contact details.
        $tenantMembers = TenantMember::whereIn('tenancy_id', $tenancyIds)
            ->with('contact')
            ->get();

        // Map the results to a unique list of tenants.
        $tenants = $tenantMembers->map(function($member) {
            if ($member->contact) {
                return [
                    'id' => $member->contact->id,
                    'full_name' => $member->contact->full_name,
                    'email' => $member->contact->email,
                    'phone' => $member->contact->phone,
                ];
            }
            return null;
        })->filter()      // Remove any null entries.
          ->unique('id')  // Ensure unique tenant contacts.
          ->values();     // Reset the keys.


        return response()->json($tenants);
    }

    /**
     * Generate a unique and sequential repair reference number.
     *
     * @return string
    */
    // Generate a unique reference number
    // private function generateRepairReferenceNumber()
    // {
    //     // Find the last inserted property
    //     $lastProperty = RepairIssue::orderBy('id', 'desc')->first();

    //     // Extract and increment the numeric part
    //     if ($lastProperty && preg_match('/RESISQREP(\d+)/', $lastProperty->reference_number, $matches)) {
    //         $number = (int)$matches[1] + 1;
    //     } else {
    //         $number = 1; // Start from 1 if no property exists
    //     }

    //     // Format the new reference number (e.g., RESISQREP0000001)
    //     return 'RESISQREP' . str_pad($number, 7, '0', STR_PAD_LEFT);
    // }


    public function workOrderInvoice($repairId)
    {
        // Fetch the repair details
        $repairIssue = RepairIssue::findOrFail($repairId);

        // Fetch the work order related to this repair
        $workorder = WorkOrder::where('repair_issue_id', $repairIssue->id)->first();

        // Fetch the invoice related to this repair
        $invoice = Invoice::where('work_order_id', $workorder->id)->first();

        $jobTypes = JobType::getHierarchy();

        // Fetch available contacts for invoice
        $contacts = Contact::all();

        // Fetch tax rates
        $taxRates = TaxRates::all();

        // Fetch contractor assignment details (cost_price & quote_attachment)
        $contractorAssignment = RepairIssueContractorAssignment::where('repair_issue_id', $repairIssue->id)
            ->where('contractor_id', $repairIssue->final_contractor_id)
            ->first();

        $contractorCost = $contractorAssignment->cost_price ?? 0; // Get assigned cost price
        $quoteAttachment = $contractorAssignment->quote_attachment ?? null; // Get quote attachment

        // Pass data to the view
        return view('backend.repair.workorder-invoice', compact('repairIssue', 'workorder', 'invoice', 'contacts', 'taxRates', 'jobTypes', 'contractorCost', 'quoteAttachment'));
    }

}

