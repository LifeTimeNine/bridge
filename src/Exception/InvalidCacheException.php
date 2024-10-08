<?php

declare(strict_types = 1);

namespace lifetime\bridge\Exception;

/**
 * 缓存异常类
 */
class InvalidCacheException extends \ErrorException
{
    /**
     * @var array
     */
    public $raw = [];

    /**
     * 构造函数
     * @param string    $message
     * @param int       $code
     * @param array     $raw
     */
    public function __construct(string $message, int $code = 0, array $raw = [])
    {
        parent::__construct($message, intval($code));
        $this->raw = $raw;
    }

    public function getRaw(): array
    {
        return $this->raw;
    }
}