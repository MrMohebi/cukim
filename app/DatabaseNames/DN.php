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
        "USERS"=>"users",
    ];

    const USERS = [
        "phone"=>"phone",
        "token"=>"token",
        "name"=>"name",
        "vCode"=>"verification_code",
        "vCodeTries"=>"verification_code_tries",
        "birthday"=>"birthday",
        "status"=>"status",
        "type"=>"type",
        "job"=>"job",
        "amount"=>"amount",
        "offCodes"=>"off_codes",
        "info"=>"info",
        "favPlaces"=>"favorite_places",
        "payments"=>"payments",
        "orders"=>"orders",
        "password"=>"password"
    ];

    const resTables = [
        "resFOODS"=>"foods",
        "resINFO"=>"info",
        'resORDERS'=>"orders",
        "resPAGERS"=>"pagers"
    ];

    const resPAGERS = [
      "table"=>"table",
      "status"=>"status",
      "userPhone"=>"user_phone"
    ];

    const resORDERS = [
        "trackingId"=>"tracking_id",
        "userPhone"=>"user_phone",
        "status"=>"order_status",
        "items"=>"items",
        "deliveryPrice"=>"delivery_price",
        "address"=>"address",
        "table"=>"table",
        "details"=>"details",
        "tPrice"=>"total_price",
        "deliveredAt"=>"delivered_at",
        "deliveryAt"=>"delivery_at",
        "deleteReason"=>"delete_reason",
        "offcode"=>"offcode",
        "howServe"=>"how_to_serve",
        "paymentStatus"=>"payment_status",
        "paidFoods"=>"paid_foods",
        "paidAmount"=>"paid_amount",
        "paymentIds"=>"payment_ids",
        "counterAppStatus"=>"counter_app_status",
    ];

    const resINFO = [
        "pName"=>"persian_name",
        "eName"=>"english_name",
        "counterPhone"=>'counter_phone',
        "type"=>"type",
        "status"=>"status",
        "phones"=>"phones",
        "address"=>"address",
        "addressLink"=>"address_link",
        "owner"=>"owner",
        "employers"=>"employers",
        "socialLinks"=>"social_links",
        "openTime"=>"open_time",
        "rate"=>"rate",
        "logoLink"=>"logo_link",
        "faviconLink"=>"favicon_link",
        "minOrderPrice"=>"minimum_order_price",
    ];

    const RESTAURANTS = [
        "username"=>"username",
        "token"=>"token",
        "pName"=>"persian_name",
        "eName"=>"english_name",
        "DBName"=>"db_name",
        "password"=>"password",
        "position"=>"position",
        "code"=>"res_code",
        "ownerId"=>"owner_id",
        "ownerName"=>"owner_name",
        "permissions"=>'permissions',
        "paymentKey"=>"payment_key",
        "ipgName"=>"ipg_name",
        "ipgToken"=>"ipg_token",
        "ipgData"=>"ipg_data"
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
        "counterAppFoodId"=>"counter_app_food_id"
    ];
}
