<?php

declare(strict_types = 1);

namespace lifetime\bridge\qiniu\kodo;

use lifetime\bridge\exception\InvalidArgumentException;
use lifetime\bridge\exception\InvalidConfigException;
use lifetime\bridge\exception\InvalidDecodeException;
use lifetime\bridge\exception\InvalidResponseException;
use lifetime\bridge\Request;
use lifetime\bridge\Tools;

/**
 * 七牛云对象存储Object接口
 * @throws InvalidConfigException
 */
class Objects extends Basic
{
    /**
     * Bucket名称
     * @var string
     */
    protected $bucketName;

    /**
     * 设置Bucket名称
     * @access  public
     * @return  self
     */
    public function setBucketName(string $name): self
    {
        $this->bucketName = $name;
        return $this;
    }

    /**
     * 获取Bucket名称
     * @access  public
     * @return  string
     * @throws  InvalidArgumentException
     */
    protected function getBucketName(): string
    {
        $name = $this->bucketName;
        if (empty($name)) $name = $this->config->bucketName();
        if (empty($name)) throw new InvalidArgumentException("Missing Options [bucketName]");
        return $name;
    }

    /**
     * 直传文件
     * @access  public
     * @param   string  $filename       文件名
     * @param   string  $data           数据
     * @param   int     $storageType    存储类型(0-标准存储,1-低频存储,2-归档存储,3-深度归档存储,4-归档直读存储)
     * @param   array   $customList     自定义变量列表['key1' => 'value1', 'key2' => 'value2']
     * @param   array   $metaList       自定义元数据['key1' => 'value1', 'key2' => 'value2']
     * @param   int     $expire         有效时间
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function upload(string $filename, string $data, int $storageType = 0, array $customList = [], array $metaList = [], int $expire = 3600): array
    {
        $method = Request::METHOD_POST;
        $host = $this->getRegion()['upload'];
        $returnBody = [
            'name' => "$(fname)",
            'size' => "$(fsize)",
            'hash' => '$(etag)',
        ];
        $formData = [
            'key' => $filename,
            'fileName' => $filename,
            'crc32' => crc32($data)
        ];
        foreach($customList as $k => $v) {
            $returnBody[$k] = "$(x:{$v})";
            $formData["x:{$k}"] = $v;
        }
        foreach($metaList as $k => $v) $formData["x-qn-meta-{$k}"] = $v;
        $formData['token'] = $this->buildUploadSign([
            'scope' => "{$this->getBucketName()}:{$filename}",
            'deadline' => time() + $expire,
            'returnBody' => json_encode($returnBody, JSON_UNESCAPED_UNICODE),
            'fileType' => $storageType,
        ]);
        list($contentType, $body) = Tools::buildFormData($formData, $filename, $data);
        $header = [
            Request::HEADER_CONTENT_TYPE => $contentType
        ];
        return $this->request($method, $host, '', $header, [], $body);
    }

    /**
     * 客户端直传文件
     * @access  public
     * @param   string  $filename       文件名
     * @param   string  $data           数据
     * @param   int     $storageType    存储类型(0-标准存储,1-低频存储,2-归档存储,3-深度归档存储,4-归档直读存储)
     * @param   array   $customList     自定义变量列表['key1' => 'value1', 'key2' => 'value2']
     * @param   array   $metaList       自定义元数据['key1' => 'value1', 'key2' => 'value2']
     * @param   int     $expire         有效时间
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     */
    public function clientUpload(string $filename, string $data, int $storageType = 0, array $customList = [], array $metaList = [], int $expire = 3600): array
    {
        $returnBody = [
            'name' => "$(fname)",
            'size' => "$(fsize)",
            'hash' => '$(etag)',
        ];
        $body = [
            'key' => $filename,
            'fileName' => $filename,
            'crc32' => crc32($data)
        ];
        foreach($customList as $k => $v) {
            $returnBody[$k] = "$(x:{$v})";
            $body["x:{$k}"] = $v;
        }
        foreach($metaList as $k => $v) $formData["x-qn-meta-{$k}"] = $v;
        $body['token'] = $this->buildUploadSign([
            'scope' => "{$this->getBucketName()}:{$filename}",
            'deadline' => time() + $expire,
            'returnBody' => json_encode($returnBody, JSON_UNESCAPED_UNICODE),
            'fileType' => $storageType,
        ]);

        return [
            'method' => Request::METHOD_POST,
            'url' => ($this->config->isSsl() ? 'https' : 'http') . "://{$this->getRegion()['upload']}",
            'content_type' => Request::CONTENT_TYPE_FORMDATA,
            'header' => [],
            'query' => [],
            'body' => Tools::arrToKeyVal($body),
            'file_key' => 'file',
            'file_path' => ($this->config->isSsl() ? 'https' : 'http') . "://{$this->config->accessDomain()}/{$filename}"
        ];
    }

