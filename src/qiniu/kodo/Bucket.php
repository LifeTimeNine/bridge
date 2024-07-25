<?php

declare(strict_types = 1);

namespace lifetime\bridge\qiniu\kodo;

use lifetime\bridge\exception\InvalidArgumentException;
use lifetime\bridge\exception\InvalidConfigException;
use lifetime\bridge\exception\InvalidDecodeException;
use lifetime\bridge\exception\InvalidResponseException;

/**
 * 七牛云存储对象Bucket相关操作
 * @throws InvalidConfigException
 */
class Bucket extends Basic
{
    /**
     * 获取存储区域列表
     * @access  public
     * @return array
     */
    public function getRegionList(): array
    {
        $result = [];
        foreach(self::$regionList as $k => $v) {
            $result[] = ['id' => $k, 'name' => $v['name']];
        }
        return $result;
    }

    /**
     * 创建Bucket
     * @access  public
     * @param   string  $regionId   区域ID
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function create(string $regionId): array
    {
        $method = self::REQUEST_METHOD_POST;
        $host = $this->getRegion($regionId)['bucket_manage'];
        $path = "/mkbucketv3/{$this->getBucketName()}/region/{$regionId}";
        $header = [
            self::S_AUTHORIZATION => $this->buildMangeSign($method, $host, $path)
        ];
        return $this->request($method, $host, $path, $header);
    }

    /**
     * 删除Bucket
     * @access  public
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function delete(): array
    {
        $method = self::REQUEST_METHOD_POST;
        $host = $this->getRegion()['bucket_manage'];
        $path = "/drop/{$this->getBucketName()}";
        $header = [
            self::S_AUTHORIZATION => $this->buildMangeSign($method, $host, $path)
        ];
        return $this->request($method, $host, $path, $header);
    }

    /**
     * 获取Bucket空间域名
     * @access  public
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getDomain(): array
    {
        $method = self::REQUEST_METHOD_GET;
        $host = $this->getRegion()['query'];
        $path = '/v6/domain/list';
        $query = [
            'tbl' => $this->getBucketName()
        ];
        $header = [
            self::S_AUTHORIZATION => $this->buildMangeSign($method, $host, $path, $query)
        ];
        return $this->request($method, $host, $path, $header, $query);
    }

    /**
     * 设置镜像源
     * @access  public
     * @param   string  $accessUrl      镜像源的访问域名
     * @param   string  $host           回源时使用的Host头部值
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setImageSource(string $accessUrl, string $host = null): array
    {
        $method = self::REQUEST_METHOD_POST;
        $host = $this->getRegion()['bucket_manage'];
        $path = "/image/{$this->getBucketName()}/from/{$this->urlBase64($accessUrl)}/host/{$this->urlBase64($host)}";
        $header = [
            self::S_AUTHORIZATION => $this->buildMangeSign($method, $host, $path)
        ];
        return $this->request($method, $host, $path, $header);
    }

    /**
     * 设置访问权限
     * @access  public
     * @param   bool    $private    是否是私有
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setAccessAuth(bool $private): array
    {
        $method = self::REQUEST_METHOD_POST;
        $host = $this->getRegion()['bucket_manage'];
        $path = '/private';
        $query = [
            'bucket' => $this->getBucketName(),
            'private' => $private ? 1 : 0
        ];
        $header = [
            self::S_CONTENT_TYPE => self::CONTENT_TYPE_URLENCODE
        ];
        $header[self::S_AUTHORIZATION] = $this->buildMangeSign($method, $host, $path, $query, $header);
        return $this->request($method, $host, $path, $header, $query);
    }

    /**
     * 设置空间标签
     * @access  public
     * @param   array   $tagList    标签列表['key1'=>'value1','key2'=>'value2']
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setTag(array $tagList): array
    {
        $method = self::REQUEST_METHOD_PUT;
        $host = $this->getRegion()['bucket_manage'];
        $path = '/bucketTagging';
        $query = [
            'bucket' => $this->getBucketName()
        ];
        $tagData = [];
        foreach($tagList as $k => $v) $tagData[] = ['Key' => $k, 'Value' => $v];
        $body = json_encode(['Tags' => $tagData], JSON_UNESCAPED_UNICODE);
        $header = [
            self::S_CONTENT_TYPE => self::CONTENT_TYPE_JSON
        ];
        $header[self::S_AUTHORIZATION] = $this->buildMangeSign($method, $host, $path, $query, $header, $body);
        return $this->request($method, $host, $path, $header, $query, $body);
    }

    /**
     * 获取空间标签
     * @access  public
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getTag(): array
    {
        $method = self::REQUEST_METHOD_GET;
        $host = $this->getRegion()['bucket_manage'];
        $path = '/bucketTagging';
        $query = [
            'bucket' => $this->getBucketName()
        ];
        $header = [
            self::S_AUTHORIZATION => $this->buildMangeSign($method, $host, $path, $query)
        ];
        return $this->request($method, $host, $path, $header, $query);
    }

    /**
     * 删除空间标签
     * @access  public
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deleteTag(): array
    {
        $method = self::REQUEST_METHOD_DELETE;
        $host = $this->getRegion()['bucket_manage'];
        $path = '/bucketTagging';
        $query = [
            'bucket' => $this->getBucketName()
        ];
        $header = [
            self::S_AUTHORIZATION => $this->buildMangeSign($method, $host, $path, $query)
        ];
        return $this->request($method, $host, $path, $header, $query);
    }
}
