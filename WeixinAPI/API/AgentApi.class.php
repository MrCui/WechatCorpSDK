<?php

namespace WeixinAPI\API;

/**
 * 微信应用管理相关接口.
 *
 * @author Cui.
 */
class AgentApi extends BaseApi
{
    /**
     * 根据应用ID 获取应用信息.
     *
     * @author Cui
     *
     * @date   2015-08-03
     *
     * @param int $agentid 应用id
     *
     * @return array
     */
    public function get($agentid)
    {
        if (!is_numeric($agentid)) {
            $this->setError('agentid 错误!');

            return false;
        }

        $node = 'get';
        $queryStr = array('agentid' => $agentid);

        return $this->_get($node, $queryStr);
    }

    /**
     * 根据应用ID s设置应用信息.
     *
     * @author Cui
     *
     * @date   2015-08-03
     *
     * @param int   $agentid 应用id
     * @param array $data    想要设置的信息
     *
     * @return array
     */
    public function set($agentid, $data)
    {
        if (!is_numeric($agentid)) {
            $this->setError('agentid 错误!');

            return false;
        }

        $node = 'set';
        $data['agentid'] = $agentid;

        return $this->_post($node, $data);
    }

    /**
     * 企业号应用的基本信息，包括头像、昵称、帐号类型、认证类型、可见范围等信息.
     *
     * @author Cui
     *
     * @date   2015-08-03
     *
     * @return array
     */
    public function getAppList()
    {
        $node = 'list';

        return $this->_get($node);
    }
}
