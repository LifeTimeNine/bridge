<?php

declare(strict_types = 1);

namespace lifetime\bridge\wechat\miniapp;

use lifetime\bridge\Cache;
use lifetime\bridge\config\WechatMiniapp;
use lifetime\bridge\exception\InvalidResponseException;
use lifetime\bridge\Tools;

/**
 * 微信小程序业务基类
 * @throws InvalidArgumentException
 */
abstract class Basic
{
    /**
     * 配置
     * @var WechatMiniapp
     */
    protected $config;

    /**
     * 访问Token异常的Code
     * @var array
     */
    protected $accessTokenErrorCode = ['40014', '40001', '41001', '42001'];

    /**
     * 构造函数
     * @access  public
     * @param   array   $config     配置信息
     * @throws InvalidArgumentException
     */
    public function __construct(array $config = [])
    {
        $this->config = new WechatMiniapp($config);
    }

    /**
     * 获取小程序访问Token
     * @access  protected
     * @return  string
     * @throws InvalidResponseException
     */
    protected function getAccessToken(): string
    {
        $key = "wechat_miniapp_access_token_{$this->config->appId()}";
        if (empty($accessToken = Cache::get($key))) {
            $result = json_decode(Tools::request('GET', 'https://api.weixin.qq.com/cgi-bin/token', [
                'query' => [
                    'appid' => $this->config->appId(),
                    'grant_type' => 'client_credential',
                    'secret' => $this->config->appSecret(),
                ]
            ]), true);
            if (!empty($result['access_token']) && !empty($result['expires_in'])) {
                Cache::set($key, $result['access_token'], (int)$result['expires_in']);
                $accessToken = $result['access_token'];
            } else {
                throw new InvalidResponseException($result['errmsg'], $result['errcode'], $result);
            }
        }
        return $accessToken;
    }

    /**
     * 发起请求
     * @access  public
     * @param   string  $method             请求方法
     * @param   string  $url                请求地址
     * @param   array   $query              请求Query参数
     * @param   array   $body               请求Body参数
     * @param   bool    $appendAccessToken  自动追加访问Token的参数
     * @return  array
     * @throws  InvalidResponseException
     */
    protected function request($method, $url, array $query = [], array $body = [], bool $appendAccessToken = true): array
    {
        $url = str_replace('ACCESS_TOKEN', urlencode($this->getAccessToken()), $url);
        try {
            $result = json_decode(Tools::request($method, $url, [
                'headers' => strtoupper($method) == 'POST' ? ['Content-Type: application/json'] : [],
                'data' => strtoupper($method) == 'POST' ? json_encode($body, JSON_UNESCAPED_UNICODE) : null,
                'query' => $appendAccessToken ? array_merge($query, ['access_token' => urlencode($this->getAccessToken())]) : $query
            ]), true);
            if (!empty($result['errcode']) && $result['errcode'] !== 0) {
                throw new InvalidResponseException($result['errmsg'], $result['errcode'], $result);
            }
        } catch (InvalidResponseException $e) {
            if (in_array($e->getCode(), $this->accessTokenErrorCode)) {
                $result = call_user_func_array([$this, __FUNCTION__], func_get_args());
            } else {
                throw $e;
            }
        }
        return $result;
    }
}
