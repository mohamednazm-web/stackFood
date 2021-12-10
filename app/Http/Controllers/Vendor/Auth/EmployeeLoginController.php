<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendorEmployee;

class EmployeeLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:vendor', ['except' => 'logout']);
    }

    public function login()
    {
        return view('vendor-views.auth.login');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);
        $employee = VendorEmployee::where('email', $request->email)->first();
        if($employee)
        {
            if($employee->vendor->status == 0)
            {
                return redirect()->back()->withInput($request->only('email', 'remember'))
            ->withErrors(['Inactive vendor! Please contact to admin.']);
            }
        }
        if (auth('vendor_employee')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            return redirect()->route('vendor.dashboard');
        }

        return redirect()->back()->withInput($request->only('email', 'remember'))
            ->withErrors(['Credentials does not match.']);
    }

    public function logout(Request $request)
    {
        auth()->guard('vendor_employee')->logout();
        return redirect()->route('vendor.auth.login');
    }
}
