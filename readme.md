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
        "avatar":"default.jpg"
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

* 头像地址为 /upload/avatar/{avatar}  

## 文档  

### 上传  

> **url** :  `/upload`  

* 访问方式：  POST  

> * 请求参数说明：
>  
> |参数名|参数类型|参数解释|示例|正则限制|  
> | :----: | :---: | :---: | :---: | :---: |
> |title|String|标题|模电资料|`/^[\s\S]{0,300}$/`|
> |description|String|摘要|2017级模电期末复习资料|`/^[\s\S]{0,600}$/`|  
> |score|Integer|下载需要积分数(0-19)|5|`/^(1|)\d$/`|
> |tag|Integer|文件标签，数组下标|0|`/^\d$/`|  

> tag 对应数组, 可改动  
```json
["物理","计算机","法学","文学","历史学","政治"]
```

* 返回示例  

```json
{
    "code":0,
    "status":"成功",
    "data":null
}
```  

> * 返回参数说明：
>  
> |参数名|参数类型|参数解释| 
> | :----: | :---: | :---: |
> |user_id|Integer|用户标识|


* 头像地址为 /uploadfolder/avatar/{avatar}  

# 维护说明  

## 使用框架  

> laravel  

## 数据库  

> 见 .env 文件  **DB_DATABASE**

## 个人配置信息  

> .env  
> config/user.php  

## 自定义全局 类、函数  

* DFA 敏感词过滤  
* check checkUser msg compress saveFile

* 文件位置
> /app/helper/DFA.php  
> /app/helper/helper.php  


## 文档保存位置  

* storage/document  

## 一些需要注意的更改(对laravel)  

> 具体更改见注释

> laravel session_id 在cookie命名 固定为 laravel_session  文件为 config/session.php  

