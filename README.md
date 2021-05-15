# Cukim :)
 
# API V1:
>   - [client](#client-api)
>   - [res](#res-api)
>   - [resOwner](#resowner-api)
>   - [admin](#admin-api)




<hr style="height: 7px; border-radius: 3px;">

## Client API 
| address | Description  |
| --- |---   |
|      |      |









<hr style="height: 7px; border-radius: 3px;">

## Res API
| address | Description  |
| --- |---   |
|      |      |










<hr style="height: 7px; border-radius: 3px;">

 ## ResOwner API
| address | Description  |
| --- |---   |
| [signup](#sign-up-res-owner) | register a new res owner |
| [createNewRes](#create-new-restaurant) | create a new restaurant |

<hr>

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
<hr>



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






<hr style="height: 7px; border-radius: 3px;">

## Admin API
| address | Description  |
| --- |---   |
|      |      |
