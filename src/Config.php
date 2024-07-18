<?php

declare(strict_types = 1);

namespace lifetime\bridge;

/**
 * 配置管理类
 * @class Config
 */
class Config
{

    /**
     * 配置
     * @var array
     */
    protected static $config = [];

    /**
     * 初始化配置
     * @access  public
     * @param   array   $config
     */
    public static function init(array $config = [])
    {
        self::$config = $config;
    }

    /**
     * 获取所有配置
     * @access  public
     * @return array
     */
    public static function all(): array
    {
        return self::$config;
    }

    /**
     * 获取平台配置
     * @access  public
     * @param   string  $name   平台名称
     * @return  array
     */
    public static function platform(string $name)
    {
        return self::$config[$name] ?? [];
    }
}
