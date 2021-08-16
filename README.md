# Cukim :)
 
# API V1:
>   - [client](#client-api)
>   - [res](#res-api)
>   - [resOwner](#resowner-api)
>   - [admin](#admin-api)
>   - [pay](#pay-api)




<hr>

## Client API 
| address | Description  |
| --- |---   |
| [getCommentsByFoodId](#cukigetcommentsbyfoodid-post) |  |
| [sendComment](#cukisendcomment-post) |  |
| [getIpInfo](#cukigetipinfo-get) |  |
| [sendOrder](#cukisendorder-post) |  |
| [getOpenOrders](#cukigetopenorders-post) |  |
| [getOrderByTrackingId](#cukigetorderbytrackingid-post) |  |
| [callPager](#cukicallpager-post) |  |
| [getPaymentByTrackingId](#cukigetpaymentbytrackingid-post) |  |
| [getResData](#cukigetresdata-post) |  |
| [getResParts](#cukigetresparts-post) |  |
| [getResInfo](#cukigetresinfo-post) |  |
| [getResFoods](#cukigetresfoods-post) |  |
| [getFoodById](#cukigetfoodbyid-post) |  |
| [getResUpdateDates](#cukigetresupdatedates-post) |  |
| [getResENameByCode](#cukigetresenamebycode-post) |  |
| [sendVCode](#cukisendvcode-post) |  |
| [verifyVCode](#cukiverifyvcode-post) |  |
| [setUserInfo](#cukisetuserinfo-post) |  |
| [getTempToken](#cukigettemptoken-post) |  |
| [getUserInfo](#cukigetuserinfo-post) |  |
| [getOffCodes](#cukigetoffcodes-post) |  |
| [validateOffCode](#cukivalidateoffcode-post) |  |
| [getResNames](#cukigetresnames-get) |  |


> ### ```/cuki/getUserInfo``` ```POST```
>
> #### Required fields:
>   - **token**
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {}  
>   }
>   ```



> ### ```/cuki/getTempToken``` ```POST```
>
> #### Required fields:
>   - **resEnglishName**
>   - **ip**
>   - **userAgent**
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {
>       "token": ""
>    }  
>   }
>   ```


> ### ```/cuki/setUserInfo``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **name**
> #### Optional fields:
>   - job
>   - birthday
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {
>       "phone": "",
>       "token": "",
>       "isInfoComplete": ""
>    }  
>   }
>   ```



> ### ```/cuki/verifyVCode``` ```POST```
>
> #### Required fields:
>   - **phone**
>   - **vCode**
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {
>       "phone": "",
>       "token": "",
>       "isInfoComplete": ""
>    }  
>   }
>   ```



> ### ```/cuki/sendVCode``` ```POST```
>
> #### Required fields:
>   - **phone**
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {
>       "phone": ""
>    }  
>   }
>   ```


> ### ```/cuki/getResENameByCode``` ```POST```
>
> #### Required fields:
>   - **resCode**
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {
>       "resEnglishName": ""
>    }  
>   }
>   ```



> ### ```/cuki/getResParts``` ```POST```
>
> #### Required fields:
>   - **resEnglishName**
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": []
>   }
>   ```


> ### ```/cuki/getResInfo``` ```POST```
>
> #### Required fields:
>   - **resEnglishName**
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": []
>   }
>   ```


> ### ```/cuki/getResFoods``` ```POST```
>
> #### Required fields:
>   - **resEnglishName**
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": []
>   }
>   ```



> ### ```/cuki/getResUpdateDates``` ```POST```
>
> #### Required fields:
>   - **resEnglishName**
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {
>       "foods": {"id": ["updateDate", "orderTimes"]},
>       "restaurantInfo": "int" 
>     }
>   }
>   ```



> ### ```/cuki/getFoodById``` ```POST```
>
> #### Required fields:
>   - **resEnglishName**
>   - **foodId**
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": []
>   }
>   ```






> ### ```/cuki/getResData``` ```POST```
>
> #### Required fields:
>   - **resEnglishName**
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": []
>   }
>   ```


> ### ```/cuki/getPaymentByTrackingId``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **table**
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": []
>   }
>   ```



> ### ```/cuki/callPager``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **resEnglishName**
>   - **table**
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": []
>   }
>   ```


> ### ```/cuki/getOrderByTrackingId``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **resEnglishName**
>   - **trackingId**
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": []
>   }
>   ```


> ### ```/cuki/getOpenOrders``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **resEnglishName**
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": []
>   }
>   ```


> ### ```/cuki/sendOrder``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **resEnglishName**
>   - **items**
> #### Optional fields:
>   - details
>   - deliveryAt
>   - deliveryPrice
>   - address
>   - table
>   - offCode
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {
>           "trackingId":  "",
>           "totalPrice":  "",
>           "deliveryAt": "",
>           "isOffCodeUsed": "bool"
>     }
>   }
>   ```





> ### ```/cuki/getCommentsByFoodId``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **resEnglishName**
>   - **foodId**
>   - **lastDate**
>   - **number**
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {
>          "comments": "array",
>          "isAllowedLeaveComment": "bool"
>     }
>   }
>   ```


> ### ```/cuki/sendComment``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **resEnglishName**
>   - **foodId**
>   - **body**
> #### Optional fields:
>   - title
>   - rate
>   - prosCons
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]"
>   }
>   ```


> ### ```/cuki/getIpInfo``` ```GET```
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {
>        "ip": "",
>        "city": "",
>        "country": "",
>        "location": "",
>        "timezone": ""
>     }
>   }
>   ```


> ### ```/cuki/getOffCodes``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **resEnglishName**
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": []
>   }
>   ```



> ### ```/cuki/validateOffCode``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **resEnglishName**
>   - **offCode**
>   - **amount**
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": [
>       "isOffCodeValid": "bool"
>     ]
>   }
>   ```





> ### ```/cuki/getResNames``` ```GET```
>
> #### Required fields:
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {
>            "englishNames": [],
>            "persianNames": []
>     }
>   }
>   ```



<hr>

## Res API
| address | Description  |
| --- |---   |
| [login](#reslogin-post) |  |
| [createCategory](#rescreatecategory-post) | |
| [createFood](#rescreatefood-post) | |
| [changeFoodInfo](#reschangefoodinfo-post) |  |
| [createFood](#rescreatefood-post) |  |
| [changeOrderStatus](#reschangeorderstatus-post) |  |
| [changeResInfo](#reschangeresinfo-post) |  |
| [changePassword](#reschangepassword-post) |  |
| [getCategoryList](#resgetcategorylist-post) |  |
| [getFoodList](#resgetfoodlist-post) |  |
| [getOrderList](#resgetorderlist-post) |  |
| [getPagerList](#resgetpagerlist-post) |  |
| [getResInfo](#resgetresinfo-post) |  |



> ### ```/res/login``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **username**
>   - **password**
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {
>         "token": "",
>         "position": "",
>         "username": "",
>         "persianName": "",
>         "englishName": "",
>         "permissions": "",
>         "ipgName": ""
>     }
>   }
>   ```







> ### ```/res/createCategory``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **catPersianName**
>   - **catEnglishName**
>   - logo
>   - type
>   - rank
>   - averageColor
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]"
>   }
>   ```



> ### ```/res/createFood``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **persianName**
>   - **group**
>   - englishName
>   - details
>   - price
>   - status
>   - deliveryTime
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]"
>   }
>   ```



> ### ```/res/changeFoodInfo``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **foodId**
>   - persianName
>   - englishName
>   - group
>   - details  [split by "+"]
>   - price
>   - status ["outOfStock", "inStock", "deleted"]
>   - discount
>   - deliveryTime
>   - counterAppFoodId
>   - foodThumbnail  [img file]
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {
>         "changedFields": [] 
>     }
>   }
>   ``` 



> ### ```/res/changeOrderStatus``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **status**
>   - **deleteReason**
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {
>         "trackingId": "",
>         "newStatus": "" 
>     }
>   }
>   ``` 



> ### ```/res/changeResInfo``` ```POST```
>
> #### Required fields:
>   - **token**
>   - persianName
>   - englishName
>   - status  ["open", "closed"]
>   - counterPhone
>   - phone
>   - addressText
>   - addressLink
>   - owner
>   - employers
>   - socialLinks
>   - openTime
>   - type
>   - minOrderPrice
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {
>         "changedFields": [] 
>     }
>   }
>   ``` 




> ### ```/res/changePassword``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **password**
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]"
>   }
>   ```




> ### ```/res/getCategoryList``` ```POST```
>
> #### Required fields:
>   - **token**
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {}
>   }
>   ```


> ### ```/res/getFoodList``` ```POST```
>
> #### Required fields:
>   - **token**
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {}
>   }
>   ```



> ### ```/res/getOrderList``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **startDate**
>   - endDate [default now]
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {}
>   }
>   ```



> ### ```/res/getPagerList``` ```POST```
>
> #### Required fields:
>   - **token**
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {}
>   }
>   ```




> ### ```/res/getResInfo``` ```POST```
>
> #### Required fields:
>   - **token**
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {}
>   }
>   ```



<hr>

## Pay API
| address | Description  |
| --- |---   |
| [createLink](#paycreatelink-post) | register a new res owner |



> ### ```/pay/createLink``` ```POST```
>
> #### Required fields:
>   - **resEnglishName**
>   - **token**
>   - **trackingId**
>   - **amount**
>   - **itemType**
>   - **items**
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {
>        "url": "https://xxx.xyz",
>        "amount": 100000,
>        "paymentId": "cukiX-XX-XXXX-X",
>        "totalPaid": "400000",
>        "totalPrice": 7000000
>     }
>   }
>   ```








<hr>

 ## ResOwner API
| address | Description  |
| --- |---   |
| [signin](#resownersignin-post) |  |
| [signup](#resownersignup-post) | register a new res owner |
| [sendVCode](#resownersendvcode-post) |  |
| [verifyVCode](#resownerverifyvcode-post) |  |
| [signupAndBuyPlan](#resownersignupandbuyplan-post) |  |
| [buyPlan](#resownerbuyplan-post) |  |
| [createNewRes](#resownercreatenewres-post) | create a new restaurant |
| [getPlans](#resownergetplans-post) |  |




> ### ```/resOwner/signin``` ```POST```
>
> #### Required fields:
>   - **username**
>   - **password**
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": []
>   }
>   ```



> ### ```/resOwner/signup``` ```POST```
>
> #### Required fields:
>   - **username**
>   - **password**
>   - **name**
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]"
>   }
>   ```


> ### ```/resOwner/sendVCode``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **phone**
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": [
>       "phone":"09111111111"
>     ]  
>   }
>   ```


> ### ```/resOwner/verifyVCode``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **vCode**
>   - **name**
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]"
>   }
>   ```



> ### ```/resOwner/signupAndBuyPlan``` ```POST```
>
> #### Required fields:
>   - **username** [__phone number__]
>   - **planId**
>   - **name**
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": [
>       "url":"",
>       "amount":"numeric",
>       "trackingId":""
>     ]  
>   }
>   ```



> ### ```/resOwner/buyPlan``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **planId**
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": [
>       "url":"",
>       "amount":"numeric",
>       "trackingId":""
>     ]  
>   }
>   ```



> ### ```/resOwner/createNewRes``` ```POST```
>
> #### Required fields:
>   - **token**
>   - **username**
>   - **password**
>   - **persianName**
>   - **englishName**
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]"
>   }
>   ```



> ### ```/resOwner/getPlans``` ```POST```
>
> #### Required fields:
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": []
>   }
>   ```




<hr>

## Admin API
| address | Description  |
| --- |---   |
| [login](#adminlogin-post) | log in |



> ### ```/admin/login``` ```POST```
>
> #### Required fields:
>   - **username**
>   - **password**
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {
>           "token" => "",
>           "position" => "",
>           "username" => "",
>           "name" => "",
>           "phone" => "",
>           "lastLogin" => "",
>           "promotedBy" => "",
>           "createdDate" => "",
>     }
>   }
> 
