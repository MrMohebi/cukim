<?php

namespace App\Http\Controllers\api\admin\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class login extends Controller
{
    function login(Request $request)
    {

        $validatedData = $request->validate([
            "username" => "required",
            "password" => "required|min:7"
        ]);

        if ($validatedData) {
            $username = $request->username;
            $password = $request->password;

            $adminInfo = DB::table('admins')->where('username', $username)->first();


            if ($adminInfo) {
                if (Hash::check($password, $adminInfo->password)) {


                    return exit(json_encode(array(
                        'statusCode' => 200,
                        'data' => array(
                            "token" => $adminInfo['token'],
                            "position" => $adminInfo['position'],
                            "username" => $request->username,
                            "name" => $adminInfo['name'],
                            "phone" => $adminInfo['phone'],
                            "lastLogin" => $adminInfo['last_login'],
                            "promotedBy" => $adminInfo['promoted_by'],
                            "createdDate" => $adminInfo['created_date'],
                        )
                    )));
                } else {
                    return exit(json_encode(array('statusCode' => 401, 'details' => "username or password are wrong")));
                }
            } else {
                return exit(json_encode(array('statusCode' => 401, 'details' => "username or password are wrong")));
            }


        } else {
            exit(json_encode(array('statusCode' => 400, "details" => "wrong inputs")));
        }
    }

}
