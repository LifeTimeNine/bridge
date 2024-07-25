<?php

declare(strict_types = 1);

namespace lifetime\bridge\qiniu\kodo;

use lifetime\bridge\exception\InvalidArgumentException;
use lifetime\bridge\exception\InvalidConfigException;
use lifetime\bridge\exception\InvalidDecodeException;
use lifetime\bridge\exception\InvalidResponseException;
use lifetime\bridge\Tools;

/**
 * 七牛云对象存储Service相关操作
 * @throws InvalidConfigException
 */
class Service extends Basic
{

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
    public function bucketList(array $tags = []): array
    {
        $method = self::REQUEST_METHOD_GET;
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
            self::S_CONTENT_TYPE => self::CONTENT_TYPE_URLENCODE
        ];
        $header[self::S_AUTHORIZATION] = $this->buildMangeSign($method, $host, $path, $query, $header);
        return $this->request($method, $host, $path, $header, $query);
    }
}
