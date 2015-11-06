<?php

namespace WeixinAPI\CacheDriver;

/**
 * 文件缓存驱动.
 *
 * @author Cui.
 */
class FileDriver extends BaseDriver
{
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
        $file = $this->cacheDir.'/'.$name;
        $data = @file_get_contents($file);
        if (!$data) {
            return;
        }

        $data = $this->unpackData($data);
        if (false === $data) {
            @unlink($file);

            $data = null;
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
        $data = $this->packData($value, $expires);

        return file_put_contents($this->cacheDir.'/'.$name, $data);
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
    private function packData($data, $expires)
    {
        $str = array();
        $str['data'] = $data;
        $str['expires'] = $expires === 0 ? 0 : time() + $expires;
        $str = serialize($str);

        return $str;
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
        $arr = unserialize($data);

        if (time() > $arr['expires'] && $arr['expires'] !== 0) {
            return false;
        }

        return $arr['data'];
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
        return md5($name);
    }
}
