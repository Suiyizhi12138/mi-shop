<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Http\Requests\AddUserAddressRequest as AddRequest;
use Illuminate\Support\Carbon;

class UserAddressController extends Controller
{
    //获取用户所有地址信息
    public function getAddresses(Request $request){
        $user = $request->user();
        $address = UserAddress::where('user_id','=',$user->id)->get();

        return response()->json($address);
    }
    //上传单个地址信心
    public function addAddress(AddRequest $request){
        
        $user = $request->user();
       
        $address = new  UserAddress([
            'contact_name' => $request->input('name'),
            'contact_phone' => $request->input('phone'),
            'district' => $request->input('district'),
            'address' => $request-> input('address'),
            'tag' => $request->input('tag'),
            'last_used_at' => Carbon::now() 
        ]);
        $address->user()->associate($user);
        
        $address->save();

        return response()->json(['status'=>'200']);
        
    }
    //修改单个地址信息
    public function updateAddress(AddRequest $request){
        
        $addressId = $request->input('ad_id');
        UserAddress::where('id','=',$addressId)->update([
            'contact_name' => $request->input('name'),
            'contact_phone' => $request->input('phone'),
            'district' => $request->input('district'),
            'address' => $request-> input('address'),
            'tag' => $request -> input('tag') 

           
        ]);

        return response()->json(['update'=>'success']);
        
    }
    //删除单个地址信息
    public function deleteAddress(Request $request){
        $id = $request->input('id');

       UserAddress::where('id','=',$id)->delete();

       return response()->json(['status'=>'200']);
    }
}
