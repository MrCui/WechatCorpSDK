<?php

namespace WeixinAPI\API;

use WeixinAPI\Api;

/**
 * 媒体文件相关接口.
 *
 * @author Cui.
 */
class MediaApi extends BaseApi
{
    /**
     * 上传临时媒体文件.
     *
     * @author Cui
     *
     * @date   2015-08-02
     *
     * @param string $file 文件路径
     * @param string $type 文件类型
     *
     * @return 接口返回结果
     */
    public function upload($file, $type)
    {
        if (!$file || !$type) {
            $this->setError('参数缺失');

            return false;
        }

        if (!file_exists($file)) {
            $this->setError('文件路径不正确');

            return false;
        }

        // 兼容php5.3-5.6 curl模块的上传操作
        if (class_exists('\CURLFile')) {
            $data = array('media' => new \CURLFile(realpath($file)));
        } else {
            $data = array('media' => '@' . realpath($file));
        }

        Api::setPostQueryStr('type', $type);

        $node = 'upload';

        return $this->_post($node, $data, false);
    }

    /**
     * 根据mediaID获取媒体文件.
     *
     * @author Cui
     *
     * @date   2015-08-02
     *
     * @param string $mediaId 由上传接口获取的媒体文件
     *
     * @return array 如果成功则返回 content是由base64编码过的文件内容 解码后为正常的文件内容.
     */
    public function get($mediaId)
    {
        $node = 'get';
        $queryStr = array('media_id' => $mediaId);

        return $this->_get($node, $queryStr);
    }
}
