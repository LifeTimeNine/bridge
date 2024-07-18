<?php

declare(strict_types = 1);

namespace lifetime\bridge\config;

/**
 * 支付宝支付配置
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
            // 商户生成签名字符串所使用的签名算法类型
            'sign_type' => 'RSA2',
            // 应用ID
            'appid' => '',
            // 公钥
            'public_key' => '',
            // 私钥
            'private_key' => '',
            // 支付宝公钥
            'alipay_public_key' => '',
        ];
    }

    /**
     * 获取必须的配置Key
     * @access  protected
     * @return array
     */
    protected function getMustConfig(): array
    {
        return ['appid', 'alipay_public_key', 'private_key'];
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
        return 'payment';
    }
}