<?php

namespace WeixinAPI\API;

use WeixinAPI\Api;

/**
 * 微信-通讯录 用户相关接口.
 *
 * @author Cui.
 */
class UserApi extends BaseApi
{
    /**
     * 创建微信OAuth协议的链接.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @param string $redirectUri 协议的回调地址
     * @param string $state       可携带的参数, 选填.
     *
     * @return string 协议地址
     */
    public function createOAuthUrl($redirectUri, $state = '')
    {
        if (!$redirectUri) {
            $this->setError('参数错误!');

            return false;
        }

        $host = isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] : '';
        $api = 'https://open.weixin.qq.com/connect/oauth2/authorize';

        $state = $state ? $state = base64_encode($state) : '';

        $url = array();
        $url['appid'] = Api::getCorpId();
        $url['redirect_uri'] = $host . $redirectUri;
        $url['response_type'] = 'code';
        $url['scope'] = 'snsapi_base';
        $url['state'] = $state;
        $url = http_build_query($url);

        $url .= '#wechat_redirect';
        $url = $api . '?' . $url;

        return $url;
    }

    /**
     * 发起OAuth认证请求.
     *
     * @author Cui
     *
     * @date   2015-07-27
     */
    public function request($redirectUri, $state = '')
    {
        $code = I('get.code', false, 'trim');
        if ($code) {
            return;
        }

        $url = $this->createOAuthUrl($redirectUri, $state);
        header('Location:' . $url);
        exit;
    }

    /**
     * 获取OAuth回调的信息.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @return array 回调信息.
     */
    public function receive()
    {
        $code = I('get.code', false, 'trim');

        if (!$code) {
            $this->setError('非法参数');

            return false;
        }

        $res = $this->getIdByCode($code);

        if (false == $res || !$res['UserId']) {
            $this->setError('对不起,您尚不是本站用户.');

            return false;
        }

        $arr = array();
        $arr['userid'] = $res['UserId'];
        $arr['state'] = I('get.state', '', 'trim,base64_decode');
        $arr['code'] = $code;

        return $arr;
    }

    /**
     * 添加用户.
     *
     * @author Cui
     *
     * @date   2015-08-02
     *
     * @param string $userid     用户的企业号唯一标识
     * @param string $name       用户的名字
     * @param array  $department 用户所在的部门列表
     * @param array  $must       添加用户时需要添加 weixinid, mobile, email三者至少填一个! 此三者名为键位.
     * @param array  $extend     用户的其他信息 非必填
     */
    public function add($userid, $name, array $department, array $must, array $extend = array())
    {
        if (!$userid || !$name) {
            $this->setError('userid和name必填!');

            return false;
        }

        if (!is_array($must) || empty($must)) {
            $this->setError('weixinid, mobile, email三者至少填一个!');

            return false;
        }

        $data = array();
        $data['userid'] = $userid;
        $data['name'] = $name;
        $data['department'] = $department;

        $data = array_merge($data, $must);
        $data = array_merge($data, $extend);

        $node = 'create';

        return $this->_post($node, $data);
    }

    /**
     * 更新用户.
     *
     * @author Cheng
     *
     * @date   2015-08-03
     *
     * @param string $userid 用户的企业号唯一标识  必填
     * @param array  $data   用户的更改的信息key=>val 非必填
     */
    public function update($userid, $data)
    {
        if (!$userid) {
            $this->setError('userid');

            return false;
        }
        $data['userid'] = $userid;
        $node = 'update';

        return $this->_post($node, $data);
    }

    /**
     * 删除成员.
     *
     * @author Cheng
     *
     * @date   2015-08-03
     *
     * @param string $userId 用户的企业号唯一标识  必填
     */
    public function delete($userId)
    {
        if (!$userId) {
            $this->setError('userid');

            return false;
        }

        $queryStr = array(
            'userid' => $userId,
        );

        $node = 'delete';

        return $this->_get($node, $queryStr);
    }

    /**
     * 批量删除成员.
     *
     * @author Cheng
     *
     * @date   2015-08-03
     *
     * @param array $userList 该数组key 为用户id组成的删除列表  必填
     */
    public function batchDelete($userList)
    {
        if (!$userList) {
            $this->setError('userList');

            return false;
        }

        $data = array();
        $data['useridlist'] = $userList;

        $node = 'batchdelete';

        return $this->_post($node, $data);
    }

    /**
     * 邀请指定ID用户关注.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @param string $userId 用户在微信端的userid.
     *
     * @return array 接口返回信息
     */
    public function invite($userId)
    {
        $userId = trim($userId);
        if (false == $userId) {
            $this->setError('参数错误');

            return false;
        }

        $data = array(
            'userid' => $userId,
        );

        $this->module = 'invite';

        $node = 'send';

        return $this->_post($node, $data);
    }

    /**
     * 根据用户ID获取用户信息.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @param string $userId 用户在微信端的userid.
     *
     * @return array 用户信息
     */
    public function getInfoById($userId)
    {
        $userId = trim($userId);
        if (false == $userId) {
            $this->setError('参数错误');

            return false;
        }

        $queryStr = array(
            'userid' => $userId,
        );

        $node = 'get';

        return $this->_get($node, $queryStr);
    }

    /**
     * 根据协议换回的code换取用户的userid.
     *
     * @author Cui
     *
     * @date   2015-07-27
     *
     * @param string $code 协议换回的code
     *
     * @return string userid
     */
    public function getIdByCode($code)
    {
        if (false == $code) {
            $this->setError('参数错误');

            return false;
        }

        $node = 'getuserinfo';

        $queryStr = array(
            'code' => $code,
        );

        return $this->_get($node, $queryStr);
    }

    /**
     * 根据部门ID获取部门成员列表.
     *
     * @author Cheng
     *
     * @date   2015-07-29
     *
     * @param int $departmentId 协议换回的code
     * @param int $fetchChild   1/0：是否递归获取子部门下面的成员
     * @param int $status       0获取全部成员，1获取已关注成员列表，2获取禁用成员列表，4获取未关注成员列表。status可叠加
     *
     * @return array 用户列表
     */
    public function getListByDepartmentId($departmentId, $fetchChild = 1, $status = 0)
    {
        if (false == $departmentId) {
            $this->setError('参数错误');

            return false;
        }

        $node = 'simplelist';

        $queryStr = array(
            'department_id' => $departmentId,
            'fetch_child' => $fetchChild,
            'status' => $status,
        );

        return $this->_get($node, $queryStr);
    }

    /**
     * 根据部门ID获取部门成员列表(详情).
     *
     * @author Cheng
     *
     * @date   2015-07-30
     *
     * @param int $departmentId 协议换回的code
     * @param int $fetchChild   1/0：是否递归获取子部门下面的成员
     * @param int $status       0获取全部成员，1获取已关注成员列表，2获取禁用成员列表，4获取未关注成员列表。status可叠加
     *
     * @return array 用户列表
     */
    public function getDetailByDepartmentId($departmentId, $fetchChild = 0, $status = 0)
    {
        if (false == $departmentId) {
            $this->setError('参数错误');

            return false;
        }

        $node = 'list';

        $queryStr = array(
            'department_id' => $departmentId,
            'fetch_child' => $fetchChild,
            'status' => $status,
        );

        return $this->_get($node, $queryStr);
    }
}
