<?php

declare(strict_types = 1);

namespace lifetime\bridge\ali\OSS;

use DateTime;
use lifetime\bridge\Exception\AliOssResponseException;
use lifetime\bridge\Exception\InvalidArgumentException;
use lifetime\bridge\Exception\InvalidConfigException;
use lifetime\bridge\Exception\InvalidDecodeException;
use lifetime\bridge\Exception\InvalidResponseException;
use lifetime\bridge\Request;
use lifetime\bridge\Tools;

/**
 * 阿里云对象存储 Object 操作
 * @throws InvalidConfigException
 */
class Objects extends Basic
{
    /**
     * 存储空间名称
     * @var string
     */
    protected $bucketName;

    /**
     * 设置存储空间名称
     * @access  public
     * @param   string  $name       空间名称
     * @return  self
     */
    public function setBucketName(string $name): self
    {
        $this->bucketName = $name;
        return $this;
    }

    /**
     * 获取存储空间名称
     * @access  protected
     * @return  string
     * @throws  InvalidArgumentException
     */
    protected function getBucketName(): string
    {
        if (!empty($this->bucketName)) {
            return $this->bucketName;
        } elseif (!empty($this->config->bucketName())) {
            return $this->config->bucketName();
        } else {
            throw new InvalidArgumentException('Miss options [bucket_name]');
        }
    }

    /**
     * 获取访问地址
     * @access  public
     * @param   string  $filename   文件名称
     * @return  string
     * @throws  InvalidArgumentException
     */
    public function getAccessPath(string $filename): string
    {
        $accessDomain = $this->config->accessDomain();
        if (empty($accessDomain)) {
            return "https://{$this->getBucketName()}.{$this->getRegion()['extranet_endpoint']}/{$filename}";
        } else {
            return ($this->config->isHttps() ? 'https' : 'http') . "://{$accessDomain}/{$filename}";
        }
    }

