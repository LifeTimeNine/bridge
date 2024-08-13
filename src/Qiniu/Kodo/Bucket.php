<?php

declare(strict_types = 1);

namespace lifetime\bridge\Qiniu\Kodo;

use lifetime\bridge\Exception\InvalidArgumentException;
use lifetime\bridge\Exception\InvalidConfigException;
use lifetime\bridge\Exception\InvalidDecodeException;
use lifetime\bridge\Exception\InvalidResponseException;
use lifetime\bridge\Request;

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
     * 获取 Bucket 列表
     * @access  public
     * @param   array   $tags   过滤空间的标签或标签值['key1'=>'value1','key2'=>'value2']
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function list(array $tags = []): array
    {
        $method = Request::METHOD_GET;
        $host = $this->getRegion()['bucket_manage'];
        $path = '/buckets';
        $query = [];
        if (!empty($tags)) {
            $query['tagCondition'] = [];
            foreach($tags as $key => $value) {
                $query['tagCondition'][] = "key={$key}&value={$value}";
            }
            $query['tagCondition'] = $this->urlBase64(implode(';', $query['tagCondition']));
        }
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED
        ];
        $header[Request::HEADER_AUTHORIZATION] = $this->buildMangeSign($method, $host, $path, $query, $header);
        return $this->request($method, $host, $path, $header, $query);
    }

    /**
     * 创建Bucket
     * @access  public
     * @param   string  $name       存储空间名称
     * @param   string  $regionId   区域ID
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function create(string $name, string $regionId): array
    {
        $method = Request::METHOD_POST;
        $host = $this->getRegion($regionId)['bucket_manage'];
        $path = "/mkbucketv3/{$name}/region/{$regionId}";
        $header = [
            Request::HEADER_AUTHORIZATION => $this->buildMangeSign($method, $host, $path)
        ];
        return $this->request($method, $host, $path, $header);
    }

    /**
     * 删除Bucket
     * @access  public
     * @param   string  $name       存储空间名称
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function delete(string $name): array
    {
        $method = Request::METHOD_POST;
        $host = $this->getRegion()['bucket_manage'];
        $path = "/drop/{$$name}";
        $header = [
            Request::HEADER_AUTHORIZATION => $this->buildMangeSign($method, $host, $path)
        ];
        return $this->request($method, $host, $path, $header);
    }

    /**
     * 获取Bucket空间域名
     * @access  public
     * @param   string  $name       存储空间名称
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getDomain(string $name): array
    {
        $method = Request::METHOD_GET;
        $host = $this->getRegion()['query'];
        $path = '/v6/domain/list';
        $query = [
            'tbl' => $name
        ];
        $header = [
            Request::HEADER_AUTHORIZATION => $this->buildMangeSign($method, $host, $path, $query)
        ];
        return $this->request($method, $host, $path, $header, $query);
    }

    /**
     * 设置镜像源
     * @access  public
     * @param   string  $name           存储空间名称
     * @param   string  $accessUrl      镜像源的访问域名
     * @param   string  $host           回源时使用的Host头部值
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setImageSource(string $name, string $accessUrl, string $host = null): array
    {
        $method = Request::METHOD_POST;
        $host = $this->getRegion()['bucket_manage'];
        $path = "/image/{$name}/from/{$this->urlBase64($accessUrl)}/host/{$this->urlBase64($host)}";
        $header = [
            Request::HEADER_AUTHORIZATION => $this->buildMangeSign($method, $host, $path)
        ];
        return $this->request($method, $host, $path, $header);
    }

    /**
     * 设置访问权限
     * @access  public
     * @param   string  $name       存储空间名称
     * @param   bool    $private    是否是私有
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setAccessAuth(string $name, bool $private): array
    {
        $method = Request::METHOD_POST;
        $host = $this->getRegion()['bucket_manage'];
        $path = '/private';
        $query = [
            'bucket' => $name,
            'private' => $private ? 1 : 0
        ];
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED
        ];
        $header[Request::HEADER_AUTHORIZATION] = $this->buildMangeSign($method, $host, $path, $query, $header);
        return $this->request($method, $host, $path, $header, $query);
    }

    /**
     * 设置空间标签
     * @access  public
     * @param   string  $name       存储空间名称
     * @param   array   $tagList    标签列表['key1'=>'value1','key2'=>'value2']
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setTag(string $name, array $tagList): array
    {
        $method = Request::METHOD_PUT;
        $host = $this->getRegion()['bucket_manage'];
        $path = '/bucketTagging';
        $query = [
            'bucket' => $name
        ];
        $tagData = [];
        foreach($tagList as $k => $v) $tagData[] = ['Key' => $k, 'Value' => $v];
        $body = json_encode(['Tags' => $tagData], JSON_UNESCAPED_UNICODE);
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_JSON
        ];
        $header[Request::HEADER_AUTHORIZATION] = $this->buildMangeSign($method, $host, $path, $query, $header, $body);
        return $this->request($method, $host, $path, $header, $query, $body);
    }

    /**
     * 获取空间标签
     * @access  public
     * @param   string  $name       存储空间名称
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getTag(string $name): array
    {
        $method = Request::METHOD_GET;
        $host = $this->getRegion()['bucket_manage'];
        $path = '/bucketTagging';
        $query = [
            'bucket' => $name
        ];
        $header = [
            Request::HEADER_AUTHORIZATION => $this->buildMangeSign($method, $host, $path, $query)
        ];
        return $this->request($method, $host, $path, $header, $query);
    }

    /**
     * 删除空间标签
     * @access  public
     * @param   string  $name       存储空间名称
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deleteTag(string $name): array
    {
        $method = Request::METHOD_DELETE;
        $host = $this->getRegion()['bucket_manage'];
        $path = '/bucketTagging';
        $query = [
            'bucket' => $name
        ];
        $header = [
            Request::HEADER_AUTHORIZATION => $this->buildMangeSign($method, $host, $path, $query)
        ];
        return $this->request($method, $host, $path, $header, $query);
    }
}
