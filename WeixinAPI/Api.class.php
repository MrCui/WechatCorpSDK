<?php

namespace WeixinAPI;

use WeixinAPI\API\BaseApi;
use WeixinAPI\Helper\Exception;

/**
 * 微信API基类.
 *
 * @author Cui.
 */
class Api
{
    private static $error = ''; // 错误信息;
    private static $selfInstanceMap = array(); // 实例列表;
    private static $CORP_ID; // 企业号corp_id;
    private static $CORP_SECRECT; // 企业号corp_secrect
    private static $CACHE_DRIVER; // 接口缓存驱动类名
    private static $postQueryStr = array(); // post数据时 需要携带的查询字符串

    const WEIXIN_BASE_API = 'https://qyapi.weixin.qq.com/cgi-bin';

    /**
     * 接口初始化, 必须执行此方法才可以使用接口.
     *
     * @author Cui
     *
     * @date   2015-07-29
     *
     * @param string $corpid      企业号corp_id
     * @param string $corpsecret  企业号corp_secrect
     * @param string $cacheDriver 接口缓存驱动类名
     */
    public static function init($corpid, $corpsecret, $cacheDriver = 'File')
    {
        self::$CORP_ID = $corpid;
        self::$CORP_SECRECT = $corpsecret;
        self::$CACHE_DRIVER = $cacheDriver;
    }

    /**
     * 工厂+多例模式 获取接口实例.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @param string $className 接口类名.
     *
     * @return object
     */
    public static function factory($className)
    {
        $className = __NAMESPACE__ . '\\API\\' . $className . 'Api';
        if (!$className || !is_string($className)) {
            throw new Exception('类名参数不正确', 1);
        }

        if (!class_exists($className)) {
            throw new Exception($className . '接口不存在', 1);
        }

        if (!array_key_exists($className, self::$selfInstanceMap)) {
            $api = new $className();
            if (!$api instanceof BaseApi) {
                throw new Exception($className . ' 必须继承 BaseApi', 1);
            }

            self::$selfInstanceMap[$className] = $api;
        }

        return self::$selfInstanceMap[$className];
    }

    /**
     * 设置错误信息.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @param string $errorText 错误信息
     */
    public static function setError($errorText)
    {
        self::$error = $errorText;
    }

    /**
     * 获取错误信息.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @return string
     */
    public static function getError()
    {
        return self::$error;
    }

    /**
     * 设置post操作的get参数.
     *
     * @author Cui
     *
     * @date   2015-08-03
     *
     * @param string $name  参数名
     * @param string $value 值
     */
    public static function setPostQueryStr($name, $value)
    {
        self::$postQueryStr[$name] = $value;
    }

    /**
     * 获取当前操作企业号的corpid.
     *
     * @author Cui
     *
     * @date   2015-07-29
     *
     * @return string
     */
    public static function getCorpId()
    {
        return self::$CORP_ID;
    }

    /**
     * 获取当前操作企业号的corpsecrect.
     *
     * @author Cui
     *
     * @date   2015-07-29
     *
     * @return string
     */
    public static function getSecrect()
    {
        return self::$CORP_SECRECT;
    }

    /**
     * 获取允许访问的token.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @return string
     */
    public static function getAccessToken()
    {
        $key = self::$CORP_SECRECT . 'access_token';
        $token = self::cache($key);
        if (false == $token) {
            $corpId = self::$CORP_ID;
            $corpSecrect = self::$CORP_SECRECT;
            $module = 'gettoken';
            $queryStr = array(
                'corpid' => $corpId,
                'corpsecret' => $corpSecrect,
            );

            $res = self::_get($module, '', $queryStr);
            if (false === $res) {
                throw new Exception('获取AccessToken失败!', 1);
            }

            $token = $res['access_token'];

            // 企业微信更新了token刷新机制
            self::cache($key, $token, 7200 - 300);
        }

        return $token;
    }

    /**
     * 用get的方式访问接口.
     *
     * @author Cui
     *
     * @date   2015-07-29
     *
     * @param string $module   指定接口模块
     * @param string $node     指定接口模块的节点
     * @param array  $queryStr 查询字符串
     * @param array  $header   http头部附加信息
     *
     * @return array 错误时返回false
     */
    public static function _get($module, $node = '', $queryStr = array(), $header = array())
    {
        if ($module != 'gettoken') {
            $queryStr['access_token'] = self::getAccessToken();
            asort($queryStr);
        }

        $queryStr = http_build_query($queryStr);
        $apiUrl = rtrim(self::WEIXIN_BASE_API . '/' . $module . '/' . $node, '/');
        $apiUrl .= '?' . $queryStr;

        $header[] = 'Bizmp-Version:2.0';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $res = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $header = '';
        $body = $res;
        if ($httpcode == 200) {
            list($header, $body) = explode("\r\n\r\n", $res, 2);
            $header = self::parseHeaders($header);
        }

        $result['info'] = $body;
        $result['header'] = $header;
        $result['status'] = $httpcode;

        return self::packData($result);
    }

