<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controller;

class RoleController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view staff roles'])->only('index');
        $this->middleware(['permission:add staff role'])->only('create');
        $this->middleware(['permission:edit staff role'])->only('edit');
        $this->middleware(['permission:delete staff role'])->only('destroy');
    }


    public function index()
    {
        $roles = Role::where('id', '!=', 1)->paginate(10);
        return view('backend.staff.staff_roles.index', compact('roles'));

        // $roles = Role::paginate(10);
        // return view('backend.staff.staff_roles.index', compact('roles'));
    }


    public function create()
    {
        return view('backend.staff.staff_roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->permissions);
        $role = Role::create(['name' => $request->name]);
        $role->givePermissionTo($request->permissions);

        flash('New Role has been added successfully')->success();
        return redirect()->route('roles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * //@return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $lang = $request->lang;
        $role = Role::findOrFail($id);
        return view('backend.staff.staff_roles.edit', compact('role', 'lang'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * //@return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        if ($request->lang == env("DEFAULT_LANGUAGE")) {
            $role->name = $request->name;
        }
        $role->syncPermissions($request->permissions);
        $role->save();

        flash('Role has been updated successfully')->success();
        return back();
        // return redirect()->route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * //@return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(env('DEMO_MODE') == 'On'){
            flash('Data can not change in demo mode.')->info();
            return back();
        }

        Role::destroy($id);
        flash('Role has been deleted successfully')->success();
        return redirect()->route('roles.index');
    }

    public function add_permission(Request $request)
    {
        $permission = Permission::create(['name' => $request->name, 'section' => $request->parent]);
        return redirect()->route('roles.index');
    }

    public function create_admin_permissions()
    {
    }
}
