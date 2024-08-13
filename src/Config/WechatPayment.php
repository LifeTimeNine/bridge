<?php

declare(strict_types = 1);

namespace lifetime\bridge\Config;

use InvalidArgumentException;

/**
 * 微信支付配置
 * @method string appId() 应用ID
 * @method string mchId() 商户ID
 * @method string mchKey() 商户支付密钥
 * @method string sslCert() 证书cert.pem路径
 * @method string sslKey() 证书key.pem路径
 * @throws InvalidConfigException
 */
class WechatPayment extends Basic
{
    /**
     * 获取默认配置
     * @access  protected
     * @return array
     */
    protected function getDefault(): array
    {
        return [
            // 应用ID
            'app_id'=> '',
            // 商户ID
            'mch_id' => '',
            // 商户支付密钥
            'mch_key' => '',
            // 证书cert.pem路径
            'ssl_cert' => '',
            // 证书key.pem路径
            'ssl_key' => '',
        ];
    }

    /**
     * 获取必须的配置Key
     * @access  protected
     * @return array
     */
    protected function getMustConfig(): array
    {
        return ['app_id','mch_id', 'mch_key', 'ssl_cert', 'ssl_key'];
    }

    /**
     * 获取平台名称
     * @access  protected
     * @return  string
     */
    protected function getPlatform(): string
    {
        return 'wechat';
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

    /**
     * 验证必须配置
     * @access  protected
     * @return  void
     */
    protected function check()
    {
        parent::check();
        if (!file_exists($this->config['ssl_cert'])) {
            throw new InvalidArgumentException("File does not exist [{$this->getPlatform()}.{$this->getProduct()}.ssl_cert]");
        }
        if (!file_exists($this->config['ssl_key'])) {
            throw new InvalidArgumentException("File does not exist [{$this->getPlatform()}.{$this->getProduct()}.ssl_key]");
        }
    }
}