<?php

namespace WeixinAPI\API;

use WeixinAPI\Api;

/**
 * 微信应用创建菜单.
 *
 * @author Cui.
 */
class MenuApi extends BaseApi
{
    /**
     * 创建菜单
     *
     * @author Cui
     *
     * @date   2015-08-23
     *
     * @param  array      $button  菜单的数据
     * @param  int        $agentId 应用ID
     */
    public function create(array $button, $agentId)
    {
        if (empty($button) || empty($agentId)) {
            $this->setError('参数错误');

            return false;
        }

        $data = array();
        $data['button'] = $button;
        Api::setPostQueryStr('agentid', $agentId);

        $node = 'create';

        return $this->_post($node, $data);
    }

    /**
     * 删除应用菜单
     *
     * @author Cui
     *
     * @date   2015-08-23
     *
     * @param  int     $agentId 应用id
     */
    public function delete($agentId)
    {
        if (empty($agentId)) {
            $this->setError('参数错误');

            return false;
        }

        $queryStr = array(
            'agentid' => $agentId,
        );

        $node = 'delete';

        return $this->_get($node, $queryStr);
    }

    /**
     * 获取应用菜单列表
     *
     * @author Cui
     *
     * @date   2015-08-23
     *
     * @param  int     $agentId 应用id
     *
     * @return array
     */
    public function get($agentId)
    {
        if (empty($agentId)) {
            $this->setError('参数错误');

            return false;
        }

        $queryStr = array(
            'agentid' => $agentId,
        );

        $node = 'get';

        return $this->_get($node, $queryStr);
    }
}
