<?php

declare(strict_types = 1);

namespace lifetime\bridge\wechat\official;

use lifetime\bridge\Cache;
use lifetime\bridge\exception\InvalidArgumentException;
use lifetime\bridge\Tools;

/**
 * 公众号网页授权
 * @throws InvalidArgumentException
 */
class Oauth extends Basic
{
    /**
     * 跳转到微信授权页面
     * @description 这是网页授权的第一步，注意调用此方法之后前端会跳转，因此不可以有任何返回数据
     * @access  public
     * @param   string  $redirectUri    跳转地址
     * @param   bool    $scope          是否获取用户详细信息
     * @param   string  $state          state参数
     * @return  void
     * @throws InvalidArgumentException
     */
    public function authorize(string $redirectUri, bool $scope = true, string $state = null)
    {
        if (empty($redirectUri)) {
            throw new InvalidArgumentException("Missing redirectUri empty");
        } else {
            $redirectUri = urlencode($redirectUri);
        }

        $scope = $scope ? 'snsapi_userinfo' : 'snsapi_base';

        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->config->appId()}&redirect_uri={$redirectUri}&response_type=code&scope={$scope}&state={$state}#wechat_redirect";
        header("Location: {$url}");
    }

    /**
     * 获取用户访问Token
     * @description 这是网页授权第第二布，通过Code获取用户访问Token
     * @access  public
     * @return  array
     * @throws InvalidArgumentException
     * @throws InvalidResponseException
     */
    public function getUserAccessToken(): array
    {
        if (empty($_GET['code'])) throw new InvalidArgumentException("Missing Option [code]");

        return $this->request('GET', 'https://api.weixin.qq.com/sns/oauth2/access_token', [
            'appid' => $this->config->appId(),
            'secret' => $this->config->appSecret(),
            'code' => $_GET['code'],
            'grant_type' => 'authorization_code'
        ]);
    }

    /**
     * 刷新 access_token
     * @access  public
     * @param   string  $refreshToken   刷新Token
     * @return array
     * @throws InvalidResponseException
     */
    public function refreshAccessToken($refreshToken): array
    {
        return $this->request('GET', 'https://api.weixin.qq.com/sns/oauth2/refresh_token', [
            'appid' => $this->config->appId(),
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token'
        ]);
    }

    /**
     * 获取用户个人信息（UnionID机制）
     * @access  public
     * @param string $accessToken   访问Token
     * @param string $openid        用户OpenId
     * @return array
     * @throws InvalidResponseException
     */
    public function getUserInfo($accessToken, $openid): array
    {
        return $this->request('GET', 'https://api.weixin.qq.com/sns/userinfo', [
            'access_token' => $accessToken,
            'openid' => $openid
        ]);
    }

    /**
     * 校验授权凭证是否有效
     * @access  public
     * @param   string  $accessToken    访问Token
     * @param   string  $openid         用户OpenId
     * @return  array
     * @throws InvalidResponseException
     */
    public function checkAccessToken(string $accessToken, string $openid): array
    {
        return $this->request('GET', 'https://api.weixin.qq.com/sns/auth', [
            'access_token' => $accessToken,
            'openid' => $openid
        ]);
    }
    
    /**
     * 获取JS-SDK使用权限
     * @access  public
     * @param   string  $url    当前网页的URL，不包含#及其后面部分
     * @return array
     */
    public function getJsSdkSign(string $url): array
    {
        $key = "wechat_jsapi_ticket_{$this->config->appId()}";
        if (empty($ticket = Cache::get($key))) {
            $result = $this->request('GET', 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=jsapi');
            Cache::set($key, $result['ticket'], $result['expires_in']);
            $ticket = $result['ticket'];
        }
        
        $data = [
            'noncestr' => Tools::createRandomStr(16),
            'jsapi_ticket' => $ticket,
            'timestamp' => time(),
            'utl' => $url
        ];

        ksort($data);

        $sign = sha1(Tools::arrToUrl($data));

        return [
            'appId' => $this->config->appId(),
            'timestamp' => $data['timestamp'],
            'nonceStr' => $data['noncestr'],
            'signature' => $sign,
        ];
    }
}
