<?php

declare(strict_types = 1);

namespace lifetime\bridge\exception;

/**
 * 阿里云对象存储响应异常类
 */
class AliOssResponseException extends \Exception
{
    /**
     * 阿里云异常码
     * @var string
     */
    protected $aliCode;

    /**
     * 阿里云请求ID
     * @var string
     */
    protected $aliRequestId;

    /**
     * 构造函数
     * @access  public
     * @param   string  $message        异常消息
     * @param   string  $aliCode        阿里云异常码
     * @param   string  $aliRequestId   阿里云请求ID
     */
    public function __construct(string $message, string $aliCode, string $aliRequestId)
    {
        parent::__construct($message, 1);
        $this->aliCode = $aliCode;
        $this->aliRequestId = $aliRequestId;
    }

    /**
     * 获取阿里云异常码
     * @access  public
     * @return  string
     */
    public function getAliCode(): string
    {
        return $this->aliCode;
    }

    /**
     * 获取阿里云请求ID
     * @access  public
     * @return  string
     */
    public function getAliRequestId(): string
    {
        return $this->aliRequestId;
    }
    
}
