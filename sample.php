<?php

use WeixinAPI\Api;

// 引入 核心类
include './WeixinAPI/autoloader.php';

$CORP_ID = ''; // 企业号CORP_ID
$CORP_SECRECT = ''; // 企业号CORP_SECRECT
$cacheDriver = 'File'; // 缓存方式 目前有两种 Redis 和 File. 使用Redis, 请先调整Redis驱动构造方法中的参数

// 初始化
Api::init($CORP_ID, $CORP_SECRECT, $cacheDriver);

// 使用工厂方法获取相应的接口模块
$api = Api::factory('Xxxx');

// 具体方法请参照 类中的具体方法注释
$res = $api->xxxx();

// 返回结果
var_dump($res);

// 如果失败
if (false === $res) {
    // 二者均可获取错误信息
    var_dump(Api::getError());
    var_dump($api->getError());
}
