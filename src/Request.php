<?php

declare(strict_types = 1);

namespace lifetime\bridge;

use Exception;

/**
 * 网络请求类
 * @class   Request
 */
class Request
{
    /** GET 请求 */
    const METHOD_GET = 'GET';
    /** POST 请求 */
    const METHOD_POST = 'POST';
    /** PUT 请求 */
    const METHOD_PUT = 'PUT';
    /** DELETE 请求 */
    const METHOD_DELETE = 'DELETE';
    /** HEAD 请求 */
    const METHOD_HEAD = 'HEAD';

    /** HTTP内容类型 - text/html */
    const CONTENT_TYPE_HTML = 'text/html';
    /** HTTP内容类型 - application/xml */
    const CONTENT_TYPE_XML = 'application/xml';
    /** HTTP内容类型 - text/plain */
    const CONTENT_TYPE_PLAIN = 'text/plain';
    /** HTTP内容类型 - application/x-www-form-urlencoded */
    const CONTENT_TYPE_URLENCODEED = 'application/x-www-form-urlencoded';
    /** HTTP内容类型 - multipart/form-data */
    const CONTENT_TYPE_FORMDATA = 'multipart/form-data';
    /** HTTP内容类型 - application/json */
    const CONTENT_TYPE_JSON = 'application/json';
    /** HTTP内容类型 - application/octet-stream */
    const CONTENT_TYPE_STREAM = 'application/octet-stream';

    /** HTTP请求头 - 内容类型 */
    const HEADER_CONTENT_TYPE = 'Content-Type';


    /**
     * curl 参数
     * @var array
     */
    protected $options = [];

    /**
     * 响应信息
     * @var array
     */
    protected $info;

    /**
     * 响应头
     * @var array
     */
    protected $responseHeader = [];

    /**
     * 构造函数
     * @access  public
     * @param   string  $url    请求地址
     * @param   string  $method 请求方法
     */
    public function __construct(string $url, string $method = self::METHOD_GET)
    {
        switch ($method) {
            case self::METHOD_GET:
            case self::METHOD_POST:
            case self::METHOD_PUT:
            case self::METHOD_DELETE:
                break;
            case self::METHOD_HEAD:
                $this->setOption([
                    CURLOPT_NOBODY => true,
                    CURLINFO_HEADER_OUT => true,
                ]);
                break;
            default:
                throw new Exception("unsupported request method: {$method}");
        }
        $this->setOption([
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $method
        ]);
        if (parse_url($url, PHP_URL_SCHEME) == 'https') {
            $this->setOption([
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);
        }
    }

    /**
     * 设置Curl选项
     * @access  public
     * @param   int|array   $option
     * @param   mixed       $value
     * @return  self
     */
    public function setOption($option, $value = null): self
    {
        if (is_array($option)) {
            foreach($option as $k => $v) $this->options[$k] = $v;
        } else {
            $this->options[$option] = $value;
        }
        return $this;
    }

    /**
     * 设置请求头
     * @access  public
     * @param   array    $headers   请求头列表
     * @return  self
     */
    public function setHeaders(array $headers): self
    {
        $headerList = [];
        foreach($headers as $key => $value) $headerList[] = "{$key}: {$value}";
        $this->setOption(CURLOPT_HTTPHEADER, $headerList);
        return $this;
    }

    /**
     * 设置请求数据
     * @access  public
     * @param   string  $data   请求数据
     * @return  self
     */
    public function setBody(?string $data): self
    {
        $this->setOption(CURLOPT_POSTFIELDS, $data);
        return $this;
    }

    /**
     * 发送请求
     * @access  public
     * @return  string
     */
    public function send(): string
    {
        $this->setOption([
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $curl = curl_init();
        curl_setopt_array($curl, $this->options);
        $content = curl_exec($curl);
        $this->info = curl_getinfo($curl);
        curl_close($curl);
        if ($content === false) return '';
        $headerArr = array_diff(explode("\r\n", substr($content, 0, $this->info['header_size'])), [""]);
        array_shift($headerArr);
        array_map(function($value) {
            if (strpos($value, ': ') === false) return;
            [$k, $v] = explode(': ', $value);
            $this->responseHeader[$k] = $v;
        }, $headerArr);
        return substr($content, $this->info['header_size']);
    }

    /**
     * 获取相应信息
     * @access  public
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed
     */
    public function getInfo(string $key = null, $default = null)
    {
        return is_null($key) ? $this->info : ($this->info[$key] ?? $default);
    }

    /**
     * 获取相应状态码
     * @access  public
     * @return  int
     */
    public function getCode(): int
    {
        return $this->getInfo('http_code');
    }

    /**
     * 获取响应头
     * @access  public
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed
     */
    public function getHeader(string $key = null, $default = null)
    {
        return is_null($key) ? $this->responseHeader : ($this->responseHeader[$key] ?? $default);
    }

    /**
     * 获取相应内容类型
     * @access  public
     * @return  string
     */
    public function getContentType(): string
    {
        return $this->info['content_type'] ?: null;
    }

    /**
     * GET 请求
     * @access  public
     * @param   string  $url    请求地址
     * @return  self
     */
    public static function get(string $url): self
    {
        return new static($url, self::METHOD_GET);
    }
    /**
     * POST 请求
     * @access  public
     * @param   string  $url    请求地址
     * @return  self
     */
    public static function post(string $url): self
    {
        return new static($url, self::METHOD_POST);
    }
    /**
     * PUT 请求
     * @access  public
     * @param   string  $url    请求地址
     * @return  self
     */
    public static function put(string $url): self
    {
        return new static($url, self::METHOD_PUT);
    }
    /**
     * DELETE 请求
     * @access  public
     * @param   string  $url    请求地址
     * @return  self
     */
    public static function delete(string $url): self
    {
        return new static($url, self::METHOD_DELETE);
    }
    /**
     * HEAD 请求
     * @access  public
     * @param   string  $url    请求地址
     * @return  self
     */
    public static function head(string $url): self
    {
        return new static($url, self::METHOD_HEAD);
    }
}