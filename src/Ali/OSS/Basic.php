<?php

declare(strict_types = 1);

namespace lifetime\bridge\ali\OSS;

use lifetime\bridge\Config\AliOss;
use lifetime\bridge\Exception\AliOssResponseException;
use lifetime\bridge\Exception\InvalidArgumentException;
use lifetime\bridge\Exception\InvalidConfigException;
use lifetime\bridge\Exception\InvalidDecodeException;
use lifetime\bridge\Exception\InvalidResponseException;
use lifetime\bridge\Request;
use lifetime\bridge\Tools;

/**
 * 阿里云对象存储基类
 * @throws InvalidConfigException
 */
abstract class Basic
{
    /**
     * 配置
     * @var AliOss
     */
    protected $config;

    /**
     * 区域列表
     * @var array
     */
    protected static $regionList = [
        'cn-hangzhou' => ['name' => '华东1（杭州）', 'extranet_endpoint' => 'oss-cn-hangzhou.aliyuncs.com', 'internal_endpoint' => 'oss-cn-hangzhou-internal.aliyuncs.com'],
        'cn-shanghai' => ['name' => '华东2（上海）', 'extranet_endpoint' => 'oss-cn-shanghai.aliyuncs.com', 'internal_endpoint' => 'oss-cn-shanghai-internal.aliyuncs.com'],
        'cn-nanjing' => ['name' => '华东5（南京-本地地域）', 'extranet_endpoint' => 'oss-cn-nanjing.aliyuncs.com', 'internal_endpoint' => 'oss-cn-nanjing-internal.aliyuncs.com'],
        'cn-fuzhou' => ['name' => '华东6（福州-本地地域）', 'extranet_endpoint' => 'oss-cn-fuzhou.aliyuncs.com', 'internal_endpoint' => 'oss-cn-fuzhou-internal.aliyuncs.com'],
        'cn-wuhan' => ['name' => '华中1（武汉-本地地域）', 'extranet_endpoint' => 'oss-cn-wuhan-lr.aliyuncs.com', 'internal_endpoint' => 'oss-cn-wuhan-lr-internal.aliyuncs.com'],
        'cn-qingdao' => ['name' => '华北1（青岛）', 'extranet_endpoint' => 'oss-cn-qingdao.aliyuncs.com', 'internal_endpoint' => 'oss-cn-qingdao-internal.aliyuncs.com'],
        'cn-beijing' => ['name' => '华北2（北京）', 'extranet_endpoint' => 'oss-cn-beijing.aliyuncs.com', 'internal_endpoint' => 'oss-cn-beijing-internal.aliyuncs.com'],
        'cn-zhangjiakou' => ['name' => '华北 3（张家口）', 'extranet_endpoint' => 'oss-cn-zhangjiakou.aliyuncs.com', 'internal_endpoint' => 'oss-cn-zhangjiakou-internal.aliyuncs.com'],
        'cn-huhehaote' => ['name' => '华北5（呼和浩特）', 'extranet_endpoint' => 'oss-cn-huhehaote.aliyuncs.com', 'internal_endpoint' => 'oss-cn-huhehaote-internal.aliyuncs.com'],
        'cn-wulanchabu' => ['name' => '华北6（乌兰察布）', 'extranet_endpoint' => 'oss-cn-wulanchabu.aliyuncs.com', 'internal_endpoint' => 'oss-cn-wulanchabu-internal.aliyuncs.com'],
        'cn-shenzhen' => ['name' => '华南1（深圳）', 'extranet_endpoint' => 'oss-cn-shenzhen.aliyuncs.com', 'internal_endpoint' => 'oss-cn-shenzhen-internal.aliyuncs.com'],
        'cn-heyuan' => ['name' => '华南2（河源）', 'extranet_endpoint' => 'oss-cn-heyuan.aliyuncs.com', 'internal_endpoint' => 'oss-cn-heyuan-internal.aliyuncs.com'],
        'cn-guangzhou' => ['name' => '华南3（广州）', 'extranet_endpoint' => 'oss-cn-guangzhou.aliyuncs.com', 'internal_endpoint' => 'oss-cn-guangzhou-internal.aliyuncs.com'],
        'cn-chengdu' => ['name' => '西南1（成都）', 'extranet_endpoint' => 'oss-cn-chengdu.aliyuncs.com', 'internal_endpoint' => 'oss-cn-chengdu-internal.aliyuncs.com'],
        'cn-hongkong' => ['name' => '中国香港', 'extranet_endpoint' => 'oss-cn-hongkong.aliyuncs.com', 'internal_endpoint' => 'oss-cn-hongkong-internal.aliyuncs.com'],
        'us-west-1' => ['name' => '美国（硅谷）', 'extranet_endpoint' => 'oss-us-west-1.aliyuncs.com', 'internal_endpoint' => 'oss-us-west-1-internal.aliyuncs.com'],
        'us-east-1' => ['name' => '美国（弗吉尼亚）', 'extranet_endpoint' => 'oss-us-east-1.aliyuncs.com', 'internal_endpoint' => 'oss-us-east-1-internal.aliyuncs.com'],
        'ap-northeast-1' => ['name' => '日本（东京）', 'extranet_endpoint' => 'oss-ap-northeast-1.aliyuncs.com', 'internal_endpoint' => 'oss-ap-northeast-1-internal.aliyuncs.com'],
        'ap-northeast-2' => ['name' => '韩国（首尔）', 'extranet_endpoint' => 'oss-ap-northeast-2.aliyuncs.com', 'internal_endpoint' => 'oss-ap-northeast-2-internal.aliyuncs.com'],
        'ap-southeast-1' => ['name' => '新加坡', 'extranet_endpoint' => 'oss-ap-southeast-1.aliyuncs.com', 'internal_endpoint' => 'oss-ap-southeast-1-internal.aliyuncs.com'],
        'ap-southeast-2' => ['name' => '澳大利亚（悉尼）', 'extranet_endpoint' => 'oss-ap-southeast-2.aliyuncs.com', 'internal_endpoint' => 'oss-ap-southeast-2-internal.aliyuncs.com'],
        'ap-southeast-3' => ['name' => '马来西亚（吉隆坡）', 'extranet_endpoint' => 'oss-ap-southeast-3.aliyuncs.com', 'internal_endpoint' => 'oss-ap-southeast-3-internal.aliyuncs.com'],
        'ap-southeast-5' => ['name' => '印度尼西亚（雅加达）', 'extranet_endpoint' => 'oss-ap-southeast-5.aliyuncs.com', 'internal_endpoint' => 'oss-ap-southeast-5-internal.aliyuncs.com'],
        'ap-southeast-6' => ['name' => '菲律宾（马尼拉）', 'extranet_endpoint' => 'oss-ap-southeast-6.aliyuncs.com', 'internal_endpoint' => 'oss-ap-southeast-6-internal.aliyuncs.com'],
        'ap-southeast-7' => ['name' => '泰国（曼谷）', 'extranet_endpoint' => 'oss-ap-southeast-7.aliyuncs.com', 'internal_endpoint' => 'oss-ap-southeast-7-internal.aliyuncs.com'],
        'ap-south-1' => ['name' => '印度（孟买）关停中', 'extranet_endpoint' => 'oss-ap-south-1.aliyuncs.com', 'internal_endpoint' => 'oss-ap-south-1-internal.aliyuncs.com'],
        'eu-central-1' => ['name' => '德国（法兰克福）', 'extranet_endpoint' => 'oss-eu-central-1.aliyuncs.com', 'internal_endpoint' => 'oss-eu-central-1-internal.aliyuncs.com'],
        'eu-west-1' => ['name' => '英国（伦敦）', 'extranet_endpoint' => 'oss-eu-west-1.aliyuncs.com', 'internal_endpoint' => 'oss-eu-west-1-internal.aliyuncs.com'],
        'me-east-1' => ['name' => '阿联酋（迪拜）', 'extranet_endpoint' => 'oss-me-east-1.aliyuncs.com', 'internal_endpoint' => 'oss-me-east-1-internal.aliyuncs.com'],
    ];

