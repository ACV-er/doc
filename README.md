# 接口文档以及维护说明
# 湘大文库接口文档  

> 见 readme.md

# 维护说明  

## 使用框架  

> laravel  

## 依赖  

> libreoffice convert  
> (libreoffice 将其他文档转为pdf convert 将pdf转为swf文件 xpdf貌似似是依赖)  
> libreoffice 需要给执行者一个HOME目录 建立/home/*username*目录(为php所有者)  

```  
// 具体见 /toJpg.php 脚本
```  

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

## 文档保存位置(文档文件和swf预览文件)  

* storage/document  
* storage/document/xxxx/*.swf  

## 预览相关  

> 由于文件转换花费时间较多，运行时给了了一个脚本 toSwf.php 位于项目根目录  
> 若遇特殊情况（可能不存在）会导致脚本先完成从而无法预览 脚本耗时在3秒以上  
> 原因时脚本访问数据库存储页码数时，文档文件并未被写入数据库 从而页码为-1 无法预览    

## 一些需要注意的更改(对laravel)  

> 具体更改见注释

> laravel session_id 在cookie命名 固定为 laravel_session  文件为 config/session.php  

## DEBUG  

> 返回数据中 code 非0一般在data中会有一个数字 为行号 、\_\_LINE\_\_  
> 错误日志位置  storage/logs  

## 后端修复日志  

## 坑集合()  

