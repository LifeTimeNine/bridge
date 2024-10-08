<?php

declare(strict_types = 1);

namespace lifetime\bridge\Wechat\MiniApp;

use \lifetime\bridge\Exception\InvalidConfigException;
use lifetime\bridge\Request;

/**
 * 微信小程序登录相关
 * @throws InvalidConfigException
 */
class Login extends Basic
{
    /**
     * 登录凭证校验
     * @access  public
     * @param   string  $jsCode         登录时获取的 code，可通过wx.login获取
     * @param   string  $grantType      授权类型，此处只需填写 authorization_code
     * @return  array
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     * @throws  WechatMiniAppResponseException
     */
    public function code2session(string $jsCode, string $grantType = 'authorization_code'): array
    {
        return $this->request(Request::METHOD_GET, 'https://api.weixin.qq.com/sns/jscode2session', [
            'appid' => $this->config->appId(),
            'secret' => $this->config->appSecret(),
            'js_code' => $jsCode,
            'grant_type' => $grantType
        ], [], false);
    }

    /**
     * 检验登录态
     * @access  public
     * @param   string  $openid     用户OpenID
     * @param   string  $signature  用户登录态签名(hash('sha256', $sessionKey))
     * @param   string  $sigMethod  用户登录态签名的哈希方法，目前只支持 hmac_sha256
     * @return  array
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     * @throws  WechatMiniAppResponseException
     */
    public function checkSession(string $openid, string $signature, string $sigMethod = 'hmac_sha256'): array
    {
        return $this->request(Request::METHOD_GET, 'https://api.weixin.qq.com/wxa/checksession', [
            'openid' => $openid,
            'signature' => $signature,
            'sig_method' => $sigMethod
        ]);
    }

    /**
     * 重置登录态
     * @access  public
     * @param   string  $openid     用户OpenID
     * @param   string  $signature  用户登录态签名(hash('sha256', $sessionKey))
     * @param   string  $sigMethod  用户登录态签名的哈希方法，目前只支持 hmac_sha256
     * @return  array
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     * @throws  WechatMiniAppResponseException
     */
    public function resetSession(string $openid, string $signature, string $sigMethod = 'hmac_sha256'): array
    {
        return $this->request(Request::METHOD_GET, 'https://api.weixin.qq.com/wxa/resetusersessionkey', [
            'openid' => $openid,
            'signature' => $signature,
            'sig_method' => $sigMethod
        ]);
    }
}
