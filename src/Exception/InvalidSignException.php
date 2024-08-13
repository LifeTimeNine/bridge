<?php

declare(strict_types = 1);

namespace lifetime\bridge\Exception;

/**
 * 签名异常类
 */
class InvalidSignException extends \InvalidArgumentException
{
    /**
     * @var array
     */
    public $raw = [];

    /**
     * InvalidArgumentException constructor.
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