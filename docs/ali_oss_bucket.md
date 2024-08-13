# 阿里云对象存储Bucket相关操作

## list
- 说明: 获取储存空间列表
- 文档: [ListBuckets（GetService）](https://help.aliyun.com/zh/oss/developer-reference/listbuckets)
- 参数
  + `prefix` (string) 前缀
  + `marker` (string) 开始的数据
  + `maxKeys` (int) 返回的最大个数
  + `resourceGroupId` (string) 资源组ID

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->list('log/');
~~~

## regionList
- 说明: 获取区域列表
- 文档: [公共云下OSS Region和Endpoint对照表](https://help.aliyun.com/zh/oss/user-guide/regions-and-endpoints)
- 参数: 无

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->regionList();
~~~

> 如果发现返回的数据与官方文档不符时，请提交issue！

## create
- 说明: 创建存储空间
- 文档: [PutBucket](https://help.aliyun.com/zh/oss/developer-reference/putbucket)
- 参数
  + `name` (string) 空间名称
  + `storageType` (string) 存储类型(Standard-标准存储,IA-低频访问,Archive-归档存储,DeepColdArchive-深度冷归档存储)
  + `dataRedundancyType` (string) 数据容灾类型(LRS-本地冗余,ZRS-同城冗余)
  + `acl` (string) 访问权限(public-read-write-公共读写,public-read-公共读,private-私有)
  + `resourceGroupId` (string) 资源组ID

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->create('bucket-name');
~~~

## delete
- 说明: 删除存储空间
- 文档: [DeleteBucket](https://help.aliyun.com/zh/oss/developer-reference/deletebucket)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->delete('bucket-name');
~~~

## getInfo
- 说明: 获取空间信息
- 文档: [GetBucketInfo](https://help.aliyun.com/zh/oss/developer-reference/getbucketinfo)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getInfo('bucket-name');
~~~

## getLocation
- 说明: 获取位置信息
- 文档: [GetBucketLocation](https://help.aliyun.com/zh/oss/developer-reference/getbucketlocation)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getLocation('bucket-name');
~~~

## getStat
- 说明: 获取状态信息
- 文档: [GetBucketStat](https://help.aliyun.com/zh/oss/developer-reference/getbucketstat)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getStat('bucket-name');
~~~

## createWorm
- 说明: 创建合规保留策略
- 文档: [InitiateBucketWorm](https://help.aliyun.com/zh/oss/developer-reference/initiatebucketworm)
- 参数
  + `name` (string) 空间名称
  + `duration` (int) 时长

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->createWorm('bucket-name', 10);
~~~

## deleteWorm
- 说明: 删除未锁定的合规保留策略
- 文档: [AbortBucketWorm](https://help.aliyun.com/zh/oss/developer-reference/abortbucketworm)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->deleteWorm('bucket-name');
~~~

## lockWorm
- 说明: 删除未锁定的合规保留策略
- 文档: [AbortBucketWorm](https://help.aliyun.com/zh/oss/developer-reference/abortbucketworm)
- 参数
  + `name` (string) 空间名称
  + `wormId` (string) 合规保留策略ID

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->lockWorm('bucket-name', 'worm-id');
~~~

## extendWorm
- 说明: 延长已锁定的合规保留策
- 文档: [ExtendBucketWorm](https://help.aliyun.com/zh/oss/developer-reference/extendbucketworm)
- 参数
  + `name` (string) 空间名称
  + `wormId` (string) 合规保留策略ID
  + `duration` (int) 时长

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->extendWorm('bucket-name', 'worm-id', 10);
~~~

## getWorm
- 说明: 获取合规保留策略信息
- 文档: [GetBucketWorm](https://help.aliyun.com/zh/oss/developer-reference/getbucketworm)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getWorm('bucket-name');
~~~

## setAcl
- 说明: 设置访问权限
- 文档: [PutBucketAcl](https://help.aliyun.com/zh/oss/developer-reference/putbucketacl)
- 参数
  + `name` (string) 空间名称
  + `acl` (string) 访问权限(public-read-write-公共读写,public-read-公共读,private-私有)

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setAcl('bucket-name', 'private');
~~~

## getAcl
- 说明: 获取访问权限
- 文档: [GetBucketAcl](https://help.aliyun.com/zh/oss/developer-reference/getbucketacl)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getAcl('bucket-name');
~~~

## setLifecycle
- 说明: 设置生命周期规则
- 文档: [PutBucketLifecycle](https://help.aliyun.com/zh/oss/developer-reference/putbucketlifecycle)
- 参数
  + `name` (string) 空间名称
  + `ruleList` (array) 规则列表[['ID'=>'rule1','status'=>'Enabled','Expiration'=>['Days'=>1]],[['ID'=>'rule2','status'=>'Enabled','AbortMultipartUpload'=>['Days'=>1]]]]
  + `name` (overlap) 规则是否允许前缀重叠

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setLifecycle('bucket-name', [
    [
        'ID' => 'rule1',
        'Status' => 'Enabled',
        'Prefix' => '/',
        'AbortMultipartUpload'=>[
            'Days'=>1
        ]
    ]
]);
~~~

## getLifecycle
- 说明: 获取生命周期规则
- 文档: [GetBucketLifecycle](https://help.aliyun.com/zh/oss/developer-reference/getbucketlifecycle)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getLifecycle('bucket-name');
~~~

## deleteLifecycle
- 说明: 删除生命周期规则
- 文档: [DeleteBucketLifecycle](https://help.aliyun.com/zh/oss/developer-reference/deletebucketlifecycle)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->deleteLifecycle('bucket-name');
~~~

## setTransferAcceleration
- 说明: 设置传输加速
- 文档: [PutBucketTransferAcceleration](https://help.aliyun.com/zh/oss/developer-reference/putbuckettransferacceleration)
- 参数
  + `name` (string) 空间名称
  + `enabled` (bool) 启用传输加速

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setTransferAcceleration('bucket-name', true);
~~~

## getTransferAcceleration
- 说明: 获取传输加速配置
- 文档: [GetBucketTransferAcceleration](https://help.aliyun.com/zh/oss/developer-reference/getbuckettransferacceleration)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getTransferAcceleration('bucket-name');
~~~

## setVersioning
- 说明: 设置版本控制
- 文档: [PutBucketVersioning](https://help.aliyun.com/zh/oss/developer-reference/putbuckettransferacceleration)
- 参数
  + `name` (string) 空间名称
  + `status` (bool) 是否开启版本控制

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setVersioning('bucket-name', true);
~~~

## getVersioning
- 说明: 获取版本控制配置
- 文档: [GetBucketVersioning](https://help.aliyun.com/zh/oss/developer-reference/getbucketversioning)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getVersioning('bucket-name');
~~~

## getVersionList
- 说明: 获取所有Object的版本信息
- 文档: [ListObjectVersions（GetBucketVersions）](https://help.aliyun.com/zh/oss/developer-reference/listobjectversions)
- 参数
  + `name` (string) 空间名称
  + `delimiter` (string) 对Object名字进行分组的字符
  + `keyMarker` (string) 设定结果从keyMarker之后按字母序开始返回
  + `versionIdMarker` (string) 设定结果从key-marker对象的version-id-marker之后按新旧版本排序开始返回
  + `maxKeys` (string) 限定此次返回Object的最大个数
  + `prefix` (string) 限定返回的Object Key必须以prefix作为前缀
  + `encodingType` (string) 对返回的内容进行编码并指定编码类型

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getVersionList('bucket-name');
~~~

## createReplication
- 说明: 创建复制规则
- 文档: [PutBucketReplication](https://help.aliyun.com/zh/oss/developer-reference/putbucketreplication)
- 参数
  + `name` (string) 空间名称
  + `targetLocation` (string) 目标区域
  + `targetBucket` (string) 目标空间名称
  + `transferType` (string) 指定数据复制时使用的数据传输链路(internal-默认传输链路,oss_acc-传输加速链路)
  + `rtc` (bool) 开启或关闭RTC功能
  + `prefixList` (array) 设置待复制Object的Prefix
  + `action` (string) 指定可以被复制到目标Bucket的操作(ALL-所有操作,PUT-写入操作)
  + `replicationHistorical` (bool) 是否复制历史数据
  + `syncRole` (string) 授权OSS使用哪个角色来进行数据复制
  + `sseKmsEnabled` (bool) 指定OSS是否复制通过SSE-KMS加密创建的对象
  + `replicaKmsKeyID` (string) 指定SSE-KMS密钥ID

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->createReplication('bucket-name','cn-beijing', 'target-bucket-name', 'internal', null, ['test'], null, null, 'oss-dev');
~~~

## setRtc
- 说明: 设置跨区域复制规则时间控制功能
- 文档: [PutBucketRTC](https://help.aliyun.com/zh/oss/developer-reference/putbucketrtc)
- 参数
  + `name` (string) 空间名称
  + `ruleId` (string) 复制规则ID
  + `status` (bool) 是否开启

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setRtc('bucket-name', 'rule-id', true);
~~~

## getReplication
- 说明: 获取数据复制规则
- 文档: [GetBucketReplication](https://help.aliyun.com/zh/oss/developer-reference/getbucketreplication)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getReplication('bucket-name');
~~~

## getReplicationLocation
- 说明: 获取可复制到的目标存储空间所在的地域
- 文档: [GetBucketReplicationLocation](https://help.aliyun.com/zh/oss/developer-reference/getbucketreplicationlocation)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getReplicationLocation('bucket-name');
~~~

## getReplicationProgress
- 说明: 获取数据复制进度
- 文档: [GetBucketReplicationProgress](https://help.aliyun.com/zh/oss/developer-reference/getbucketreplicationprogress)
- 参数
  + `name` (string) 空间名称
  + `ruleId` (string) 复制规则ID

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getReplicationProgress('bucket-name', 'rule-id');
~~~

## deleteReplication
- 说明: 删除数据复制规则
- 文档: [DeleteBucketReplication](https://help.aliyun.com/zh/oss/developer-reference/deletebucketreplication)
- 参数
  + `name` (string) 空间名称
  + `ruleId` (string) 复制规则ID

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->deleteReplication('bucket-name', 'rule-id');
~~~

## setPolicy
- 说明: 设置授权策略
- 文档: [PutBucketPolicy](https://help.aliyun.com/zh/oss/developer-reference/putbucketpolicy)
- 参数
  + `name` (string) 空间名称
  + `policy` (array) 策略

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setPolicy('bucket-name', [
  "Version" => "1",
  "Statement" => [
      [
          "Effect" => "Allow",
          "Action" => [
              "oss:*"
          ],
          "Principal" => [
              "xxxx"
          ],
          "Resource" => [
              "acs:oss:*:xxx:bucket-name",
              "acs:oss:*:xxx:bucket-name/*"
          ]
      ]
  ]
]);
~~~

## getPolicy
- 说明: 获取授权策略
- 文档: [GetBucketPolicy](https://help.aliyun.com/zh/oss/developer-reference/getbucketpolicy)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getPolicy('bucket-name');
~~~

## getPolicyStatus
- 说明: 获取授权策略状态
- 文档: [GetBucketPolicyStatus](https://help.aliyun.com/zh/oss/developer-reference/getbucketpolicystatus)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getPolicyStatus('bucket-name');
~~~

## deletePolicy
- 说明: 删除授权策略
- 文档: [DeleteBucketPolicy](https://help.aliyun.com/zh/oss/developer-reference/deletebucketpolicy)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->deletePolicy('bucket-name');
~~~

## createInventory
- 说明: 创建清单规则
- 文档: [PutBucketInventory](https://help.aliyun.com/zh/oss/developer-reference/putbucketinventory)
- 参数
  + `name` (string) 空间名称
  + `id` (string) 规则ID
  + `enabled` (bool) 是否启用
  + `destination` (array) 存放位置[Format,AccountId,RoleArn,Bucket,Prefix,Encryption]
  + `schedule` (string) 导出周期(Daily-按天导出,Weekly-周导出)
  + `versions` (string) 是否在清单中包含Object版本信息(All-导出所有版本信息,Current-导出当前版本信息)
  + `filter` (array) 过滤条件[Prefix,LastModifyBeginTimeStamp,LastModifyEndTimeStamp,LowerSizeBound,UpperSizeBound,StorageClass]
  + `fieldList` (array) 包含的配置项[Size,LastModifiedDate,ETag,StorageClass,IsMultipartUploaded,EncryptionStatus,ObjectAcl,TaggingCount,ObjectType,Crc64]

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->createInventory('bucket-name');
~~~

## getInventory
- 说明: 获取清单规则
- 文档: [GetBucketInventory](https://help.aliyun.com/zh/oss/developer-reference/getbucketinventory)
- 参数
  + `name` (string) 空间名称
  + `id` (string) 规则ID

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getInventory('bucket-name', 'id');
~~~

## getInventoryList
- 说明: 获取清单规则列表
- 文档: [ListBucketInventory](https://help.aliyun.com/zh/oss/developer-reference/listbucketinventory)
- 参数
  + `name` (string) 空间名称
  + `continuationToken` (string) 指定List操作需要从此token开始

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getInventoryList('bucket-name');
~~~

## deleteInventory
- 说明: 删除清单规则
- 文档: [DeleteBucketInventory](https://help.aliyun.com/zh/oss/developer-reference/deletebucketinventory)
- 参数
  + `name` (string) 空间名称
  + `id` (string) 规则ID

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->deleteInventory('bucket-name', 'id');
~~~

## setLogging
- 说明: 设置日志转存
- 文档: [PutBucketLogging](https://help.aliyun.com/zh/oss/developer-reference/putbucketlogging)
- 参数
  + `name` (string) 空间名称
  + `targetBucket` (string) 指定存储访问日志的Bucket(如果需要关闭，请传入null)
  + `targetPrefix` (string) 指定保存的日志文件前缀

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setLogging('bucket-name', 'target-bucket-name', 'log/');
~~~

## getLogging
- 说明: 获取日志转存配置
- 文档: [GetBucketLogging](https://help.aliyun.com/zh/oss/developer-reference/getbucketlogging)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getLogging('bucket-name');
~~~

## deleteLogging
- 说明: 关闭日志转存配置
- 文档: [DeleteBucketLogging](https://help.aliyun.com/zh/oss/developer-reference/deletebucketlogging)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->deleteLogging('bucket-name');
~~~

## setLoggingUserField
- 说明: 设置日志转存用户定义字段
- 文档: [PutUserDefinedLogFieldsConfig](https://help.aliyun.com/zh/oss/developer-reference/putuserdefinedlogfieldsconfig)
- 参数
  + `name` (string) 空间名称
  + `header` (array) 自定义请求头
  + `param` (array) 自定义查询参数

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setLoggingUserField('bucket-name', ['a' => '1'], ['b' => 2]);
~~~

## getLoggingUserField
- 说明: 获取日志转存用户定义字段
- 文档: [GetUserDefinedLogFieldsConfig](https://help.aliyun.com/zh/oss/developer-reference/getuserdefinedlogfieldsconfig)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getLoggingUserField('bucket-name');
~~~

## deleteLoggingUserField
- 说明: 删除日志转存用户定义字段
- 文档: [DeleteUserDefinedLogFieldsConfig](https://help.aliyun.com/zh/oss/developer-reference/deleteuserdefinedlogfieldsconfig)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->deleteLoggingUserField('bucket-name');
~~~

## setWebsite
- 说明: 设置静态网站规则
- 文档: [PutBucketWebsite](https://help.aliyun.com/zh/oss/developer-reference/putbucketwebsite)
- 参数
  + `name` (string) 空间名称
  + `indexDocument` (array) 默认主页配置[Suffix,SupportSubDir,Type]
  + `errorDocument` (array) 404页面配置[Key,HttpStatus]
  + `ruleList` (array) 路由列表[['RuleNumber' => 1, 'Condition' => ['KeyPrefixEquals' => 'anc'], 'Redirect' => ['RedirectType' => 'AliCDN']]]

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setWebsite('bucket-name', [
  'Suffix' => 'index.html'
]);
~~~

## getWebsite
- 说明: 获取静态网站规则
- 文档: [GetBucketWebsite](https://help.aliyun.com/zh/oss/developer-reference/getbucketwebsite)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getWebsite('bucket-name');
~~~

## deleteWebsite
- 说明: 关闭静态网站规则
- 文档: [DeleteBucketWebsite](https://help.aliyun.com/zh/oss/developer-reference/deletebucketwebsite)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->deleteWebsite('bucket-name');
~~~

## setReferer
- 说明: 设置防盗链
- 文档: [PutBucketReferer](https://help.aliyun.com/zh/oss/developer-reference/putbucketreferer)
- 参数
  + `name` (string) 空间名称
  + `allowEmpty` (bool) 指定是否允许Referer字段为空的请求访问OSS
  + `refererList` (array) 访问白名单
  + `allowTruncateQuery` (bool) 指定匹配Referer时，是否截断URL中的QueryString
  + `truncatePath` (bool) 指定匹配Referer时，是否截断URL中包括Path在内的后续所有部分
  + `refererBlackList` (array) 访问黑名单

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setReferer('bucket-name', false, ['https://*'], false);
~~~

## getReferer
- 说明: 获取防盗链设置
- 文档: [GetBucketReferer](https://help.aliyun.com/zh/oss/developer-reference/getbucketreferer)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getReferer('bucket-name');
~~~

## setTag
- 说明: 设置标签
- 文档: [PutBucketTags](https://help.aliyun.com/zh/oss/developer-reference/putbuckettags)
- 参数
  + `name` (string) 空间名称
  + `tagList` (array) 标签列表['key1' => 'value1', 'key2' => 'value2']

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setTag('bucket-name', ['a' => 1, 'b' => 2]);
~~~

## getTag
- 说明: 获取标签
- 文档: [GetBucketTags](https://help.aliyun.com/zh/oss/developer-reference/getbuckettags)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getTag('bucket-name');
~~~

## deleteTag
- 说明: 删除标签
- 文档: [DeleteBucketTags](https://help.aliyun.com/zh/oss/developer-reference/deletebuckettags)
- 参数
  + `name` (string) 空间名称
  + `keyList` (array) 要删除的Key列表(如果为空，表示删除所有)

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->deleteTag('bucket-name', ['a']);
~~~

## setEncryption
- 说明: 设置加密规则
- 文档: [PutBucketEncryption](https://help.aliyun.com/zh/oss/developer-reference/putbucketencryption)
- 参数
  + `name` (string) 空间名称
  + `sseAlgorithm` (string) 设置服务器端默认加密方式(KMS,AES256,SM4)
  + `kmsDataEncryption` (string) 指定Object的加密算法。如果未指定此选项，表明Object使用AES256加密算法
  + `kmsMasterKeyID` (string) 当SSEAlgorithm值为KMS，且使用指定的密钥加密时，需输入KMSMasterKeyID。其他情况下，必须为空

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setEncryption('bucket-name', 'AES256');
~~~

## getEncryption
- 说明: 获取加密规则
- 文档: [GetBucketEncryption](https://help.aliyun.com/zh/oss/developer-reference/getbucketencryption)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getEncryption('bucket-name');
~~~

## deleteEncryption
- 说明: 删除加密规则
- 文档: [DeleteBucketEncryption](https://help.aliyun.com/zh/oss/developer-reference/deletebucketencryption)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->deleteEncryption('bucket-name');
~~~

## setRequestPayment
- 说明: 设置请求者付费
- 文档: [PutBucketRequestPayment](https://help.aliyun.com/zh/oss/developer-reference/putbucketrequestpayment)
- 参数
  + `name` (string) 空间名称
  + `payer` (string) 指定付费类型(BucketOwner-由Bucket拥有者付费,Requester-由请求者付费)

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setRequestPayment('bucket-name', 'BucketOwner');
~~~

## getRequestPayment
- 说明: 获取请求者付费配置
- 文档: [GetBucketRequestPayment](https://help.aliyun.com/zh/oss/developer-reference/getbucketrequestpayment)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getRequestPayment('bucket-name');
~~~

## setCors
- 说明: 设置跨域资源共享
- 文档: [PutBucketCors](https://help.aliyun.com/zh/oss/developer-reference/putbucketcors)
- 参数
  + `name` (string) 空间名称
  + `ruleList` (array) 规则列表[['AllowedOrigin'=>['*'],'AllowedMethod'=>['GET'],'AllowedHeader'=>['Token'],'ExposeHeader'=>['Etag'],'MaxAgeSeconds'=>10]]
  + `responseVary` (bool) 是否返回Vary: Origin头

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setCors('bucket-name', ['AllowedOrigin' => ['*'],'AllowedMethod'=>['GET']]);
~~~

## getCors
- 说明: 获取跨域资源共享配置
- 文档: [GetBucketCors](https://help.aliyun.com/zh/oss/developer-reference/getbucketcors)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getCors('bucket-name');
~~~

## deleteCors
- 说明: 删除跨域资源共享配置
- 文档: [DeleteBucketCors](https://help.aliyun.com/zh/oss/developer-reference/deletebucketcors)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->deleteCors('bucket-name');
~~~

## setAccessMonitor
- 说明: 设置访问跟踪
- 文档: [PutBucketAccessMonitor](https://help.aliyun.com/zh/oss/developer-reference/putbucketaccessmonitor)
- 参数
  + `name` (string) 空间名称
  + `status` (bool) 是否启用

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setAccessMonitor('bucket-name', true);
~~~

## getAccessMonitor
- 说明: 获取访问跟踪配置
- 文档: [GetBucketAccessMonitor](https://help.aliyun.com/zh/oss/developer-reference/getbucketaccessmonitor)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getAccessMonitor('bucket-name');
~~~

## openMetaQuery
- 说明: 开启元数据管理
- 文档: [OpenMetaQuery](https://help.aliyun.com/zh/oss/developer-reference/openmetaquery)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->openMetaQuery('bucket-name');
~~~

## getMetaQuery
- 说明: 获取元数据索引库信息
- 文档: [GetMetaQueryStatus](https://help.aliyun.com/zh/oss/developer-reference/getmetaquerystatus)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getMetaQuery('bucket-name');
~~~

## doMetaQuery
- 说明: 查询满足指定条件的文件并按照指定字段和排序方式列出文件信息
- 文档: [DoMetaQuery](https://help.aliyun.com/zh/oss/developer-reference/dometaquery)
- 参数
  + `name` (string) 空间名称
  + `query` (string) 查询条件
  + `nextToken` (string) 用于翻页的token
  + `maxResults` (int) 返回Object的最大个数
  + `sort` (string) 对指定字段排序
  + `order` (string) 排序方式(asc-升序,desc-降序)
  + `aggregationList` (array) 聚合操作列表[['Field' => 'a', 'Operation' => 'min']]

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->doMetaQuery('bucket-name', json_encode(['Field' => 'Size', 'Value' => 10, 'Operation' => 'gte']));
~~~

## closeMetaQuery
- 说明: 关闭元数据管理
- 文档: [CloseMetaQuery](https://help.aliyun.com/zh/oss/developer-reference/closemetaquery)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->closeMetaQuery('bucket-name');
~~~

## setResourceGroupId
- 说明: 设置资源组
- 文档: [PutBucketResourceGroup](https://help.aliyun.com/zh/oss/developer-reference/putbucketresourcegroup)
- 参数
  + `name` (string) 空间名称
  + `resourceGroupId` (string) 资源组ID,如果此项值设置为空，则表示移动到默认资源组

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setResourceGroupId('bucket-name', 'resource-group-id');
~~~

## getResourceGroupId
- 说明: 获取资源组配置
- 文档: [GetBucketResourceGroup](https://help.aliyun.com/zh/oss/developer-reference/getbucketresourcegroup)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getResourceGroupId('bucket-name');
~~~

## createCnameToken
- 说明: 创建域名所有权验证所需的Token
- 文档: [CreateCnameToken](https://help.aliyun.com/zh/oss/developer-reference/createcnametoken)
- 参数
  + `name` (string) 空间名称
  + `domain` (string) 自定义域名

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->createCnameToken('bucket-name', 'xxx.domain.com');
~~~

## getCnameToken
- 说明: 获取已创建的CnameToken
- 文档: [GetCnameToken](https://help.aliyun.com/zh/oss/developer-reference/getcnametoken)
- 参数
  + `name` (string) 空间名称
  + `domain` (string) 自定义域名

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getCnameToken('bucket-name', 'xxx.domain.com');
~~~

## bindCname
- 说明: 绑定自定义域名
- 文档: [PutCname](https://help.aliyun.com/zh/oss/developer-reference/putcname)
- 参数
  + `name` (string) 空间名称
  + `domain` (string) 自定义域名
  + `delete` (bool) 是否删除证书
  + `force` (bool) 是否强制覆盖证书
  + `certId` (string) 证书ID
  + `publicKey` (string) 证书公钥
  + `privateKey` (string) 证书私钥
  + `previousCertId` (string) 当前证书ID

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->bindCname('bucket-name', 'xxx.domain.com');
~~~

## getCname
- 说明: 获取已绑定的域名列表
- 文档: [ListCname](https://help.aliyun.com/zh/oss/developer-reference/listcname)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getCname('bucket-name');
~~~

## deleteCname
- 说明: 删除已绑定的域名
- 文档: [DeleteCname](https://help.aliyun.com/zh/oss/developer-reference/deletecname)
- 参数
  + `name` (string) 空间名称
  + `domain` (string) 自定义域名

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->deleteCname('bucket-name', 'xxx.domain.com');
~~~

## createImageStyle
- 说明: 创建图片样式
- 文档: [PutStyle](https://help.aliyun.com/zh/oss/developer-reference/putstyle)
- 参数
  + `name` (string) 空间名称
  + `styleName` (string) 样式名称
  + `style` (string) 样式

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->createImageStyle('bucket-name', 'style1', 'image/resize,p_50');
~~~

## getImageStyle
- 说明: 创建图片样式
- 文档: [GetStyle](https://help.aliyun.com/zh/oss/developer-reference/getstyle)
- 参数
  + `name` (string) 空间名称
  + `styleName` (string) 样式名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getImageStyle('bucket-name', 'style1');
~~~

## getImageStyleList
- 说明: 获取所有图片样式列表
- 文档: [ListStyle](https://help.aliyun.com/zh/oss/developer-reference/liststyle)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getImageStyleList('bucket-name');
~~~

## deleteImageStyle
- 说明: 删除图片样式
- 文档: [DeleteStyle](https://help.aliyun.com/zh/oss/developer-reference/deletestyle)
- 参数
  + `name` (string) 空间名称
  + `styleName` (string) 样式名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->deleteImageStyle('bucket-name', 'style1');
~~~

## setTls
- 说明: 设置TLS配置
- 文档: [PutBucketHttpsConfig](https://help.aliyun.com/zh/oss/developer-reference/putbuckethttpsconfig)
- 参数
  + `name` (string) 空间名称
  + `tlsVersion` (array) TLS版本(TLSv1.0,TLSv1.1,TLSv1.2,TLSv1.3,空数组表示关闭TLS)

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setTls('bucket-name', ['TLSv1.2']);
~~~

## getTls
- 说明: 获取TLS配置
- 文档: [GetBucketHttpsConfig](https://help.aliyun.com/zh/oss/developer-reference/getbuckethttpsconfig)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getTls('bucket-name');
~~~

## createDataRedundancyTransition
- 说明: 创建冗余转换任务
- 文档: [CreateBucketDataRedundancyTransition](https://help.aliyun.com/zh/oss/developer-reference/createbucketdataredundancytransition)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->createDataRedundancyTransition('bucket-name');
~~~

## getDataRedundancyTransition
- 说明: 获取冗余转换任务
- 文档: [GetBucketDataRedundancyTransition](https://help.aliyun.com/zh/oss/developer-reference/getbucketdataredundancytransition)
- 参数
  + `name` (string) 空间名称
  + `id` (string) 转换任务ID

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getDataRedundancyTransition('bucket-name', 'id');
~~~

## deleteDataRedundancyTransition
- 说明: 删除冗余转换任务
- 文档: [DeleteBucketDataRedundancyTransition](https://help.aliyun.com/zh/oss/developer-reference/deletebucketdataredundancytransition)
- 参数
  + `name` (string) 空间名称
  + `id` (string) 转换任务ID

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->deleteDataRedundancyTransition('bucket-name', 'id');
~~~

## getUserDataRedundancyTransitionList
- 说明: 获取请求者所有转换任务
- 文档: [ListUserDataRedundancyTransition](https://help.aliyun.com/zh/oss/developer-reference/listuserdataredundancytransition)
- 参数
  + `name` (string) 空间名称
  + `continuationToken` (string) 指定List操作需要从此token开始
  + `maxKeys` (int) 限定此次返回任务的最大个数

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getUserDataRedundancyTransitionList('bucket-name');
~~~

## getDataRedundancyTransitionList
- 说明: 获取所有转换任务
- 文档: [ListBucketDataRedundancyTransition](https://help.aliyun.com/zh/oss/developer-reference/listbucketdataredundancytransition)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getDataRedundancyTransitionList('bucket-name');
~~~

## createAccessPoint
- 说明: 创建接入点
- 文档: [CreateAccessPoint](https://help.aliyun.com/zh/oss/developer-reference/createaccesspoint)
- 参数
  + `name` (string) 空间名称
  + `pointName` (string) 接入点名称
  + `networkOrigin` (string) 接入点网络来源(vpc-限制仅支持通过指定的VPC ID访问接入点,internet-同时支持通过外网和内网Endpoint访问接入点)
  + `vpcId` (string) 仅当NetworkOrigin取值为vpc时，需要指定VPC ID

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->createAccessPoint('bucket-name', 'ap1', 'internet');
~~~

## getAccessPoint
- 说明: 获取接入点
- 文档: [GetAccessPoint](https://help.aliyun.com/zh/oss/developer-reference/getaccesspoint)
- 参数
  + `name` (string) 空间名称
  + `pointName` (string) 接入点名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getAccessPoint('bucket-name', 'ap1');
~~~

## deleteAccessPoint
- 说明: 删除接入点
- 文档: [DeleteAccessPoint](https://help.aliyun.com/zh/oss/developer-reference/deleteaccesspoint)
- 参数
  + `name` (string) 空间名称
  + `pointName` (string) 接入点名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->deleteAccessPoint('bucket-name', 'ap1');
~~~

## getAccessPointList
- 说明: 获取接入点列表
- 文档: [ListAccessPoints](https://help.aliyun.com/zh/oss/developer-reference/listaccesspoints)
- 参数
  + `name` (string) 空间名称
  + `continuationToken` (string) 指定List操作需要从此token开始
  + `maxKeys` (int) 指定返回接入点的最大数量

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getAccessPointList('bucket-name');
~~~

## setAccessPointPolicy
- 说明: 设置接入点策略
- 文档: [PutAccessPointPolicy](https://help.aliyun.com/zh/oss/developer-reference/putaccesspointpolicy)
- 参数
  + `name` (string) 空间名称
  + `pointName` (string) 接入点名称
  + `policy` (array) 策略

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setAccessPointPolicy('bucket-name', 'ap1', [
  "Version" => "1",
  "Statement" => [
      [
          "Effect" => "Allow",
          "Action" => [
              "oss:*"
          ],
          "Principal" => [
              "xxxx"
          ],
          "Resource" => [
              'acs:oss:cn-beijing:xxxx:accesspoint/ap1',
              "acs:oss:cn-beijing:xxxx:accesspoint/ap1/object/*"
          ]
      ]
  ]
]);
~~~

## getAccessPointPolicy
- 说明: 获取接入点策略配置
- 文档: [GetAccessPointPolicy](https://help.aliyun.com/zh/oss/developer-reference/getaccesspointpolicy)
- 参数
  + `name` (string) 空间名称
  + `pointName` (string) 接入点名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getAccessPointPolicy('bucket-name', 'ap1');
~~~

## deleteAccessPointPolicy
- 说明: 获取接入点策略配置
- 文档: [DeleteAccessPointPolicy](https://help.aliyun.com/zh/oss/developer-reference/deleteaccesspointpolicy)
- 参数
  + `name` (string) 空间名称
  + `pointName` (string) 接入点名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->deleteAccessPointPolicy('bucket-name', 'ap1');
~~~

## setGlobalPublicAccessBlock
- 说明: 设置全局阻止公共访问
- 文档: [PutPublicAccessBlock](https://help.aliyun.com/zh/oss/developer-reference/putpublicaccessblock)
- 参数
  + `status` (bool) 是否开启阻止公共访问

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setGlobalPublicAccessBlock(false);
~~~

## getGlobalPublicAccessBlock
- 说明: 设置全局阻止公共访问
- 文档: [GetPublicAccessBlock](https://help.aliyun.com/zh/oss/developer-reference/getpublicaccessblock)
- 参数: 无

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getGlobalPublicAccessBlock();
~~~

## deleteGlobalPublicAccessBlock
- 说明: 删除全局阻止公共访问配置
- 文档: [DeletePublicAccessBlock](https://help.aliyun.com/zh/oss/developer-reference/deletepublicaccessblock)
- 参数: 无

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->deleteGlobalPublicAccessBlock();
~~~

## setPublicAccessBlock
- 说明: 设置阻止公共访问
- 文档: [PutBucketPublicAccessBlock](https://help.aliyun.com/zh/oss/developer-reference/putbucketpublicaccessblock)
- 参数
  + `name` (string) 空间名称
  + `status` (bool) 是否开启阻止公共访问

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setPublicAccessBlock('bucket-name', true);
~~~

## getPublicAccessBlock
- 说明: 获取阻止公共访问配置
- 文档: [GetBucketPublicAccessBlock](https://help.aliyun.com/zh/oss/developer-reference/getbucketpublicaccessblock)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getPublicAccessBlock('bucket-name');
~~~

## deletePublicAccessBlock
- 说明: 删除阻止公共访问配置
- 文档: [DeleteBucketPublicAccessBlock](https://help.aliyun.com/zh/oss/developer-reference/deletebucketpublicaccessblock)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->deletePublicAccessBlock('bucket-name');
~~~

## setAccessPointPublicAccessBlock
- 说明: 设置接入点阻止公共访问
- 文档: [PutAccessPointPublicAccessBlock](https://help.aliyun.com/zh/oss/developer-reference/putaccesspointpublicaccessblock)
- 参数
  + `name` (string) 空间名称
  + `pointName` (string) 接入点名称
  + `status` (bool) 是否开启阻止公共访问

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setAccessPointPublicAccessBlock('bucket-name', 'ap1', true);
~~~

## getAccessPointPublicAccessBlock
- 说明: 获取接入点阻止公共访问配置
- 文档: [GetAccessPointPublicAccessBlock](https://help.aliyun.com/zh/oss/developer-reference/getaccesspointpublicaccessblock)
- 参数
  + `name` (string) 空间名称
  + `pointName` (string) 接入点名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getAccessPointPublicAccessBlock('bucket-name', 'ap1');
~~~

## deleteAccessPointPublicAccessBlock
- 说明: 删除接入点阻止公共访问配置
- 文档: [DeleteAccessPointPublicAccessBlock](https://help.aliyun.com/zh/oss/developer-reference/deleteaccesspointpublicaccessblock)
- 参数
  + `name` (string) 空间名称
  + `pointName` (string) 接入点名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->deleteAccessPointPublicAccessBlock('bucket-name', 'ap1');
~~~

## setArchiveDirectRead
- 说明: 设置归档直读配置
- 文档: [PutBucketArchiveDirectRead](https://help.aliyun.com/zh/oss/developer-reference/putbucketarchivedirectread)
- 参数
  + `name` (string) 空间名称
  + `status` (bool) 是否开启归档直读

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->setArchiveDirectRead('bucket-name', true);
~~~

## getArchiveDirectRead
- 说明: 获取归档直读配置
- 文档: [GetBucketArchiveDirectRead](https://help.aliyun.com/zh/oss/developer-reference/getbucketarchivedirectread)
- 参数
  + `name` (string) 空间名称

示例
~~~php
$result = (new \lifetime\bridge\Ali\OSS\Bucket())->getArchiveDirectRead('bucket-name');
~~~
