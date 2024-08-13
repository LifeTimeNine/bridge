<?php

declare(strict_types = 1);

namespace lifetime\bridge\ali\OSS;

use lifetime\bridge\Tools;
use lifetime\bridge\Exception\AliOssResponseException;
use lifetime\bridge\Exception\InvalidConfigException;
use lifetime\bridge\Exception\InvalidDecodeException;
use lifetime\bridge\Exception\InvalidResponseException;
use lifetime\bridge\Request;

/**
 * 阿里云对象存储 Bucket 操作
 * @throws InvalidConfigException
 */
class Bucket extends Basic
{
    /**
     * 获取储存空间列表
     * @access  public
     * @param   string  $prefix             前缀
     * @param   string  $marker             开始的数据
     * @param   int     $maxKeys            返回的最大个数
     * @param   string  $resourceGroupId    资源组ID
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function list(string $prefix = null, string $marker = null, int $maxKeys = null, string $resourceGroupId = null): array
    {
        $header = [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED];
        if (!empty($resourceGroupId)) $header['x-oss-resource-group-id'] = $resourceGroupId;

        $query = [];
        if (!empty($prefix)) $query['prefix'] = $prefix;
        if (!empty($marker)) $query['marker'] = $marker;
        if (!empty($maxKeys)) $query['max-keys'] = $maxKeys;

        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            null,
            null,
            $header,
            $query
        );
    }

    /**
     * 获取区域列表
     * @access  public
     * @return  array
     */
    public function regionList(): array
    {
        $result = [];
        foreach(self::$regionList as $key => $value) {
            $result[] = ['id' => $key, 'name' => $value['name']];
        }
        return $result;
    }