    /**
     * 获取所有Object信息
     * @access  public
     * @param   string  $delimiter          对Object名字进行分组的字符,所有Object名字包含指定的前缀
     * @param   string  $startAfter         设定从startAfter之后按字母排序开始返回Object
     * @param   string  $continuationToken  指定List操作需要从此token开始
     * @param   string  $maxKeys            指定返回Object的最大数
     * @param   string  $prefix             限定返回文件的Key必须以prefix作为前缀
     * @param   string  $encodingType       对返回的内容进行编码并指定编码的类型
     * @param   bool    $fetchOwner         指定是否在返回结果中包含owner信息
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function list(string $delimiter = null, string $startAfter = null, string $continuationToken = null, string $maxKeys = null, string $prefix = null, string $encodingType = null, bool $fetchOwner = false): array
    {
        $query = ['list-type' => 2];
        if (!empty($delimiter)) $query['delimiter'] = $delimiter;
        if (!empty($startAfter)) $query['start-after'] = $startAfter;
        if (!empty($continuationToken)) $query['continuation-token'] = $continuationToken;
        if (!empty($maxKeys)) $query['max-keys'] = $maxKeys;
        if (!empty($prefix)) $query['prefix'] = $prefix;
        if (!empty($encodingType)) $query['encoding-type'] = $encodingType;
        if (!empty($fetchOwner)) $query['fetch-owner'] = $fetchOwner;
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $this->getBucketName(),
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            $query
        );
    }

    /**
     * 上传文件
     * @access  public
     * @param   string      $filename               文件名称
     * @param   string      $data                   文件数据
     * @param   string      $acl                    访问权限(default:遵循所在存储空间的访问权限,private:私有资源,public-read:公共读资源,public-read-write:公共读写资源)
     * @param   string      $storageType            存储类型(Standard:标准存储,IA:低频访问,Archive:归档存储,ColdArchive:冷归档存储,DeepColdArchive:深度冷归档存储)
     * @param   string      $cacheControl           指定该Object被下载时网页的缓存行为(no-cache:不可直接使用缓存,no-store:所有内容都不会被缓存,public:所有内容都将被缓存,private:所有内容只在客户端缓存,max-age=<seconds>:缓存内容的相对过期时间，单位为秒)
     * @param   string      $disposition            展示形式(inline:直接预览文件内容,attachment:下载到浏览器指定路径,attachment;{urlencode(filename)}.{ext}:指定文件名下载到浏览器)
     * @param   string      $encoding               编码方式(identity:未经过压缩或编码,gzip:采用Lempel-Ziv（LZ77）压缩算法以及32位CRC校验,compress:采用Lempel-Ziv-Welch（LZW）压缩算法,deflate:采用zlib结构和deflate压缩算法,br:采用Brotli算法)
     * @param   string      $md5                    用于检查消息内容是否与发送时一致
     * @param   DateTime    $expires                缓存内容的绝对过期时间
     * @param   bool        $overwrite              是否覆盖同名Object(默认覆盖)
     * @param   string      $encryption             指定服务器端加密方式(AES256,KMS,SM4)
     * @param   string      $dataEncryption         指定Object的加密算法,如果未指定此选项，表明Object使用AES256加密算法(AES256,KMS,SM4)
     * @param   string      $encryptionKey          KMS托管的用户主密钥
     * @param   array       $metaList               元数据列表[key1=>value1,key2=>value2]
     * @param   array       $tagList                标签列表[key1=>value1,key2=>value2]
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function put(
        string $filename,
        string $data,
        string $acl = null,
        string $storageType = null,
        string $cacheControl = null,
        string $disposition = null,
        string $encoding = null,
        string $md5 = null,
        ?\DateTime $expires = null,
        ?bool $overwrite = null,
        string $encryption = null,
        string $dataEncryption = null,
        string $encryptionKey = null,
        array $metaList = [],
        array $tagList = []
    ): array
    {
        $header = [Request::HEADER_CONTENT_TYPE => Tools::getMimetype($filename)];
        if (!empty($acl)) $header['x-oss-object-acl'] = $acl;
        if (!empty($storageType)) $header['x-oss-storage-class'] = $storageType;
        if (!empty($cacheControl)) $header['Cache-Control'] = $cacheControl;
        if (!empty($disposition)) $header['Content-Disposition'] = $disposition;
        if (!empty($encoding)) $header['Content-Encoding'] = $encoding;
        if (!empty($md5)) $header['Content-MD5'] = $md5;
        if (!is_null($expires)) $header['Expires'] = $expires->setTimezone(new \DateTimeZone('GMT'))->format('Y-m-d\TH:i:s\Z');
        if (!is_null($overwrite)) $header['x-oss-forbid-overwrite'] = $overwrite ? 'false' : 'true';
        if (!empty($encryption)) $header['x-oss-server-side-encryption'] = $encryption;
        if (!empty($dataEncryption)) $header['x-oss-server-side-data-encryption'] = $dataEncryption;
        if (!empty($encryptionKey)) $header['x-oss-server-side-encryption-key-id'] = $encryptionKey;
        foreach($metaList as $k => $v) {
            $header["x-oss-meta-{$k}"] = $v;
        }
        if (!empty($tagList)) $header['x-oss-tagging'] = Tools::arrToUrl($tagList);
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            $header,
            [],
            $data
        );
    }

    /**
     * 获取文件
     * @access  public
     * @param   string      $filename                   文件名称
     * @param   string      $responseContentType        指定返回请求的content-type头
     * @param   string      $responseContentLanguage    指定回请求的content-language头
     * @param   DateTime    $responseExpires            指定返回请求的expires头
     * @param   string      $responseCacheControl       指定返回请求的cache-control头
     * @param   string      $responseDisposition        指定返回请求的content-disposition头
     * @param   string      $responseEncoding           指定返回请求的content-encoding头
     * @param   string      $range                      指定文件传输的范围
     * @param   DateTime    $ifModifiedSince            如果指定的时间早于实际修改时间或指定的时间不符合规范，则直接返回Object，并返回200 OK；如果指定的时间等于或者晚于实际修改时间，则返回304 Not Modified
     * @param   DateTime    $ifUnmodifiedSince          如果指定的时间等于或者晚于Object实际修改时间，则正常传输Object，并返回200 OK；如果指定的时间早于实际修改时间，则返回412 Precondition Failed
     * @param   string      $ifMatch                    如果传入的ETag和Object的ETag匹配，则正常传输Object，并返回200 OK；如果传入的ETag和Object的ETag不匹配，则返回412 Precondition Failed
     * @param   string      $ifNoneMatch                如果传入的ETag值和Object的ETag不匹配，则正常传输Object，并返回200 OK；如果传入的ETag和Object的ETag匹配，则返回304 Not Modified
     * @param   string      $acceptEncoding             指定客户端的编码类型
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function get(
        string $filename,
        string $responseContentType = null,
        string $responseContentLanguage = null,
        ?\DateTime $responseExpires = null,
        string $responseCacheControl = null,
        string $responseDisposition = null,
        string $responseEncoding = null,
        string $range = null,
        ?\DateTime $ifModifiedSince = null,
        ?\DateTime $ifUnmodifiedSince = null,
        string $ifMatch = null,
        string $ifNoneMatch = null,
        string $acceptEncoding = null
    ): array
    {
        $header = [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED];
        $query = [];
        if (!empty($responseContentType)) $query['response-content-type'] = $responseContentType;
        if (!empty($responseContentLanguage)) $query['response-content-language'] = $responseContentLanguage;
        if (!empty($responseExpires)) $query['response-expires'] = $responseExpires->setTimezone(new \DateTimeZone('GMT'))->format('Y-m-d\TH:i:s\Z');
        if (!empty($responseCacheControl)) $query['response-cache-control'] = $responseCacheControl;
        if (!empty($responseDisposition)) $query['response-content-disposition'] = $responseDisposition;
        if (!empty($responseEncoding)) $query['response-content-encoding'] = $responseEncoding;
        if (!empty($range)) $header['Range'] = $range;
        if (!empty($ifModifiedSince)) $header['If-Modified-Since'] = $ifModifiedSince->setTimezone(new \DateTimeZone('GMT'))->format('Y-m-d\TH:i:s\Z');
        if (!empty($ifUnmodifiedSince)) $header['If-Unmodified-Since'] = $ifUnmodifiedSince->setTimezone(new \DateTimeZone('GMT'))->format('Y-m-d\TH:i:s\Z');
        if (!empty($ifMatch)) $header['If-Match'] = $ifMatch;
        if (!empty($ifNoneMatch)) $header['If-None-Match'] = $ifNoneMatch;
        if (!empty($acceptEncoding)) $header['Accept-Encoding'] = $acceptEncoding;

        $result = $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            $header,
            $query,
            null,
            true
        );
        return [
            'content' => $result[0][0],
            'header' => $result[1]
        ];
    }

    /**
     * 复制文件
     * @access  public
     * @param   string      $filename                   文件名称
     * @param   string      $sourceFilename             源文件名称
     * @param   string      $sourceBucket               源存储空间(空表示当前空间)
     * @param   string      $acl                        访问权限(default:遵循所在存储空间的访问权限,private:私有资源,public-read:公共读资源,public-read-write:公共读写资源)
     * @param   string      $storageType                存储类型(Standard:标准存储,IA:低频访问,Archive:归档存储,ColdArchive:冷归档存储,DeepColdArchive:深度冷归档存储)
     * @param   DateTime    $ifModifiedSince            如果指定的时间早于实际修改时间或指定的时间不符合规范，则直接返回Object，并返回200 OK；如果指定的时间等于或者晚于实际修改时间，则返回304 Not Modified
     * @param   DateTime    $ifUnmodifiedSince          如果指定的时间等于或者晚于Object实际修改时间，则正常传输Object，并返回200 OK；如果指定的时间早于实际修改时间，则返回412 Precondition Failed
     * @param   string      $ifMatch                    如果传入的ETag和Object的ETag匹配，则正常传输Object，并返回200 OK；如果传入的ETag和Object的ETag不匹配，则返回412 Precondition Failed
     * @param   string      $ifNoneMatch                如果传入的ETag值和Object的ETag不匹配，则正常传输Object，并返回200 OK；如果传入的ETag和Object的ETag匹配，则返回304 Not Modified
     * @param   string      $encryption                 指定服务器端加密方式(AES256,KMS,SM4)
     * @param   string      $encryptionKey              KMS托管的用户主密钥
     * @param   string      $metaDirective              指定如何设置元数据(COPY:复制源Object的元数据到目标,REPLACE:忽略源的元数据，直接采用请求中指定的元数据)
     * @param   array       $metaList                   元数据列表[key1=>value1,key2=>value2]
     * @param   string      $taggingDirective           指定如何设置标签(COPY:复制源Object的标签到目标,REPLACE:忽略源的标签，直接采用请求中指定的标签)
     * @param   array       $tagList                    标签列表[key1=>value1,key2=>value2]
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function copy(
        string $filename,
        string $sourceFilename,
        string $sourceBucket = null,
        string $acl = null,
        string $storageType = null,
        ?\DateTime $ifModifiedSince = null,
        ?\DateTime $ifUnmodifiedSince = null,
        string $ifMatch = null,
        string $ifNoneMatch = null,
        string $encryption = null,
        string $encryptionKey = null,
        string $metaDirective = null,
        array $metaList = [],
        string $taggingDirective = null,
        array $tagList = []
    ): array
    {
        if (empty($sourceBucket)) $sourceBucket = $this->getBucketName();
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED,
            'x-oss-copy-source' => "/{$sourceBucket}/{$sourceFilename}"
        ];
        if (!empty($acl)) $header['x-oss-object-acl'] = $acl;
        if (!empty($storageType)) $header['x-oss-storage-class'] = $storageType;
        if (!empty($ifModifiedSince)) $header['x-oss-copy-source-if-modified-since'] = $ifModifiedSince->setTimezone(new \DateTimeZone('GMT'))->format('Y-m-d\TH:i:s\Z');
        if (!empty($ifUnmodifiedSince)) $header['xx-oss-copy-source-if-unmodified-since'] = $ifUnmodifiedSince->setTimezone(new \DateTimeZone('GMT'))->format('Y-m-d\TH:i:s\Z');
        if (!empty($ifMatch)) $header['x-oss-copy-source-if-match'] = $ifMatch;
        if (!empty($ifNoneMatch)) $header['x-oss-copy-source-if-none-match'] = $ifNoneMatch;
        if (!empty($encryption)) $header['x-oss-server-side-encryption'] = $encryption;
        if (!empty($encryptionKey)) $header['x-oss-server-side-encryption-key-id'] = $encryptionKey;
        if (!empty($metaDirective)) $header['x-oss-metadata-directive'] = $metaDirective;
        foreach($metaList as $k => $v) {
            $header["x-oss-meta-{$k}"] = $v;
        }
        if (!empty($taggingDirective)) $header['x-oss-tagging-directive'] = $taggingDirective;
        if (!empty($dataEncryption)) $header['x-oss-tagging'] = Tools::arrToUrl($tagList);

        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            $header
        );
    }

    /**
     * 追加写的方式上传文件
     * @access  public
     * @param   string      $filename               文件名称
     * @param   int         $position               位置
     * @param   string      $data                   文件数据
     * @param   string      $acl                    访问权限(default:遵循所在存储空间的访问权限,private:私有资源,public-read:公共读资源,public-read-write:公共读写资源)
     * @param   string      $storageType            存储类型(Standard:标准存储,IA:低频访问,Archive:归档存储,ColdArchive:冷归档存储,DeepColdArchive:深度冷归档存储)
     * @param   string      $cacheControl           指定该Object被下载时网页的缓存行为(no-cache:不可直接使用缓存,no-store:所有内容都不会被缓存,public:所有内容都将被缓存,private:所有内容只在客户端缓存,max-age=<seconds>:缓存内容的相对过期时间，单位为秒)
     * @param   string      $disposition            展示形式(inline:直接预览文件内容,attachment:下载到浏览器指定路径,attachment;{urlencode(filename)}.{ext}:指定文件名下载到浏览器)
     * @param   string      $encoding               编码方式(identity:未经过压缩或编码,gzip:采用Lempel-Ziv（LZ77）压缩算法以及32位CRC校验,compress:采用Lempel-Ziv-Welch（LZW）压缩算法,deflate:采用zlib结构和deflate压缩算法,br:采用Brotli算法)
     * @param   string      $md5                    用于检查消息内容是否与发送时一致
     * @param   DateTime    $expires                缓存内容的绝对过期时间
     * @param   bool        $overwrite              是否覆盖同名Object(默认覆盖)
     * @param   string      $encryption             指定服务器端加密方式(AES256,KMS,SM4)
     * @param   string      $dataEncryption         指定Object的加密算法,如果未指定此选项，表明Object使用AES256加密算法(AES256,KMS,SM4)
     * @param   string      $encryptionKey          KMS托管的用户主密钥
     * @param   array       $metaList               元数据列表[key1=>value1,key2=>value2]
     * @param   array       $tagList                标签列表[key1=>value1,key2=>value2]
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function append(
        string $filename,
        int $position,
        string $data,
        string $acl = null,
        string $storageType = null,
        string $cacheControl = null,
        string $disposition = null,
        string $encoding = null,
        string $md5 = null,
        ?\DateTime $expires = null,
        ?bool $overwrite = null,
        string $encryption = null,
        string $dataEncryption = null,
        string $encryptionKey = null,
        array $metaList = [],
        array $tagList = []
    ): array
    {
        $header = [Request::HEADER_CONTENT_TYPE => Tools::getMimetype($filename)];
        if (!empty($acl)) $header['x-oss-object-acl'] = $acl;
        if (!empty($storageType)) $header['x-oss-storage-class'] = $storageType;
        if (!empty($cacheControl)) $header['Cache-Control'] = $cacheControl;
        if (!empty($disposition)) $header['Content-Disposition'] = $disposition;
        if (!empty($encoding)) $header['Content-Encoding'] = $encoding;
        if (!empty($md5)) $header['Content-MD5'] = $md5;
        if (!is_null($expires)) $header['Expires'] = $expires->setTimezone(new \DateTimeZone('GMT'))->format('Y-m-d\TH:i:s\Z');
        if (!is_null($overwrite)) $header['x-oss-forbid-overwrite'] = $overwrite ? 'false' : 'true';
        if (!empty($encryption)) $header['x-oss-server-side-encryption'] = $encryption;
        if (!empty($dataEncryption)) $header['x-oss-server-side-data-encryption'] = $dataEncryption;
        if (!empty($encryptionKey)) $header['x-oss-server-side-encryption-key-id'] = $encryptionKey;
        foreach($metaList as $k => $v) {
            $header["x-oss-meta-{$k}"] = $v;
        }
        if (!empty($dataEncryption)) $header['x-oss-tagging'] = Tools::arrToUrl($tagList);
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_POST,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            $header,
            ['append' => null, 'position' => $position],
            $data
        );
    }

    /**
     * 删除文件
     * @access  public
     * @param   string  $filename   文件名称
     * @param   string  $versionId  版本ID(null表示临时删除, 空字符串表示彻底删除)
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function delete(string $filename, string $versionId = null): array
    {
        $query = [];
        if (!is_null($versionId)) $query['versionId'] = $versionId ?: 'null';
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            $query
        );
    }

    /**
     * 删除多个文件
     * @description 当versionId为空字符串时表示彻底删除
     * @access  public
     * @param   array   $fileList   文件列表['filename1', 'filename2' => 'versionId', 'filename3' => 'versionId']
     * @param   bool    $quiet      打开简单响应模式
     * @param   string  $encodeType 指定Encoding-type对返回结果中的Key进行编码
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function deleteMultiple(array $fileList, bool $quiet = false, string $encodingType = null): array
    {
        $objectList = [];
        foreach($fileList as $k => $v) {
            if (is_numeric($k)) {
                $objectList[] = ['Key' => $v];
            } else {
                $objectList[] = ['Key' => $k, 'VersionId' => $v ?: 'null'];
            }
        }
        $body = Tools::arrToXml(['Quiet' => $quiet ? 'true' : 'false', 'Object' => $objectList], 'Delete');
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML,
            'Content-MD5' => base64_encode(md5($body, true))
        ];
        if (!empty($encodeType)) $header['Encoding-type'] = $encodingType;
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_POST,
            '/',
            $this->getBucketName(),
            null,
            $header,
            ['delete' => null],
            $body
        );
    }

    /**
     * 获取文件头信息
     * @access  public
     * @param   string      $filename           文件名称
     * @param   string      $versionId          版本ID
     * @param   DateTime    $ifModifiedSince    如果指定的时间早于实际修改时间或指定的时间不符合规范，则直接返回Object，并返回200 OK；如果指定的时间等于或者晚于实际修改时间，则返回304 Not Modified
     * @param   DateTime    $ifUnmodifiedSince  如果指定的时间等于或者晚于Object实际修改时间，则正常传输Object，并返回200 OK；如果指定的时间早于实际修改时间，则返回412 Precondition Failed
     * @param   string      $ifMatch            如果传入的ETag和Object的ETag匹配，则正常传输Object，并返回200 OK；如果传入的ETag和Object的ETag不匹配，则返回412 Precondition Failed
     * @param   string      $ifNoneMatch        如果传入的ETag值和Object的ETag不匹配，则正常传输Object，并返回200 OK；如果传入的ETag和Object的ETag匹配，则返回304 Not Modified
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function getHead(string $filename, string $versionId = null, ?\DateTime $ifModifiedSince = null, ?\DateTime $ifUnmodifiedSince = null, string $ifMatch = null, string $ifNoneMatch = null): array
    {
        $query = [];
        if (!empty($versionId)) $query['versionId'] = $versionId;
        $header = [];
        if (!empty($ifModifiedSince)) $header['If-Modified-Since'] = $ifModifiedSince->setTimezone(new \DateTimeZone('GMT'))->format('Y-m-d\TH:i:s\Z');
        if (!empty($ifUnmodifiedSince)) $header['If-Unmodified-Since'] = $ifUnmodifiedSince->setTimezone(new \DateTimeZone('GMT'))->format('Y-m-d\TH:i:s\Z');
        if (!empty($ifMatch)) $header['If-Match'] = $ifMatch;
        if (!empty($ifNoneMatch)) $header['If-None-Match'] = $ifNoneMatch;
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_HEAD,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            $header,
            $query,
            null,
            true
        )[1];
    }

    /**
     * 获取文件元数据
     * @access  public
     * @param   string  $filename   文件名称
     * @param   string  $versionId  版本ID
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function getMeta(string $filename, string $versionId = null): array
    {
        $query = ['objectMeta' => null];
        if (!empty($versionId)) $query['versionId'] = $versionId;
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_HEAD,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            $query,
            null,
            true
        )[1];
    }

    /**
     * 表单上传
     * @description 此方法返回上传参数，需自行构建请求进行上传
     * @access  public
     * @param   string      $filename               文件名称
     * @param   int         $expire                 有效期
     * @param   string      $acl                    访问权限(default:遵循所在存储空间的访问权限,private:私有资源,public-read:公共读资源,public-read-write:公共读写资源)
     * @param   string      $storageType            存储类型(Standard:标准存储,IA:低频访问,Archive:归档存储,ColdArchive:冷归档存储,DeepColdArchive:深度冷归档存储)
     * @param   string      $successRedirectUrl     上传成功后客户端跳转到的URL
     * @param   int         $successStatusCode      未指定跳转URL时上传成功后返回的状态码(200,201,204)
     * @param   string      $cacheControl           指定该Object被下载时网页的缓存行为(no-cache:不可直接使用缓存,no-store:所有内容都不会被缓存,public:所有内容都将被缓存,private:所有内容只在客户端缓存,max-age=<seconds>:缓存内容的相对过期时间，单位为秒)
     * @param   string      $disposition            展示形式(inline:直接预览文件内容,attachment:下载到浏览器指定路径,attachment;{urlencode(filename)}.{ext}:指定文件名下载到浏览器)
     * @param   string      $encoding               编码方式(identity:未经过压缩或编码,gzip:采用Lempel-Ziv（LZ77）压缩算法以及32位CRC校验,compress:采用Lempel-Ziv-Welch（LZW）压缩算法,deflate:采用zlib结构和deflate压缩算法,br:采用Brotli算法)
     * @param   DateTime    $expires                缓存内容的绝对过期时间
     * @param   string      $dataEncryption         指定加密算法,如果未指定此选项，表明Object使用AES256加密算法(AES256,KMS,SM4)
     * @param   string      $encryptionKey          KMS托管的用户主密钥
     * @param   bool        $overwrite              是否覆盖同名Object(默认覆盖)
     * @param   array       $mateList               元信息
     * @param   string      $securityToken          安全令牌
     * @return  array
     * @throws  InvalidArgumentException
     */
    public function post(
        string $filename,
        int $expire = 60,
        string $acl = null,
        string $storageType = null,
        string $successRedirectUrl = null,
        ?int $successStatusCode = null,
        string $cacheControl = null,
        string $disposition = null,
        string $encoding = null,
        \DateTime $expires = null,
        string $dataEncryption = null,
        string $encryptionKey = null,
        ?bool $overwrite = null,
        array $metaList = [],
        string $securityToken = null
    ): array
    {
        $time = time();
        $date = date('Ymd');
        $gmdate = gmdate('Ymd\THis\Z', $time);
        $credential = "{$this->config->accessKeyId()}/{$date}/{$this->config->regionId()}/oss/aliyun_v4_request";
        $form = [
            'x-oss-signature-version' => 'OSS4-HMAC-SHA256',
            'x-oss-credential' => $credential,
            'x-oss-date' => $gmdate,
            'key' => $filename,
            'x-oss-content-type' => Tools::getMimetype($filename),
            'OSSAccessKeyId' => $this->config->accessKeyId()
        ];
        $policy = [
            'expiration' => gmdate('Y-m-d\TH:i:s\Z', $time + $expire),
            'conditions' => [
                ['bucket' => $this->getBucketName()],
                ['x-oss-signature-version' => 'OSS4-HMAC-SHA256'],
                ['x-oss-credential' => $credential],
                ['x-oss-date' => $gmdate],
                ['eq', '$key', $filename],
                ['eq', '$content-type', Tools::getMimetype($filename)],
            ]
        ];
        if (!empty($cacheControl)) {
            $form['Cache-Control'] = $cacheControl;
            $policy['conditions'][] = ['eq', '$cache-control', $cacheControl];
        }
        if (!empty($disposition)) $form['Content-Disposition'] = $disposition;
        if (!empty($encoding)) $form['Content-Encoding'] = $encoding;
        if (!empty($expires)) $form['Expires'] = $expires->setTimezone(new \DateTimeZone('GMT'))->format('Y-m-d\TH:i:s\Z');
        if (!empty($dataEncryption)) $form['x-oss-server-side-data-encryption'] = $dataEncryption;
        if (!empty($encryptionKey)) $form['x-oss-server-side-encryption-key-id'] = $encryptionKey;
        if (!is_null($overwrite)) $form['x-oss-forbid-overwrite'] = $overwrite ? 'false' : 'true';
        if (!empty($acl)) $form['x-oss-object-acl'] = $acl;
        if (!empty($storageType)) $form['x-oss-storage-class'] = $storageType;
        if (!empty($successRedirectUrl)) $form['success_action_redirect'] = $successRedirectUrl;
        if (!empty($successStatusCode)) {
            $form['success_action_status'] = $successStatusCode;
            $policy['conditions'][] = ['eq', '$success_action_status', (string)$successStatusCode];
        }
        if (!empty($securityToken)) $form['x-oss-security-token'] = $securityToken;
        foreach($metaList as $k => $v) {
            $form["x-oss-meta-{$k}"] = $v;
        }

        $form['policy'] = base64_encode(json_encode($policy));
        $dateKey = hash_hmac('sha256', $date, "aliyun_v4{$this->config->accessKeySecret()}", true);
        $dateRegionKey = hash_hmac('sha256', $this->config->regionId(), $dateKey, true);
        $dateRegionServiceKey = hash_hmac('sha256', 'oss', $dateRegionKey, true);
        $signingKey = hash_hmac('sha256', 'aliyun_v4_request', $dateRegionServiceKey, true);

        $form['x-oss-signature'] = hash_hmac('sha256', $form['policy'], $signingKey);
        return [
            'method' => Request::METHOD_POST,
            'url' => "https://{$this->getBucketName()}.{$this->getRegion()['extranet_endpoint']}",
            'content_type' => Request::CONTENT_TYPE_FORMDATA,
            'header' => [],
            'query' => [],
            'body' => Tools::arrToKeyVal($form),
            'file_key' => 'file',
            'file_path' => $this->getAccessPath($filename)
        ];
    }

