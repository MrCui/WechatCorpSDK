<?php
spl_autoload_register('autoloader::autoload');

/**
 * 自动加载.
 *
 * @author Cui.
 */
class autoloader
{
    private static $selfInstanceMap = array(); // 实例列表;

    /**
     * 自动加载方法
     *
     * @auhtor Cui
     *
     * @date   2016-05-13
     *
     * @param  object     $class 未引入的类
     */
    public static function autoload($class)
    {
        static $_map;
        if (!isset($_map[$class])) {
            $class = explode('\\', $class);
            $rootpath = array_shift($class);
            if ($rootpath != "WeixinAPI") {
                return;
            }

            $class = implode(DIRECTORY_SEPARATOR, $class);
            $file = (__DIR__ . DIRECTORY_SEPARATOR . $class . '.class.php');

            if (!file_exists($file)) {
                return false;
            }

            include $file;

            $_map[$class] = $file;
        }
    }
}
