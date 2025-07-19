<?php

namespace App\Http\Controllers\Backend;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\WorkOrder;
use App\Models\RepairIssue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController
{
    public function dashboard()
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');  // Redirect to the login page if not authenticated
        }

        // Get the authenticated user
        $user = Auth::user();

        // Check if the authenticated user has the correct role (e.g., admin roles)
        if (!$user->hasAnyRole([
            'Super Admin',
            'Owner',
            'Property Manager',
            'Landlord',       // include any roles that should see the dashboard
            'Estate Agent',
            // etc.
        ])) {
            abort(403, 'Unauthorized.');
        }

        // Fetch all users with their roles (assuming the role relationship is defined in the User model)
        // $users = User::with('role')->get();
        $usersCount = User::count();
        $propertiesCount = Property::count();
        $invoicesCount = Invoice::count();
        $workOrdersCount = WorkOrder::count();
        $repairIssuesCount = RepairIssue::count();
        // Pass users to the view
        return view('backend.dashboard', compact(
            // 'users',
            'usersCount',
            'propertiesCount',
            'invoicesCount',
            'workOrdersCount',
            'repairIssuesCount'
        ));
    }


}
