<?php

declare(strict_types = 1);

namespace lifetime\bridge\qiniu\kodo;

use lifetime\bridge\config\QiniuKodo;
use lifetime\bridge\exception\InvalidArgumentException;
use lifetime\bridge\exception\InvalidConfigException;
use lifetime\bridge\exception\InvalidDecodeException;
use lifetime\bridge\exception\InvalidResponseException;
use lifetime\bridge\exception\QiniuKodoResponseException;
use lifetime\bridge\Request;
use lifetime\bridge\Tools;

/**
 * 七牛云对象存储基类
 * @throws InvalidConfigException
 */
abstract class Basic
{
    /**
     * 配置
     * @var QiniuKodo
     */
    protected $config;

    /**
     * Bucket名称
     * @var string
     */
    protected $bucketName;

    /**
     * 区域列表
     * @var array
     */
    protected static $regionList = [
        'zo' => ['name' => '华东-浙江', 'bucket_manage' => 'uc.qiniuapi.com', 'upload' => 'up-z0.qiniup.com', 'download' => 'iovip-z0.qiniuio.com', 'object_manage'=> 'rs-z0.qiniuapi.com', 'object_enum' => 'rsf-z0.qiniuapi.com', 'query' => 'api-zo.qiniuapi.com'],
        'cn-east-2' => ['name' => '华东-浙江2', 'bucket_manage' => 'uc.qiniuapi.com', 'upload' => 'up-cn-east-2.qiniup.com', 'download' => 'iovip-cn-east-2.qiniuio.com', 'object_manage'=> 'rs-cn-east-2.qiniuapi.com', 'object_enum' => 'rsf-cn-east-2.qiniuapi.com', 'query' => 'api-cn-east-2.qiniuapi.com'],
        'z1' => ['name' => '华北-河北', 'bucket_manage' => 'uc.qiniuapi.com', 'upload' => 'up-z1.qiniup.com', 'download' => 'iovip-z1.qiniuio.com', 'object_manage'=> 'rs-z1.qiniuapi.com', 'object_enum' => 'rsf-z1.qiniuapi.com', 'query' => 'api-z1.qiniuapi.com'],
        'z2' => ['name' => '华南-广东', 'bucket_manage' => 'uc.qiniuapi.com', 'upload' => 'up-z2.qiniup.com', 'download' => 'iovip-z2.qiniuio.com', 'object_manage'=> 'rs-z2.qiniuapi.com', 'object_enum' => 'rsf-z2.qiniuapi.com', 'query' => 'api-z2.qiniuapi.com'],
        'na0' => ['name' => '北美-洛杉矶', 'bucket_manage' => 'uc.qiniuapi.com', 'upload' => 'up-na0.qiniup.com', 'download' => 'iovip-na0.qiniuio.com', 'object_manage'=> 'rs-na0.qiniuapi.com', 'object_enum' => 'rsf-na0.qiniuapi.com', 'query' => 'api-na0.qiniuapi.com'],
        'as0' => ['name' => '亚太-新加坡', 'bucket_manage' => 'uc.qiniuapi.com', 'upload' => 'up-as0.qiniup.com', 'download' => 'iovip-as0.qiniuio.com', 'object_manage'=> 'rs-as0.qiniuapi.com', 'object_enum' => 'rsf-as0.qiniuapi.com', 'query' => 'api-as0.qiniuapi.com'],
        'ap-southeast-2' => ['name' => '亚太-河内', 'bucket_manage' => 'uc.qiniuapi.com', 'upload' => 'up-ap-southeast-2.qiniup.com', 'download' => 'iovip-ap-southeast-2.qiniuio.com', 'object_manage'=> 'rs-ap-southeast-2.qiniuapi.com', 'object_enum' => 'rsf-ap-southeast-2.qiniuapi.com', 'query' => 'api-ap-southeast-2.qiniuapi.com'],
        'ap-southeast-3' => ['name' => '亚太-胡志明', 'bucket_manage' => 'uc.qiniuapi.com', 'upload' => 'up-ap-southeast-3.qiniup.com', 'download' => 'iovip-ap-southeast-3.qiniuio.com', 'object_manage'=> 'rs-ap-southeast-3.qiniuapi.com', 'object_enum' => 'rsf-ap-southeast-3.qiniuapi.com', 'query' => 'api-ap-southeast-3.qiniuapi.com'],
    ];

    /**
     * 构造函数
     * @access  public
     * @param   array   $config 配置
     * @throws InvalidConfigException
     */
    public function __construct(array $config = [])
    {
        $this->config = new QiniuKodo($config);
    }

    /**
     * 设置Bucket名称
     * @access  public
     * @return  self
     */
    public function setBucketName(string $name): self
    {
        $this->bucketName = $name;
        return $this;
    }

