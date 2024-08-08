<?php

declare(strict_types = 1);

namespace lifetime\bridge\config;

/**
 * 阿里云OSS配置
 * @method  string  accessKeyId 访问KeyID
 * @method  string  accessKeySecret 访问Key秘钥
 * @method  string  regionId 区域ID
 * @method  string  bucketName 默认空间名称
 * @method  string  accessDomain 访问域名
 * @method  bool isHttps 是否使用HTTPS
 * @method  bool internalAccess 是否内网访问
 */
class AliOss extends Basic
{
    /**
     * 获取默认配置
     * @access  protected
     * @return array
     */
    protected function getDefault(): array
    {
        return [
            // 访问Key ID
            'access_key_id' => '',
            // 访问key 秘钥
            'access_key_secret' => '',
            // 区域ID
            'region_id' => '',
            // 默认空间名称
            'bucket_name' => '',
            // 访问域名
            'access_domain' => '',
            // 是否使用HTTPS
            'is_https' => true,
            // 是否内网访问
            'internal_access'=> false
        ];
    }

    /**
     * 获取必须的配置Key
     * @access  protected
     * @return array
     */
    protected function getMustConfig(): array
    {
        return ['access_key_id', 'access_key_secret', 'region_id'];
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
        return 'oss';
    }
}