    /**
     * 创建存储空间
     * @access  public
     * @param   string  $name                   名称
     * @param   string  $storageType            存储类型(Standard-标准存储,IA-低频访问,Archive-归档存储,DeepColdArchive-深度冷归档存储)
     * @param   string  $dataRedundancyType     数据容灾类型(LRS-本地冗余,ZRS-同城冗余)
     * @param   string  $acl                    访问权限(public-read-write-公共读写,public-read-公共读,private-私有)
     * @param   string  $resourceGroupId        资源组ID
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function create(string $name, string $storageType = null, string $dataRedundancyType = null, string $acl = null, string $resourceGroupId = null): array
    {
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML
        ];
        if (!empty($acl)) $header['x-oss-acl'] = $acl;
        if (!empty($resourceGroupId)) $header['x-oss-resource-group-id'] = $resourceGroupId;
        $body = [];
        if (!empty($storageType)) $body['StorageClass'] = $storageType;
        if (!empty($dataRedundancyType)) $body['DataRedundancyType'] = $dataRedundancyType;
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '',
            $name,
            null,
            $header,
            [],
            Tools::arrToXml($body, 'CreateBucketConfiguration')
        );
    }

    /**
     * 删除存储空间
     * @access  public
     * @param   string  $name   名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function delete(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            "/",
            $name
        );
    }

    /**
     * 获取空间信息
     * @access  public
     * @param   string  $name   名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getInfo(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['bucketInfo'=> null]
        )['Bucket'];
    }

    /**
     * 获取位置信息
     * @access  public
     * @param   string  $name   名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getLocation(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['location'=> null]
        );
    }

    /**
     * 获取状态信息
     * @access  public
     * @param   string  $name   名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getStat(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['stat'=> null]
        );
    }

    /**
     * 创建合规保留策略
     * @access  public
     * @param   string  $name       空间名称
     * @param   int     $duration   时长
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function createWorm(string $name, int $duration): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_POST,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['worm' => null],
            Tools::arrToXml(['RetentionPeriodInDays' => $duration], 'InitiateWormConfiguration')
        );
    }

    /**
     * 删除未锁定的合规保留策略
     * @access  public
     * @param   string  $name       空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deleteWorm(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            '/',
            $name,
            null,
            [],
            ['worm' => null]
        );
    }

    /**
     * 锁定合规保留策略
     * @access  public
     * @param   string  $name       空间名称
     * @param   string  $wormId     合规保留策略ID
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function lockWorm(string $name, string $wormId): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_POST,
            '/',
            $name,
            null,
            [],
            ['wormId' => $wormId]
        );
    }

    /**
     * 延长已锁定的合规保留策
     * @access  public
     * @param   string  $name       空间名称
     * @param   string  $wormId     合规保留策略ID
     * @param   int     $duration   时长
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function extendWorm(string $name, string $wormId, int $duration): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_POST,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['wormId' => $wormId, 'wormExtend' => null],
            Tools::arrToXml(['RetentionPeriodInDays' => $duration], 'ExtendWormConfiguration')
        );
    }

    /**
     * 获取合规保留策略信息
     * @access  public
     * @param   string  $name       空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getWorm(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['worm' => null]
        );
    }

    /**
     * 设置访问权限
     * @access  public
     * @param   string  $name   空间名称
     * @param   string  $acl    访问权限(public-read-write-公共读写,public-read-公共读,private-私有)
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setAcl(string $name, string $acl): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            ['x-oss-acl' => $acl],
            ['acl' => null]
        );
    }

    /**
     * 获取访问权限
     * @access  public
     * @param   string  $name   空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getAcl(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['acl' => null]
        );
    }

    /**
     * 设置生命周期规则
     * @access  public
     * @param   string          $name                   空间名称
     * @param   array           $ruleList               规则列表[['ID'=>'rule1','status'=>'Enabled','Expiration'=>['Days'=>1]],[['ID'=>'rule2','status'=>'Enabled','AbortMultipartUpload'=>['Days'=>1]]]]
     * @param   bool            $overlap                规则是否允许前缀重叠
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setLifecycle(string $name, array $ruleList, ?bool $overlap = null): array
    {
        $header = [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML];
        if (!is_null($overlap)) $header['x-oss-allow-same-action-overlap'] = $overlap ? 'true' : 'false';
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            $header,
            ['lifecycle' => null],
            Tools::arrToXml(['Rule' => $ruleList], 'LifecycleConfiguration')
        );
    }

    /**
     * 获取生命周期规则
     * @access  public
     * @param   string  $name   空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getLifecycle(string $name):  array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['lifecycle' => null]
        );
    }

    /**
     * 删除生命周期规则
     * @access  public
     * @param   string  $name   空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deleteLifecycle(string $name):  array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            '/',
            $name,
            null,
            [],
            ['lifecycle' => null]
        );
    }

    /**
     * 设置传输加速
     * @access  public
     * @param   string  $name       空间名称
     * @param   bool    $enabled    启用传输加速
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setTransferAcceleration(string $name, bool $enabled): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['transferAcceleration' => null],
            Tools::arrToXml([
                'Enabled' => $enabled ? 'true' : 'false'
            ], 'TransferAccelerationConfiguration')
        );
    }

    /**
     * 获取传输加速配置
     * @access  public
     * @param   string  $name       空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getTransferAcceleration(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['transferAcceleration' => null]
        );
    }

    /**
     * 设置版本控制
     * @access  public
     * @param   string  $name       空间名称
     * @param   bool    $status     是否开启版本控制
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setVersioning(string $name, bool $status): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['versioning' => null],
            Tools::arrToXml([
                'Status' => $status ? 'Enabled' : 'Suspended'
            ], 'VersioningConfiguration')
        );
    }

    /**
     * 获取版本控制配置
     * @access  public
     * @param   string  $name       空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getVersioning(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['versioning' => null]
        );
    }

    /**
     * 获取所有Object的版本信息
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $delimiter          对Object名字进行分组的字符
     * @param   string  $keyMarker          设定结果从keyMarker之后按字母序开始返回
     * @param   string  $versionIdMarker    设定结果从key-marker对象的version-id-marker之后按新旧版本排序开始返回
     * @param   string  $maxKeys            限定此次返回Object的最大个数
     * @param   string  $prefix             限定返回的Object Key必须以prefix作为前缀
     * @param   string  $encodingType       对返回的内容进行编码并指定编码类型
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getVersionList(string $name, string $delimiter = null, string $keyMarker = null, string $versionIdMarker = null, string $maxKeys = null, string $prefix = null, string $encodingType = null): array
    {
        $query = [];
        if (!empty($delimiter)) $query['delimiter'] = $delimiter;
        if (!empty($keyMarker)) $query['key-marker'] = $keyMarker;
        if (!empty($versionIdMarker)) $query['version-id-marker'] = $versionIdMarker;
        if (!empty($maxKeys)) $query['max-keys'] = $maxKeys;
        if (!empty($prefix)) $query['prefix'] = $prefix;
        if (!empty($encodingType)) $query['encoding-type'] = $encodingType;
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            $query
        );
    }

    /**
     * 创建复制规则
     * @access  public
     * @param   string  $name                   空间名称
     * @param   string  $targetLocation         目标区域
     * @param   string  $targetBucket           目标空间名称
     * @param   string  $transferType           指定数据复制时使用的数据传输链路(internal-默认传输链路,oss_acc-传输加速链路)
     * @param   bool    $rtc                    开启或关闭RTC功能
     * @param   array   $prefixList             设置待复制Object的Prefix
     * @param   string  $action                 指定可以被复制到目标Bucket的操作(ALL-所有操作,PUT-写入操作)
     * @param   bool    $replicationHistorical  是否复制历史数据
     * @param   string  $syncRole               授权OSS使用哪个角色来进行数据复制
     * @param   bool    $sseKmsEnabled          指定OSS是否复制通过SSE-KMS加密创建的对象
     * @param   string  $replicaKmsKeyID        指定SSE-KMS密钥ID
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function createReplication(
        string $name,
        string $targetLocation,
        string $targetBucket,
        string $transferType = 'internal',
        ?bool $rtc,
        array $prefixList = [],
        string $action = null,
        ?bool $replicationHistorical = null,
        string $syncRole = null,
        ?bool $sseKmsEnabled = null,
        string $replicaKmsKeyID = null
    ): array
    {
        $rule = [
            'Destination' => [
                'Bucket' => $targetBucket,
                'Location' => $targetLocation,
                'TransferType' => $transferType
            ]
        ];
        if (!is_null($rtc)) $rule['RTC'] = ['Status' => $rtc ? 'enabled' : 'disabled'];
        if (!empty($prefixList)) $rule['PrefixSet'] = ['Prefix' => $prefixList];
        if (!empty($action)) $rule['Action'] = $action;
        if (!is_null($replicationHistorical)) $rule['HistoricalObjectReplication'] = $replicationHistorical ? 'enabled' : 'disabled';
        if (!empty($syncRole)) $rule['SyncRole'] = $syncRole;
        if (!is_null($sseKmsEnabled)) $rule['SourceSelectionCriteria'] = ['SseKmsEncryptedObjects' => ['Status' => $sseKmsEnabled ? 'Enabled' : 'Disabled']];
        if (!empty($replicaKmsKeyID)) $rule['EncryptionConfiguration'] = ['ReplicaKmsKeyID' => $replicaKmsKeyID];
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_POST,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['replication' => null, 'comp' => 'add'],
            Tools::arrToXml(['Rule' => $rule], 'ReplicationConfiguration')
        );
    }

    /**
     * 设置跨区域复制规则时间控制功能
     * @access  public
     * @param   string  $name       空间名称
     * @param   string  $ruleId     复制规则ID
     * @param   bool    $status     是否开启
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setRtc(string $name, string $ruleId, bool $status): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['rtc' => null],
            Tools::arrToXml([
                'RTC' => ['Status' => $status ? 'enabled' : 'disabled'],
                'ID' => $ruleId
            ], 'ReplicationRule')
        );
    }

    /**
     * 获取数据复制规则
     * @access  public
     * @param   string  $name       空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getReplication(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['replication' => null]
        );
    }

    /**
     * 获取可复制到的目标存储空间所在的地域
     * @access  public
     * @param   string  $name       空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getReplicationLocation(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['replicationLocation' => null]
        );
    }

    /**
     * 获取数据复制进度
     * @access  public
     * @param   string  $name       空间名称
     * @param   string  $ruleId     复制规则ID
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getReplicationProgress(string $name, string $ruleId): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['replicationProgress' => null, 'rule-id' => $ruleId]
        );
    }

    /**
     * 删除数据复制规则
     * @access  public
     * @param   string  $name       空间名称
     * @param   string  $ruleId     复制规则ID
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deleteReplication(string $name, string $ruleId): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_POST,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['replication' => null, 'comp' => 'delete'],
            Tools::arrToXml(['ID' => $ruleId], 'ReplicationRules')
        );
    }

    /**
     * 设置授权策略
     * @access  public
     * @param   string  $name       空间名称
     * @param   array   $policy     策略
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setPolicy(string $name, array $policy): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['policy' => null],
            json_encode($policy, JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * 获取授权策略
     * @access  public
     * @param   string  $name       空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getPolicy(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['policy' => null]
        );
    }

    /**
     * 获取授权策略状态
     * @access  public
     * @param   string  $name       空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getPolicyStatus(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['policyStatus' => null]
        );
    }

    /**
     * 删除授权策略
     * @access  public
     * @param   string  $name       空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deletePolicy(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['policy' => null]
        );
    }

    /**
     * 创建清单规则
     * @access  public
     * @param   string  $name           空间名称
     * @param   string  $id             规则ID
     * @param   bool    $enabled        是否启用
     * @param   array   $destination    存放位置[Format,AccountId,RoleArn,Bucket,Prefix,Encryption]
     * @param   string  $schedule       导出周期(Daily-按天导出,Weekly-周导出)
     * @param   string  $versions       是否在清单中包含Object版本信息(All-导出所有版本信息,Current-导出当前版本信息)
     * @param   array   $filter         过滤条件[Prefix,LastModifyBeginTimeStamp,LastModifyEndTimeStamp,LowerSizeBound,UpperSizeBound,StorageClass]
     * @param   array   $fieldList      包含的配置项[Size,LastModifiedDate,ETag,StorageClass,IsMultipartUploaded,EncryptionStatus,ObjectAcl,TaggingCount,ObjectType,Crc64]
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function createInventory(string $name, string $id, bool $enabled, array $destination, string $schedule, string $versions, array $filter = [], array $fieldList = []): array
    {
        $body = [
            'Id' => $id,
            'IsEnabled' => $enabled ? 'true' : 'false',
            'Destination' => ['OSSBucketDestination' => $destination],
            'Schedule' => ['Frequency' => $schedule],
            'IncludedObjectVersions' => $versions
        ];
        if (!empty($filter)) $body['Filter'] = $filter;
        if (!empty($fieldList)) $body['OptionalFields'] = ['Field' => $fieldList];
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['inventory' => null, 'inventoryId' => $id],
            Tools::arrToXml($body, 'InventoryConfiguration')
        );
    }

    /**
     * 获取清单规则
     * @access  public
     * @param   string  $name           空间名称
     * @param   string  $id             规则ID
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getInventory(string $name, string $id): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['inventory' => null, 'inventoryId' => $id]
        );
    }

    /**
     * 获取清单规则列表
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $continuationToken  指定List操作需要从此token开始
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getInventoryList(string $name, string $continuationToken = null): array
    {
        $query = ['inventory' => null];
        if (!empty($continuationToken)) {
            $query['continuation-token'] = $continuationToken;
        }
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            $query
        );
    }

    /**
     * 删除清单规则
     * @access  public
     * @param   string  $name           空间名称
     * @param   string  $id             规则ID
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deleteInventory(string $name, string $id): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['inventory' => null, 'inventoryId' => $id]
        );
    }

    /**
     * 设置日志转存
     * @access  public
     * @param   string  $name           空间名称
     * @param   string  $targetBucket   指定存储访问日志的Bucket(如果需要关闭，请传入null)
     * @param   string  $targetPrefix   指定保存的日志文件前缀
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setLogging(string $name, string $targetBucket = null, string $targetPrefix = null): array
    {
        $body = $enabled = [];
        if (!empty($targetBucket)) $enabled['TargetBucket'] = $targetBucket;
        if (!empty($targetPrefix)) $enabled['TargetPrefix'] = $targetPrefix;
        if (!empty($enabled)) {
            $body['LoggingEnabled'] = $enabled;
        }
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['logging' => null],
            Tools::arrToXml($body, 'BucketLoggingStatus')
        );
    }

    /**
     * 获取日志转存配置
     * @access  public
     * @param   string  $name           空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getLogging(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['logging' => null]
        );
    }

    /**
     * 关闭日志转存配置
     * @access  public
     * @param   string  $name           空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deleteLogging(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['logging' => null]
        );
    }

    /**
     * 设置日志转存用户定义字段
     * @param   string  $name           空间名称
     * @param   array   $header         自定义请求头
     * @param   array   $param          自定义查询参数
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setLoggingUserField(string $name, array $header = [], array $param = []): array
    {
        $body = [];
        if (!empty($header)) $body['HeaderSet'] = ['header' => $header];
        if (!empty($param)) $body['ParamSet'] = ['parameter' => $param];
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['userDefinedLogFieldsConfig' => null],
            Tools::arrToXml($body, 'UserDefinedLogFieldsConfiguration')
        );
    }

    /**
     * 获取日志转存用户定义字段
     * @param   string  $name           空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getLoggingUserField(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['userDefinedLogFieldsConfig' => null]
        );
    }

    /**
     * 删除日志转存用户定义字段
     * @param   string  $name           空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deleteLoggingUserField(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['userDefinedLogFieldsConfig' => null]
        );
    }

    /**
     * 设置静态网站规则
     * @access  public
     * @param   string  $name           空间名称
     * @param   array   $indexDocument  默认主页配置[Suffix,SupportSubDir,Type]
     * @param   array   $errorDocument  404页面配置[Key,HttpStatus]
     * @param   array   $ruleList       路由列表[['RuleNumber' => 1, 'Condition' => ['KeyPrefixEquals' => 'anc'], 'Redirect' => ['RedirectType' => 'AliCDN']]]
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setWebsite(string $name, array $indexDocument = [], array $errorDocument = [], array $ruleList = []): array
    {
        $body = [];
        if (!empty($indexDocument)) $body['IndexDocument'] = $indexDocument;
        if (!empty($errorDocument)) $body['ErrorDocument'] = $errorDocument;
        if (!empty($ruleList)) $body['RoutingRules'] = ['RoutingRule' => $ruleList];
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['website' => null],
            Tools::arrToXml($body, 'WebsiteConfiguration')
        );
    }

    /**
     * 获取静态网站规则
     * @access  public
     * @param   string  $name           空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getWebsite(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['website' => null]
        );
    }

    /**
     * 关闭静态网站规则
     * @access  public
     * @param   string  $name           空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deleteWebsite(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['website' => null]
        );
    }

    /**
     * 设置防盗链
     * @access  public
     * @param   string  $name               空间名称
     * @param   bool    $allowEmpty         指定是否允许Referer字段为空的请求访问OSS
     * @param   array   $refererList        访问白名单
     * @param   bool    $allowTruncateQuery 指定匹配Referer时，是否截断URL中的QueryString
     * @param   bool    $truncatePath       指定匹配Referer时，是否截断URL中包括Path在内的后续所有部分
     * @param   array   $refererBlackList   访问黑名单
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setReferer(string $name, bool $allowEmpty, array $refererList, ?bool $allowTruncateQuery = null, ?bool $truncatePath = null, array $refererBlackList = []): array
    {
        $body = [
            'AllowEmptyReferer' => $allowEmpty ? 'true' : 'false',
            'RefererList' => ['Referer' => $refererList]
        ];
        if (!is_null($allowTruncateQuery)) $body['AllowTruncateQueryString'] = $allowTruncateQuery ? 'true' : 'false';
        if (!is_null($truncatePath)) $body['TruncatePath'] = $truncatePath ? 'true' : 'false';
        if (!empty($refererBlackList)) $body['RefererBlacklist'] = ['Referer' => $refererBlackList];
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['referer' => null],
            Tools::arrToXml($body, 'RefererConfiguration')
        );
    }

    /**
     * 获取防盗链设置
     * @access  public
     * @param   string  $name               空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getReferer(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['referer' => null]
        );
    }

    /**
     * 设置标签
     * @access  public
     * @param   string  $name       空间名称
     * @param   array   $tagList    标签列表['key1' => 'value1', 'key2' => 'value2']
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setTag(string $name, array $tagList): array
    {
        $body = ['TagSet' => []];
        if (!empty($tagList)) {
            $body['TagSet']['Tag'] = [];
            foreach($tagList as $k => $v) {
                $body['TagSet']['Tag'][] = ['Key' => $k, 'Value' => $v];
            }
        }
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['tagging' => null],
            Tools::arrToXml($body, 'Tagging')
        );
    }

    /**
     * 获取标签
     * @access  public
     * @param   string  $name       空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getTag(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['tagging' => null]
        );
    }
    
    /**
     * 删除标签
     * @access  public
     * @param   string  $name       空间名称
     * @param   array   $keyList    要删除的Key列表(如果为空，表示删除所有)
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deleteTag(string $name, array $keyList = []): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['tagging' => implode(',', $keyList) ?: null]
        );
    }

    /**
     * 设置加密规则
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $sseAlgorithm       设置服务器端默认加密方式(KMS,AES256,SM4)
     * @param   string  $kmsDataEncryption  指定Object的加密算法。如果未指定此选项，表明Object使用AES256加密算法
     * @param   string  $kmsMasterKeyID     当SSEAlgorithm值为KMS，且使用指定的密钥加密时，需输入KMSMasterKeyID。其他情况下，必须为空
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setEncryption(string $name, string $sseAlgorithm, string $kmsDataEncryption = null, string $kmsMasterKeyID = null): array
    {
        $body = ['ApplyServerSideEncryptionByDefault' => ['SSEAlgorithm' => $sseAlgorithm]];
        if (!empty($kmsDataEncryption)) $body['ApplyServerSideEncryptionByDefault']['KMSDataEncryption'] = $kmsDataEncryption;
        if (!empty($kmsMasterKeyID)) $body['KMSMasterKeyID']['KMSMasterKeyID'] = $kmsMasterKeyID;
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['encryption' => null],
            Tools::arrToXml($body, 'ServerSideEncryptionRule')
        );
    }

    /**
     * 获取加密规则
     * @access  public
     * @param   string  $name               空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getEncryption(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['encryption' => null]
        );
    }

    /**
     * 删除加密规则
     * @access  public
     * @param   string  $name               空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deleteEncryption(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['encryption' => null]
        );
    }

    /**
     * 设置请求者付费
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $payer              指定付费类型(BucketOwner-由Bucket拥有者付费,Requester-由请求者付费)
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setRequestPayment(string $name, string $payer): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['requestPayment' => null],
            Tools::arrToXml(['Payer' => $payer], 'RequestPaymentConfiguration')
        );
    }

    /**
     * 获取请求者付费配置
     * @access  public
     * @param   string  $name               空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getRequestPayment(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['requestPayment' => null]
        );
    }

    /**
     * 设置跨域资源共享
     * @access  public
     * @param   string  $name               空间名称
     * @param   array   $ruleList           规则列表[['AllowedOrigin'=>['*'],'AllowedMethod'=>['GET'],'AllowedHeader'=>['Token'],'ExposeHeader'=>['Etag'],'MaxAgeSeconds'=>10]]
     * @param   bool    $responseVary       是否返回Vary: Origin头
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setCors(string $name, array $ruleList, ?bool $responseVary = null): array
    {
        $body = [];
        if (!empty($ruleList)) $body['CORSRule'] = $ruleList;
        if (!is_null($responseVary)) $body['ResponseVary'] = $responseVary ? 'true' : 'false';
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['cors' => null],
            Tools::arrToXml($body, 'CORSConfiguration')
        );
    }

    /**
     * 获取跨域资源共享配置
     * @access  public
     * @param   string  $name               空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getCors(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['cors' => null]
        );
    }

    /**
     * 删除跨域资源共享配置
     * @access  public
     * @param   string  $name               空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deleteCors(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['cors' => null]
        );
    }

    /**
     * 设置访问跟踪
     * @access  public
     * @param   string  $name               空间名称
     * @param   bool    $status             是否启用
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setAccessMonitor(string $name, bool $status): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['accessmonitor' => null],
            Tools::arrToXml(['Status' => $status ? 'Enabled' : 'Disabled'], 'AccessMonitorConfiguration')
        );
    }

    /**
     * 获取访问跟踪配置
     * @access  public
     * @param   string  $name               空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getAccessMonitor(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['accessmonitor' => null]
        );
    }

    /**
     * 开启元数据管理
     * @param   string  $name               空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function openMetaQuery(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_POST,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['metaQuery' => null, 'comp' => 'add']
        );
    }

    /**
     * 获取元数据索引库信息
     * @access  public
     * @param   string  $name               空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getMetaQuery(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['metaQuery' => null]
        );
    }

    /**
     * 查询满足指定条件的文件并按照指定字段和排序方式列出文件信息
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $query              查询条件
     * @param   string  $nextToken          用于翻页的token
     * @param   int     $maxResults         返回Object的最大个数
     * @param   string  $sort               对指定字段排序
     * @param   string  $order              排序方式(asc-升序,desc-降序)
     * @param   array   $aggregationList    聚合操作列表[['Field' => 'a', 'Operation' => 'min']]
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function doMetaQuery(string $name, string $query, string $nextToken = null, ?int $maxResults = null, string $sort = null, string $order = null, array $aggregationList = []): array
    {
        $body = ['Query' => $query];
        if (!empty($nextToken)) $body['NextToken'] = $nextToken;
        if (!is_null($maxResults)) $body['MaxResults'] = $maxResults;
        if (!empty($sort)) $body['Sort'] = $sort;
        if (!empty($order)) $body['Order'] = $order;
        if (!empty($aggregationList)) $body['Aggregations'] = ['Aggregation' => $aggregationList];
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_POST,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['metaQuery' => null, 'comp' => 'query'],
            Tools::arrToXml($body, 'MetaQuery')
        );
    }

    /**
     * 关闭元数据管理
     * @param   string  $name               空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function closeMetaQuery(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_POST,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['metaQuery' => null, 'comp' => 'delete']
        );
    }

    /**
     * 设置资源组
     * @param   string  $name               空间名称
     * @param   string  $resourceGroupId    资源组ID,如果此项值设置为空，则表示移动到默认资源组
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setResourceGroupId(string $name, string $resourceGroupId = null): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['resourceGroup' => null],
            Tools::arrToXml(['ResourceGroupId' => $resourceGroupId], 'BucketResourceGroupConfiguration')
        );
    }

    /**
     * 获取资源组配置
     * @access  public
     * @param   string  $name               空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getResourceGroupId(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['resourceGroup' => null]
        );
    }

    /**
     * 创建域名所有权验证所需的Token
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $domain             自定义域名
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function createCnameToken(string $name, string $domain): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_POST,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['cname' => null, 'comp' => 'token'],
            Tools::arrToXml(['Cname' => ['Domain' => $domain]], 'BucketCnameConfiguration')
        );
    }

    /**
     * 获取已创建的CnameToken
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $domain             自定义域名
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getCnameToken(string $name, string $domain): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['cname' => $domain, 'comp' => 'token']
        );
    }

    /**
     * 绑定自定义域名
     * @param   string  $name               空间名称
     * @param   string  $domain             自定义域名
     * @param   bool    $delete             是否删除证书
     * @param   bool    $force              是否强制覆盖证书
     * @param   string  $certId             证书ID
     * @param   string  $publicKey          证书公钥
     * @param   string  $privateKey         证书私钥
     * @param   string  $previousCertId     当前证书ID
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function bindCname(string $name, string $domain, ?bool $delete = null, ?bool $force = null, string $certId = null, string $publicKey = null, string $privateKey = null, string $previousCertId = null): array
    {
        $body = [
            'Cname' => [
                'Domain' => $domain
            ]
        ];
        if (!is_null($delete) && !is_null($force) && !empty($certId) && !empty($publicKey) && !empty($privateKey)) {
            $body['Cname']['CertificateConfiguration'] = [
                'DeleteCertificate' => $delete ? 'true' : 'false',
                'Force' => $force ? 'true' : 'false',
                'CertId' => $certId,
                'Certificate' => $publicKey,
                'PrivateKey' => $privateKey,
                'PreviousCertId' => $previousCertId
            ];
        }
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_POST,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['cname' => null, 'comp' => 'add'],
            Tools::arrToXml($body, 'BucketCnameConfiguration')
        );
    }

    /**
     * 获取已绑定的域名列表
     * @access  public
     * @param   string  $name               空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getCname(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['cname' => null]
        );
    }

    /**
     * 删除已绑定的域名
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $domain             自定义域名
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deleteCname(string $name, string $domain): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_POST,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['cname' => null, 'comp' => 'delete'],
            Tools::arrToXml(['Cname' => ['Domain' => $domain]], 'BucketCnameConfiguration')
        );
    }

    /**
     * 创建图片样式
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $styleName          样式名称
     * @param   string  $style              样式
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function createImageStyle(string $name, string $styleName, string $style): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['style' => null, 'styleName' => $styleName],
            Tools::arrToXml(['Content' => $style], 'Style')
        );
    }

    /**
     * 获取图片样式
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $styleName          样式名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getImageStyle(string $name, string $styleName): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['style' => null, 'styleName' => $styleName]
        );
    }

    /**
     * 获取所有图片样式列表
     * @access  public
     * @param   string  $name               空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getImageStyleList(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['style' => null]
        );
    }

    /**
     * 删除图片样式
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $styleName          样式名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deleteImageStyle(string $name, string $styleName): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['style' => $name, 'styleName' => $styleName]
        );
    }

    /**
     * 设置TLS配置
     * @access  public
     * @param   string  $name               空间名称
     * @param   array   $tlsVersion         TLS版本(TLSv1.0,TLSv1.1,TLSv1.2,TLSv1.3,空数组表示关闭TLS)
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setTls(string $name, array $tlsVersion): array
    {
        $body = ['TLS' => ['Enable' => empty($tlsVersion) ? 'false' : 'true']];
        if (!empty($tlsVersion)) $body['TLS']['TLSVersion'] = $tlsVersion;
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['httpsConfig' => null],
            Tools::arrToXml($body, 'HttpsConfiguration')
        );
    }

    /**
     * 获取TLS配置
     * @access  public
     * @param   string  $name               空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getTls(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['httpsConfig' => null]
        );
    }

    /**
     * 创建冗余转换任务
     * @access  public
     * @param   string  $name               空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function createDataRedundancyTransition(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_POST,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['redundancyTransition' => null, 'x-oss-target-redundancy-type' => 'ZRS']
        );
    }

    /**
     * 获取冗余转换任务
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $id                 转换任务ID
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getDataRedundancyTransition(string $name, string $id): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['redundancyTransition' => null, 'x-oss-redundancy-transition-taskid' => $id]
        );
    }

    /**
     * 删除冗余转换任务
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $id                 转换任务ID
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deleteDataRedundancyTransition(string $name, string $id): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['redundancyTransition' => null, 'x-oss-redundancy-transition-taskid' => $id]
        );
    }

    /**
     * 获取请求者所有转换任务
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $continuationToken  指定List操作需要从此token开始
     * @param   int     $maxKeys            限定此次返回任务的最大个数
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getUserDataRedundancyTransitionList(string $name, string $continuationToken = null, ?int $maxKeys = null): array
    {
        $query = ['redundancyTransition' => null];
        if (!empty($continuationToken)) $query['continuation-token'] = $continuationToken;
        if (!is_null($maxKeys)) $query['max-keys'] = $maxKeys;
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            $query
        );
    }

    /**
     * 获取所有转换任务
     * @access  public
     * @param   string  $name               空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getDataRedundancyTransitionList(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['redundancyTransition' => null]
        );
    }

    /**
     * 创建接入点
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $pointName          接入点名称
     * @param   string  $networkOrigin      接入点网络来源(vpc-限制仅支持通过指定的VPC ID访问接入点,internet-同时支持通过外网和内网Endpoint访问接入点)
     * @param   string  $vpcId              仅当NetworkOrigin取值为vpc时，需要指定VPC ID
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function createAccessPoint(string $name, string $pointName, string $networkOrigin, string $vpcId = null): array
    {
        $body = ['AccessPointName' => $pointName, 'NetworkOrigin' => $networkOrigin];
        if (!empty($vpcId)) $body['VpcConfiguration'] = ['VpcId' => $vpcId];
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['accessPoint' => null],
            Tools::arrToXml($body, 'CreateAccessPointConfiguration')
        );
    }

    /**
     * 获取接入点
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $pointName          接入点名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getAccessPoint(string $name, string $pointName): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [
                Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED,
                'x-oss-access-point-name' => $pointName
            ],
            ['accessPoint' => null]
        );
    }

    /**
     * 删除接入点
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $pointName          接入点名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deleteAccessPoint(string $name, string $pointName): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            '/',
            $name,
            null,
            [
                Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED,
                'x-oss-access-point-name' => $pointName
            ],
            ['accessPoint' => null]
        );
    }

    /**
     * 获取接入点列表
     * @access  public
     * @param   string  $name               空间名称(如果为空，表示获取用户级别)
     * @param   string  $continuationToken  指定List操作需要从此token开始
     * @param   int     $maxKeys            指定返回接入点的最大数量
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getAccessPointList(string $name = null, string $continuationToken = null, ?int $maxKeys = null): array
    {
        $query = ['accessPoint' => null];
        if (!empty($continuationToken)) $query['continuation-token'] = $continuationToken;
        if (!is_null($maxKeys)) $query['max-keys'] = $maxKeys;
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            $query
        );
    }

    /**
     * 设置接入点策略
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $pointName          接入点名称
     * @param   array   $policy             策略
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setAccessPointPolicy(string $name, string $pointName, array $policy): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [
                Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_JSON,
                'x-oss-access-point-name' => $pointName
            ],
            ['accessPointPolicy ' => null],
            json_encode($policy, JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * 获取接入点策略配置
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $pointName          接入点名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getAccessPointPolicy(string $name, string $pointName): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [
                Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED,
                'x-oss-access-point-name' => $pointName
            ],
            ['accessPointPolicy ' => null]
        );
    }

    /**
     * 删除接入点策略配置
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $pointName          接入点名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deleteAccessPointPolicy(string $name, string $pointName): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            '/',
            $name,
            null,
            [
                Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED,
                'x-oss-access-point-name' => $pointName
            ],
            ['accessPointPolicy ' => null]
        );
    }

    /**
     * 设置全局阻止公共访问
     * @access  public
     * @param   bool    $status             是否开启阻止公共访问
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setGlobalPublicAccessBlock(bool $status): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            null,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['publicAccessBlock' => null],
            Tools::arrToXml(['BlockPublicAccess' => $status ? 'true' : 'false'], 'PublicAccessBlockConfiguration')
        );
    }

    /**
     * 获取全局阻止公共访问配置
     * @access  public
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getGlobalPublicAccessBlock(): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            null,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['publicAccessBlock' => null]
        );
    }

    /**
     * 删除全局阻止公共访问配置
     * @access  public
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deleteGlobalPublicAccessBlock(): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            '/',
            null,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['publicAccessBlock' => null]
        );
    }

    /**
     * 设置阻止公共访问
     * @access  public
     * @param   string  $name               空间名称
     * @param   bool    $status             是否开启阻止公共访问
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setPublicAccessBlock(string $name, bool $status): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['publicAccessBlock' => null],
            Tools::arrToXml(['BlockPublicAccess' => $status ? 'true' : 'false'], 'PublicAccessBlockConfiguration')
        );
    }

    /**
     * 获取阻止公共访问配置
     * @access  public
     * @param   string  $name               空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getPublicAccessBlock(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['publicAccessBlock' => null]
        );
    }

    /**
     * 删除阻止公共访问配置
     * @access  public
     * @param   string  $name               空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deletePublicAccessBlock(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['publicAccessBlock' => null]
        );
    }

    /**
     * 设置接入点阻止公共访问
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $pointName          接入点名称
     * @param   bool    $status             是否开启阻止公共访问
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setAccessPointPublicAccessBlock(string $name, string $pointName, bool $status): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['publicAccessBlock' => null, 'x-oss-access-point-name' => $pointName],
            Tools::arrToXml(['BlockPublicAccess' => $status ? 'true' : 'false'], 'PublicAccessBlockConfiguration')
        );
    }

    /**
     * 获取接入点阻止公共访问配置
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $pointName          接入点名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getAccessPointPublicAccessBlock(string $name, string $pointName): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['publicAccessBlock' => null, 'x-oss-access-point-name' => $pointName]
        );
    }

    /**
     * 删除接入点阻止公共访问配置
     * @access  public
     * @param   string  $name               空间名称
     * @param   string  $pointName          接入点名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function deleteAccessPointPublicAccessBlock(string $name, string $pointName): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_DELETE,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_URLENCODEED],
            ['publicAccessBlock' => null, 'x-oss-access-point-name' => $pointName]
        );
    }

    /**
     * 设置归档直读配置
     * @access  public
     * @param   string  $name       空间名称
     * @param   bool    $status     是否开启归档直读
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function setArchiveDirectRead(string $name, bool $status): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_PUT,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['bucketArchiveDirectRead' => null],
            Tools::arrToXml(['Enabled' => $status ? 'true' : 'false'], 'ArchiveDirectReadConfiguration')
        );
    }

    /**
     * 获取归档直读配置
     * @access  public
     * @param   string  $name       空间名称
     * @return  array
     * @throws  AliOssResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidResponseException
     */
    public function getArchiveDirectRead(string $name): array
    {
        return $this->buildHeaderSignAndRequest(
            Request::METHOD_GET,
            '/',
            $name,
            null,
            [Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_XML],
            ['bucketArchiveDirectRead' => null]
        );
    }
}