    /**
     * 获取Bucket名称
     * @access  public
     * @return  string
     * @throws  InvalidArgumentException
     */
    protected function getBucketName(): string
    {
        $name = $this->bucketName;
        if (empty($name)) $name = $this->config->bucketName();
        if (empty($name)) throw new InvalidArgumentException("Missing Options [bucketName]");
        return $name;
    }

    /**
     * URL安全的Base64编码
     * @access  protected
     * @param   string  $str        要签名的字符串
     * @return  string
     */
    protected function urlBase64(string $str): string
    {
        if (empty($str)) return $str;
        return strtr(base64_encode($str), ['+' => '-', '/' => '_']);
    }


    /**
     * 获取区域信息
     * @access  protected
     * @param   string  $id     区域ID
     * @return  array
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    protected function getRegion(string $id = null): array
    {
        $useConfigId = false;
        if (empty($id)) {
            $id = $this->config->regionId();
            $useConfigId = true;
        }
        if (!array_key_exists($id, self::$regionList)) {
            if ($useConfigId) {
                throw new InvalidConfigException("Unknown region Id {$id}");
            } else {
                throw new InvalidArgumentException("Unknown region Id {$id}");
            }
        }
        return self::$regionList[$id];
    }

    /**
     * 构建管理凭证
     * @access  protected
     * @param   string  $method         请求方法
     * @param   string  $host           请求域名
     * @param   string  $path           请求路径
     * @param   array   $query          请求Query参数
     * @param   array   $header         请求头
     * @param   string  $body           请求体
     * @return  string
     */
    protected function buildMangeSign(string $method, string $host, string $path, array $query = [], array $header = [], string $body = null): string
    {
        $signStr = "{$method} {$path}";
        if (!empty($query)) $signStr .= ('?' . Tools::arrToUrl($query));
        $signStr .= "\nHost: {$host}";
        if (!empty($header[Request::HEADER_CONTENT_TYPE])) $signStr .= "\nContent-Type: {$header[Request::HEADER_CONTENT_TYPE]}";
        foreach($header as $key => $value) {
            if(strpos('X-Qiniu-', $key) !== 0) continue;
            $signStr .= "\n{$key}: {$value}";
        }
        $signStr .= "\n\n";
        if (!empty($body) && !empty($header[Request::HEADER_CONTENT_TYPE]) && $header[Request::HEADER_CONTENT_TYPE] <> Request::CONTENT_TYPE_STREAM) {
            $signStr .= $body;
        }
        $sign = $this->urlBase64(hash_hmac('sha1', $signStr, $this->config->secretKey(), true));
        return "Qiniu {$this->config->accessKey()}:{$sign}";
    }

    /**
     * 构建上传凭证
     * @access  protected
     * @param   array   $uploadStrategy     上传策略
     * @return  string
     */
    protected function buildUploadSign(array $uploadStrategy): string
    {
        $signStr = $this->urlBase64(json_encode($uploadStrategy, JSON_UNESCAPED_UNICODE));
        $sign = $this->urlBase64(hash_hmac('sha1', $signStr, $this->config->secretKey(), true));
        return "{$this->config->accessKey()}:{$sign}:{$signStr}";
    }

    /**
     * 发起请求
     * @access  protected
     * @param   string  $method             请求方法
     * @param   string  $host               请求域名
     * @param   string  $path               请求路径
     * @param   array   $header             请求头
     * @param   array   $query              请求Query参数
     * @param   string  $body               请求体
     * @param   bool    $isEmptyResponse    是否是空响应
     * @return  array
     * @throws  InvalidDecodeException
     */
    protected function request(string $method, string $host, string $path, array $header = [], array $query = [], string $body = null, bool $isEmptyResponse = false): array
    {
        $protocol = $this->config->isSsl() ? 'https' : 'http';
        $headerData = [
            'Date: ' . gmdate("D, d M Y H:i:s"),
            "Host: {$host}",
        ];
        if (!empty($body)) {
            $headerData[] = 'Content-Length: ' . strlen($body);
        }
        $url = "{$protocol}://{$host}{$path}";
        if (empty($query)) {
            $url .= '?' . Tools::arrToUrl($query);
        }
        $request = new Request($url, $method);
        $request->setHeaders($header)
            ->setBody($body);
        $response = $request->send();

        if ($request->getCode() <> 200 && $request->getCode() <> 204) {
            if (empty($response)) {
                throw new InvalidResponseException('Request exception', 1);
            } else {
                $response = json_decode($response, true);
                throw new QiniuKodoResponseException($response['error'], $response['code']);
            }
        }

        // 如果是空响应，直接返回
        if ($isEmptyResponse) return [];
        $response = json_decode($response, true);
        if (json_last_error() > 0) {
            throw new InvalidDecodeException(json_last_error_msg(), json_last_error());
        }
        return $response ?: [];
    }
}