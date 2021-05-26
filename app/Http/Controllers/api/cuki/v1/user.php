<?php

namespace App\Http\Controllers\api\cuki\v1;

use App\CustomFunctions\CusStFunc;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ghasedak\GhasedakApi;
use App\DatabaseNames\DN;


class user extends Controller
{
    public function sendVCode(Request $request){
        $validator = Validator::make($request->all(),[
            'phone'=>"required|size:11|starts_with:09",
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],"400");

        $phone = $request->input("phone");
        $smsApi = new GhasedakApi(env('GHASEDAKAPI_KEY'));

        $code = rand(1000,9999);

        $user = DB::table(DN::tables["USERS"])
            ->where(DN::USERS["phone"], $phone);

        // create new user if it doesnt exist
        if(!$user->exists()){
            $insertNewUserParams = [
                DN::USERS["phone"]=> $phone,
                DN::CA=> Carbon::now()->timestamp,
            ];
            DB::table(DN::tables["USERS"])->insert($insertNewUserParams);
        }

        $user->update([
           DN::USERS["vCode"]=>$code,
           DN::USERS["vCodeTries"]=>0,
           DN::UA=> Carbon::now()->timestamp,
        ]);


        if($smsApi->Verify($phone,1,'loginfoodusers',$code)){
            return response([
                "data"=> [
                    "phone"=> $phone
                ],
                "statusCode"=> 200]);
        }
        return response(["massage"=>"server error", "statusCode"=>500],"500");
    }

    public function verifyVCode(Request $request){
        $validator = Validator::make($request->all(),[
            'phone'=>"required|size:11|starts_with:09",
            "vCode"=>'required|size:4',
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],400);

        $phone = $request->input("phone");
        $vCode = (int)$request->input("vCode");

        $user = DB::table(DN::tables["USERS"])
            ->where(DN::USERS["phone"], $phone);

        // dont let attacker to find whom use ur service
        if(!$user->exists())
            return response(["massage"=>"code is not correct", "statusCode"=>401],401);

        if($user->value(DN::USERS["vCode"]) === $vCode){
            $token = CusStFunc::randomStringLower(32);
            $user->update([
                DN::USERS["vCode"]=>null,
                DN::USERS["vCodeTries"]=>0,
                DN::USERS["token"]=>$token,
                DN::UA=> Carbon::now()->timestamp,
            ]);
            return response(["data"=>[
                "phone"=>$phone,
                "token"=>$token,
                "isInfoComplete"=> strlen($user->value("name")) > 2,
            ],'statusCode'=>200]);
        }else{
            $user->increment(DN::USERS["vCodeTries"]);
            if($user->value(DN::USERS["vCodeTries"]) > 15)
                return response(["massage"=>"too many tries, please request new code", "statusCode"=>429],"429");

            return response(["massage"=>"code is not correct", "statusCode"=>401],"401");
        }

    }

    public function setUserInfo(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>"required|min:3",
            'job'=>"max:254",
            'birthday'=>"numeric",
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],"400");

        $name = $request->input("name");
        $job = $request->input("job");
        $birthday = $request->input("birthday");

        $user = DB::table(DN::tables["USERS"])->where(DN::USERS["token"],$request->input("token"));

        $user->update([
            DN::USERS["name"]=>$name,
            DN::USERS["job"]=>$job,
            DN::USERS["birthday"]=>$birthday,
            DN::UA=>Carbon::now()->timestamp,
        ]);

        return response(["statusCode"=> 200]);
    }

    public function getTempToken(Request $request){
        $validator = Validator::make($request->all(),[
            'resEnglishName'=>"required|min:3",
            'ip'=>"required|ip",
            'userAgent'=>"required",
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],400);


        $resEnglishName =  $request->input("resEnglishName");
        $ip =  $request->input("ip");
        $isp =  $request->input("isp");
        $city =  $request->input("city");
        $userAgent =  $request->input("userAgent");

        if(!DB::table(DN::tables["RESTAURANTS"])->where(DN::RESTAURANTS["eName"],$resEnglishName)->exists())
            return response(["massage"=>"restaurant is not valid", "statusCode"=>400],400);


        $newUserInfo_str = json_encode(array(
            'resEnglishName'=>$resEnglishName,
            'ip'=>$ip,
            'isp'=>$isp,
            'city'=>$city,
            'userAgent'=>$userAgent,
        ));

        // generate new temp token
        $userToken = "TEMPUSER_".CusStFunc::randomStringLower(64);

        $insertUserParams = array(
            DN::USERS["type"]=>"temp",
            DN::USERS["token"]=>$userToken,
            DN::USERS["info"]=>$newUserInfo_str,
            DN::USERS["phone"]=>'RAN'.rand(11111111,99999999),
            DN::UA=>Carbon::now()->timestamp,
            DN::CA=>Carbon::now()->timestamp,
        );



        if(DB::table(DN::tables["USERS"])->insert($insertUserParams)){
            return response(
                array(
                    'statusCode'=>200,
                    'data'=>array(
                        'token'=> $userToken
                    )
                )
            );
        }else{
            return response(["massage"=>"some thing went wrong! try again", "statusCode"=>500],500);
        }
    }


    public function getUserInfo(Request $request){
        $user = DB::table(DN::tables["USERS"])->where(DN::USERS["token"],$request->input("token"))->first();
        unset($user->{DN::USERS["vCode"]});
        unset($user->{DN::USERS["vCodeTries"]});
        unset($user->{DN::USERS["password"]});

        return response(["data"=>CusStFunc::arrayKeysToCamel(json_decode(json_encode($user))),"statusCode"=>200]);
    }

    public function getCustomerInfo(Request $request){
        $validator = Validator::make($request->all(),[
            'resEnglishName'=>"required|min:3",
        ]);

        if($validator->fails())
            return response(["massage"=>$validator->errors()->all(), "statusCode"=>400],400);


        $user = DB::table(DN::tables["USERS"])->where(DN::USERS["token"], $request->input("token"));
        $userPhone = $user->value(DN::USERS["phone"]);
        $customerInfo = DB::connection("resConn")
            ->table(DN::resTables["resCUSTOMERS"])
            ->where(DN::resCUSTOMERS["phone"],$userPhone);

        $ordersListInfo = DB::connection("resConn")
            ->table(DN::resTables["resORDERS"])
            ->where(DN::resORDERS["userPhone"],$userPhone)
            ->orderByDesc(DN::CA)
            ->limit(40);

        if($customerInfo->exists()){
            $customerInfo = $customerInfo->first();
            $customerInfo_arranged = array(
                'phone'=> $customerInfo->{DN::resCUSTOMERS["phone"]},
                'totalBought'=> $customerInfo->{DN::resCUSTOMERS["tOrderedPrice"]},
                'orderTimes'=> $customerInfo->{DN::resCUSTOMERS["orderTimes"]},
                'score'=> $customerInfo->{DN::resCUSTOMERS["score"]},
                'orderList'=> json_decode(json_encode($ordersListInfo->get()), true),
                'rank'=> $customerInfo->{DN::resCUSTOMERS["rank"]},
                'lastOrderDate'=> $customerInfo->{DN::UA},
            );
            return response(array('statusCode'=>200, 'data'=>CusStFunc::arrayKeysToCamel($customerInfo_arranged)));
        }else{
            return response(["massage"=>"customer didn't find OR didn't order anything from here yet", "statusCode"=>404],404);
        }

    }

}
