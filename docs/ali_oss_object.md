# 阿里云对象存储Object相关操作

## setBucketName
- 说明: 设置存储空间名称
- 文档: 无
- 参数:
  + `name`: (string) 存储空间名称

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->setBucketName('bucket-name');
~~~

## getAccessPath
- 说明: 获取访问地址
- 文档: 无
- 参数:
  + `filename`: (string) 文件名称

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->getAccessPath('text.txt');
~~~

## list
- 说明: 获取所有Object信息
- 文档: [ListObjectsV2](https://help.aliyun.com/zh/oss/developer-reference/listobjectsv2)
- 参数:
  + `delimiter`: (string) 对Object名字进行分组的字符,所有Object名字包含指定的前缀
  + `startAfter`: (string) 设定从startAfter之后按字母排序开始返回Object
  + `continuationToken`: (string) 指定List操作需要从此token开始
  + `maxKeys`: (string) 指定返回Object的最大数
  + `prefix`: (string) 限定返回文件的Key必须以prefix作为前缀
  + `encodingType`: (string) 对返回的内容进行编码并指定编码的类型
  + `fetchOwner`: (bool) 指定是否在返回结果中包含owner信息

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->list();
~~~

## put
- 说明: 上传文件
- 文档: [PutObject](https://help.aliyun.com/zh/oss/developer-reference/putobject)
- 参数:
  + `filename`: (string) 文件名称
  + `data`: (string) 文件数据
  + `acl`: (string) 访问权限(default:遵循所在存储空间的访问权限,private:私有资源,public-read:公共读资源,public-read-write:公共读写资源)
  + `storageType`: (string) 存储类型(Standard:标准存储,IA:低频访问,Archive:归档存储,ColdArchive:冷归档存储,DeepColdArchive:深度冷归档存储)
  + `cacheControl`: (string) 指定该Object被下载时网页的缓存行为(no-cache:不可直接使用缓存,no-store:所有内容都不会被缓存,public:所有内容都将被缓存,private:所有内容只在客户端缓存,max-age=<seconds>:缓存内容的相对过期时间，单位为秒)
  + `disposition`: (string) 展示形式(inline:直接预览文件内容,attachment:下载到浏览器指定路径,attachment;{urlencode(filename)}.{ext}:指定文件名下载到浏览器)
  + `encoding`: (string) 编码方式(identity:未经过压缩或编码,gzip:采用Lempel-Ziv（LZ77）压缩算法以及32位CRC校验,compress:采用Lempel-Ziv-Welch（LZW）压缩算法,deflate:采用zlib结构和deflate压缩算法,br:采用Brotli算法)
  + `md5`: (string) 用于检查消息内容是否与发送时一致
  + `expires`: (DateTime) 缓存内容的绝对过期时间
  + `overwrite`: (bool) 是否覆盖同名Object(默认覆盖)
  + `encryption`: (string) 指定服务器端加密方式(AES256,KMS,SM4)
  + `dataEncryption`: (string) 指定Object的加密算法,如果未指定此选项，表明Object使用AES256加密算法(AES256,KMS,SM4)
  + `encryptionKey`: (string) KMS托管的用户主密钥
  + `metaList`: (array) 元数据列表[key1=>value1,key2=>value2]
  + `tagList`: (array) 标签列表[key1=>value1,key2=>value2]

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->put('test.txt', '0');
~~~

## get
- 说明: 获取文件
- 文档: [GetObject](https://help.aliyun.com/zh/oss/developer-reference/getobject)
- 参数:
  + `filename`: (string) 文件名称
  + `responseContentType`: (string) 指定返回请求的content-type头
  + `responseContentLanguage`: (string) 指定回请求的content-language头
  + `responseExpires`: (DateTime) 指定返回请求的expires头
  + `responseCacheControl`: (string) 指定返回请求的cache-control头
  + `responseDisposition`: (string) 指定返回请求的content-disposition头
  + `responseEncoding`: (string) 指定返回请求的content-encoding头
  + `range`: (string) 指定文件传输的范围
  + `ifModifiedSince`: (DateTime) 如果指定的时间早于实际修改时间或指定的时间不符合规范，则直接返回Object，并返回200 OK；如果指定的时间等于或者晚于实际修改时间，则返回304 Not Modified
  + `ifUnmodifiedSince`: (DateTime) 如果指定的时间等于或者晚于Object实际修改时间，则正常传输Object，并返回200 OK；如果指定的时间早于实际修改时间，则返回412 Precondition Failed
  + `ifMatch`: (string) 如果传入的ETag和Object的ETag匹配，则正常传输Object，并返回200 OK；如果传入的ETag和Object的ETag不匹配，则返回412 Precondition Failed
  + `ifNoneMatch`: (string) 如果传入的ETag值和Object的ETag不匹配，则正常传输Object，并返回200 OK；如果传入的ETag和Object的ETag匹配，则返回304 Not Modified
  + `acceptEncoding`: (string) 指定客户端的编码类型

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->get('test.txt');
~~~

## copy
- 说明: 复制文件
- 文档: [CopyObject](https://help.aliyun.com/zh/oss/developer-reference/copyobject)
- 参数:
  + `filename`: (string) 文件名称
  + `sourceFilename`: (string) 源文件名称
  + `sourceBucket`: (string) 源存储空间(空表示当前空间)
  + `acl`: (string) 访问权限(default:遵循所在存储空间的访问权限,private:私有资源,public-read:公共读资源,public-read-write:公共读写资源)
  + `storageType`: (string) 存储类型(Standard:标准存储,IA:低频访问,Archive:归档存储,ColdArchive:冷归档存储,DeepColdArchive:深度冷归档存储)
  + `ifModifiedSince`: (DateTime) 如果指定的时间早于实际修改时间或指定的时间不符合规范，则直接返回Object，并返回200 OK；如果指定的时间等于或者晚于实际修改时间，则返回304 Not Modified
  + `ifUnmodifiedSince`: (DateTime) 如果指定的时间等于或者晚于Object实际修改时间，则正常传输Object，并返回200 OK；如果指定的时间早于实际修改时间，则返回412 Precondition Failed
  + `ifMatch`: (string) 如果传入的ETag和Object的ETag匹配，则正常传输Object，并返回200 OK；如果传入的ETag和Object的ETag不匹配，则返回412 Precondition Failed
  + `ifNoneMatch`: (string) 如果传入的ETag值和Object的ETag不匹配，则正常传输Object，并返回200 OK；如果传入的ETag和Object的ETag匹配，则返回304 Not Modified
  + `encryption`: (string) 指定服务器端加密方式(AES256,KMS,SM4)
  + `encryptionKey`: (string) KMS托管的用户主密钥
  + `metaDirective`: (string) 指定如何设置元数据(COPY:复制源Object的元数据到目标,REPLACE:忽略源的元数据，直接采用请求中指定的元数据)
  + `metaList`: (array) 元数据列表[key1=>value1,key2=>value2]
  + `taggingDirective`: (string) 指定如何设置标签(COPY:复制源Object的标签到目标,REPLACE:忽略源的标签，直接采用请求中指定的标签)
  + `tagList`: (array) 标签列表[key1=>value1,key2=>value2]

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->copy('test.txt', 'test1.txt');
~~~

## append
- 说明: 追加写的方式上传文件
- 文档: [AppendObject](https://help.aliyun.com/zh/oss/developer-reference/appendobject)
- 参数:
  + `filename`: (string) 文件名称
  + `position`: (string) 位置
  + `data`: (string) 文件数据
  + `acl`: (string) 访问权限(default:遵循所在存储空间的访问权限,private:私有资源,public-read:公共读资源,public-read-write:公共读写资源)
  + `storageType`: (string) 存储类型(Standard:标准存储,IA:低频访问,Archive:归档存储,ColdArchive:冷归档存储,DeepColdArchive:深度冷归档存储)
  + `cacheControl`: (string) 指定该Object被下载时网页的缓存行为(no-cache:不可直接使用缓存,no-store:所有内容都不会被缓存,public:所有内容都将被缓存,private:所有内容只在客户端缓存,max-age=<seconds>:缓存内容的相对过期时间，单位为秒)
  + `disposition`: (string) 展示形式(inline:直接预览文件内容,attachment:下载到浏览器指定路径,attachment;{urlencode(filename)}.{ext}:指定文件名下载到浏览器)
  + `encoding`: (string) 编码方式(identity:未经过压缩或编码,gzip:采用Lempel-Ziv（LZ77）压缩算法以及32位CRC校验,compress:采用Lempel-Ziv-Welch（LZW）压缩算法,deflate:采用zlib结构和deflate压缩算法,br:采用Brotli算法)
  + `md5`: (string) 用于检查消息内容是否与发送时一致
  + `expires`: (DateTime) 缓存内容的绝对过期时间
  + `overwrite`: (bool) 是否覆盖同名Object(默认覆盖)
  + `encryption`: (string) 指定服务器端加密方式(AES256,KMS,SM4)
  + `dataEncryption`: (string) 指定Object的加密算法,如果未指定此选项，表明Object使用AES256加密算法(AES256,KMS,SM4)
  + `encryptionKey`: (string) KMS托管的用户主密钥
  + `metaList`: (array) 元数据列表[key1=>value1,key2=>value2]
  + `tagList`: (string) 标签列表[key1=>value1,key2=>value2]

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->copy('test.txt', 0， '0');
~~~

## delete
- 说明: 删除文件
- 文档: [DeleteObject](https://help.aliyun.com/zh/oss/developer-reference/deleteobject?spm=a2c4g.11186623.0.0.449e51efgGsyHs)
- 参数:
  + `filename`: (string) 文件名称
  + `filename`: (versionId) 版本ID(null表示临时删除, 空字符串表示彻底删除)

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->delete('test.txt');
~~~

## deleteMultiple
- 说明: 删除多个文件
- 文档: [DeleteMultipleObjects](https://help.aliyun.com/zh/oss/developer-reference/deletemultipleobjects)
- 参数:
  + `fileList`: (array) 文件列表['filename1', 'filename2' => 'versionId', 'filename3' => 'versionId']
  + `quiet`: (bool) 打开简单响应模式
  + `encodeType`: (string) 指定Encoding-type对返回结果中的Key进行编码

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->deleteMultiple(['test.txt', 'test1.txt']);
~~~

## getHead
- 说明: 获取文件头信息
- 文档: [HeadObject](https://help.aliyun.com/zh/oss/developer-reference/headobject)
- 参数:
  + `filename`: (string) 文件名称
  + `versionId`: (string) 版本ID
  + `ifModifiedSince`: (DateTime) 如果指定的时间早于实际修改时间或指定的时间不符合规范，则直接返回Object，并返回200 OK；如果指定的时间等于或者晚于实际修改时间，则返回304 Not Modified
  + `ifUnmodifiedSince`: (DateTime) 如果指定的时间等于或者晚于Object实际修改时间，则正常传输Object，并返回200 OK；如果指定的时间早于实际修改时间，则返回412 Precondition Failed
  + `ifMatch`: (string) 如果传入的ETag和Object的ETag匹配，则正常传输Object，并返回200 OK；如果传入的ETag和Object的ETag不匹配，则返回412 Precondition Failed
  + `ifNoneMatch`: (string) 如果传入的ETag值和Object的ETag不匹配，则正常传输Object，并返回200 OK；如果传入的ETag和Object的ETag匹配，则返回304 Not Modified

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->getHead('test.txt');
~~~

## getMeta
- 说明: 获取文件元数据
- 文档: [GetObjectMeta](https://help.aliyun.com/zh/oss/developer-reference/getobjectmeta)
- 参数:
  + `filename`: (string) 文件名称
  + `versionId`: (string) 版本ID

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->getMeta('test.txt');
~~~

## post
- 说明: 表单上传 (此方法返回上传参数，需自行构建请求进行上传)
- 文档: [PostObject](https://help.aliyun.com/zh/oss/developer-reference/postobject)
- 参数
  + `filename`: (string) 文件名称
  + `expire`: (int) 有效期
  + `acl`: (string) 访问权限(default:遵循所在存储空间的访问权限,private:私有资源,public-read:公共读资源,public-read-write:公共读写资源)
  + `storageType`: (string) 存储类型(Standard:标准存储,IA:低频访问,Archive:归档存储,ColdArchive:冷归档存储,DeepColdArchive:深度冷归档存储)
  + `successRedirectUrl`: (string) 上传成功后客户端跳转到的URL
  + `successStatusCode`: (int) 未指定跳转URL时上传成功后返回的状态码(200,201,204)
  + `cacheControl`: (string) 指定该Object被下载时网页的缓存行为(no-cache:不可直接使用缓存,no-store:所有内容都不会被缓存,public:所有内容都将被缓存,private:所有内容只在客户端缓存,max-age=<seconds>:缓存内容的相对过期时间，单位为秒)
  + `disposition`: (string) 展示形式(inline:直接预览文件内容,attachment:下载到浏览器指定路径,attachment;{urlencode(filename)}.{ext}:指定文件名下载到浏览器)
  + `encoding`: (string) 编码方式(identity:未经过压缩或编码,gzip:采用Lempel-Ziv（LZ77）压缩算法以及32位CRC校验,compress:采用Lempel-Ziv-Welch（LZW）压缩算法,deflate:采用zlib结构和deflate压缩算法,br:采用Brotli算法)
  + `expires`: (DateTime) 缓存内容的绝对过期时间
  + `dataEncryption`: (string) 指定加密算法,如果未指定此选项，表明Object使用AES256加密算法(AES256,KMS,SM4)
  + `encryptionKey`: (string) KMS托管的用户主密钥
  + `overwrite`: (bool) 是否覆盖同名Object(默认覆盖)
  + `mateList`: (array) 元信息
  + `securityToken`: (string) 安全令牌

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->post('test.txt');
~~~

## restore
- 说明: 解冻
- 文档: [RestoreObject](https://help.aliyun.com/zh/oss/developer-reference/restoreobject)
- 参数:
  + `filename`: (string) 文件名称
  + `day`: (int) 解冻天数
  + `tier`: (string) 优先级(Standard-标准,Expedited-高优先级,Bulk-批量)

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->restore('test.txt'， 1);
~~~

## initPart
- 说明: 初始化分片上传
- 文档: [InitiateMultipartUpload](https://help.aliyun.com/zh/oss/developer-reference/initiatemultipartupload)
- 参数:
  + `filename`: (string) 文件名称
  + `storageType`: (string) 存储类型(Standard:标准存储,IA:低频访问,Archive:归档存储,ColdArchive:冷归档存储,DeepColdArchive:深度冷归档存储)
  + `cacheControl`: (string) 指定该Object被下载时网页的缓存行为(no-cache:不可直接使用缓存,no-store:所有内容都不会被缓存,public:所有内容都将被缓存,private:所有内容只在客户端缓存,max-age=<seconds>:缓存内容的相对过期时间，单位为秒)
  + `disposition`: (string) 展示形式(inline:直接预览文件内容,attachment:下载到浏览器指定路径,attachment;{urlencode(filename)}.{ext}:指定文件名下载到浏览器)
  + `encoding`: (string) 编码方式(identity:未经过压缩或编码,gzip:采用Lempel-Ziv（LZ77）压缩算法以及32位CRC校验,compress:采用Lempel-Ziv-Welch（LZW）压缩算法,deflate:采用zlib结构和deflate压缩算法,br:采用Brotli算法)
  + `expires`: (DateTime) 缓存内容的绝对过期时间
  + `overwrite`: (bool) 是否覆盖同名Object(默认覆盖)
  + `encryption`: (string) 指定服务器端加密方式(AES256,KMS,SM4)
  + `encryptionKey`: (string) KMS托管的用户主密钥
  + `tagList`: (array) 标签列表

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->initPart('test.txt');
~~~

## uploadPart
- 说明: 分片上传
- 文档: [UploadPart](https://help.aliyun.com/zh/oss/developer-reference/uploadpart)
- 参数:
  + `filename`: (string) 文件名称
  + `uploadId`: (string) 分片上传任务ID
  + `partNumber`: (int) 分片标识
  + `data`: (string) 数据

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->uploadPart('test.txt', 'upload-id', 1, '1');
~~~

## clientUploadPart
- 说明: 客户端分片上传 (此方法返回上传参数，需自行构建请求进行上传)
- 文档: [UploadPart](https://help.aliyun.com/zh/oss/developer-reference/uploadpart)
- 参数:
  + `filename`: (string) 文件名称
  + `uploadId`: (string) 分片上传任务ID
  + `partNumber`: (int) 分片标识

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->clientUploadPart('test.txt', 'upload-id', 1);
~~~

## copyPart
- 说明: 拷贝现有文件到分片
- 文档: [UploadPartCopy](https://help.aliyun.com/zh/oss/developer-reference/uploadpartcopy)
- 参数:
  + `filename`: (string) 文件名称
  + `uploadId`: (string) 分片上传任务ID
  + `partNumber`: (int) 分片标识
  + `sourceFilename`: (string) 源文件名称
  + `sourceBucket`: (string) 源存储空间(默认当前空间)
  + `copySourceRange`: (string) 拷贝源文件的范围(bytes=0-9)
  + `versionId`: (string) 版本ID
  + `ifModifiedSince`: (DateTime) 如果指定的时间早于实际修改时间或指定的时间不符合规范，则直接返回Object，并返回200 OK；如果指定的时间等于或者晚于实际修改时间，则返回304 Not Modified
  + `ifUnmodifiedSince`: (DateTime) 如果指定的时间等于或者晚于Object实际修改时间，则正常传输Object，并返回200 OK；如果指定的时间早于实际修改时间，则返回412 Precondition Failed
  + `ifMatch`: (string) 如果传入的ETag和Object的ETag匹配，则正常传输Object，并返回200 OK；如果传入的ETag和Object的ETag不匹配，则返回412 Precondition Failed
  + `ifNoneMatch`: (string) 如果传入的ETag值和Object的ETag不匹配，则正常传输Object，并返回200 OK；如果传入的ETag和Object的ETag匹配，则返回304 Not Modified

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->copyPart('test.txt', 'upload-id', 1, 'source.txt');
~~~

## completePart
- 说明: 完成分片上传
- 文档: [CompleteMultipartUpload](https://help.aliyun.com/zh/oss/developer-reference/completemultipartupload)
- 参数:
  + `filename`: (string) 文件名称
  + `uploadId`: (string) 分片上传任务ID
  + `eTagList`: (array) ETag列表[partNumber1 => etag1, partNumber2=>etag2]
  + `encoding`: (string) 指定对返回的Key进行编码
  + `overwrite`: (bool) 是否覆盖同名Object(默认覆盖)
  + `completeAll`: (bool) 指定是否列举当前UploadId已上传的所有Part

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->completePart('test.txt', 'upload-id', [1 => 'etag1', 2 => 'etag2']);
~~~

## abortPart
- 说明: 取消分片上传
- 文档: [AbortMultipartUpload](https://help.aliyun.com/zh/oss/developer-reference/abortmultipartupload)
- 参数:
  + `filename`: (string) 文件名称
  + `uploadId`: (string) 分片上传任务ID

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->abortPart('test.txt', 'upload-id');
~~~

## partTaskList
- 说明: 分片上传任务列表
- 文档: [ListMultipartUploads](https://help.aliyun.com/zh/oss/developer-reference/listmultipartuploads)
- 参数:
  + `delimiter`: (string) 用于分组的字符
  + `maxUploads`: (int) 限定此次返回的最大任务数量
  + `keyMarker`: (string) 用于指定返回结果的起始位置
  + `prefix`: (string) 限定必须以prefix作为前缀
  + `uploadIdMarker`: (string) 用于指定返回结果的起始位置
  + `encoding`: (string) 指定对返回的Key进行编码

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->partTaskList();
~~~

## abortPart
- 说明: 取消分片上传
- 文档: [AbortMultipartUpload](https://help.aliyun.com/zh/oss/developer-reference/abortmultipartupload)
- 参数:
  + `filename`: (string) 文件名称
  + `uploadId`: (string) 分片上传任务ID

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->abortPart('test.txt', 'upload-id');
~~~

## partList
- 说明: 分片列表
- 文档: [ListParts](https://help.aliyun.com/zh/oss/developer-reference/listparts)
- 参数:
  + `filename`: (string) 文件名称
  + `uploadId`: (string) 分片上传任务ID
  + `maxParts`: (int) 限定此次返回的最大分片数量
  + `partNumberMarker`: (int) 指定List的起始位置
  + `encoding`: (string) 指定对返回的Key进行编码

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->partList('test.txt', 'upload-id');
~~~

## setAcl
- 说明: 设置访问权限
- 文档: [PutObjectACL](https://help.aliyun.com/zh/oss/developer-reference/putobjectacl)
- 参数:
  + `filename`: (string) 文件名称
  + `acl`: (string) 访问权限(default:遵循所在存储空间的访问权限,private:私有资源,public-read:公共读资源,public-read-write:公共读写资源)

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->setAcl('test.txt', 'private');
~~~

## getAcl
- 说明: 获取访问权限
- 文档: [GetObjectACL](https://help.aliyun.com/zh/oss/developer-reference/getobjectacl)
- 参数:
  + `filename`: (string) 文件名称

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->getAcl('test.txt');
~~~

## createSymlink
- 说明: 创建软链接
- 文档: [PutSymlink](https://help.aliyun.com/zh/oss/developer-reference/putsymlink)
- 参数:
  + `filename`: (string) 文件名称
  + `sourceFilename`: (string) 源文件名称
  + `overwrite`: (bool) 是否覆盖
  + `acl`: (string) 访问权限(default:遵循所在存储空间的访问权限,private:私有资源,public-read:公共读资源,public-read-write:公共读写资源)
  + `storageType`: (string) 存储类型(Standard:标准存储,IA:低频访问,Archive:归档存储)

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->createSymlink('test.txt', 'source.txt');
~~~

## getSymlink
- 说明: 获取软连接
- 文档: [GetSymlink](https://help.aliyun.com/zh/oss/developer-reference/getsymlink)
- 参数:
  + `filename`: (string) 文件名称
  + `versionId`: (string) 版本ID

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->getSymlink('test.txt');
~~~

## setTag
- 说明: 设置标签
- 文档: [PutObjectTagging](https://help.aliyun.com/zh/oss/developer-reference/putobjecttagging)
- 参数:
  + `filename`: (string) 文件名称
  + `tagList`: (array) 标签列表[key1=>value1, key2=>value2]
  + `versionId`: (string) 版本ID

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->setTag('test.txt', ['key1' => 'value1', 'key2' => 'value2']);
~~~

## getTag
- 说明: 获取标签
- 文档: [GetObjectTagging](https://help.aliyun.com/zh/oss/developer-reference/getobjecttagging)
- 参数:
  + `filename`: (string) 文件名称
  + `versionId`: (string) 版本ID

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->getTag('test.txt');
~~~

## deleteTag
- 说明: 删除标签
- 文档: [DeleteObjectTagging](https://help.aliyun.com/zh/oss/developer-reference/deleteobjecttagging)
- 参数:
  + `filename`: (string) 文件名称
  + `versionId`: (string) 版本ID

示例
~~~php
(new \lifetime\bridge\ali\oss\Objects())->deleteTag('test.txt');
~~~