    /**
     * 初始化分片上传
     * @access  public
     * @param   string  $filename       文件名
     * @param   int     $storageType    存储类型(0-标准存储,1-低频存储,2-归档存储,3-深度归档存储,4-归档直读存储)
     * @param   int     $expire         有效时间
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function initPart(string $filename, int $storageType = 0, int $expire = 3600): array
    {
        $method = Request::METHOD_POST;
        $host = $this->getRegion()['upload'];
        $path = "/buckets/{$this->getBucketName()}/objects/{$this->urlBase64($filename)}/uploads";
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED,
            Request::HEADER_AUTHORIZATION => 'UpToken ' . $this->buildUploadSign([
                'scope' => "{$this->getBucketName()}:{$filename}",
                'deadline' => time() + $expire,
                'fileType' => $storageType,
            ])
        ];
        return $this->request($method, $host, $path, $header);
    }

    /**
     * 分片上传数据
     * @access  public
     * @param   string  $filename       文件名
     * @param   string  $uploadId       任务Id
     * @param   int     $partNumber     上传标记(0-1000,大小1MB-1GB)
     * @param   string  $data           上传的数据
     * @param   int     $expire         有效时间
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function uploadPart(string $filename, string $uploadId, int $partNumber, string $data, int $expire = 3600): array
    {
        $method = Request::METHOD_PUT;
        $host = $this->getRegion()['upload'];
        $path = "/buckets/{$this->getBucketName()}/objects/{$this->urlBase64($filename)}/uploads/{$uploadId}/{$partNumber}";
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_STREAM,
            Request::HEADER_AUTHORIZATION => 'UpToken ' . $this->buildUploadSign([
                'scope' => "{$this->getBucketName()}:{$filename}",
                'deadline' => time() + $expire,
            ])
        ];
        return $this->request($method, $host, $path, $header, [], $data);
    }

    /**
     * 客户端分片上传数据
     * @access  public
     * @param   string  $filename       文件名
     * @param   string  $uploadId       任务Id
     * @param   int     $partNumber     上传标记(0-1000,大小1MB-1GB)
     * @param   int     $expire         有效时间
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     */
    public function clientUploadPart(string $filename, string $uploadId, int $partNumber, int $expire = 3600): array
    {
        $host = $this->getRegion()['upload'];
        $path = "/buckets/{$this->getBucketName()}/objects/{$this->urlBase64($filename)}/uploads/{$uploadId}/{$partNumber}";
        $header = [
            Request::HEADER_AUTHORIZATION => 'UpToken ' . $this->buildUploadSign([
                'scope' => "{$this->getBucketName()}:{$filename}",
                'deadline' => time() + $expire,
            ])
        ];
        return [
            'method' => Request::METHOD_PUT,
            'url' => ($this->config->isSsl() ? 'https' : 'http') . "://{$host}{$path}",
            'content_type' => Request::CONTENT_TYPE_STREAM,
            'header' => Tools::arrToKeyVal($header),
            'query' => [],
            'part_number' => $partNumber,
            'file_path' => ($this->config->isSsl() ? 'https' : 'http') . "://{$this->config->accessDomain()}/{$filename}"
        ];
    }

