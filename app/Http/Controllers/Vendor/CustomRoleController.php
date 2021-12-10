<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\EmployeeRole;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomRoleController extends Controller
{
    public function create()
    {
        $rl=EmployeeRole::orderBy('name')->paginate(config('default_pagination'));
        return view('vendor-views.custom-role.create',compact('rl'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:employee_roles',
        ],[
            'name.required'=>'Role name is required!'
        ]);
        DB::table('employee_roles')->insert([
            'name'=>$request->name,
            'modules'=>json_encode($request['modules']),
            'status'=>1,
            'created_at'=>now(),
            'updated_at'=>now()
        ]);

        Toastr::success('Role added successfully!');
        return back();
    }

    public function edit($id)
    {
        $role=EmployeeRole::where(['id'=>$id])->first(['id','name','modules']);
        return view('vendor-views.custom-role.edit',compact('role'));
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'name' => 'required|unique:employee_roles,name,'.$id,
        ],[
            'name.required'=>'Role name is required!',
            'name.unique'=>'Role name already taken!',
        ]);

        DB::table('employee_roles')->where(['id'=>$id])->update([
            'name'=>$request->name,
            'modules'=>json_encode($request['modules']),
            'status'=>1,
            'updated_at'=>now()
        ]);

        Toastr::info(trans('messages.role_updated_successfully'));
        return redirect()->route('vendor.custom-role.create');
    }

    public function distroy($id)
    {
        $role=EmployeeRole::where(['id'=>$id])->delete();
        Toastr::info(trans('messages.role_deleted_successfully'));
        return back();
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $rl=EmployeeRole::
        where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->limit(50)->get();
        return response()->json([
            'view'=>view('vendor-views.custom-role.partials._table',compact('rl'))->render()
        ]);
    }
}