    /**
     * 解冻
     * @access  public
     * @param   string  $filename   文件名称
     * @param   int     $day        解冻天数
     * @param   string  $tier       优先级(Standard-标准,Expedited-高优先级,Bulk-批量)
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function restore(string $filename, int $day, string $tier = null): array
    {
        $body = ['Days' => $day];
        if (!empty($tier)) $body['JobParameters'] = ['Tier' => $tier];
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_POST,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['restore' => null],
            Tools::arrToXml($body, 'RestoreRequest')
        );
    }

    /**
     * 初始化分片上传
     * @access  public
     * @param   string      $filename               文件名称
     * @param   string      $storageType            存储类型(Standard:标准存储,IA:低频访问,Archive:归档存储,ColdArchive:冷归档存储,DeepColdArchive:深度冷归档存储)
     * @param   string      $cacheControl           指定该Object被下载时网页的缓存行为(no-cache:不可直接使用缓存,no-store:所有内容都不会被缓存,public:所有内容都将被缓存,private:所有内容只在客户端缓存,max-age=<seconds>:缓存内容的相对过期时间，单位为秒)
     * @param   string      $disposition            展示形式(inline:直接预览文件内容,attachment:下载到浏览器指定路径,attachment;{urlencode(filename)}.{ext}:指定文件名下载到浏览器)
     * @param   string      $encoding               编码方式(identity:未经过压缩或编码,gzip:采用Lempel-Ziv（LZ77）压缩算法以及32位CRC校验,compress:采用Lempel-Ziv-Welch（LZW）压缩算法,deflate:采用zlib结构和deflate压缩算法,br:采用Brotli算法)
     * @param   DateTime    $expires                缓存内容的绝对过期时间
     * @param   bool        $overwrite              是否覆盖同名Object(默认覆盖)
     * @param   string      $encryption             指定服务器端加密方式(AES256,KMS,SM4)
     * @param   string      $encryptionKey          KMS托管的用户主密钥
     * @param   array       $tagList                标签列表
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function initPart(
        string $filename,
        string $storageType = null,
        string $cacheControl = null,
        string $disposition = null,
        string $encoding = null,
        \DateTime $expire = null,
        ?bool $overwrite = null,
        string $encryption = null,
        string $encryptionKey = null,
        array $tagList = []
    ): array
    {
        $header = [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED];
        if (!empty($storageType)) $header['x-oss-storage-class'] = $storageType;
        if (!empty($cacheControl)) $header['Cache-Control'] = $cacheControl;
        if (!empty($disposition)) $header['Content-Disposition'] = $disposition;
        if (!empty($encoding)) $header['Content-Encoding'] = $encoding;
        if (!empty($expire)) $header['Expires'] = $expire->setTimezone(new \DateTimeZone('GMT'))->format('Y-m-d\TH:i:s\Z');
        if (!is_null($overwrite)) $header['x-oss-forbid-overwrite'] = $overwrite ? 'false' : 'true';
        if (!empty($encryption)) $header['x-oss-server-side-encryption'] = $encryption;
        if (!empty($encryptionKey)) $header['x-oss-server-side-encryption-key-id'] = $encryptionKey;
        if (!empty($tagList)) $header['x-oss-tagging'] = Tools::arrToUrl($tagList);
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_POST,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            $header,
            ['uploads' => null]
        );
    }

    /**
     * 分片上传
     * @access  public
     * @param   string  $filename       文件名称
     * @param   string  $uploadId       分片上传任务ID
     * @param   int     $partNumber     分片标识
     * @param   string  $data           数据
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function uploadPart(string $filename, string $uploadId, int $partNumber, string $data): array
    {
        $result = $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_STREAM],
            ['partNumber' => $partNumber, 'uploadId' => $uploadId],
            $data,
            true
        )[1];
        return [
            'ETag' => $result['ETag'],
            'MD5' => $result['Content-MD5'],
            'hash-crc64ecma' => $result['x-oss-hash-crc64ecma']
        ];
    }

    /**
     * 客户端分片上传
     * @description 此方法返回上传参数，需自行构建请求进行上传
     * @access  public
     * @param   string  $filename       文件名称
     * @param   string  $uploadId       分片上传任务ID
     * @param   int     $partNumber     分片标识
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     */
    public function clientUploadPart(string $filename, string $uploadId, int $partNumber): array
    {
        $host = "{$this->getBucketName()}.{$this->getRegion()['extranet_endpoint']}";
        $header = [
            'Host' => $host,
            'x-oss-date' => gmdate('Ymd\THis\Z', time()),
            'x-oss-content-sha256' => 'UNSIGNED-PAYLOAD',
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_STREAM
        ];
        $query = ['partNumber' => $partNumber, 'uploadId' => $uploadId];
        $header['Authorization'] = $this->buildHeaderSign(Request::METHOD_PUT, $this->getBucketName(), $filename, $query, $header);
        return [
            'method' => Request::METHOD_PUT,
            'url' => "https://{$host}/{$filename}",
            'content_type' => Request::CONTENT_TYPE_STREAM,
            'header' => Tools::arrToKeyVal($header),
            'query' => Tools::arrToKeyVal($query),
            'part_number' => $partNumber,
            'file_path' => $this->getAccessPath($filename)
        ];
    }

