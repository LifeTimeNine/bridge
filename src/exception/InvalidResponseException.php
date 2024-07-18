<?php

declare(strict_types = 1);

namespace lifetime\bridge\exception;

/**
 * 响应异常类
 */
class InvalidResponseException extends \Exception
{
    /**
     * @var array
     */
    public $raw = [];

    /**
     * InvalidResponseException constructor.
     * @param string $message
     * @param integer $code
     * @param array $raw
     */
    public function __construct($message, $code = 0, $raw = [])
    {
        parent::__construct($message, intval($code));
        $this->raw = $raw;
    }

    public function getRaw()
    {
        return $this->raw;
    }
}
