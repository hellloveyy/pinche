# pinche
### 1、fork,然后 clone 一份
### 2、创建数据库 pinche
    php artisan migrate
### 3、执行 https://github.com/wutongwan/laravel-lego 所需
### 4、更新包 composer update -vvv 

## 关于版本
### master 
#### master 是含有申请流程等的一套拼车程序不涉及到公众号纯 web 层面的操作,具体使用 clone 代码之后看使用须知。
建议没有使用公众号,以及不想做微信那一整套复杂认证流程的同学使用,酌情更新
### version2
#### version2 是包含微信端操作的一套程序,取消了申请的流程,由车主点击车满,也是考虑到    