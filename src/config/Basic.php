<?php

declare(strict_types = 1);

namespace lifetime\bridge\config;

use ArrayAccess;
use BadMethodCallException;
use lifetime\bridge\Config;
use lifetime\bridge\exception\InvalidConfigException;
use lifetime\bridge\Tools;

/**
 * 配置基类
 */
abstract class Basic implements ArrayAccess
{
    /**
     * 当前配置值
     * @var array
     */
    protected $config = [];

    /**
     * 构造函数
     * @access  public
     * @param   array   $config     配置项
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->getDefault(), Config::platform($this->getPlatform())[$this->getProduct()] ?? [], $config);
        $this->check();
    }

    /**
     * 设置配置项值
     * @access  public
     * @param   string      $offset
     * @param   mixed       $value
     */
    public function set(string $offset, $value)
    {
        $this->offsetSet($offset, $value);
    }

    /**
     * 获取配置项参数
     * @access  public
     * @param   string  $offset
     * @return  mixed
     */
    public function get(string $offset = null, $default = null)
    {
        return $this->offsetGet($offset, $default);
    }

    /**
     * 合并数据到对象
     * @param array $data 需要合并的数据
     * @param bool $append 是否追加数据
     * @return array
     */
    public function merge(array $data, $append = false)
    {
        if ($append) {
            return $this->config = array_merge($this->config, $data);
        }
        return array_merge($this->config, $data);
    }

    /**
     * 设置配置项值
     * @access  public
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->config[] = $value;
        } else {
            $this->config[$offset] = $value;
        }
    }

    /**
     * 判断配置Key是否存在
     * @access  public
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->config[$offset]);
    }

    /**
     * 清理配置项
     * @access  public
     * @param   string|null     $offset
     */
    public function offsetUnset($offset): void
    {
        if (is_null($offset)) {
            $this->config = [];
        } else {
            unset($this->config[$offset]);
        }
    }

    /**
     * 获取配置项参数
     * @access  public
     * @param   mixed   $offset
     * @param   mixed   $default
     * @return  mixed
     */
    public function offsetGet($offset, $default = null)
    {
        if (is_null($offset)) {
            return $this->config;
        }
        return isset($this->config[$offset]) ? $this->config[$offset] : $default;
    }

    /**
     * 获取默认配置
     * @access  protected
     * @return array
     */
    abstract protected function getDefault(): array;

    /**
     * 获取必须的配置Key
     * @access  protected
     * @return array
     */
    abstract protected function getMustConfig(): array;

    /**
     * 获取平台名称
     * @access  protected
     * @return  string
     */
    abstract protected function getPlatform(): string;

    /**
     * 获取产品名称
     * @access  protected
     * @return  string
     */
    abstract protected function getProduct(): string;

    /**
     * 验证必须配置
     * @access  protected
     * @return  void
     * @throws InvalidConfigException
     */
    protected function check()
    {
        foreach($this->getMustConfig() as $key) {
            if (empty($this->config[$key])) {
                throw new InvalidConfigException("Missing Config [{$this->getPlatform()}.{$this->getProduct()}.{$key}]");
            }
        }
    }

    public function __call($name, $arguments)
    {
        $key = Tools::hump2underline($name);
        if (isset($this->config[$key])) {
            return $this->config[$key];
        } else {
            throw new BadMethodCallException('Call to undefined method ' . get_class($this) . ":{$name}()");
        }
    }
}