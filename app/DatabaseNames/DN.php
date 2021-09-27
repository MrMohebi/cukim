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
        "PAYMENTS"=>"payments",
        "PLANS"=>"plans",
        "OFF_CODES"=>"off_codes",
        "ADMINS"=>"admins",
        "INVOICES"=>"invoices",
        "TEMP_RES_NAMES"=>"temp_res_names",
    ];

    const TEMP_RES_NAMES = [
        "resOwnerId"=>"res_owner_id",
        "pName"=>"persianName",
        "eName"=>"englishName"
    ];

    const INVOICES = [
        "resEName"=>"res_english_name",
        "paymentKey"=>"payment_key",
        "details"=>"details",
        "onlineTillNow"=>"online_till_now",
        "offlineTillNow"=>"offline_till_now",
        "onlineFromPrevious"=>"online_from_previous",
        "offlineFromPrevious"=>'offline_from_previous',
        "toPay"=>"to_pay",
        "status"=>"status",
        "paidAt"=>"paid_at",
        "paidAmount"=>"paid_amount",
        "resCardNumber"=>"res_card_number",
        "ourCardNumber"=>"our_card_number",
        "bankTrackingId"=>"bank_tracking_id",
        "creatorSupportName"=>"creator_support_name",
        "creatorSupportId"=>"creator_support_id",
        "payerSupportName"=>"payer_support_name",
        "payerSupportId"=>"payer_support_id",
        "vCodeTries"=>"verification_code_tries",
    ];

    const ADMINS = [
        "username"=>"username",
        "password"=>"password",
        "email"=>"email",
        "name"=>"name",
        "position"=>"position",
        "phone"=>"phone",
        "status"=>"status",
        "token"=>"token",
        "lastLogin"=>"last_login",
        "promotedBy"=>"promoted_by",
    ];

    const OFF_CODES =[
        "code"=>"code",
        "creator"=>"creator",
        "target"=>"target",
        "place"=>"place",
        "times"=>"times",
        "used"=>"used",
        "maxAmount"=>"max_amount",
        "minAmount"=>"min_amount",
        "disPercentage"=>"discount_percentage",
        "disAmount"=>"discount_amount",
        "name"=>"name",
        "body"=>"body",
        "from"=>"from",
        "to"=>"to",
        "status"=>"status"
    ];

    const PLANS =[
        "pName"=>"persian_name",
        "eName"=>"english_name",
        "items"=>"items",
        "details"=>"details",
        "price"=>"price",
        "disPercentage"=>"discount_percentage",
        "disAmount"=>"discount_amount",
        "buyTimes"=>"buy_times",
    ];

    const PAYMENTS = [
        "trackingId"=>"tracking_id",
        "amount"=>"amount",
        "status"=>"status",
        "item"=>"item",
        "itemType"=>"item_type",
        'paymentId'=>"payment_id",
        "paymentKey"=>"payment_key",
        "paymentCode"=>"payment_code",
        "paymentGroup"=>"payment_group",
        "paymentNum"=>"payment_num",
        "ipg"=>"ipg",
        "details"=>"details",
        "payerPhone"=>"payer_phone",
        "payerName"=>"payer_name",
        "payerCard"=>"payer_card",
        "payerCardHash"=>"payer_card_hash",
        "paypingCode"=>"payping_code",
        "paidAt"=>"paid_at",
        "verifiedAt"=>"verified_at"
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
        "resPAGERS"=>"pagers",
        "resCOMMENTS"=>"comments",
        "resCUSTOMERS"=>"customers",
    ];

    const resCUSTOMERS = [
        "phone"=>"phone",
        "orderTimes"=>"order_times",
        "orderList"=>"order_list",
        "score"=>"score",
        "tOrderedPrice"=>"total_order_price",
        "rank"=>"rank",
        "offCodes"=>"off_codes",
    ];

    const resCOMMENTS = [
        "phone"=>"phone",
        "name"=>"name",
        "trackingId"=>"tracking_id",
        "foodId"=>"food_id",
        "title"=>"title",
        "body"=>"body",
        "rate"=>"rate",
        "orderType"=>"order_type",
        "prosCons"=>"pros_cons",
        "status"=>"status",
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
        'phone'=>"phone",
        "username"=>"username",
        "password"=>"password",
        "name"=>"name",
        "restaurantsIds"=>"restaurants_ids",
        "paymentIds"=>"payment_ids",
        "plans"=>"plans",
        "email"=>"email",
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
