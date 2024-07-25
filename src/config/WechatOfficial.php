<?php

declare(strict_types = 1);

namespace lifetime\bridge\config;

/**
 * 微信公众号配置
 * @method string appId() 获取APPID
 * @method string appSecret() 获取APP Secret
 * @throws InvalidConfigException
 */
class WechatOfficial extends Basic
{
    /**
     * 获取默认配置
     * @access  protected
     * @return array
     */
    protected function getDefault(): array
    {
        return [
            // 公众号appid
            'app_id' => '',
            // 公众号secret
            'app_secret' => ''
        ];
    }

    /**
     * 获取必须的配置Key
     * @access  protected
     * @return array
     */
    protected function getMustConfig(): array
    {
        return ['app_id','app_secret'];
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
        return 'official';
    }
}