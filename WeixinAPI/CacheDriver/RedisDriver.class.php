<?php

namespace WeixinAPI\CacheDriver;

/**
 * 文件缓存驱动.
 *
 * @author Cui.
 */
class RedisDriver extends BaseDriver
{
    private $redis;
    private $pre;

    public function __construct()
    {
        parent::__construct();
        if (!class_exists('redis')) {
            exit('Redis初始化连接失败');
        }

        ini_set('default_socket_timeout', 50);//socket连接超时时间;
        $this->redis = new \redis();
        // 如果用框架 此处最好走配置文件
        $host = '127.0.0.1';
        $port = '6379';
        $this->pre = 'WeixinApi:';
        $link = $this->redis->connect($host, $port, 40);

        if (!$link) {
            exit('Redis初始化连接失败');
        }
    }

    /**
     * 根据缓存名获取缓存内容.
     *
     * @author Cui
     *
     * @date   2015-07-29
     *
     * @param string $name 缓存名
     */
    public function _get($name)
    {
        $name = $this->createFileName($name);
        $data = $this->redis->get($name);
        if ($data) {
            $data = $this->unpackData($data);
        }

        return $data;
    }

    /**
     * 根据缓存名 设置缓存值和超时时间.
     *
     * @author Cui
     *
     * @date   2015-07-29
     *
     * @param string $name    缓存名
     * @param void   $value   缓存值
     * @param int    $expires 超时时间
     *
     * @return boolean;
     */
    public function _set($name, $value, $expires)
    {
        $name = $this->createFileName($name);
        $data = $this->packData($value);
        if ($expires === 0) {
            return $this->redis->set($name, $data);
        }

        return $this->redis->setex($name, $expires, $data);
    }

    /**
     * 数据打包.
     *
     * @author Cui
     *
     * @date   2015-07-29
     *
     * @param void $data    缓存值
     * @param int  $expires 超时时间
     *
     * @return string
     */
    private function packData($data)
    {
        return serialize($data);
    }

    /**
     * 数据解包.
     *
     * @author Cui
     *
     * @date   2015-07-29
     *
     * @param string $data 被打包后的数据
     */
    private function unpackData($data)
    {
        return unserialize($data);
    }

    /**
     * 创建缓存文件名.
     *
     * @author Cui
     *
     * @date   2015-07-29
     *
     * @param string $name 缓存名
     *
     * @return string
     */
    private function createFileName($name)
    {
        return $this->pre.md5($name);
    }
}
