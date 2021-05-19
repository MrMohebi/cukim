<?php


namespace App\DatabaseNames;


class DN
{
    const CA = "created_at";
    const UA = "updated_at";

    const tables = [
        "RES_OWNERS"=>"res_owners",
        "FOOD_GROUPS"=>"food_groups",
        "RESTAURANTS"=>"restaurants",
    ];
    const resTables = [
        "resFOODS"=>"foods",
        "resINFO"=>"info"
    ];
    const resINFO = [
        "counterPhone"=>'counter_phone',
        "type"=>"type",
    ];

    const RESTAURANTS = [
        "token"=>"token",
        "eName"=>"english_name",
        "DBName"=>"db_name",
    ];

    const RES_OWNERS = [
        'vCode'=>"verification_code",
        'vCodeTries'=>"verification_code_tries",
        'token'=>"token",
        'phone'=>"phone"
    ];

    const FOOD_GROUPS=[
        "pName"=>"persian_name",
        "eName"=>'english_name',
        "logo"=>"logo",
        "status"=>"status",
        "type"=>"type",
        "rank"=>"rank",
        "resEName"=>"res_english_name",
        "averageColor"=>"average_color",
    ];

    const resFOODS=[
        "eName"=>"english_name",
        "pName"=>"persian_name",
        'group'=>"group",
        'groupId'=>"group_id",
        "details"=>"details",
        "price"=>"price",
        "status"=>"status",
        "orderTimes"=>"order_times",
        "discount"=>"discount",
        "deliveryTime"=>"delivery_time",
        "thumbnail"=>"thumbnail",
        "photos"=>"photos",
        "3dModel"=>"model3d",
        "relatedPName"=>"related_main_persian_name",
        "relatedEName"=>"related_main_english_name",
        "relatedPriceRange"=>"related_price_range",
        "relatedThumbnail"=>"related_thumbnail",
    ];
}
