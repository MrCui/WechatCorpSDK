#WeixinAPI PHP

##微信企业号主动调用接口的 PHP实现类包

##已实现了企业号接口中的大部分常用功能.

#WeixinAPI-PHP 使用文档

###1.使用微信api，需先将Api.class.php引入，该文件为本类包的核心类，对微信接口的操作都是基于此类进行开发. 核心类自动构建accesstoken,并实现了自动加载, 如引入到开发框架中,请自行调整自动加载功能.

###2.API文件夹为实际操作微信接口的class, 每个class的类名 对应微信企业号接口的模块(由基类的构造方法自动获得), 具体的接口节点由每个class里面的方法自行设置.

如: UserApi.class.php 类. 其对应的微信接口是

https://qyapi.weixin.qq.com/cgi-bin/user/

UserApi.class.php 类里的add 方法请求了节点 create 

所以其请求的完整接口 为:
https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token=ACCESS_TOKEN

###当前API列表:

BaseApi.class.php  		基类	

UserApi.class.php 		对用户的操作

DepartmentApi.class.php 对部门的操作

TagApi.class.php 		对标签的操作

AgentApi.class.php 		对应用的操作

JSSDKApi.class.php  	对jssdk的签名获取

MediaApi.class.php  	对媒体文件的操作

MessageApi.class.php 	对消息的操作


###3.Cache目录缓存AccessToken和js_ticket(如果缓存驱动设为File的情况下)

###4.CacheDriver目录为缓存驱动 其中Redis驱动 如需使用, 请先调整下参数, 最好引用框架的相关配置参数

#WeixinAPI-PHP 的扩展方法

想要扩展,只需在API文件夹下创建文件即可，但创建的class的命名规范须遵循此接口下的开发规范，命名为 微信接口模块名+Api.class.php并继承BaseApi, 如UserApi.class.php

其中User说过了, 是对应微信接口的模块, 至于对应的接口模块的节点请自行看其它Api中的调用方式.

##使用方法:
use WeixinAPI\Api;

// 引入 核心类
include '/WeixinAPI/Api.class.php';


$CORP_ID      = '';     // 企业号CORP_ID

$CORP_SECRECT = '';     // 企业号CORP_SECRECT

$cacheDriver  = 'File'; // 缓存方式 目前有两种 Redis 和 File


// 初始化
Api::init($CORP_ID, $CORP_SECRECT, $cacheDriver);


// 使用工厂方法获取相应的接口模块
$api = Api::factory('Xxxx');


// 具体方法请参照 类中的具体方法注释
$api->xxxx();


// 返回结果
$res = var_dump($res);


// 如果失败
if (false === $res) {
    // 二者均可获取错误信息
    var_dump(Api::getError());
    var_dump($api->getError());
}


###其中调用Message接口的操作方法略微特殊 采用的是链式操作

如$Message->touser()->toparty()->totag->file/image/text/news/()->issafe()->send($agentid);

其中touser, toparty, totag三者必须调用其一, 三者的实参: 单个 用户/部门/标签id 可以为一个字符串, 多个ID可以为一个一维数组.

file/image/text/news等具体的详细类型方法 只能同时调用一个, 否则覆盖.

issafe 是否是安全消息

send方法 为最终发送动作 其实参为 微信的agentid.

send之前的方法, 顺序任意.

联系邮箱:mhmrcui@126.com