    /**
     * 用post的方式访问接口.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @param string $module     指定接口模块
     * @param string $node       指定接口模块的节点
     * @param array  $data       要发送的数据
     * @param bool   $jsonEncode 是否转换为jsons数据
     *
     * @return array 错误时返回false;
     */
    public static function _post($module, $node = '', $data, $jsonEncode = true)
    {
        $token = self::getAccessToken();
        if (false === $token) {
            return false;
        }

        $postQueryStr = self::$postQueryStr;
        $postQueryStr['access_token'] = $token;
        asort($postQueryStr);

        $postQueryStr = http_build_query($postQueryStr);

        // 获取数据后 重置数据;
        self::$postQueryStr = array();

        $apiUrl = rtrim(self::WEIXIN_BASE_API . '/' . $module . '/' . $node, '/');
        $apiUrl .= '?' . $postQueryStr;

        if ($jsonEncode) {
            if (is_array($data)) {
                if (!defined('JSON_UNESCAPED_UNICODE')) {
                    // 解决php 5.3版本 json转码时 中文编码问题.
                    $data = json_encode($data);
                    $data = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $data);
                } else {
                    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
                }
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);

        // 对上传操作做的特殊判断
        if (class_exists('\CURLFile')) {
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
        } else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
            }
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $res = trim(curl_exec($ch));
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $header = '';
        $body = $res;
        if ($httpcode == 200) {
            list($header, $body) = explode("\r\n\r\n", $res, 2);
            $header = self::parseHeaders($header);
        }

        $result['info'] = $body;
        $result['header'] = $header;
        $result['status'] = $httpcode;

        return self::packData($result);
    }

    /**
     * 对接口返回的数据进行验证和组装.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @param array $apiReturnData 由_post|| _get方法返回的数据.
     *
     * @return array
     */
    private static function packData($apiReturnData)
    {
        if ($apiReturnData['status'] != 200) {
            self::setError('微信接口服务器连接失败.');

            return false;
        }

        $status = $apiReturnData['status'];
        $info = $apiReturnData['info'];
        $header = $apiReturnData['header'];
        $apiReturnData = json_decode($info, true);

        $log = array();
        $log['httpcode'] = $status;
        $log['response'] = $info;

        if ($status != 200 && !$apiReturnData) {
            self::setError($info);

            return false;
        }

        // 获取文件的特殊设置.
        if (!$apiReturnData) {
            $log['response'] = array();
            $apiReturnData = array();
            $apiReturnData['content'] = base64_encode($info);
            $apiReturnData['type'] = $header['Content-Type'];
            $apiReturnData['size'] = $header['Content-Length'];

            if (isset($header['Content-disposition'])) {
                $res = preg_match('/".+"/', $header['Content-disposition'], $matchArr);

                if ($res && $matchArr) {
                    $apiReturnData['filename'] = reset($matchArr);
                    $log['response']['filename'] = $apiReturnData['filename'];
                }
            }

            $log['response']['type'] = $apiReturnData['type'];
            $log['response']['size'] = $apiReturnData['size'];
        }

        if (isset($apiReturnData['errcode']) && $apiReturnData['errcode'] != 0) {
            self::setError('错误码:' . $apiReturnData['errcode'] . ', 错误信息:' . $apiReturnData['errmsg']);

            return false;
        }

        if (isset($apiReturnData['errcode'])) {
            unset($apiReturnData['errcode']);
        }

        if (count($apiReturnData) > 1 && isset($apiReturnData['errmsg'])) {
            unset($apiReturnData['errmsg']);
        }

        if (count($apiReturnData) == 1) {
            $apiReturnData = reset($apiReturnData);
        }

        return $apiReturnData;
    }

    /**
     * 解析头部信息.
     *
     * @author 互联网
     *
     * @date   2015-08-03
     *
     * @param array $raw_headers http header
     *
     * @return array
     */
    public static function parseHeaders($raw_headers)
    {
        if (function_exists('http_parse_headers')) {
            return http_parse_headers($raw_headers);
        }

        $headers = array();
        $key = '';

        foreach (explode("\n", $raw_headers) as $i => $h) {
            $h = explode(':', $h, 2);

            if (isset($h[1])) {
                if (!isset($headers[$h[0]])) {
                    $headers[$h[0]] = trim($h[1]);
                } elseif (is_array($headers[$h[0]])) {
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1])));
                } else {
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1])));
                }

                $key = $h[0];
            } else {
                if (substr($h[0], 0, 1) == "\t") {
                    $headers[$key] .= "\r\n\t" . trim($h[0]);
                } elseif (!$key) {
                    $headers[0] = trim($h[0]);
                }
                trim($h[0]);
            }
        }

        return $headers;
    }

    /**
     * 缓存方法.
     *
     * @author Cui
     *
     * @date   2015-07-29
     *
     * @param string $name    缓存名
     * @param string $value   缓存值 如果不输入值 则根据缓存名返回缓存值.
     * @param string $expires 缓存过期时间 默认0 即永不超时. 单位秒
     */
    public static function cache($name, $value = '', $expires = 0)
    {
        if (!$name || !is_string($name)) {
            self::setError('参数错误!');

            return false;
        }

        static $cacheDriver;
        if (!isset($cacheDriver)) {
            $cacheDriver = __NAMESPACE__ . '\\CacheDriver\\' . self::$CACHE_DRIVER . 'Driver';
            $cacheDriver = new $cacheDriver(__DIR__ . '/Cache/');
        }

        if (!$value && $value !== 0) {
            $value = $cacheDriver->_get($name);
            if (false == $value) {
                $value = null;
            }

            return $value;
        }

        $res = $cacheDriver->_set($name, $value, $expires);

        return $res ? true : false;
    }
}
