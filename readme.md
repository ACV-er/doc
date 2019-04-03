# 接口文档以及维护说明
# 湘大文库接口文档  

## 统一状态码  

|code|状态|生产环境出现|  
| :----: | :----: | :----: |
|0|成功|1|
|1|缺失参数|0|
|2|账号密码错误|1|
|3|错误访问|0|
|4|未知错误|0|
|5|其他错误|0|
|6|未登录|1|  

## PUT DELETE请求  

* 1  

> 直接使用PUT DELETE  

* 2 (laravel支持，该项目可用)

> 使用POST 用 _method 规定请求方式  
> 分别为 `_method=put` `_method=delete`   

## 用户  

### 登录  

> **url** :  `/login`  

* 访问方式：  POST  

> * 请求参数说明：
>  
> |参数名|参数类型|参数解释|示例|正则限制|  
> | :----: | :---: | :---: | :---: |:---:|
> |sid_id|String|学号|201705550800|`/^20[\d]{8,10}$/`|
> |password|String|教务密码|sky31sky31|`/^[^\s]{8,20}$/`|  

* 返回示例  

```json  
{
    "code":0,
    "status":"成功",
    "data":{
        "user_id":1,
        "stu_id":"201705550800",
        "nickname":"小白",
        "email":"23333@sky31.com",
        "score":100,
        "avatar":"default.jpg"，
        "downloads":3,
        "collections":0
    }
}
```  

> * 返回参数说明：
>  
> |参数名|参数类型|参数解释| 
> | :----: | :---: | :---: |
> |user_id|Integer|用户标识|
> |score|Integer|用户积分|
> |avatar|String|用户头像|
> |downloads|Integer|下载数|
> |collections|Integer|收藏数|

* 头像地址为 /upload/avatar/{avatar}  

### 用户信息  

> 描述： 获取当前登录用户信息  

> **url** : `/user/info`  

* 访问方式： GET  

> * 请求参数说明：无  

* 返回示例：  

```json
{
    "code":0,
    "status":"成功",
    "data":{
        "user_id":1,
        "stu_id":"201705550820",
        "nickname":"未命名的小朋友",
        "email":null,
        "score":50,
        "avatar":"default.jpg",
        "downloads":3,
        "collections":0
    }
}
```  

* 返回参数说明： 同登录接口  

### 上传头像  

> 描述：改变当前用户头像  

> **url** : `/user/avatar`  

* 访问方式： POST  

> * 请求参数说明  
>  
> |参数名|参数类型|参数解释| 
> | :----: | :---: | :---: |
> |avatar|bytes|头像文件，二进制形式|

* 返回示例  

```json
{
    "code":0,
    "status":"成功",
    "data":104
}
```  

> * 返回参数说明：无  

### 昵称更改  

> 描述： 更改用户昵称  

> **url** : `/user/nickname`  

* 访问方式：POST  

> * 请求参数说明  
>  
> |参数名|参数类型|参数解释|示例|正则限制|
> | :----: | :---: | :---: | :---: | :---: |
> |nickname|String|新昵称|小白|`/^[\s\S]{2,60}$/`|  

* 返回示例  

```json  
{
    "code":0,
    "status":"成功",
    "data":119
}
```  

> * 返回参数说明：无  

## 文档  

### 上传  

> **url** :  `/document`  

* 访问方式：  PUT  

> * 请求参数说明：
>  
> |参数名|参数类型|参数解释|示例|正则限制|  
> | :----: | :---: | :---: | :---: | :---: |
> |title|String|标题|模电资料|`/^[\s\S]{0,300}$/`|
> |description|String|摘要|2017级模电期末复习资料|`/^[\s\S]{0,600}$/`|  
> |score|Integer|下载需要积分数(0-19)|5|`/^(1|)\d$/`|
> |tag|Integer|文件标签，数组下标|0|`/^\d$/`|
> |document|bytes|文档文件，二进制形式|||  

> tag 对应数组, 可改动  
```json
["物理","计算机","法学","文学","历史学","政治"]
```

* 返回示例  

```json  
{
    "code":0,
    "status":"成功",
    "data":123
}
```  

> * 返回参数说明：无  

### 文档信息  

> **url** : `/document/info/{id}`  

* 访问方式： GET  

> * 请求参数说明: id为文件id  

* 返回示例  

```json  
{
    "code":0,
    "status":"成功",
    "data":{
        "id":2,
        "name":"湘大文库改版说明文档.docx",
        "type":3,
        "tag":"3",
        "uploader":1,
        "uploader_nickname":"未命名的小朋友",
        "score":5,
        "downloads":0,
        "description":"湘大文科需求文档",
        "title":"湘大文科需求文档",
        "buy":false
    }
}
```  

> * 返回参数说明：
>  
> |参数名|参数类型|参数解释| 
> | :----: | :---: | :---: |
> |uploader|Integer|上传者id|
> |score|Integer|下载所需积分|  
> |buy|bool|是否已购买|  

### 文档信息更新  

> **url** : `/document/info/{id}`  

* 访问方式： POST  

> * 请求参数说明 id 为文件id  
>   
> |参数名|参数类型|参数解释|示例|正则限制|  
> | :----: | :---: | :---: | :---: | :---: |
> |title|String|标题|模电资料|`/^[\s\S]{0,300}$/`|
> |description|String|摘要|2017级模电期末复习资料|`/^[\s\S]{0,600}$/`|  
> |score|Integer|下载需要积分数(0-19)|5|`/^(1|)\d$/`|
> |tag|Integer|文件标签，数组下标|0|`/^\d$/`|  

* 返回示例  

```json
{
    "code":0,
    "status":"成功",
    "data":120
}
```  

> * 返回参数说明：无 

### 文档文件更新  

> **url** : `/document/{id}`  
 
* 访问方式： POST  

> * 请求参数说明 id 为文件id  
>   
> |参数名|参数类型|参数解释|
> | :----: | :---: | :---: |
> |document|bytes|文档文件，二进制形式|  

### 文档下载  


* 返回示例  

```json
{
    "code":0,
    "status":"成功",
    "data":120
}
```  

> * 返回参数说明：无  

### 添加、取消收藏  

> **url** : `/collection/{id}`  

* 访问方式： PUT(添加) DELETE(取消)  

* 参数说明：无  

* 返回示例  

```json
{
    "code":0,
    "status":"成功",
    "data":104
}
```  

> * 返回参数说明：无  


# 维护说明  

## 使用框架  

> laravel  

## 数据库  

> 见 .env 文件  **DB_DATABASE**  
> 数据库结构见数据库本身 或 database/migrations 内迁移文件

## 配置信息  

> .env  
> config/user.php  

## 自定义全局 类、函数  

* DFA 敏感词过滤  
* check checkUser msg compress saveFile(具体功能看文件注释)  

* 文件位置
> /app/helper/DFA.php  
> /app/helper/helper.php  

## 文档保存位置  

* storage/document  

## 一些需要注意的更改(对laravel)  

> 具体更改见注释

> laravel session_id 在cookie命名 固定为 laravel_session  文件为 config/session.php  

## DEBUG  

> 返回数据中 code 非0一般在data中会有一个数字 为行号 、\_\_LINE\_\_  
> 错误日志位置  storage/logs

