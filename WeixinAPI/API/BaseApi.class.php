<?php

namespace WeixinAPI\API;

use WeixinAPI\Api;

class BaseApi
{
    protected $module; // 接口模块
    protected $className;

    /**
     * 构造方法 根据类名设置 当前要访问的接口模块.
     *
     * @author Cui
     *
     * @date   2015-07-31
     */
    public function __construct()
    {
        $className = get_called_class();
        $className = explode('\\', $className);
        $className = end($className);
        $className = str_replace('Api', '', $className);
        $className = strtolower($className);

        $this->module = $className;
        $this->className = $className;
    }

    /**
     * get发送数据.
     *
     * @author Cui
     *
     * @date   2015-07-31
     *
     * @param string $node     接口节点
     * @param array  $queryStr 需要携带的查询字符串
     *
     * @return 接口返回的结果
     */
    final protected function _get($node, $queryStr = array())
    {
        $module = $this->module;

        if ($this->module != $this->className) {
            $this->module = $this->className;
        }

        return Api::_get($module, $node, $queryStr);
    }

    /**
     * post发送数据.
     *
     * @author Cui
     *
     * @date   2015-07-31
     *
     * @param string $node       接口节点
     * @param array  $data       需要发送的数据
     * @param bool   $jsonEncode 是否转换为jsons数据
     *
     * @return 接口返回的结果
     */
    final protected function _post($node, array $data, $jsonEncode = true)
    {
        $module = $this->module;

        if ($this->module != $this->className) {
            $this->module = $this->className;
        }

        return Api::_post($module, $node, $data, $jsonEncode);
    }

    /**
     * 设置错误信息.
     *
     * @author Cui
     *
     * @date   2015-07-31
     *
     * @param string $error 错误信息
     */
    final protected function setError($error)
    {
        Api::setError($error);
    }

    /**
     * 返回错误信息.
     *
     * @author Cui
     *
     * @date   2015-07-31
     *
     * @return string
     */
    final public function getError()
    {
        return Api::getError();
    }
}
