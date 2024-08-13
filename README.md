# Bridge

本项目的主要作用是对接各大平台的开放接口，因此称它为“桥”。

引入
~~~
composer require lifetime/bridge
~~~

## 配置

在项目初始化的时候调用初始化的方法
~~~php
\lifetime\bridge\Config::init([
  'ali' => [],
  'wechat' => [],
  'qiniu' => [],
  'cache_path' => '/tmp/lifetime-bridge',
  'cache_callable' => [
    'set' => null,
    'get' => null,
    'del' => null
  ]
]);
~~~

### 配置项说明

- `ali`，`wechat`，`byte_dance`是各个平台的配置项，在下文中会具体说明。
- `cache_path` 缓存目录，注意读写权限
- `cache_callable` 自定义缓存方法，如果设置了此选择，缓存目录将会失效

### 自定义缓存方法说明

- `set(string $name, $value, int $expired = 0)` 设置缓存
  + `$name` 缓存名称
  + `$value` 缓存值
  + `$expired` 有效期(0表示永久)
- `get(string $name, $default = null)` 获取缓存
  + `$name` 缓存名称
  + `$default` 缓存值
- `del(string $name)` 删除缓存
  + `$name` 缓存名称

## 阿里云和支付宝

### 支付宝支付

配置说明
~~~php
$config = [
  'ali' => [
    'alipay' => [
        // 是否是沙箱
      'sandbox' => false,
      // 应用ID
      'app_id' => '',
      // 应用公钥
      'app_public_key' => '',
      // 应用私钥
      'app_private_key' => '',
      // 支付宝公钥
      'alipay_public_key' => '',
      // 应用公钥证书地址
      'app_public_cert_path' => '',
      // 支付宝公钥证书地址
      'alipay_public_cert_path' => '',
      // 支付宝根证书地址
      'alipay_root_cert_path' => '',
      // 加密key
      'encrypt_key' => ''
    ]
  ]
];
~~~

> 如果设置了支付宝公钥证书地址【alipay_public_cert_path】，将使用证书模式
> 如果设置了加密Key【encrypt_key】，将对请求数据进行加密

