<?php

namespace WeixinAPI\API;

/**
 * 微信通讯录管理部门.
 *
 * @author Cheng.
 */
class DepartmentApi extends BaseApi
{
    /**
     * 创建部门.
     *
     * @author Cheng
     *
     * @date   2015-08-03
     *
     * @param string $name     部门名称   必填
     * @param int    $oarentid 父亲部门id。根部门id为1  必填
     * @param int    $order    在父部门中的次序值。order值小的排序靠前。  非必填
     * @param int    $id       部门id，整型。指定时必须大于1，不指定时则自动生成 。  非必填
     *
     * @return array
     */
    public function create($name, $parentid, $order = '', $id = '')
    {
        if (!$name || !$parentid) {
            $this->setError('name or parentid错误!');

            return false;
        }

        $node = 'create';
        $data['name'] = $name;
        $data['parentid'] = $parentid;
        if (!empty($order)) {
            $data['order'] = $order;
        }
        if (!empty($id)) {
            $data['id'] = $id;
        }

        return $this->_post($node, $data);
    }

    /**
     *  更新部门.
     *
     * @author Cheng
     *
     * @date   2015-08-03
     *
     * @param int $id       部门id    必填
     * @param int $oarentid 父亲部门id。根部门id为1  必填
     * @param int $order    在父部门中的次序值。order值小的排序靠前。  非必填
     * @param int $name     部门名称 。  非必填
     */
    public function update($id, $name = '', $parentid = '', $order = '')
    {
        if (!$id) {
            $this->setError('id错误!');

            return false;
        }

        $node = 'update';
        $data['id'] = $id;
        $data['name'] = $name;
        $data['parentid'] = $parentid;
        $data['order'] = $order;

        return $this->_post($node, $data);
    }

    /**
     *  删除部门.
     *
     * @author Cheng
     *
     * @date   2015-08-03
     *
     * @param int $id 部门id   必填
     */
    public function delete($id)
    {
        if (!$id) {
            $this->setError('id错误!');

            return false;
        }

        $node = 'delete';
        $queryStr = array(
            'id' => $id,
        );

        return $this->_get($node, $queryStr);
    }

    /**
     * 获取部门列表.
     *
     * @author Cheng
     *
     * @date   2015-08-03
     *
     * @param int $id 部门id，整型.获取制定部门下的 。  非必填
     *
     * @return array
     */
    public function getDepartmentList($id = '')
    {
        $node = 'list';

        return $this->_get($node);
    }
}
