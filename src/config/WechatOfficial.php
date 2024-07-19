<?php

declare(strict_types = 1);

namespace lifetime\bridge\config;

/**
 * 微信公众号配置
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

    /**
     * 获取APPID
     * @access  public
     * @return  string
     */
    public function appId(): string
    {
        return $this->config['app_id'];
    }

    /**
     * 获取APP Secret
     * @access  public
     * @return  string
     */
    public function appSecret(): string
    {
        return $this->config['app_secret'];
    }
}