<?php

declare(strict_types = 1);

namespace lifetime\bridge\wechat\miniapp;

use lifetime\bridge\exception\InvalidArgumentException;
use lifetime\bridge\exception\InvalidConfigException;
use lifetime\bridge\exception\InvalidDecodeException;

/**
 * 微信小程序用户相关
 * @throws InvalidConfigException
 */
class User extends Basic
{
    /**
     * 获取插件用户openpid
     * @access  public
     * @param   string  $code   通过 wx.pluginLogin 获得的插件用户标志凭证 code
     * @return  array
     * @throws  InvalidResponseException
     */
    public function getPluginOpenPid(string $code): array
    {
        return $this->request('POST', 'https://api.weixin.qq.com/wxa/getpluginopenpid', [], [
            'code' => $code
        ]);
    }

    /**
     * 检查加密信息
     * @access  public
     * @param   string  $encryptedMsgHash   加密数据的sha256，通过Hex（Base16）编码后的字符串
     * @return  array
     * @throws  InvalidResponseException
     */
    public function checkEncryptedData(string $encryptedMsgHash): array
    {
        return $this->request('POST', 'https://api.weixin.qq.com/wxa/business/checkencryptedmsg', [], [
            'encrypted_msg_hash' => $encryptedMsgHash
        ]);
    }

    /**
     * 支付后获取 Unionid
     * @access  public
     * @param   string  $openid         用户OpenID
     * @param   string  $transactionId  微信支付订单号
     * @param   string  $mchId          微信支付分配的商户号，和商户订单号配合使用
     * @param   string  $outTradeNo     微信支付商户订单号，和商户号配合使用
     * @return  array
     * @throws  InvalidResponseException
     */
    public function getPaidUnionId(string $openid, string $transactionId = null, string $mchId = null, string $outTradeNo = null): array
    {
        return $this->request('GET', 'https://api.weixin.qq.com/wxa/getpaidunionid', [
            'openid' => $openid,
            'transaction_id' => $transactionId,
            'mch_id' => $mchId,
            'out_trade_no' => $outTradeNo
        ]);
    }

    /**
     * 获取用户encryptKey
     * @access  public
     * @param   string  $openid     用户OpenID
     * @param   string  $signature  用户登录态签名(hash('sha256', $sessionKey))
     * @param   string  $sigMethod  签名方法，只支持 hmac_sha256
     * @return  array
     * @throws  InvalidResponseException
     */
    public function getUserEncryptKey(string $openid, string $signature, string $sigMethod = 'hmac_sha256'): array
    {
        return $this->request('GET', 'https://api.weixin.qq.com/wxa/business/getuserencryptkey', [
            'openid' => $openid,
            'signature' => $signature,
            'sig_method' => $sigMethod
        ]);
    }

    /**
     * 获取手机号
     * @access  public
     * @param   string  $code   手机号获取凭证
     * @param   string  $openid 用户OpenID
     * @return  array
     * @throws  InvalidResponseException
     */
    public function getPhoneNumber(string $code, string $openid = null): array
    {
        return $this->request('POST', 'https://api.weixin.qq.com/wxa/business/getuserphonenumber', [], [
            'code' => $code,
            'openid' => $openid
        ]);
    }

     /**
     * 验证用户信息
     * @access  public
     * @param   string      $rawData        数据字符串
     * @param   string      $signature      签名字符串
     * @param   string      $sessionKey     session_key
     * @return  boolean
     */
    public function check(string $rawData, string $signature, string $sessionKey): bool
    {
        return sha1("{$rawData}{$sessionKey}") == $signature;
    }

    /**
     * 用户信息解密
     * @access  public
     * @param   string      $encryptedData  密文
     * @param   string      $iv             解密算法初始向量
     * @param   string      $sessionKey     session_key
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidDecodeException
     */
    public function decodeUserInfo(string $encryptedData, string $iv, string $sessionKey): array
    {
        if (strlen($sessionKey) <> 24) throw new InvalidArgumentException('Missing Options [session_key]');
        if (strlen($iv) > 24) throw new InvalidArgumentException('Missing Options [iv]');
        $result = json_decode(openssl_decrypt(
            base64_decode($encryptedData),
            'AES-128-CBC',
            base64_decode($sessionKey),
            1,
            base64_decode($iv)
        ), true);
        if (json_last_error() > 0) {
            throw new InvalidDecodeException(json_last_error_msg(), json_last_error());
        }
        return $result;
    }
}
