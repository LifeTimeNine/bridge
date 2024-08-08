<?php

declare(strict_types = 1);

namespace lifetime\bridge\wechat\official;

use lifetime\bridge\Cache;
use lifetime\bridge\config\WechatOfficial;
use lifetime\bridge\exception\InvalidResponseException;
use lifetime\bridge\exception\InvalidConfigException;
use lifetime\bridge\exception\InvalidDecodeException;
use lifetime\bridge\exception\WechatOfficialResponseException;
use lifetime\bridge\Request;

/**
 * 微信公众号业务基类
 * @throws InvalidConfigException
 */
abstract class Basic
{
    /**
     * 配置
     * @var WechatOfficial
     */
    protected $config;

    /**
     *  accessToken 异常Code
     * @var array
     */
    protected $accessTokenErrorCode = ['40014', '40001', '41001', '42001'];

    /**
     * 构造函数
     * @access  public
     * @param   array   $config     配置参数
     * @throws InvalidConfigException
     */
    public function __construct($config = [])
    {
        $this->config = new WechatOfficial($config);
        Cache::init();
    }

    /**
     * 获取access_token
     * @access  protected
     * @return  string
     * @throws InvalidResponseException
     */
    protected function getAccessToken(): string
    {
        $key = "wechat_official_access_token_{$this->config->appId()}";

        $accessToken = Cache::get($key);

        if (!empty($accessToken)) return $accessToken;

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->config->appId()}&secret={$this->config->appSecret()}";

        $res = json_decode(Request::get($url)->send(), true);

        if (!empty($res['errcode'])) throw new InvalidResponseException($res['errmsg'], $res['errcode'], $res);

        Cache::set($key, $res['access_token'], $res['expires_in'] - 300);

        return $res['access_token'];
    }

    /**
     * 发起请求
     * @access  protected
     * @param   string  $method             请求方法
     * @param   string  $url                请求地址
     * @param   array   $query              请求Query参数
     * @param   array   $body               请求Body参数
     * @param   bool    $appendAccessToken  自动追加访问Token的参数
     * @return  array
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     * @throws  WechatOfficialResponseException
     */
    protected function request(string $method, string $url, array $query = [], array $body = [], bool $appendAccessToken = true): array
    {
        if ($appendAccessToken) $query['access_token'] = $this->getAccessToken();
        if (!empty($query)) $url .= '?' . http_build_query($query);

        $request = new Request($url, $method);
        if ($method == Request::METHOD_POST) {
            $request->setHeaders([Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_JSON])
                ->setBody(json_encode($body, JSON_UNESCAPED_UNICODE));
        }
        $response = $request->send();
        if ($request->getCode() <> 200) throw new InvalidResponseException('Request exception', 1);

        $response = json_decode($response, true);
        if (json_last_error() > 0) throw new InvalidDecodeException(json_last_error_msg(), json_last_error());

        if (isset($response['errcode'])) {
            if (in_array($response['errcode'], $this->accessTokenErrorCode)) {
                $response = call_user_func_array([$this, __FUNCTION__], func_get_args());
            } else {
                throw new WechatOfficialResponseException($response['errmsg'], (int)$response['errcode']);
            }
        }

        return $response ?: [];
    }
}