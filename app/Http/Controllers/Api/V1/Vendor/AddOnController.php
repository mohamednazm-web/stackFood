<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Models\AddOn;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;

class AddOnController extends Controller
{
    public function list(Request $request)
    {
        $vendor = $request['vendor'];
        // $limit = $request['limite']??25;
        // $offset = $request['offset']??1;
        $addons = AddOn::withoutGlobalScopes()->whereHas('restaurant',function($query)use($vendor){
            return $query->where('vendor_id', $vendor['id']);
        })->get();
        // ->orderBy('name')->paginate($limit, ['*'], 'page', $offset);
        // $data = [
        //     'total_size' => $addons->total(),
        //     'limit' => $limit,
        //     'offset' => $offset,
        //     'addons' => $addons->items()
        // ];

        return response()->json($addons,200);
    }

    public function store(Request $request)
    {
        if(!$request->vendor->restaurants[0]->food_section)
        {
            return response()->json([
                'errors'=>[
                    ['code'=>'unauthorized', 'message'=>trans('messages.permission_denied')]
                ]
            ],403);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $vendor = $request['vendor'];


        $addon = new AddOn();
        $addon->name = $request->name;
        $addon->price = $request->price;
        $addon->restaurant_id = $vendor->restaurants[0]->id;
        $addon->save();

        return response()->json(['message' => trans('messages.addon_added_successfully')], 200);
    }


    public function update(Request $request)
    {
        if(!$request->vendor->restaurants[0]->food_section)
        {
            return response()->json([
                'errors'=>[
                    ['code'=>'unauthorized', 'message'=>trans('messages.permission_denied')]
                ]
            ],403);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'price' => 'required',
        ], [
            'name.required' => 'Name is required!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $addon = AddOn::withoutGlobalScopes()->find($request->id);
        $addon->name = $request->name;
        $addon->price = $request->price;
        $addon->save();

        return response()->json(['message' => trans('messages.addon_updated_successfully')], 200);
    }

    public function delete(Request $request)
    {
        if(!$request->vendor->restaurants[0]->food_section)
        {
            return response()->json([
                'errors'=>[
                    ['code'=>'unauthorized', 'message'=>trans('messages.permission_denied')]
                ]
            ],403);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $addon = AddOn::withoutGlobalScopes()->findOrFail($request->id);
        $addon->delete();

        return response()->json(['message' => trans('messages.addon_deleted_successfully')], 200);
    }

    public function status(Request $request)
    {
        if(!$request->vendor->restaurants[0]->food_section)
        {
            return response()->json([
                'errors'=>[
                    ['code'=>'unauthorized', 'message'=>trans('messages.permission_denied')]
                ]
            ],403);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $addon_data = AddOn::withoutGlobalScopes()->findOrFail($request->id);
        $addon_data->status = $request->status;
        $addon_data->save();

        return response()->json(['message' => trans('messages.addon_status_updated')], 200);
    }

    public function search(Request $request){

        $vendor = $request['vendor'];
        $limit = $request['limite']??25;
        $offset = $request['offset']??1;

        $key = explode(' ', $request['search']);
        $addons=AddOn::withoutGlobalScopes()->whereHas('restaurant',function($query)use($vendor){
            return $query->where('vendor_id', $vendor['id']);
        })->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->orderBy('name')->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total_size' => $addons->total(),
            'limit' => $limit,
            'offset' => $offset,
            'addons' => $addons->items()
        ];

        return response()->json([$data],200);
    }
}
