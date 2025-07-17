<?php

namespace App\Http\Controllers\Backend;

use Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StaffController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view all staffs'])->only('index');
        $this->middleware(['permission:add staff'])->only('create');
        $this->middleware(['permission:edit staff'])->only('edit');
        $this->middleware(['permission:delete staff'])->only('destroy');
    }

    public function index()
    {
        $staffs = Staff::paginate(10);
        return view('backend.staff.staffs.index', compact('staffs'));
    }

    public function create()
    {
        $roles = Role::where('id','!=',1)->orderBy('id', 'desc')->get();
        return view('backend.staff.staffs.create', compact('roles'));
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name'      => 'required|string|max:255',
                'email'     => 'required|email|unique:users,email',
                'password'  => 'required',
                'role_id'   => 'required|exists:roles,id',
            ]);
        } catch (ValidationException $e) {
            flashValidationErrors($e);
            return back()->withInput();
        }

        // 2) Wrap in transaction
        DB::beginTransaction();
        try {
            // 3) Create User
            $user = User::create([
                'name'           => $data['name'],
                'email'          => $data['email'],
                // 'phone'          => $data['mobile'] ?? null,
                'user_type'      => 'staff',
                'password'       => Hash::make($data['password']),
            ]);

            // 4) Assign Spatie role
            $roleName = Role::findOrFail($data['role_id'])->name;
            $user->assignRole($roleName);

            // 5) Create Staff record
            Staff::create([
                'user_id' => $user->id,
                'role_id' => $data['role_id'],
            ]);

            DB::commit();

            flash()->success('Staff has been added successfully');
            return redirect()->route('staffs.index');
        }
        catch (\Throwable $e) {
            DB::rollBack();
            flash()->error('Failed to add staff: '.$e->getMessage());
            return back();
        }
    }

    public function edit($id)
    {
        $staff = Staff::findOrFail(decrypt($id));
        $roles = $roles = Role::where('id','!=',1)->orderBy('id', 'desc')->get();
        return view('backend.staff.staffs.edit', compact('staff', 'roles'));
    }

    public function update(Request $request, $id)
    {
        // 1) Retrieve staff
        $staff = Staff::findOrFail($id);

        // 2) Validate inputs and flash errors with AIZ notify
        try {
            $data = $request->validate([
                'name'      => 'required|string|max:255',
                'email'     => "required|email|unique:users,email,{$staff->user->id}",
                // 'mobile' => 'nullable|string|max:20',
                'password'  => 'nullable',
                'role_id'   => 'required|exists:roles,id',
            ]);
        } catch (ValidationException $e) {
            flashValidationErrors($e);
            return back()->withInput();
        }
        // 3) Wrap in transaction
        DB::beginTransaction();
        try {
            $user  = $staff->user;

            // 4) Update User fields
            $user->name  = $data['name'];
            $user->email = $data['email'];
            // $user->phone = $data['mobile'] ?? null;

            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            $user->save();

            // 5) Update Staff.role_id
            $staff->role_id = $data['role_id'];
            $staff->save();

            // 6) Sync Spatie roles
            $roleName = Role::findOrFail($data['role_id'])->name;
            $user->syncRoles($roleName);

            DB::commit();

            flash()->success('Staff has been updated successfully');
            return redirect()->route('staffs.index');
        }
        catch (\Throwable $e) {
            DB::rollBack();
            flash()->error('Failed to update staff: '.$e->getMessage());
            return back();
        }
    }

    public function destroy($id)
    {
        User::destroy(Staff::findOrFail($id)->user->id);
        if(Staff::destroy($id)){
            flash('Staff has been deleted successfully')->success();
            return response(redirect()->route('staffs.index'));
        }
        flash()->error('Something went wrong');
        return back();
    }
}
