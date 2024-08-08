<?php

declare(strict_types = 1);

namespace lifetime\bridge\exception;

/**
 * 微信支付响应异常类
 */
class WechatPaymentResponseException extends \Exception
{
    /**
     * Http状态码
     * @var int
     */
    protected $httpCode;

    /**
     * 异常码
     * @var string
     */
    protected $errorCode;

    /**
     * 构造函数
     * @access  public
     * @param   string  $message        异常消息
     * @param   string  $httpCode       Http状态码
     * @param   string  $errorCode      异常码
     */
    public function __construct(string $message, int $httpCode, string $errorCode)
    {
        parent::__construct($message, 1);
        $this->httpCode = $httpCode;
        $this->errorCode = $errorCode;
    }

    /**
     * 获取Http状态码
     * @access  public
     * @return  int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
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
}