#### 支付相关
| 方法 | 说明 |
| -- | -- |
| [app](./docs/ali_alipay_trade.md#app) | APP支付 |
| [wap](./docs/ali_alipay_trade.md#wap) | 手机网站支付 |
| [page](./docs/ali_alipay_trade.md#page) | 电脑网站支付 |
| [notify](./docs/ali_alipay_trade.md#notify) | 对支付后异步的通知进行处理 |
| [query](./docs/ali_alipay_trade.md#query) | 订单查询 |
| [refund](./docs/ali_alipay_trade.md#refund) | 退款 |
| [refundQuery](./docs/ali_alipay_trade.md#refundQuery) | 退款查询 |
| [tradeClose](./docs/ali_alipay_trade.md#tradeClose) | 交易关闭 |

#### 资金相关
| 方法 | 说明 |
| -- | -- |
| [accountQuery](./docs/ali_alipay_fund.md#accountQuery) | 资产查询 |
| [quotaQuery](./docs/ali_alipay_fund.md#quotaQuery) | 转账额度查询 |
| [transfer](./docs/ali_alipay_fund.md#transfer) | 单笔转账 |

### 对象存储

配置说明
~~~php
$config = [
  'ali' => [
    'oss' => [
      // 访问KeyID
      'access_key_id' => '',
      // 访问秘钥
      'access_key_secret' => '',
      // 区域ID
      'region_id' => '',
      // 默认空间名称
      'bucket_name' => '',
      // 访问域名
      'access_domain' => '',
      // 是否使用HTTPS
      'is_https' => true
    ]
  ]
];
~~~

#### Bucket相关操作
| 方法 | 说明 |
| -- | -- |
| [list](./docs/ali_oss_bucket.md#list) | 获取储存空间列表 |
| [regionList](./docs/ali_oss_bucket.md#regionList) | 获取区域列表 |
| [create](./docs/ali_oss_bucket.md#create) | 创建存储空间 |
| [delete](./docs/ali_oss_bucket.md#delete) | 删除存储空间 |
| [getInfo](./docs/ali_oss_bucket.md#getInfo) | 获取空间信息 |
| [getLocation](./docs/ali_oss_bucket.md#getLocation) | 获取位置信息 |
| [getStat](./docs/ali_oss_bucket.md#getStat) | 获取状态信息 |
| [createWorm](./docs/ali_oss_bucket.md#createWorm) | 创建合规保留策略 |
| [deleteWorm](./docs/ali_oss_bucket.md#deleteWorm) | 删除未锁定的合规保留策略 |
| [lockWorm](./docs/ali_oss_bucket.md#lockWorm) | 删除未锁定的合规保留策略 |
| [extendWorm](./docs/ali_oss_bucket.md#extendWorm) | 延长已锁定的合规保留策 |
| [getWorm](./docs/ali_oss_bucket.md#getWorm) | 获取合规保留策略信息 |
| [setAcl](./docs/ali_oss_bucket.md#setAcl) | 设置访问权限 |
| [getAcl](./docs/ali_oss_bucket.md#getAcl) | 获取访问权限 |
| [setLifecycle](./docs/ali_oss_bucket.md#setLifecycle) | 设置生命周期规则 |
| [getLifecycle](./docs/ali_oss_bucket.md#getLifecycle) | 获取生命周期规则 |
| [deleteLifecycle](./docs/ali_oss_bucket.md#deleteLifecycle) | 删除生命周期规则 |
| [setTransferAcceleration](./docs/ali_oss_bucket.md#setTransferAcceleration) | 设置传输加速 |
| [getTransferAcceleration](./docs/ali_oss_bucket.md#getTransferAcceleration) | 获取传输加速配置 |
| [setVersioning](./docs/ali_oss_bucket.md#setVersioning) | 设置版本控制 |
| [getVersioning](./docs/ali_oss_bucket.md#getVersioning) | 获取版本控制配置 |
| [getVersionList](./docs/ali_oss_bucket.md#getVersionList) | 获取所有Object的版本信息 |
| [createReplication](./docs/ali_oss_bucket.md#createReplication) | 创建复制规则 |
| [setRtc](./docs/ali_oss_bucket.md#setRtc) | 设置跨区域复制规则时间控制功能 |
| [getReplication](./docs/ali_oss_bucket.md#getReplication) | 获取数据复制规则 |
| [getReplicationLocation](./docs/ali_oss_bucket.md#getReplicationLocation) | 获取可复制到的目标存储空间所在的地域 |
| [getReplicationProgress](./docs/ali_oss_bucket.md#getReplicationProgress) | 获取数据复制进度 |
| [deleteReplication](./docs/ali_oss_bucket.md#deleteReplication) | 删除数据复制规则 |
| [setPolicy](./docs/ali_oss_bucket.md#setPolicy) | 设置授权策略 |
| [getPolicy](./docs/ali_oss_bucket.md#getPolicy) | 获取授权策略 |
| [getPolicyStatus](./docs/ali_oss_bucket.md#getPolicyStatus) | 获取授权策略状态 |
| [deletePolicy](./docs/ali_oss_bucket.md#deletePolicy) | 删除授权策略 |
| [createInventory](./docs/ali_oss_bucket.md#createInventory) | 创建清单规则 |
| [getInventory](./docs/ali_oss_bucket.md#getInventory) | 获取清单规则 |
| [getInventoryList](./docs/ali_oss_bucket.md#getInventoryList) | 获取清单规则列表 |
| [deleteInventory](./docs/ali_oss_bucket.md#deleteInventory) | 删除清单规则 |
| [setLogging](./docs/ali_oss_bucket.md#setLogging) | 设置日志转存 |
| [getLogging](./docs/ali_oss_bucket.md#getLogging) | 获取日志转存配置 |
| [deleteLogging](./docs/ali_oss_bucket.md#deleteLogging) | 关闭日志转存配置 |
| [setLoggingUserField](./docs/ali_oss_bucket.md#setLoggingUserField) | 设置日志转存用户定义字段 |
| [getLoggingUserField](./docs/ali_oss_bucket.md#getLoggingUserField) | 获取日志转存用户定义字段 |
| [deleteLoggingUserField](./docs/ali_oss_bucket.md#deleteLoggingUserField) | 删除日志转存用户定义字段 |
| [setWebsite](./docs/ali_oss_bucket.md#setWebsite) | 设置静态网站规则 |
| [getWebsite](./docs/ali_oss_bucket.md#getWebsite) | 获取静态网站规则 |
| [deleteWebsite](./docs/ali_oss_bucket.md#deleteWebsite) | 关闭静态网站规则 |
| [setReferer](./docs/ali_oss_bucket.md#setReferer) | 设置防盗链 |
| [getReferer](./docs/ali_oss_bucket.md#getReferer) | 获取防盗链设置 |
| [setTag](./docs/ali_oss_bucket.md#setTag) | 设置标签 |
| [getTag](./docs/ali_oss_bucket.md#getTag) | 获取标签 |
| [deleteTag](./docs/ali_oss_bucket.md#deleteTag) | 删除标签 |
| [setEncryption](./docs/ali_oss_bucket.md#setEncryption) | 设置加密规则 |
| [getEncryption](./docs/ali_oss_bucket.md#getEncryption) | 获取加密规则 |
| [deleteEncryption](./docs/ali_oss_bucket.md#deleteEncryption) | 删除加密规则 |
| [setRequestPayment](./docs/ali_oss_bucket.md#setRequestPayment) | 设置请求者付费 |
| [getRequestPayment](./docs/ali_oss_bucket.md#getRequestPayment) | 获取请求者付费配置 |
| [setCors](./docs/ali_oss_bucket.md#setCors) | 设置跨域资源共享 |
| [getCors](./docs/ali_oss_bucket.md#getCors) | 获取跨域资源共享配置 |
| [deleteCors](./docs/ali_oss_bucket.md#deleteCors) | 删除跨域资源共享配置 |
| [setAccessMonitor](./docs/ali_oss_bucket.md#setAccessMonitor) | 设置访问跟踪 |
| [getAccessMonitor](./docs/ali_oss_bucket.md#getAccessMonitor) | 获取访问跟踪配置 |
| [openMetaQuery](./docs/ali_oss_bucket.md#openMetaQuery) | 开启元数据管理 |
| [getMetaQuery](./docs/ali_oss_bucket.md#getMetaQuery) | 获取元数据索引库信息 |
| [doMetaQuery](./docs/ali_oss_bucket.md#doMetaQuery) | 查询满足指定条件的文件并按照指定字段和排序方式列出文件信息 |
| [closeMetaQuery](./docs/ali_oss_bucket.md#closeMetaQuery) | 关闭元数据管理 |
| [setResourceGroupId](./docs/ali_oss_bucket.md#setResourceGroupId) | 设置资源组 |
| [getResourceGroupId](./docs/ali_oss_bucket.md#getResourceGroupId) | 获取资源组配置 |
| [createCnameToken](./docs/ali_oss_bucket.md#createCnameToken) | 创建域名所有权验证所需的Token |
| [getCnameToken](./docs/ali_oss_bucket.md#getCnameToken) | 获取已创建的CnameToken |
| [bindCname](./docs/ali_oss_bucket.md#bindCname) | 绑定自定义域名 |
| [getCname](./docs/ali_oss_bucket.md#getCname) | 获取已绑定的域名列表 |
| [deleteCname](./docs/ali_oss_bucket.md#deleteCname) | 删除已绑定的域名 |
| [createImageStyle](./docs/ali_oss_bucket.md#createImageStyle) | 创建图片样式 |
| [getImageStyle](./docs/ali_oss_bucket.md#getImageStyle) | 创建图片样式 |
| [getImageStyleList](./docs/ali_oss_bucket.md#getImageStyleList) | 获取所有图片样式列表 |
| [deleteImageStyle](./docs/ali_oss_bucket.md#deleteImageStyle) | 删除图片样式 |
| [setTls](./docs/ali_oss_bucket.md#setTls) | 设置TLS配置 |
| [getTls](./docs/ali_oss_bucket.md#getTls) | 获取TLS配置 |
| [createDataRedundancyTransition](./docs/ali_oss_bucket.md#createDataRedundancyTransition) | 创建冗余转换任务 |
| [getDataRedundancyTransition](./docs/ali_oss_bucket.md#getDataRedundancyTransition) | 获取冗余转换任务 |
| [deleteDataRedundancyTransition](./docs/ali_oss_bucket.md#deleteDataRedundancyTransition) | 删除冗余转换任务 |
| [getUserDataRedundancyTransitionList](./docs/ali_oss_bucket.md#getUserDataRedundancyTransitionList) | 获取请求者所有转换任务 |
| [getDataRedundancyTransitionList](./docs/ali_oss_bucket.md#getDataRedundancyTransitionList) | 获取所有转换任务 |
| [createAccessPoint](./docs/ali_oss_bucket.md#createAccessPoint) | 创建接入点 |
| [getAccessPoint](./docs/ali_oss_bucket.md#getAccessPoint) | 获取接入点 |
| [deleteAccessPoint](./docs/ali_oss_bucket.md#deleteAccessPoint) | 删除接入点 |
| [getAccessPointList](./docs/ali_oss_bucket.md#getAccessPointList) | 获取接入点列表 |
| [setAccessPointPolicy](./docs/ali_oss_bucket.md#setAccessPointPolicy) | 设置接入点策略 |
| [getAccessPointPolicy](./docs/ali_oss_bucket.md#getAccessPointPolicy) | 获取接入点策略配置 |
| [deleteAccessPointPolicy](./docs/ali_oss_bucket.md#deleteAccessPointPolicy) | 获取接入点策略配置 |
| [setGlobalPublicAccessBlock](./docs/ali_oss_bucket.md#setGlobalPublicAccessBlock) | 设置全局阻止公共访问 |
| [getGlobalPublicAccessBlock](./docs/ali_oss_bucket.md#getGlobalPublicAccessBlock) | 设置全局阻止公共访问 |
| [deleteGlobalPublicAccessBlock](./docs/ali_oss_bucket.md#deleteGlobalPublicAccessBlock) | 删除全局阻止公共访问配置 |
| [setPublicAccessBlock](./docs/ali_oss_bucket.md#setPublicAccessBlock) | 设置阻止公共访问 |
| [getPublicAccessBlock](./docs/ali_oss_bucket.md#getPublicAccessBlock) | 获取阻止公共访问配置 |
| [deletePublicAccessBlock](./docs/ali_oss_bucket.md#deletePublicAccessBlock) | 删除阻止公共访问配置 |
| [setAccessPointPublicAccessBlock](./docs/ali_oss_bucket.md#setAccessPointPublicAccessBlock) | 设置接入点阻止公共访问 |
| [getAccessPointPublicAccessBlock](./docs/ali_oss_bucket.md#getAccessPointPublicAccessBlock) | 获取接入点阻止公共访问配置 |
| [deleteAccessPointPublicAccessBlock](./docs/ali_oss_bucket.md#deleteAccessPointPublicAccessBlock) | 删除接入点阻止公共访问配置 |
| [setArchiveDirectRead](./docs/ali_oss_bucket.md#setArchiveDirectRead) | 设置归档直读配置 |
| [getArchiveDirectRead](./docs/ali_oss_bucket.md#getArchiveDirectRead) | 获取归档直读配置 |

#### Object相关操作

> 注意  
> 请先调用`setBucketName()`设置空间名称,如果不设置，将使用配置中的存储空间名称
~~~php
$result = (new \lifetime\bridge\ali\oss\Objects())->setBucketName('bucket');
~~~

| 方法 | 说明 |
| -- | -- |
| [setBucketName](./docs/ali_oss_object.md#setBucketName) | 设置存储空间名称 |
| [getAccessPath](./docs/ali_oss_object.md#getAccessPath) | 获取访问地址 |
| [list](./docs/ali_oss_object.md#list) | 获取所有Object信息 |
| [put](./docs/ali_oss_object.md#put) | 上传文件 |
| [get](./docs/ali_oss_object.md#get) | 获取文件 |
| [copy](./docs/ali_oss_object.md#copy) | 复制文件 |
| [append](./docs/ali_oss_object.md#append) | 追加写的方式上传文件 |
| [delete](./docs/ali_oss_object.md#delete) | 删除文件 |
| [deleteMultiple](./docs/ali_oss_object.md#deleteMultiple) | 删除多个文件 |
| [getHead](./docs/ali_oss_object.md#getHead) | 获取文件头信息 |
| [getMeta](./docs/ali_oss_object.md#getMeta) | 获取文件元数据 |
| [post](./docs/ali_oss_object.md#post) | 表单上传 (此方法返回上传参数，需自行构建请求进行上传) |
| [restore](./docs/ali_oss_object.md#restore) | 解冻 |
| [initPart](./docs/ali_oss_object.md#initPart) | 初始化分片上传 |
| [uploadPart](./docs/ali_oss_object.md#uploadPart) | 分片上传 |
| [clientUploadPart](./docs/ali_oss_object.md#clientUploadPart) | 客户端分片上传 (此方法返回上传参数，需自行构建请求进行上传) |
| [copyPart](./docs/ali_oss_object.md#copyPart) | 拷贝现有文件到分片 |
| [completePart](./docs/ali_oss_object.md#completePart) | 完成分片上传 |
| [abortPart](./docs/ali_oss_object.md#abortPart) | 取消分片上传 |
| [partTaskList](./docs/ali_oss_object.md#partTaskList) | 分片上传任务列表 |
| [abortPart](./docs/ali_oss_object.md#abortPart) | 取消分片上传 |
| [partList](./docs/ali_oss_object.md#partList) | 分片列表 |
| [setAcl](./docs/ali_oss_object.md#setAcl) | 设置访问权限 |
| [getAcl](./docs/ali_oss_object.md#getAcl) | 获取访问权限 |
| [createSymlink](./docs/ali_oss_object.md#createSymlink) | 创建软链接 |
| [getSymlink](./docs/ali_oss_object.md#getSymlink) | 获取软连接 |
| [setTag](./docs/ali_oss_object.md#setTag) | 设置标签 |
| [getTag](./docs/ali_oss_object.md#getTag) | 获取标签 |
| [deleteTag](./docs/ali_oss_object.md#deleteTag) | 删除标签 |

## 微信

### 微信支付

配置说明
~~~php
$config = [
  'wechat' => [
    'payment' => [
        // 应用ID
        'app_id'=> '',
        // 商户ID
        'mch_id' => '',
        // 商户支付密钥
        'mch_key' => '',
        // 证书cert.pem路径
        'ssl_cert' => '',
        // 证书key.pem路径
        'ssl_key' => '',
    ]
  ]
];
~~~

| 方法 | 说明 |
| -- | -- |
| [jsapi](./docs/wechat_payment.md#jsapi) | JSAPI下单 |
| [app](./docs/wechat_payment.md#app) | APP下单 |
| [h5](./docs/wechat_payment.md#h5) | H5下单 |
| [native](./docs/wechat_payment.md#native) | Native下单 |
| [miniApp](./docs/wechat_payment.md#miniApp) | 小程序下单 |
| [query](./docs/wechat_payment.md#query) | 订单号查询订单 |
| [close](./docs/wechat_payment.md#close) | 关闭订单 |
| [refund](./docs/wechat_payment.md#refund) | 退款申请 |
| [refundQuery](./docs/wechat_payment.md#refundQuery) | 查询单笔退款（通过商户退款单号） |
| [notify](./docs/wechat_payment.md#notify) | 支付通知 |

> 如果需要不同的`app_id`,可以在实例化时进行配置覆盖
~~~php
<?php

new \lifetime\bridge\wechat\Payment(['app_id' => ''])

~~~

### 微信公众号

配置说明
~~~php
$config = [
  'wechat' => [
    // 公众号相关配置
    'official' => [
        // 公众号appid
        'app_id' => '',
        // 公众号secret
        'app_secret' => ''
    ]
  ]
];
~~~


#### 网页授权

| 方法 | 说明 |
| -- | -- |
| [authorize](./docs/wechat_official_oauth.md#authorize) | 这是网页授权的第一步, 跳转到微信授权, 获取Code |
| [getUserAccessToken](./docs/wechat_official_oauth.md#getUserAccessToken) | 这是网页授权第二步，通过Code获取用户访问Token |
| [getUserInfo](./docs/wechat_official_oauth.md#getUserInfo) | 获取用户个人信息（UnionID机制） |
| [refreshAccessToken](./docs/wechat_official_oauth.md#refreshAccessToken) | 刷新访问Token |
| [checkAccessToken](./docs/wechat_official_oauth.md#checkAccessToken) | 校验授权凭证是否有效 |
| [getJsSdkSign](./docs/wechat_official_oauth.md#getJsSdkSign) | 获取JS-SDK使用权限 |

#### 模板消息

| 方法 | 说明 |
| -- | -- |
| [setIndustry](./docs/wechat_official_template.md#setindustry) | 设置所属行业 |
| [getIndustry](./docs/wechat_official_template.md#getIndustry) | 获取所属行业 |
| [addTemplate](./docs/wechat_official_template.md#addTemplate) | 添加模板 |
| [getAllPrivateTemplate](./docs/wechat_official_template.md#getAllPrivateTemplate) | 获取模板列表 |
| [deletePrivateTemplate](./docs/wechat_official_template.md#deletePrivateTemplate) | 删除模板 |
| [send](./docs/wechat_official_template.md#getAllPrivateTemplate) | 发送模板消息 |

#### 用户管理

| 方法 | 说明 |
| -- | -- |
| [createTag](./docs/wechat_official_user.md#createtag) | 创建标签 |
| [getTag](./docs/wechat_official_user.md#getTag) | 获取已经创建的标签 |
| [updateTag](./docs/wechat_official_user.md#updateTag) | 更新标签信息 |
| [deleteTag](./docs/wechat_official_user.md#deleteTag) | 删除标签 |
| [getTagUser](./docs/wechat_official_user.md#getTagUser) | 获取某个标签下的用户列表 |
| [batchBindTag](./docs/wechat_official_user.md#batchBindTag) | 批量为用户绑定标签 |
| [batchUnBindTag](./docs/wechat_official_user.md#batchUnBindTag) | 批量为用户解绑标签 |
| [getUserTag](./docs/wechat_official_user.md#getUserTag) | 获取用户绑定的标签 |
| [updateRemark](./docs/wechat_official_user.md#updateRemark) | 设置用户备注名 |
| [getUserInfo](./docs/wechat_official_user.md#getUserInfo) | 获取用户基本信息(UnionID机制) |
| [batchGetUserInfo](./docs/wechat_official_user.md#batchGetUserInfo) | 批量获取用户基本信息 |
| [getUserList](./docs/wechat_official_user.md#getUserList) | 获取用户列表 |
| [getBlackList](./docs/wechat_official_user.md#getBlackList) | 获取黑名单列表 |
| [batchBlack](./docs/wechat_official_user.md#batchBlack) | 批量拉黑用户 |
| [batchUnBlack](./docs/wechat_official_user.md#batchUnBlack) | 批量取消拉黑用户 |

> 仅对接了以上几个业务，如需其他业务，可以继承`\lifetime\bridge\wechat\official\Basic`类，按照官方文档说明，封装方法。

### 微信小程序

配置说明
~~~php
$config = [
  'wechat' => [
    // 小程序相关配置
    'miniapp' => [
        // 小程序appid
        'app_id' => '',
        // 小程序secret
        'app_secret' => ''
    ]
  ]
];
~~~

#### 登录

| 方法 | 说明 |
| -- | -- |
| [code2session](./docs/wechat_miniapp_login.md#code2session) | 小程序登录 |
| [checkSession](./docs/wechat_miniapp_login.md#checkSession) | 检验登录态 |
| [resetSession](./docs/wechat_miniapp_login.md#resetSession) | 重置登录态 |

#### 用户信息

| 方法 | 说明 |
| -- | -- |
| [getPluginOpenPid](./docs/wechat_miniapp_user.md#getpluginopenpid) | 获取插件用户OpenID |
| [checkEncryptedData](./docs/wechat_miniapp_user.md#checkEncryptedData) | 检查加密信息 |
| [getPaidUnionId](./docs/wechat_miniapp_user.md#getPaidUnionId) | 支付后获取 Unionid |
| [getUserEncryptKey](./docs/wechat_miniapp_user.md#getUserEncryptKey) | 获取用户encryptKey |
| [getPhoneNumber](./docs/wechat_miniapp_user.md#getPhoneNumber) | 获取手机号 |
| [check](./docs/wechat_miniapp_user.md#check) | 验证用户信息 |
| [decodeUserInfo](./docs/wechat_miniapp_user.md#decodeUserInfo) | 用户信息解密 |

## 七牛云

### Kodo 对象存储

> 注意  
> 请先调用`setBucketName()`设置空间名称,如果不设置，将使用配置中的存储空间名称
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->setBucketName('bucket');
~~~


配置说明
~~~php
$config = [
  'qiniu' => [
    // 对象存储配置
    'kodo' => [
        // AccessKey
        'access_key' => '',
        // SecretKey
        'secret_key' => '',
        // 区域ID
        'region_id' => '',
        // 访问域名
        'access_domain' => '',
        // 是否使用SSL
        'is_ssl' => false,
        // 默认Bucket名称
        'bucket_name' => ''
    ],
  ]
];
~~~

#### Bucket 相关操作

| 方法 | 说明 |
| -- | -- |
| [list](./docs/qiniu_kodo_bucket.md#bucketlistlist) | 获取Bucket列表 |
| [getRegionList](./docs/qiniu_kodo_bucket.md#getRegionList) | 获取存储区域列表 |
| [create](./docs/qiniu_kodo_bucket.md#create) | 创建Bucket |
| [delete](./docs/qiniu_kodo_bucket.md#delete) | 删除Bucket |
| [getDomain](./docs/qiniu_kodo_bucket.md#getDomain) | 获取Bucket空间域名 |
| [setImageSource](./docs/qiniu_kodo_bucket.md#setImageSource) | 设置镜像源 |
| [setAccessAuth](./docs/qiniu_kodo_bucket.md#setAccessAuth) | 设置访问权限 |
| [setTag](./docs/qiniu_kodo_bucket.md#setTag) | 设置空间标签 |
| [getTag](./docs/qiniu_kodo_bucket.md#getTag) | 获取空间标签 |
| [deleteTag](./docs/qiniu_kodo_bucket.md#deleteTag) | 删除空间标签 |

#### Object 相关操作

| 方法 | 说明 |
| -- | -- |
| [upload](./docs/qiniu_kodo_objcet.md#upload) | 直传文件 |
| [clientUpload](./docs/qiniu_kodo_objcet.md#clientUpload) | 客户端直传文件 |
| [initPart](./docs/qiniu_kodo_objcet.md#initPart) | 初始化分片上传 |
| [uploadPart](./docs/qiniu_kodo_objcet.md#uploadPart) | 分片上传数据 |
| [clientUploadPart](./docs/qiniu_kodo_objcet.md#clientUploadPart) | 客户端分片上传数据 |
| [completePart](./docs/qiniu_kodo_objcet.md#completePart) | 完成分片上传 |
| [stopPart](./docs/qiniu_kodo_objcet.md#stopPart) | 终止分片上传任务 |
| [partList](./docs/qiniu_kodo_objcet.md#partList) | 列举已经上传的分片 |
| [list](./docs/qiniu_kodo_objcet.md#list) | 资源列举 |
| [getMetaData](./docs/qiniu_kodo_objcet.md#getMetaData) | 获取资源元信息 |
| [setMetaData](./docs/qiniu_kodo_objcet.md#setMetaData) | 修改资源元信息 |
| [move](./docs/qiniu_kodo_objcet.md#move) | 移动资源 |
| [copy](./docs/qiniu_kodo_objcet.md#copy) | 复制资源 |
| [delete](./docs/qiniu_kodo_objcet.md#delete) | 删除资源 |
| [setStatus](./docs/qiniu_kodo_objcet.md#setStatus) | 修改文件状态 |
| [setStorageType](./docs/qiniu_kodo_objcet.md#setStorageType) | 修改文件存储类型 |
| [thaw](./docs/qiniu_kodo_objcet.md#thaw) | 解冻归档/深度归档存储文件 |
| [setExpireDeleteDuration](./docs/qiniu_kodo_objcet.md#setExpireDeleteDuration) | 修改文件过期删除时间 |
| [setLifecycle](./docs/qiniu_kodo_objcet.md#setLifecycle) | 修改文件生命周期 |
| [imageSourceUpdate](./docs/qiniu_kodo_objcet.md#imageSourceUpdate) | 镜像资源更新 |
| [createAsyncFetchTask](./docs/qiniu_kodo_objcet.md#createAsyncFetchTask) | 发起异步抓取任务 |
| [queryAsyncFetchTask](./docs/qiniu_kodo_objcet.md#queryAsyncFetchTask) | 查询异步抓取任务 |
| [batch](./docs/qiniu_kodo_objcet.md#batch) | 批量操作 |