    /**
     * 构造函数
     * @access  public
     * @param   array   $config 配置
     * @throws InvalidConfigException
     */
    public function __construct(array $config = [])
    {
        $this->config = new AliOss($config);
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
     * 生成Header签名
     * @access  protected
     * @param   string  $method     请求方法
     * @param   string  $bucket     存储空间名称
     * @param   string  $object     存储对象名称
     * @param   array   $query      请求Query参数
     * @param   array   $header     请求头
     * @return  string
     */
    protected function buildHeaderSign(string $method, string $bucket = null, string $object = null, array $query = [], array $header = []): string
    {
        // 构造CanonicalRequest
        $canonicalUri = '/';
        if (!empty($bucket)) {
            $canonicalUri .= "{$bucket}/";
            if (!empty($object)) {
                $canonicalUri .= $object;
            }
        }
        $canonicalUri = str_replace(array('%2F'), array('/'), rawurlencode($canonicalUri));
        $queryData = [];
        foreach($query as $k => $v) {
            $queryData[rawurlencode($k)] = is_null($v) ? $v : rawurlencode((string)$v);
        }
        ksort($queryData);
        $querySignList = [];
        foreach($queryData as $k => $v) {
            if (is_null($v)) {
                $querySignList[] = $k;
            } else {
                $querySignList[] = "{$k}={$v}";
            }
        }
        $canonicalQuery = implode('&', $querySignList);

        $addHeaderList = [];
        $headerSignData = [];
        foreach($header as $k => $v) {
            $k = strtolower($k);
            if (!in_array($k, ['content-type', 'content-md5']) && strpos($k, 'x-oss-') !== 0) {
                $addHeaderList[] = $k;
            }
            $headerSignData[$k] = trim((string)$v);
        }
        ksort($headerSignData);
        sort($addHeaderList);
        $canonicalHeader = '';
        foreach($headerSignData as $k => $v) {
            $canonicalHeader .= "{$k}:{$v}\n";
        }
        $canonicalAddHeader = implode(';', $addHeaderList);
        $hashPayload = 'UNSIGNED-PAYLOAD';

        $canonicalRequest = $method . "\n"
            . $canonicalUri . "\n"
            . $canonicalQuery . "\n"
            . $canonicalHeader . "\n"
            . $canonicalAddHeader . "\n"
            . $hashPayload;
        // 构造待签名字符串（StringToSign）
        $timestamp = $header['x-oss-date'] ?? gmdate('Ymd\THis\Z');
        $date = date('Ymd');
        $region = $this->config->regionId();
        $product = 'oss';
        $scope = "{$date}/{$region}/{$product}/aliyun_v4_request";
        $stringToSign = 'OSS4-HMAC-SHA256' . "\n"
            . $timestamp . "\n"
            . $scope . "\n"
            . hash('sha256', $canonicalRequest);
        //计算Signature
        $dateKey = hash_hmac('sha256', $date, "aliyun_v4{$this->config->accessKeySecret()}", true);
        $dataRegionKey = hash_hmac('sha256', $region, $dateKey, true);
        $dataRegionServiceKey = hash_hmac('sha256', $product, $dataRegionKey, true);
        $signKey = hash_hmac('sha256', 'aliyun_v4_request', $dataRegionServiceKey, true);
        $sign = bin2hex(hash_hmac('sha256', $stringToSign, $signKey, true));
        return "OSS4-HMAC-SHA256 Credential={$this->config->accessKeyId()}/{$date}/{$region}/{$product}/aliyun_v4_request,AdditionalHeaders={$canonicalAddHeader},Signature={$sign}";
    }

    /**
     * 构建header签名并发送请求
     * @access  protected
     * @param   string  $method         请求方法
     * @param   string  $uri            请求地址
     * @param   string  $bucket         存储空间名称
     * @param   string  $object         存储对象名称
     * @param   array   $header         请求头
     * @param   array   $query          请求Query
     * @param   string  $body           请求Body
     * @param   bool    $returnHeader   是否返回响应头
     * @return  array
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     * @throws  AliOssResponseException
     */
    protected function buildHeaderSignAndRequest(string $method, string $uri, string $bucket = null, string $object = null, array $header = [], array $query = [], string $body = null, bool $returnHeader = false): array
    {
        $host = $this->getRegion()['extranet_endpoint'];
        if (!empty($bucket)) $host = "{$bucket}.{$host}";
        $header['Host'] = $host;
        $header['x-oss-date'] = gmdate('Ymd\THis\Z', time());
        $header['x-oss-content-sha256'] = 'UNSIGNED-PAYLOAD';
        $header['Authorization'] = $this->buildHeaderSign($method, $bucket, $object, $query, $header);

        if (count($query) > 0) {
            $url = "https://{$host}{$uri}?" . Tools::arrToUrl($query);
        } else {
            $url = "https://{$host}{$uri}";
        }

        $request = new Request($url, $method);
        $request->setHeaders($header)
            ->setBody($body);
        
        $response = $request->send();
        if ($request->getCode() <> 200 && $request->getCode() <> 204) {
            if (!empty($response) && $request->getContentType() == Request::CONTENT_TYPE_XML) {
                $response = Tools::xmlToArr($response);
                throw new AliOssResponseException($response['Message'], $response['Code'], $response['RequestId']);
            } else {
                throw new InvalidResponseException('Request exception', 1);
            }
        }

        if (empty($response)) {
            $response = [];
        } elseif ($request->getContentType() == Request::CONTENT_TYPE_XML) {
            $response = Tools::xmlToArr($response);
        } elseif ($request->getContentType() == Request::CONTENT_TYPE_JSON) {
            $response = json_decode($response, true);
        } else {
            $response = [$response];
        }
        return $returnHeader ? [$response, $request->getHeader()] : $response;
    }
}