    /**
     * 完成分片上传
     * @access  public
     * @param   string  $filename       文件名
     * @param   string  $uploadId       任务Id
     * @param   array   $data           参数['partNumber1' => 'etag1', 'partNumber2' => 'etag2']
     * @param   int     $expire         有效时间
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function completePart(string $filename, string $uploadId, array $data, int $expire = 3600): array
    {
        $method = Request::METHOD_POST;
        $host = $this->getRegion()['upload'];
        $path = "/buckets/{$this->getBucketName()}/objects/{$this->urlBase64($filename)}/uploads/{$uploadId}";
        $parts = [];
        foreach($data as $k => $v) $parts[] = ['partNumber' => $k, 'etag' => $v];
        $body = json_encode([
            'parts' => $parts,
            'fname' => $filename
        ], JSON_UNESCAPED_UNICODE);
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_JSON,
            Request::HEADER_AUTHORIZATION => 'UpToken ' . $this->buildUploadSign([
                'scope' => "{$this->getBucketName()}:{$filename}",
                'deadline' => time() + $expire,
            ])
        ];
        return $this->request($method, $host, $path, $header, [], $body);
    }

    /**
     * 终止分片上传任务
     * @access  public
     * @param   string  $filename       文件名
     * @param   string  $uploadId       上传任务ID
     * @param   int     $expire         有效时间
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function stopPart(string $filename, string $uploadId, int $expire = 3600): array
    {
        $method = Request::METHOD_DELETE;
        $host = $this->getRegion()['upload'];
        $path = "/buckets/{$this->getBucketName()}/objects/{$this->urlBase64($filename)}/uploads/{$uploadId}";
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED,
            Request::HEADER_AUTHORIZATION => 'UpToken ' . $this->buildUploadSign([
                'scope' => "{$this->getBucketName()}:{$filename}",
                'deadline' => time() + $expire,
            ])
        ];
        return $this->request($method, $host, $path, $header);
    }

    /**
     * 列举已经上传的分片
     * @access  public
     * @param   string  $filename           文件名
     * @param   string  $uploadId           任务Id
     * @param   int     $partNumberMarker   指定列举的起始位置
     * @param   int     $maxParts           响应中的最大 Part 数目
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function partList(string $filename, string $uploadId, int $partNumberMarker = null, int $maxParts = 1000): array
    {
        $method = Request::METHOD_GET;
        $host = $this->getRegion()['upload'];
        $path = "/buckets/{$this->getBucketName()}/objects/{$this->urlBase64($filename)}/uploads/{$uploadId}";
        $query = ['max-parts' => $maxParts];
        if (!empty($partNumberMarker)) $query['part-number-marker'] = $partNumberMarker;
        $header = [
            Request::HEADER_AUTHORIZATION => 'UpToken ' . $this->buildUploadSign([
                'scope' => "{$this->getBucketName()}:{$filename}",
                'deadline' => time() + 10,
            ])
        ];
        return $this->request($method, $host, $path, $header, $query);
    }

    /**
     * 资源列举
     * @access  public
     * @param   string  $marker     上一次列举返回的位置标记,作为本次列举的起点信息
     * @param   int     $limit      本次列举的条目数,范围为1-1000
     * @param   string  $prefix     指定前缀,只有资源名匹配该前缀的资源会被列出。
     * @param   string  $delimiter  指定目录分隔符,列出所有公共前缀(模拟列出目录效果)
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function list(string $marker = null, int $limit = 1000, string $prefix = null, string $delimiter = null): array
    {
        $method = Request::METHOD_GET;
        $host = $this->getRegion()['object_enum'];
        $path = '/list';
        $query = [
            'bucket' => $this->getBucketName()
        ];
        if (!empty($marker)) $query['marker'] = $marker;
        if (!empty($limit)) $query['limit'] = $limit;
        if (!empty($prefix)) $query['prefix'] = $this->urlBase64($prefix);
        if (!empty($delimiter)) $query['delimiter'] = $this->urlBase64($delimiter);
        $header = [
            Request::HEADER_AUTHORIZATION => $this->buildMangeSign($method, $host, $path, $query)
        ];
        return $this->request($method, $host, $path, $header, $query);
    }

    /**
     * 查询资源元信息
     * @access  public
     * @param   string  $filename   文件名称
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getMetaData(string $filename): array
    {
        $method = Request::METHOD_GET;
        $host = $this->getRegion()['object_manage'];
        $path = "/stat/{$this->urlBase64("{$this->getBucketName()}:{$filename}")}";
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED
        ];
        $header[Request::HEADER_AUTHORIZATION] = $this->buildMangeSign($method, $host, $path, [], $header);
        return $this->request($method, $host, $path, $header);
    }

    /**
     * 修改资源元信息
     * @access  public
     * @param   string  $filename   文件名称
     * @param   string  $mimeType   新的 mimeType
     * @param   array   $metaList   新的Meta数据['key1' => 'value1', 'key2' => 'value2']
     * @param   array   $cond       自定义条件信息['hash' => 'xxxx', 'mime' => 'text/plain']
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setMetaData(string $filename, string $mimeType = null, array $metaList = [], array $cond = []): array
    {
        $method = Request::METHOD_POST;
        $host = $this->getRegion()['object_manage'];
        $path = "/chgm/{$this->urlBase64("{$this->getBucketName()}:{$filename}")}";
        if (!empty($mimeType)) {
            $path .= "/mime/{$this->urlBase64($mimeType)}";
        }
        foreach($metaList as $key => $value) {
            $path .= "/x-qn-meta-{$key}/{$this->urlBase64($value)}";
        }
        if (count($cond) > 0) {
            $path .= "/cond/{$this->urlBase64(Tools::arrToUrl($cond))}";
        }
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED
        ];
        $header[Request::HEADER_AUTHORIZATION] = $this->buildMangeSign($method, $host, $path, [], $header);
        return $this->request($method, $host, $path, $header, [], null, true);
    }

    /**
     * 移动资源
     * @access  public
     * @param   string  $sourceFilename 源文件名
     * @param   string  $targetFilename 目标文件名
     * @param   string  $targetBucket   目标空间(留空表示与源文件同一空间)
     * @param   bool    $forceCover     强制覆盖目标资源
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function move(string $sourceFilename, string $targetFilename, string $targetBucket = null, bool $forceCover = false): array
    {
        $method = Request::METHOD_POST;
        $host = $this->getRegion()['object_manage'];
        if (empty($targetBucket)) $targetBucket = $this->getBucketName();
        $forceCoverText = $forceCover ? 'true' : 'false';
        $path = "/move/{$this->urlBase64("{$this->getBucketName()}:{$sourceFilename}")}/{$this->urlBase64("{$targetBucket}:{$targetFilename}")}/force/{$forceCoverText}";
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED
        ];
        $header[Request::HEADER_AUTHORIZATION] = $this->buildMangeSign($method, $host, $path, [], $header);
        return $this->request($method, $host, $path, $header, [], null, true);
    }

    /**
     * 复制资源
     * @access  public
     * @param   string  $sourceFilename 源文件名
     * @param   string  $targetFilename 目标文件名
     * @param   string  $targetBucket   目标空间(留空表示与源文件同一空间)
     * @param   string  $forceCover     强制覆盖目标资源
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function copy(string $sourceFilename, string $targetFilename, string $targetBucket = null, bool $forceCover = false): array
    {
        $method = Request::METHOD_POST;
        $host = $this->getRegion()['object_manage'];
        if (empty($targetBucket)) $targetBucket = $this->getBucketName();
        $forceCoverText = $forceCover ? 'true' : 'false';
        $path = "/copy/{$this->urlBase64("{$this->getBucketName()}:{$sourceFilename}")}/{$this->urlBase64("{$targetBucket}:{$targetFilename}")}/force/{$forceCoverText}";
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED
        ];
        $header[Request::HEADER_AUTHORIZATION] = $this->buildMangeSign($method, $host, $path, [], $header);
        return $this->request($method, $host, $path, $header, [], null, true);
    }

    /**
     * 删除资源
     * @access  public
     * @param   string  $filename   文件名称
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function delete(string $filename): array
    {
        $method = Request::METHOD_POST;
        $host = $this->getRegion()['object_manage'];
        $path = "/delete/{$this->urlBase64("{$this->getBucketName()}:{$filename}")}";
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED
        ];
        $header[Request::HEADER_AUTHORIZATION] = $this->buildMangeSign($method, $host, $path, [], $header);
        return $this->request($method, $host, $path, $header, [], null, true);
    }

    /**
     * 修改文件状态
     * @access  public
     * @param   string  $filename   文件名称
     * @param   bool    $disable    禁用
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setStatus(string $filename, bool $disable): array
    {
        $method = Request::METHOD_POST;
        $host = $this->getRegion()['object_manage'];
        $status = $disable ? 1 : 0;
        $path = "/chstatus/{$this->urlBase64("{$this->getBucketName()}:{$filename}")}/status/{$status}";
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED
        ];
        $header[Request::HEADER_AUTHORIZATION] = $this->buildMangeSign($method, $host, $path, [], $header);
        return $this->request($method, $host, $path, $header, [], null, true);
    }

    /**
     * 修改文件存储类型
     * @access  public
     * @param   string  $filename       文件名称
     * @param   int     $storageType    存储类型(0-标准存储,1-低频存储,2-归档存储,3-深度归档存储,4-归档直读存储)
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setStorageType(string $filename, int $storageType): array
    {
        $method = Request::METHOD_POST;
        $host = $this->getRegion()['object_manage'];
        $path = "/chtype/{$this->urlBase64("{$this->getBucketName()}:{$filename}")}/type/{$storageType}";
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED
        ];
        $header[Request::HEADER_AUTHORIZATION] = $this->buildMangeSign($method, $host, $path, [], $header);
        return $this->request($method, $host, $path, $header, [], null, true);
    }

    /**
     * 解冻归档/深度归档存储文件
     * @access  public
     * @param   string  $filename   文件名称
     * @param   int     $duration   解冻时长(1-7天)
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function thaw(string $filename, int $duration): array
    {
        $method = Request::METHOD_POST;
        $host = $this->getRegion()['object_manage'];
        $path = "/restoreAr/{$this->urlBase64("{$this->getBucketName()}:{$filename}")}/freezeAfterDays/{$duration}";
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED
        ];
        $header[Request::HEADER_AUTHORIZATION] = $this->buildMangeSign($method, $host, $path, [], $header);
        return $this->request($method, $host, $path, $header, [], null, true);
    }

    /**
     * 修改文件过期删除时间
     * @access  public
     * @param   string  $filename   文件名称
     * @param   int     $duration   时长（天，设置为 0 表示取消过期删除设置）
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setExpireDeleteDuration(string $filename, int $duration): array
    {
        $method = Request::METHOD_POST;
        $host = $this->getRegion()['object_manage'];
        $path = "/deleteAfterDays/{$this->urlBase64("{$this->getBucketName()}:{$filename}")}/{$duration}";
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED
        ];
        $header[Request::HEADER_AUTHORIZATION] = $this->buildMangeSign($method, $host, $path, [], $header);
        return $this->request($method, $host, $path, $header, [], null, true);
    }

    /**
     * 修改文件生命周期
     * @access  public
     * @param   string  $filename               文件名称
     * @param   int     $toIAAfterDays          上传后N天转换为低频存储类型, 设置-1表示取消设置
     * @param   int     $toArchiveIRAfterDays   上传后N天转换为归档直读存储类型, 设置-1表示取消设置
     * @param   int     $toArchiveAfterDays     上传后N天转换为归档存储类型, 设置-1表示取消设置
     * @param   int     $toDeepArchiveAfterDays 上传后N天转换为深度归档存储类型, 设置-1表示取消设置
     * @param   int     $deleteAfterDays        上传后N天删除, 设置-1表示取消设置
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setLifecycle(string $filename, int $toIAAfterDays = null, int $toArchiveIRAfterDays = null, int $toArchiveAfterDays = null, int $toDeepArchiveAfterDays = null, int $deleteAfterDays = null): array
    {
        $method = Request::METHOD_POST;
        $host = $this->getRegion()['object_manage'];
        $path = "/lifecycle/{$this->urlBase64("{$this->getBucketName()}:{$filename}")}";
        if (!empty($toIAAfterDays)) {
            $path .= "/toIAAfterDays/{$toIAAfterDays}";
        }
        if (!empty($toArchiveIRAfterDays)) {
            $path .= "/toArchiveIRAfterDays/{$toArchiveIRAfterDays}";
        }
        if (!empty($toArchiveAfterDays)) {
            $path .= "/toArchiveIRAfterDays/{$toArchiveAfterDays}";
        }
        if (!empty($toDeepArchiveAfterDays)) {
            $path .= "/toDeepArchiveAfterDays/{$toDeepArchiveAfterDays}";
        }
        if (!empty($deleteAfterDays)) {
            $path .= "/deleteAfterDays/{$deleteAfterDays}";
        }
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED
        ];
        $header[Request::HEADER_AUTHORIZATION] = $this->buildMangeSign($method, $host, $path, [], $header);
        return $this->request($method, $host, $path, $header, [], null, true);
    }

    /**
     * 镜像资源更新
     * @access  public
     * @param   string  $filename   文件名称
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function imageSourceUpdate(string $filename): array
    {
        $method = Request::METHOD_POST;
        $host = $this->getRegion()['download'];
        $path = "/prefetch/{$this->urlBase64("{$this->getBucketName()}:{$filename}")}";
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED
        ];
        $header[Request::HEADER_AUTHORIZATION] = $this->buildMangeSign($method, $host, $path, [], $header);
        return $this->request($method, $host, $path, $header, [], null, true);
    }

    /**
     * 发起异步抓取任务
     * @docs    https://developer.qiniu.com/kodo/4097/asynch-fetch
     * @access  public
     * @param   string  $url        需要抓取的 url,支持设置多个用于高可用,以';'分隔,
     * @param   array   $opions    参数(除url,bucket以外的参数)
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function createAsyncFetchTask(string $url, array $opions = []): array
    {
        $method = Request::METHOD_POST;
        $host = $this->getRegion()['query'];
        $path = '/sisyphus/fetch';
        $body = json_encode(array_merge([
            'url' => $url,
            'bucket' => $this->getBucketName()
        ], $opions), JSON_UNESCAPED_UNICODE);
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_JSON
        ];
        $header[Request::HEADER_AUTHORIZATION] = $this->buildMangeSign($method, $host, $path, [], $header, $body);
        return $this->request($method, $host, $path, $header, [], $body);
    }

    /**
     * 查询异步抓取任务
     * @access  public
     * @param   string  $taskId     任务ID
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function queryAsyncFetchTask(string $taskId): array
    {
        $method = Request::METHOD_GET;
        $host = $this->getRegion()['query'];
        $path = '/sisyphus/fetch';
        $query = ['id' => $taskId];
        $header = [
            Request::HEADER_AUTHORIZATION => $this->buildMangeSign($method, $host, $path, $query)
        ];
        return $this->request($method, $host, $path, $header, $query);
    }

    /**
     * 批量操作
     * @description 支持 查询元信息、修改元信息、移动、复制、删除、修改状态、修改存储类型、修改生命周期 和 解冻 操作，所有操作名称和参数名称参考具体的方法
     * @warning 任意一个操作失败，将抛出异常，异常的消息是操作的结果
     * @access  public
     * @param   array   $operationList  操作列表([['move', ['a.txt', 'b.txt', null, true]], ['delete', ['a.txt']]])
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidConfigException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function batch(array $operationList): array
    {
        $data = [];
        $bucket = $this->getBucketName();
        foreach($operationList as $index => $item) {
            if (!isset($item[0])) {
                throw new InvalidArgumentException("Missing parameter: Operation at index {$index}", 1, $item);
            }
            $arg = $item[1] ?? [];
            switch($item[0]) {
                case 'getMetaData':
                    if (!isset($arg[0])) throw new InvalidArgumentException("Operation getMetaData missing parameter: filename at index {$index}", 1, $arg);
                    $data[] = "op=/stat/{$this->urlBase64("{$bucket}:{$arg[0]}")}";
                    break;
                case 'setMetaData':
                    if (!isset($arg[0])) throw new InvalidArgumentException("Operation setMetaData missing parameter: filename at index {$index}", 1, $arg);
                    $op = "op=/chgm/{$this->urlBase64("{$bucket}:{$arg[0]}")}";
                    if (!empty($arg[1])) $op .= "/mime/{$arg[1]}";
                    if (!empty($arg[2]) && is_array($arg[2])) {
                        foreach($arg[2] as $k => $v) {
                            $op .= "/x-qn-meta-{$k}/{$this->urlBase64($v)}";
                        }
                    }
                    if (!empty($arg[3]) && is_array($arg[3])) {
                        $op .= "/cond/{$this->urlBase64(Tools::arrToUrl($arg[3]))}";
                    }
                    $data[] = $op;
                    break;
                case 'move':
                    if (!isset($arg[0])) throw new InvalidArgumentException("Operation move missing parameter: sourceFilename at index {$index}", 1, $arg);
                    if (!isset($arg[1])) throw new InvalidArgumentException("Operation move missing parameter: sourceFilename at index {$index}", 1, $arg);
                    $targetBucket = $arg[2] ?? null ?: $bucket;
                    $forceCover = empty($arg[3]) ? 'false' : 'true';
                    $data[] = "op=/move/{$this->urlBase64("{$bucket}:{$arg[0]}")}/{$this->urlBase64("{$targetBucket}:{$arg[1]}")}/force/{$forceCover}";
                    break;
                case 'copy':
                    if (!isset($arg[0])) throw new InvalidArgumentException("Operation copy missing parameter: sourceFilename at index {$index}", 1, $arg);
                    if (!isset($arg[1])) throw new InvalidArgumentException("Operation copy missing parameter: sourceFilename at index {$index}", 1, $arg);
                    $targetBucket = $arg[2] ?? null ?: $bucket;
                    $forceCover = empty($arg[3]) ? 'false' : 'true';
                    $data[] = "op=/copy/{$this->urlBase64("{$bucket}:{$arg[0]}")}/{$this->urlBase64("{$targetBucket}:{$arg[1]}")}/force/{$forceCover}";
                    break;
                case 'delete':
                    if (!isset($arg[0])) throw new InvalidArgumentException("Operation delete missing parameter: filename at index {$index}", 1, $arg);
                    $data[] = "op=/delete/{$this->urlBase64("{$bucket}:{$arg[0]}")}";
                    break;
                case 'setStatus':
                    if (!isset($arg[0])) throw new InvalidArgumentException("Operation setStatus missing parameter: filename at index {$index}", 1, $arg);
                    if (!isset($arg[1])) throw new InvalidArgumentException("Operation setStatus missing parameter: disable at index {$index}", 1, $arg);
                    $status = $arg[1] ? 1 : 0;
                    $data[] = "op=/chstatus/{$this->urlBase64("{$bucket}:{$arg[0]}")}/status/{$status}";
                    break;
                case 'setStorageType':
                    if (!isset($arg[0])) throw new InvalidArgumentException("Operation setStorageType missing parameter: filename at index {$index}", 1, $arg);
                    if (!isset($arg[1])) throw new InvalidArgumentException("Operation setStorageType missing parameter: storageType at index {$index}", 1, $arg);
                    $data[] = "op=/chtype/{$this->urlBase64("{$bucket}:{$arg[0]}")}/type/{$arg[1]}";
                    break;
                case 'thaw':
                    if (!isset($arg[0])) throw new InvalidArgumentException("Operation thaw missing parameter: filename at index {$index}", 1, $arg);
                    if (!isset($arg[1])) throw new InvalidArgumentException("Operation thaw missing parameter: duration at index {$index}", 1, $arg);
                    $data[] = "op=/restoreAr/{$this->urlBase64("{$bucket}:{$arg[0]}")}/freezeAfterDays/{$arg[1]}";
                    break;
                case 'setExpireDeleteDuration':
                    if (!isset($arg[0])) throw new InvalidArgumentException("Operation setExpireDeleteDuration missing parameter: filename at index {$index}", 1, $arg);
                    if (!isset($arg[1])) throw new InvalidArgumentException("Operation setExpireDeleteDuration missing parameter: duration at index {$index}", 1, $arg);
                    $data[] = "op=/deleteAfterDays/{$this->urlBase64("{$bucket}:{$arg[0]}")}/{$arg[1]}";
                    break;
                case 'setLifecycle':
                    if (!isset($arg[0])) throw new InvalidArgumentException("Operation setLifecycle missing parameter: filename at index {$index}", 1, $arg);
                    $op = "op=/lifecycle/{$this->urlBase64("{$bucket}:{$arg[0]}")}";
                    if (!empty($arg[1])) $op .= "/toIAAfterDays/{$arg[1]}";
                    if (!empty($arg[2])) $op .= "/toArchiveIRAfterDays/{$arg[2]}";
                    if (!empty($arg[3])) $op .= "/toArchiveIRAfterDays/{$arg[3]}";
                    if (!empty($arg[4])) $op .= "/toDeepArchiveAfterDays/{$arg[4]}";
                    if (!empty($arg[5])) $op .= "/deleteAfterDays/{$arg[5]}";
                    $data[] = $op;
                    break;
                default:
                    throw new InvalidArgumentException("Unknown operation: {$item[0]} at index {$index}", 1, $item);
            }
        }
        $method = Request::METHOD_POST;
        $host = $this->getRegion()['object_manage'];
        $path = '/batch';
        $body = implode('&', $data);
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED
        ];
        $header[Request::HEADER_AUTHORIZATION] = $this->buildMangeSign($method, $host, $path, [], $header, $body);
        return $this->request($method, $host, $path, $header, [], $body);
    }
}
