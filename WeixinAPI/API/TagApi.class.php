<?php

namespace WeixinAPI\API;

/**
 * 微信-通讯录标签 用户相关接口.
 *
 * @author Cui.
 */
class TagApi extends BaseApi
{
    /**
     * 创建标签.
     *
     * @author Cheng
     *
     * @date   2015-08-03
     *
     * @param string $tagname 标签名称，长度为1~64个字节，标签名不可与其他标签重名。   必填
     * @param int    $tagid   标签id 非必填
     */
    public function add($tagname, $tagid = '')
    {
        if (!$tagname) {
            $this->setError('tagname');

            return false;
        }
        $data['tagname'] = $tagname;
        $node = 'create';

        return $this->_post($node, $data);
    }

    /**
     * 更新标签名字.
     *
     * @author Cheng
     *
     * @date   2015-08-03
     *
     * @param string $tagname 标签名称，长度为1~64个字节，标签不可与其他标签重名。    必填
     * @param int    $tagid   标签id 必填
     */
    public function update($tagname, $tagid)
    {
        if (!$tagname || !$tagid) {
            $this->setError('tagname || tagid');

            return false;
        }
        $data['tagname'] = $tagname;
        $data['tagid'] = $tagid;
        $node = 'update';

        return $this->_post($node, $data);
    }

    /**
     * 删除标签.
     *
     * @author Cheng
     *
     * @date   2015-08-03
     *
     * @param int $tagid 标签id 必填
     */
    public function delete($tagid)
    {
        if (!$tagid) {
            $this->setError('tagid');

            return false;
        }

        $node = 'delete';

        $queryStr = array(
            'tagid' => $tagid,
        );

        return $this->_get($node, $queryStr);
    }

    /**
     * 获取标签成员.
     *
     * @author Cheng
     *
     * @date   2015-08-03
     *
     * @param int $tagid 标签id 必填
     */
    public function getInfoById($tagid)
    {
        if (!$tagid) {
            $this->setError('tagid');

            return false;
        }

        $node = 'get';

        $queryStr = array(
            'tagid' => $tagid,
        );

        return $this->_get($node, $queryStr);
    }

    /**
     * 增加标签成员.
     *
     * @author Cheng
     *
     * @date   2015-08-03
     *
     * @param int   $tagid     标签id 必填
     * @param array $userlist  企业成员ID列表，注意：userlist、partylist不能同时为空
     * @param array $partylist 企业部门ID列表，注意：userlist、partylist不能同时为空
     */
    public function addTagUsers($tagid, array $userlist, $partylist = '')
    {
        if (!$tagid) {
            $this->setError('tagid');

            return false;
        }

        if (!$userlist && !$partylist) {
            $this->setError('userlist,partylist至少填一项');

            return false;
        }
        $node = 'addtagusers';
        $data = array();
        $data['tagid'] = $tagid;
        if (!empty($userlist)) {
            $data['userlist'] = $userlist;
        }

        if (!empty($partylist)) {
            $data['partylist'] = $partylist;
        }

        return $this->_post($node, $data);
    }

    /**
     * 删除标签成员.
     *
     * @author Cheng
     *
     * @date   2015-08-03
     *
     * @param int   $tagid     标签id 必填
     * @param array $userlist  企业成员ID列表，注意：userlist、partylist不能同时为空
     * @param array $partylist 企业部门ID列表，注意：userlist、partylist不能同时为空
     */
    public function delTagUsers($tagid, array $userlist, $partylist = '')
    {
        if (!$tagid) {
            $this->setError('tagid');

            return false;
        }

        if (!$userlist && !$partylist) {
            $this->setError('userlist,partylist至少填一项');

            return false;
        }
        $node = 'deltagusers';
        $data = array();
        $data['tagid'] = $tagid;
        if (!empty($userlist)) {
            $data['userlist'] = $userlist;
        }

        if (!empty($partylist)) {
            $data['partylist'] = $partylist;
        }

        return $this->_post($node, $data);
    }

    /**
     * 获取标签列表.
     *
     * @author Cheng
     *
     * @date   2015-08-03
     */
    public function getTagList()
    {
        $node = 'list';

        return $this->_get($node, '');
    }
}
