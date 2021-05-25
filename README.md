# Cukim :)
 
# API V1:
>   - [client](#client-api)
>   - [res](#res-api)
>   - [resOwner](#resowner-api)
>   - [admin](#admin-api)




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
| [getResENameByCode](#cukigetresenamebycode-post) |  |
| [sendVCode](#cukisendvcode-post) |  |
| [verifyVCode](#cukiverifyvcode-post) |  |
| [setUserInfo](#cukisetuserinfo-post) |  |
| [getTempToken](#cukigettemptoken-post) |  |
| [getUserInfo](#cukigetuserinfo-post) |  |


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
>   - **token**
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
>   - **token**
>   - **resEnglishName**
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
>   - **token**
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
>
>   #### Return Values ``JSON``:
>   ```json
>   {
>     "statusCode": "[code]",
>     "data": {
>           "trackingId":  "",
>           "totalPrice":  "",
>           "deliveryAt": ""
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






<hr>

## Res API
| address | Description  |
| --- |---   |
|      |      |










<hr>

 ## ResOwner API
| address | Description  |
| --- |---   |
| [signup](#resownersignup-post) | register a new res owner |
| [createNewRes](#resownercreatenewres-post) | create a new restaurant |


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
