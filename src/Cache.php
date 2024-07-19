<?php

declare(strict_types = 1);

namespace lifetime\bridge;

use lifetime\bridge\exception\InvalidCacheException;

/**
 * 缓存类
 */
class Cache
{
    /**
     * 缓存路径
     * @var string
     */
    private static $path;

    /**
     * 自定义缓存操作方法
     * @var array
     */
    private static $callable = [
        'set' => null,
        'get' => null,
        'del' => null
    ];

    /**
     * 是否初始化
     * @var bool
     */
    private static $initiated = false;

    /**
     * 使用自定义缓存
     * @var bool
     */
    private static $usingCustom = false;

    /**
     * 初始化
     * @access  public
     * @return  void
     * @throws InvalidCacheException
     */
    public static function init()
    {
        if (self::$initiated) return;

        self::$path = Config::all()['cache_path'] ?? null ?: '/tmp/bridge_cache';
        self::$callable = array_merge(self::$callable, Config::all()['cache_callable'] ?? []);

        if (empty(self::$callable['set']) || empty(self::$callable['get']) || empty(self::$callable['del'])) {
            if (!is_dir(self::$path) && !mkdir(self::$path, 0777, true)) {
                throw new InvalidCacheException('Cache directory creation failed', 1, ['dir' => self::$path]);
            }
        } else {
            self::$usingCustom = true;
        }

        self::$initiated = true;
    }

    /**
     * 设置缓存
     * @access  public
     * @param   string  $name       缓存名称
     * @param   mixed   $value      缓存内容
     * @param   int     $expired    缓存有效期
     * @return  bool
     * @throws InvalidCacheException
     */
    public static function set(string $name, $value, int $expired = 0): bool
    {
        if (self::$usingCustom) {
            return (bool)call_user_func_array(self::$callable['set'], [$name, $value, $expired]);
        } else {
            if ($expired > 0) {
                $expired = time() + $expired;
            } elseif ($expired < 0) {
                $expired = time();
            }
            $data = ['value' => $value, 'expired' => $expired];
            if (!@file_put_contents(self::$path . DIRECTORY_SEPARATOR . self::_getName($name), serialize($data))) {
                throw new InvalidCacheException('Cache write failed', 1, $data);
            }
            return true;
        }
    }

    /**
     * 获取缓存
     * @access  public
     * @param   string  $name       缓存名称
     * @param   mixed   $default    默认值
     * @return  mixed
     */
    public static function get(string $name, $default = null)
    {
        if (self::$usingCustom) {
            return call_user_func_array(self::$callable['get'], [$name, $default]);
        } else {
            $filePath = self::$path . DIRECTORY_SEPARATOR . self::_getName($name);
            if (file_exists($filePath) && ($content = file_get_contents($filePath)) !== false) {
                $data = unserialize($content);
                if (isset($data['expired']) && $data['expired'] > 0 && $data['expired'] < time()) {
                    self::del($name);
                    return $default;
                } else {
                    return $data['value'] ?? null;
                }
            } else {
                return $default;
            }
        }
    }

    /**
     * 删除缓存
     * @access  public
     * @param   string  $name   缓存名称
     * @return  bool
     */
    public static function del(string $name): bool
    {
        if (self::$usingCustom) {
            return (bool)call_user_func_array(self::$callable['del'], [$name]);
        } else {
            $filePath = self::$path . DIRECTORY_SEPARATOR . self::_getName($name);
            return file_exists($filePath) ? unlink($filePath) : true;
        }
    }

    /**
     * 获取格式化之后的名称
     * @access  private
     * @param   string  $name   缓存名称
     * @return  string
     */
    private static function _getName(string $name): string
    {
        return md5($name);
    }
}