# 湘大文库接口文档  

## 统一状态码  

|code|状态|生产环境出现|
| :----: | :----: | :----: |
|0|成功|1|
|1|缺失参数|0|
|2|账号密码错误|1|
|3|错误访问|0|
|4|未知错误|1|
|5|其他错误|0|
|6|未登录|1|
|7|操作频繁|1|

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
> |stu_id|String|学号|201705550800|`/^20[\d]{8,10}$/`|
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
        "avatar":"default.jpg",
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

### 个人(下载 上传 收藏)列表  及 最新上传 下载排行  

> 描述 单页最多十条数据，data为空则无数据

> **url** : `/user/download/{page}` `/user/upload/{page}` 
`/user/collection/{page}` `/upload/new/{page}` `/upload/sort/{page}`  

* 访问方式： GET  

> 请求参数说明 {page} 为获取第几页 从1开始  

> 请求示例 `/user/download/1`

* 返回示例

```json  
{
    "code":0,
    "status":"成功",
    "data":[
        {
            "id":1,
            "name":"湘大文库改版说明文档.docx",
            "type":3,
            "tag":"3",
            "uploader":1,
            "uploader_nickname":"小白",
            "title":"湘大文科需求文档",
            "downloads":0,
            "description":"湘大文科需求文档更改",
            "score":5,
            "size":962545,
            "md5":"8075739a3b804ee0c316572aaabdea55",
            "page":20,
            "filename":"1554562783809.docx",
            "created_at":"2019-04-03 02:44:33"
        },
        {
            "id":2,
            "name":"湘大文库改版说明文档.docx",
            "type":3,
            "tag":"3",
            "uploader":1,
            "uploader_nickname":"未命名的小朋友",
            "title":"湘大文科需求文档",
            "downloads":0,
            "description":"湘大文科需求文档更改",
            "score":5,
            "size":962545,
            "md5":"8075739a3b804ee0c316572aaabdea55",
            "page":20,
            "filename":"1554562783809.docx",
            "created_at":"2019-04-03 02:44:33"
        }
    ]
}
```

* type tag 值与数字对应关系(以后有改动)

> > type对应数组:  
> 
> ```  
> ['pdf' => 1, 'doc' => 2, 'docx' => 3, 'txt' => 4, 'md' => 5, 'xlsx' => 6, 'ppt' => 7]
> ```
> 
> > tag 对应数组:  
> 
> ``` 
> ["物理" => 1, "计算机" => 2, "法学" => 3, "文学" => 4, "历史学" => 5, "政治" => 6]
> ```

> * 返回参数说明：
>  
> |参数名|参数类型|参数解释|
> | :----: | :---: | :---: |
> |uploader|Integer|上传者id|
> |uploader_nickname|string|上传者nickname|
> |score|Integer|下载所需积分|
> |page|Integer|文档页码|
> |filename|String|文档文件存储名|
> |created_at|String|文件发布时间|

### 添加、取消收藏  

> **url** : `/collection/{id}`  

* 访问方式： PUT(添加) DELETE(取消)  

> * 请求参数说明：无  

* 返回示例  

```json  
{
    "code":0,
    "status":"成功",
    "data":104
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


* 返回示例  

```json  
{
    "code":0,
    "status":"成功",
    "data":123
}
```

> * 返回参数说明：无  

### 单个文档信息  

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
        "page":20,
        "filename":"1554562783809.docx",
        "created_at":"2019-04-03 02:44:30",
        "buy":false
    }
}
```

> * 返回参数说明：
>  
> |参数名|参数类型|参数解释|
> | :----: | :---: | :---: |
> |uploader|Integer|上传者id|
> |uploader_nickname|string|上传者nickname|
> |score|Integer|下载所需积分|
> |buy|bool|是否已购买|
> |page|Integer|文档页码|
> |filename|String|文档文件存储名|
> |created_at|String|文件发布时间|

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

### 文档删除  

> **url** : `/document/{id}`  

* 访问方式： DELETE(取消)  

> * 请求参数说明：id 为文件 id  

* 返回示例  

```json  
{
    "code":0,
    "status":"成功",
    "data":174
}
```

> * 返回参数说明：无  

### 文档下载  

> 描述 直接访问为下载文件(没有权限则跳转到{待定})  

> **url** : `/download/{id}`  

* 访问方式： GET

> * 请求参数说明：id 为文件 id  

* 返回示例：无  

### 分类与搜索  

> **url** : `/document/search/{page}`  

* 访问方式： GET

> * 请求参数说明：page 为页码  
>   
> |参数名|参数类型|参数解释|示例|限制|
> | :----: | :---: | :---: | :---: | :---: |
> |tag|Integer|文件标签|[1,2,3,4]|数字数组 转 JSON|
> |type|Integer|文件类型|[1,5]|数字数组 转 JSON|
> |keyword|Integer|关键字|["自动化","期末"]|JSON|

> * 返回示例： 

```json  
{
    "code":0,
    "status":"成功",
    "data":[
        {
            "id":23,
            "name":"湘大文库改版说明文档.docx",
            "type":3,
            "tag":"3",
            "uploader":1,
            "uploader_nickname":"小白",
            "title":"湘大文科需求文档",
            "downloads":0,
            "description":"湘大文科需求文档~小白",
            "score":5,
            "size":962545,
            "page":20,
            "filename":"1554562783809.docx",
            "md5":"8075739a3b804ee0c316572aaabdea55",
            "created_at":"2019-04-05 13:44:39"
        }
    ]
}
```

> * 返回参数解释：无  

### 文档预览  

> 描述：返回图片 为该文章的一页  

* **url** : `/document/view/{id}/{page}`  

* 访问方式： GET  

> * 请求参数说明：  
>   
> |参数名|参数类型|参数解释|
> | :----: | :---: | :---: |
> |id|Integer|文章id|
> |page|Integer|页码|

