<?php

declare(strict_types = 1);

namespace lifetime\bridge\exception;

/**
 * 支付宝支付响应异常类
 */
class AliPaymentResponseException extends \Exception
{
    /**
     * 业务返回码
     * @var string
     */
    protected $subCode;

    /**
     * 业务返回码描述
     * @var string
     */
    protected $subMsg;

    /**
     * 构造函数
     * @access  public
     * @param   string  $message        返回码描述
     * @param   int     $code           返回码
     * @param   string  $subCode        业务返回码
     * @param   string  $subMsg         业务返回码描述
     */
    public function __construct(string $message, int $code, string $subCode, string $subMsg)
    {
        parent::__construct($message, $code);
        $this->subCode = $subCode;
        $this->subMsg = $subMsg;
    }

    /**
     * 获取业务返回码
     * @access  public
     * @return  string
     */
    public function getSubCode(): string
    {
        return $this->subCode;
    }

    /**
     * 获取业务返回码描述
     * @access  public
     * @return  string
     */
    public function getSubMsg(): string
    {
        return $this->subMsg;
    }
    
}
