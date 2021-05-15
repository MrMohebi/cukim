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
|      |      |









<hr>

## Res API
| address | Description  |
| --- |---   |
|      |      |










<hr>

 ## ResOwner API
| address | Description  |
| --- |---   |
| [signup](#sign-up-res-owner) | register a new res owner |
| [createNewRes](#create-new-restaurant) | create a new restaurant |


### sign up res owner

> ```url: PUBLIC-DOMAIN/api/v1/resOwner/signUp``` ```POST```
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



### create new restaurant

> ```url: PUBLIC-DOMAIN/api/v1/resOwner/createNewRes``` ```POST```
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
|      |      |
