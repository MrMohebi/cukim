<?php


namespace App\DatabaseNames;


class DN
{
    const tables = [
        "RES_OWNERS"=>"res_owners",
    ];

    const RES_OWNERS = [
        'vCode'=>"verification_code",
        'vCodeTries'=>"verification_code_tries",
        'token'=>"token",
        'phone'=>"phone"
    ];

}
