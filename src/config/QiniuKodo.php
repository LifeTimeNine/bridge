<?php

declare(strict_types = 1);

namespace lifetime\bridge\config;

/**
 * 七牛云对象存储配置
 * @method string accessKey() 访问Key
 * @method string secretKey() 秘钥Key
 * @method string regionId() 区域ID
 * @method string accessDomain() 访问域名
 * @method string isSsl() 是否使用SSL
 * @method string bucketName() 默认Bucket名称
 * @throws InvalidConfigException
 */
class QiniuKodo extends Basic
{
    /**
     * 获取默认配置
     * @access  protected
     * @return array
     */
    protected function getDefault(): array
    {
        return [
            // AccessKey
            'access_key' => '',
            // SecretKey
            'secret_key' => '',
            // 区域ID
            'region_id' => '',
            // 访问域名
            'access_domain' => '',
            // 是否使用SSL
            'is_ssl' => true,
            // 默认Bucket名称
            'bucket_name' => ''
        ];
    }

    /**
     * 获取必须的配置Key
     * @access  protected
     * @return array
     */
    protected function getMustConfig(): array
    {
        return ['access_key', 'secret_key', 'region_id', 'access_domain'];
    }

    /**
     * 获取平台名称
     * @access  protected
     * @return  string
     */
    protected function getPlatform(): string
    {
        return 'qiniu';
    }

    /**
     * 获取产品名称
     * @access  protected
     * @return  string
     */
    protected function getProduct(): string
    {
        return 'kodo';
    }
}