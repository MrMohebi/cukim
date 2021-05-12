<?php

namespace App\Http\Controllers\api\admin\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class login extends Controller
{
    function login(Request $request)
    {

        $validatedData = Validator::make($request->all(),[
            "username" => "required",
            "password" => "required|min:7"
        ]);

        if (!$validatedData->fails()) {
            $username = $request->username;
            $password = $request->password;

            $adminInfo = DB::table('admins')->where('username', $username)->first();
            if ($adminInfo) {
                if (Hash::check($password, $adminInfo->password)) {
                    return response([
                        'statusCode' => 200,
                        'data' => [
                            "token" => $adminInfo->token,
                            "position" => $adminInfo->position,
                            "username" => $request->username,
                            "name" => $adminInfo->name,
                            "phone" => $adminInfo->phone,
                            "lastLogin" => $adminInfo->last_login,
                            "promotedBy" => $adminInfo->promoted_by,
                            "createdDate" => $adminInfo->created_at,
                        ]
                    ]);

                } else {
                    return response(['message' => "username or password are wrong",'statusCode' => 401,],401);
                }
            } else {
                return response(['message' => "username or password are wrong",'statusCode' => 401],401);
            }

        } else {
            return response(['message' => "wrong inputs",'statusCode' => 400],401);
        }
    }

}
