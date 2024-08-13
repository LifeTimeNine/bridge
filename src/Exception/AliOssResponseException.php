<?php

declare(strict_types = 1);

namespace lifetime\bridge\Exception;

/**
 * 阿里云对象存储响应异常类
 */
class AliOssResponseException extends \Exception
{
    /**
     * 异常码
     * @var string
     */
    protected $errorCode;

    /**
     * 阿里云请求ID
     * @var string
     */
    protected $aliRequestId;

    /**
     * 构造函数
     * @access  public
     * @param   string  $message        异常消息
     * @param   string  $errorCode      阿里云异常码
     * @param   string  $aliRequestId   阿里云请求ID
     */
    public function __construct(string $message, string $errorCode, string $aliRequestId)
    {
        parent::__construct($message, 1);
        $this->errorCode = $errorCode;
        $this->aliRequestId = $aliRequestId;
    }

    /**
     * 获取异常码
     * @access  public
     * @return  string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
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
