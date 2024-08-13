<?php

declare(strict_types = 1);

namespace lifetime\bridge\Config;

/**
 * 支付宝支付配置
 * @method string sandbox() 是否是沙箱
 * @method string appId() 应用ID
 * @method string appPublicKey() 应用公钥
 * @method string appPrivateKey() 应用私钥
 * @method string alipayPublicKey() 支付宝公钥
 * @method string appPublicCertPath() 应用公钥地址
 * @method string alipayPublicCertPath() 支付宝公钥
 * @method string alipayRootCertPath() 支付宝根证书地址
 * @method string encryptKey() 加密Key
 * @throws InvalidConfigException
 */
class AliPayment extends Basic
{
    /**
     * 获取默认配置
     * @access  protected
     * @return array
     */
    protected function getDefault(): array
    {
        return [
            // 是否是沙箱
            'sandbox' => false,
            // 应用ID
            'app_id' => '',
            // 应用公钥
            'app_public_key' => '',
            // 应用私钥
            'app_private_key' => '',
            // 支付宝公钥
            'alipay_public_key' => '',
            // 应用公钥证书地址
            'app_public_cert_path' => '',
            // 支付宝公钥证书地址
            'alipay_public_cert_path' => '',
            // 支付宝根证书地址
            'alipay_root_cert_path' => '',
            // 加密key
            'encrypt_key' => ''
        ];
    }

    /**
     * 获取必须的配置Key
     * @access  protected
     * @return array
     */
    protected function getMustConfig(): array
    {
        $keyList = ['app_id', 'app_private_key'];
        if (!empty($this->config['app_public_cert_path'])) {
            $keyList = array_merge($keyList, ['app_public_cert_path', 'alipay_public_cert_path', 'alipay_root_cert_path']);
        } else {
            $keyList = array_merge($keyList, ['alipay_public_key']);
        }
        return $keyList;
    }

    /**
     * 获取平台名称
     * @access  protected
     * @return  string
     */
    protected function getPlatform(): string
    {
        return 'ali';
    }

    /**
     * 获取产品名称
     * @access  protected
     * @return  string
     */
    protected function getProduct(): string
    {
        return 'alipay';
    }
}