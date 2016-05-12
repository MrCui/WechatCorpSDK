<?php

namespace WeixinAPI\API;

/**
 * 微信 发送消息的相关接口.
 * 注意本类采用链式操作 ->touser()->news()->send();
 *
 * @author Cui.
 */
class MessageApi extends BaseApi
{
    private $touserData = array();
    private $topartyData = array();
    private $totagData = array();
    private $messageData = array();
    private $safe = 0;
    private $messageType = '';

    /**
     * 设置需要接收消息的用户列表.
     *
     * @author Cui
     *
     * @date   2015-08-03
     *
     * @param array/string $users 用户userid列表 array 或者 单个用户的userid string
     *
     * @return object
     */
    public function touser($users)
    {
        $users = $this->toString($users);
        $this->touserData = $users;

        return $this;
    }

    /**
     * 设置需要接收消息的部门列表.
     *
     * @author Cui
     *
     * @date   2015-08-03
     *
     * @param array/string $partys 部门ID列表 array 或者 单个部门的ID string
     *
     * @return object
     */
    public function toparty($partys)
    {
        $partys = $this->toString($partys);
        $this->topartyData = $partys;

        return $this;
    }

    /**
     * 设置需要接收消息的标签列表.
     *
     * @author Cui
     *
     * @date   2015-08-03
     *
     * @param array/string $tags 标签ID列表 array 或者 单个标签的ID string
     *
     * @return object
     */
    public function totag($tags)
    {
        $tags = $this->toString($tags);
        $this->totagData = $tags;

        return $this;
    }

    /**
     * 设置安全消息.
     *
     * @author Cui
     *
     * @date   2015-08-03
     *
     * @param bool $flag true : 安全消息 false : 非安全消息
     *
     * @return bool
     */
    public function issafe($flag = true)
    {
        $this->safe = $flag ? 1 : 0;

        return $this;
    }

    /**
     * 设置text消息.
     *
     * @author Cui
     *
     * @date   2015-08-03
     *
     * @param string $content 文本消息内容
     *
     * @return object
     */
    public function text($content)
    {
        $data = array(
            'content' => $content,
        );

        $this->messageType = 'text';
        $this->messageData = $data;

        return $this;
    }

    /**
     * 设置news消息.
     *
     * @author Cui
     *
     * @date   2015-08-03
     *
     * @param 注意此方法参数特殊:
     * 传递方法
     * news($data, $data, $data....);每多传递一个实参, 则news消息多一条, 最多传递十个.或者第一个参数也可以是个多维数组
     * $data格式 array(
     *     'title'      => title,
     *     'description'=> description,
     *     'url'        => url,
     *     'picurl'     => picurl.
     * )
     *
     * @return object
     */
    public function news()
    {
        $data = array();

        $args = func_get_args();
        $num = func_num_args();
        if ($num > 10 || $num < 1) {
            $this->setError('news 参数错误!');

            return false;
        }

        foreach ($args as $article) {

            if (count($article) != count($article, 1)) {
                foreach ($article as $art) {
                    $data['articles'][] = $art;
                }

            } else {
                $data['articles'][] = $article;
            }
        }

        $this->messageType = 'news';
        $this->messageData = $data;

        return $this;
    }

    /**
     * 设置image消息.
     *
     * @author Cui
     *
     * @date   2015-08-03
     *
     * @param string $mediaId 由上传接口得到的媒体ID
     *
     * @return object
     */
    public function image($mediaId)
    {
        $data = array(
            'media_id' => $mediaId,
        );

        $this->messageType = 'image';
        $this->messageData = $data;

        return $this;
    }

    /**
     * 设置file消息.
     *
     * @author Cui
     *
     * @date   2015-08-03
     *
     * @param string $mediaId 由上传接口得到的媒体ID
     *
     * @return object
     */
    public function file($mediaId)
    {
        $data = array(
            'media_id' => $mediaId,
        );

        $this->messageType = 'file';
        $this->messageData = $data;

        return $this;
    }

    /**
     * 设置voice消息.
     *
     * @author Cui
     *
     * @date   2015-08-03
     *
     * @param string $mediaId 由上传接口得到的媒体ID
     *
     * @return object
     */
    public function voice($mediaId)
    {
        $data = array(
            'media_id' => $mediaId,
        );

        $this->messageType = 'voice';
        $this->messageData = $data;

        return $this;
    }

    /**
     * 设置video消息.
     *
     * @author Cui
     *
     * @date   2015-08-03
     *
     * @param string $mediaId     由上传接口得到的媒体ID
     * @param string $title       视频标题
     * @param string $description 视频简介
     *
     * @return [type]
     */
    public function video($mediaId, $title = '', $description = '')
    {
        $data = array(
            'media_id' => $mediaId,
        );

        $title && $data['title'] = $title;
        $description && $data['description'] = $description;

        $this->messageType = 'video';
        $this->messageData = $data;

        return $this;
    }

    /**
     * 发送消息.
     *
     * @author Cui
     *
     * @date   2015-08-03
     *
     * @param int $agentId 应用ID
     *
     * @return 接口返回结果
     */
    public function send($agentId)
    {
        if (!is_numeric($agentId)) {
            $this->setError('应用ID未设置!');

            return false;
        }

        $toUser = $this->touserData;
        $toParty = $this->topartyData;
        $toTag = $this->totagData;

        if (!$toUser && !$toParty && !$toTag) {
            $this->setError('接收消息的用户,部门和标签, 不能同时为空!');

            return false;
        }

        $data = array();
        $toUser && $data['touser'] = $toUser;
        $toParty && $data['toparty'] = $toParty;
        $toTag && $data['totag'] = $toTag;

        $messageType = $this->messageType;
        $types = array('image', 'file', 'text', 'news', 'voice', 'video', 'mpnews');
        if (!$messageType || !in_array($messageType, $types)) {
            $this->setError('消息类型未设置或设置不正确!');

            return false;
        }
        $data['msgtype'] = $messageType;

        $messageData = $this->messageData;
        if (empty($messageData)) {
            $this->setError('发送的数据不允许为空!');

            return false;
        }

        $data[$messageType] = $messageData;
        $data['agentid'] = $agentId;
        $data['safe'] = $this->safe;

        $node = 'send';

        // 清空数据;
        $this->touserData = array();
        $this->topartyData = array();
        $this->totagData = array();
        $this->messageData = array();
        $this->safe = 0;
        $this->messageType = '';

        return $this->_post($node, $data);
    }

    /**
     * 串行化.
     *
     * @author Cui
     *
     * @date   2015-08-03
     *
     * @param array $arr 待转换的数据
     *
     * @return string
     */
    public function toString($arr)
    {
        if (!is_array($arr)) {
            return $arr;
        }

        return implode('|', $arr);
    }
}
