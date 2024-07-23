<?php

declare(strict_types = 1);

namespace lifetime\bridge\wechat\official;

use lifetime\bridge\Cache;
use lifetime\bridge\config\WechatOfficial;
use lifetime\bridge\exception\InvalidResponseException;
use lifetime\bridge\Tools;

/**
 * 微信公众号业务基类
 * @throws InvalidArgumentException
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

        $res = json_decode(Tools::request('get', $url), true);

        if (!empty($res['errcode'])) throw new InvalidResponseException($res['errmsg'], $res['errcode'], $res);

        Cache::set($key, $res['access_token'], $res['expires_in'] - 300);

        return $res['access_token'];
    }

    /**
     * 发起请求
     * @access  protected
     * @param   string  $method     请求方法
     * @param   string  $url        请求地址
     * @param   array   $query      请求Query参数
     * @param   array   $body       请求Body参数
     * @return  array
     * @throws InvalidResponseException
     */
    protected function request(string $method, string $url, array $query = [], array $body = []): array
    {
        $url = str_replace('ACCESS_TOKEN', urlencode($this->getAccessToken()), $url);
        try {
            $result = json_decode(Tools::request($method, $url, [
                'headers' => strtoupper($method) == 'POST' ? ['Content-Type: application/json'] : [],
                'data' => strtoupper($method) == 'POST' ? json_encode($body, JSON_UNESCAPED_UNICODE) : null,
                'query' => $query
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