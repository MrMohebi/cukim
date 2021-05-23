<?php

namespace App\Http\Controllers\api\res\v1;

use App\DatabaseNames\DN;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class resLogin extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "username" => "required",
            "password" => "required|min:7"
        ]);


        if ($validator->fails())
            return response(["massage" => $validator->errors()->all(), "statusCode" => 400], 400);

        $username = $request->input("username");
        $password = $request->input("password");

        $res = DB::table(DN::tables["RESTAURANTS"])->where(DN::RESTAURANTS["username"], $username);

        if ($res->exists()) {
            if (password_verify($password, $res->value(DN::RESTAURANTS["password"]))) {
                return response([
                    'statusCode' => 200,
                    'data' => [
                        "token" => $res->value(DN::RESTAURANTS["token"]),
                        "position" => $res->value(DN::RESTAURANTS["position"]),
                        "username" => $res->value(DN::RESTAURANTS["username"]),
                        "persianName" => $res->value(DN::RESTAURANTS["pName"]),
                        "englishName" => $res->value(DN::RESTAURANTS["eName"]),
                        "permissions" => $res->value(DN::RESTAURANTS["permissions"]),
                        "ipgName" => $res->value(DN::RESTAURANTS["ipgName"])
                    ]
                ]);

            } else {
                return response(['message' => "username or password are wrong", 'statusCode' => 401,], 401);
            }
        } else {
            return response(['message' => "username or password are wrong", 'statusCode' => 401], 401);
        }
    }
}