    /**
     * 拷贝现有文件到分片
     * @access  public
     * @param   string      $filename           文件名称
     * @param   string      $uploadId           分片上传任务ID
     * @param   int         $partNumber         分片标识
     * @param   string      $sourceFilename     源文件名称
     * @param   string      $sourceBucket       源存储空间(默认当前空间
     * @param   string      $copySourceRange    拷贝源文件的范围(bytes=0-9)
     * @param   string      $versionId          版本ID
     * @param   DateTime    $ifModifiedSince    如果指定的时间早于实际修改时间或指定的时间不符合规范，则直接返回Object，并返回200 OK；如果指定的时间等于或者晚于实际修改时间，则返回304 Not Modified
     * @param   DateTime    $ifUnmodifiedSince  如果指定的时间等于或者晚于Object实际修改时间，则正常传输Object，并返回200 OK；如果指定的时间早于实际修改时间，则返回412 Precondition Failed
     * @param   string      $ifMatch            如果传入的ETag和Object的ETag匹配，则正常传输Object，并返回200 OK；如果传入的ETag和Object的ETag不匹配，则返回412 Precondition Failed
     * @param   string      $ifNoneMatch        如果传入的ETag值和Object的ETag不匹配，则正常传输Object，并返回200 OK；如果传入的ETag和Object的ETag匹配，则返回304 Not Modified
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function copyPart(
        string $filename,
        string $uploadId,
        int $partNumber,
        string $sourceFilename,
        string $sourceBucket = null,
        string $copySourceRange = null,
        string $versionId = null,
        \DateTime $ifModifiedSince = null,
        \DateTime $ifUnmodifiedSince = null,
        string $ifMatch = null,
        string $ifNoneMatch = null
    ): array
    {
        $query = ['uploadId' => $uploadId, 'partNumber' => $partNumber];
        if (empty($sourceBucket)) $sourceBucket = $this->getBucketName();
        $source = "/{$sourceBucket}/{$sourceFilename}";
        if (!empty($versionId)) $source .= "?versionId={$versionId}";
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED,
            'x-oss-copy-source' => $source,
        ];
        if (!empty($copySourceRange)) $header['x-oss-copy-source-range'] = $copySourceRange;
        if (!empty($ifModifiedSince)) $header['x-oss-copy-source-if-modified-since'] = $ifModifiedSince;
        if (!empty($ifUnmodifiedSince)) $header['x-oss-copy-source-if-unmodified-since'] = $ifUnmodifiedSince;
        if (!empty($ifMatch)) $header['x-oss-copy-source-if-match'] = $ifMatch;
        if (!empty($ifNoneMatch)) $header['x-oss-copy-source-if-none-match'] = $ifNoneMatch;
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            $header,
            $query
        );
    }

    /**
     * 完成分片上传
     * @access  public
     * @param   string  $filename       文件名称
     * @param   string  $uploadId       分片上传任务ID
     * @param   array   $eTagList       ETag列表[partNumber1 => etag1, partNumber2=>etag2]
     * @param   string  $encoding       指定对返回的Key进行编码
     * @param   bool    $overwrite      是否覆盖同名Object(默认覆盖)
     * @param   bool    $completeAll    指定是否列举当前UploadId已上传的所有Part
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function completePart(string $filename, string $uploadId, array $eTagList, string $encoding = null, ?bool $overwrite = null, ?bool $completeAll = null): array
    {
        $query = ['uploadId' => $uploadId];
        if (!empty($encoding)) $query['encoding-type'] = $encoding;
        $header = [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML];
        if (!is_null($overwrite)) $header['x-oss-forbid-overwrite'] = $overwrite ? 'false' : 'true';
        if (!is_null($completeAll) && $completeAll) $header['x-oss-complete-all'] = 'yes';
        $body = ['Part' => []];
        foreach($eTagList as $partNumber => $eTag) {
            $body['Part'][] = ['PartNumber' => $partNumber, 'ETag' => $eTag];
        }
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_POST,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            $header,
            $query,
            !is_null($completeAll) && $completeAll ? null : Tools::arrToXml($body, 'CompleteMultipartUpload')
        );
    }

    /**
     * 取消分片上传
     * @access  public
     * @param   string  $filename   文件名称
     * @param   string  $uploadId   分片上传任务ID
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function abortPart(string $filename, string $uploadId): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['uploadId' => $uploadId]
        );
    }

    /**
     * 分片上传任务列表
     * @access  public
     * @param   string  $delimiter      用于分组的字符
     * @param   int     $maxUploads     限定此次返回的最大任务数量
     * @param   string  $keyMarker      用于指定返回结果的起始位置
     * @param   string  $prefix         限定必须以prefix作为前缀
     * @param   string  $uploadIdMarker 用于指定返回结果的起始位置
     * @param   string  $encoding       指定对返回的Key进行编码
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function partTaskList(string $delimiter = null, ?int $maxUploads = null, string $keyMarker = null, string $prefix = null, string $uploadIdMarker = null, string $encoding = null): array
    {
        $query = ['uploads' => null];
        if (!empty($delimiter)) $query['delimiter'] = $delimiter;
        if (!is_null($maxUploads)) $query['max-uploads'] = $maxUploads;
        if (!empty($keyMarker)) $query['key-marker'] = $keyMarker;
        if (!empty($prefix)) $query['prefix'] = $prefix;
        if (!empty($uploadIdMarker)) $query['upload-id-marker'] = $uploadIdMarker;
        if (!empty($encoding)) $query['encoding-type'] = $encoding;
        dump($query);
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $this->getBucketName(),
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            $query
        );
    }

    /**
     * 分片列表
     * @access  public
     * @param   string  $filename           文件名称
     * @param   string  $uploadId           分片上传任务ID
     * @param   int     $maxParts           限定此次返回的最大分片数量
     * @param   int     $partNumberMarker   指定List的起始位置
     * @param   string  $encoding           指定对返回的Key进行编码
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function partList(string $filename, string $uploadId, ?int $maxParts = null, ?int $partNumberMarker = null, string $encoding = null): array
    {
        $query = ['uploadId' => $uploadId];
        if (!is_null($maxParts)) $query['max-parts'] = $maxParts;
        if (!is_null($partNumberMarker)) $query['part-number-marker'] = $partNumberMarker;
        if (!empty($encoding)) $query['encoding-type'] = $encoding;
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            $query
        );
    }

    /**
     * 设置访问权限
     * @access  public
     * @param   string  $filename   文件名称
     * @param   string  $acl        访问权限(default:遵循所在存储空间的访问权限,private:私有资源,public-read:公共读资源,public-read-write:公共读写资源)
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function setAcl(string $filename, string $acl): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            [
                Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED,
                'x-oss-object-acl' => $acl
            ],
            ['acl' => null]
        );
    }

    /**
     * 获取访问权限
     * @access  public
     * @param   string  $filename   文件名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function getAcl(string $filename): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['acl' => null]
        );
    }

    /**
     * 创建软链接
     * @access  public
     * @param   string  $filename       文件名称
     * @param   string  $sourceFilename 源文件名称
     * @param   bool    $overwrite      是否覆盖
     * @param   string  $acl            访问权限(default:遵循所在存储空间的访问权限,private:私有资源,public-read:公共读资源,public-read-write:公共读写资源)
     * @param   string  $storageType    存储类型(Standard:标准存储,IA:低频访问,Archive:归档存储)
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function createSymlink(string $filename, string $sourceFilename, ?bool $overwrite = null, string $acl = null, string $storageType = null): array
    {
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED,
            'x-oss-symlink-target' => rawurlencode($sourceFilename)
        ];
        if (!is_null(($overwrite))) $header['x-oss-forbid-overwrite'] = $overwrite ? 'false' : 'true';
        if (!empty($acl)) $header['x-oss-object-acl'] = $acl;
        if (!empty($storageType)) $header['x-oss-storage-class'] = $storageType;
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            $header,
            ['symlink' => null]
        );
    }

    /**
     * 获取软连接
     * @access  public
     * @param   string  $filename   文件名称
     * @param   string  $versionId  版本ID
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function getSymlink(string $filename, string $versionId = null): array
    {
        $query = ['symlink' => null];
        if (!empty($versionId)) $query['versionId'] = $versionId;
        $result = $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            $query,
            null,
            true
        )[1];
        return [
            'source_filename' => $result['x-oss-symlink-target']
        ];
    }

    /**
     * 设置标签
     * @access  public
     * @param   string  $filename   文件名称
     * @param   array   $tagList    标签列表[key1=>value1, key2=>value2]
     * @param   string  $versionId  版本ID
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function setTag(string $filename, array $tagList, string $versionId = null)
    {
        $query = ['tagging' => null];
        if(!empty($versionId)) $query['versionId'] = $versionId;
        $body = ['TagSet' => ['Tag' => []]];
        foreach($tagList as $k => $v) {
            $body['TagSet']['Tag'][] = ['Key' => $k, 'Value' => $v];
        }
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            $query,
            Tools::arrToXml($body, 'Tagging')
        );
    }

    /**
     * 获取标签
     * @access  public
     * @param   string  $filename   文件名称
     * @param   string  $versionId  版本ID
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function getTag(string $filename, string $versionId = null)
    {
        $query = ['tagging' => null];
        if(!empty($versionId)) $query['versionId'] = $versionId;
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            $query
        );
    }

    /**
     * 删除标签
     * @access  public
     * @param   string  $filename   文件名称
     * @param   string  $versionId  版本ID
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  InvalidDecodeException
     */
    public function deleteTag(string $filename, string $versionId = null)
    {
        $query = ['tagging' => null];
        if(!empty($versionId)) $query['versionId'] = $versionId;
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            "/{$filename}",
            $this->getBucketName(),
            $filename,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            $query
        );
    }
}
