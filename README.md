# pinche
## 使用步骤：
### 1、fork,然后 clone 一份
### 2、根据实际使用的邮件提供商配置 env 文件，我用的是 mailgun
### 3、创建数据库 pinche ，之后根据所想使用的版本创建所需表结构
    php artisan migrate
### 4、执行 https://github.com/wutongwan/laravel-lego 所需
### 5、更新包 composer update -vvv 

## 关于版本：
### master 
master 是含有申请流程等的一套拼车程序不涉及到公众号纯 web 层面的操作,具体操作流程请 clone 代码之后看使用须知。
建议没有使用公众号,以及不想做微信那一整套复杂认证流程的同学使用,酌情更新
### version2
version2 是包含微信端操作的一套程序,取消了申请的流程,由车主点击车满,也是考虑到对接微信节育用户操作以及学习成本，为主要开发分支
具体操作流程请 clone 代码之后看使用须知  

## 有问题以及建议请联系 qq:952446652 

