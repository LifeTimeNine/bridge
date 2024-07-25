# Bridge

本项目的主要作用是对接各大平台的第三方接口，因此称它为“桥”。

## 配置

在项目初始化的时候调用初始化的方法
~~~php
<?php

\lifetime\bridge\Config::init([
  'ali' => [],
  'wechat' => [],
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

类: `\lifetime\bridge\ali\Payment`

配置说明
~~~php
<?php

$config = [
  'ali' => [
    'payment' => [
        // 应用ID
        'appid' => '',
        // 公钥
        'public_key' => '',
        // 私钥
        'private_key' => '',
        // 支付宝公钥
        'alipay_public_key' => '',
    ]
  ]
];
~~~

| 方法 | 说明 |
| -- | -- |
| [app](./docs/ali_payment.md#app) | APP支付 |
| [wap](./docs/ali_payment.md#wap) | 手机网站支付 |
| [page](./docs/ali_payment.md#page) | 电脑网站支付 |
| [notify](./docs/ali_payment.md#notify) | 对支付后异步的通知进行处理 |
| [query](./docs/ali_payment.md#query) | 订单查询 |
| [refund](./docs/ali_payment.md#refund) | 退款 |
| [refundQuery](./docs/ali_payment.md#refundQuery) | 退款查询 |
| [tradeClose](./docs/ali_payment.md#tradeClose) | 交易关闭 |

## 微信

### 微信支付

类: `\lifetime\bridge\wechat\Payment`

配置说明
~~~php
<?php

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
<?php

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
> 所有需要空间名称的方法请先调用`setBucketName()`设置空间名称
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Bucket())
  ->setBucketName('bucket')
  ->getDomain()
~~~

> 实例化的对象支持重复调用
~~~php
$bucket = (new \lifetime\bridge\qiniu\kodo\Bucket())->setBucketName('bucket');
$res1 = $bucket->setTag(['tag1' => 'v1']);
$res2 = $bucket->getTag();
var_dump($res1, $res2);
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

#### Service 相关操作

| 方法 | 说明 |
| -- | -- |
| [bucketList](./docs/qiniu_kodo_service.md#bucketlist) | 获取Bucket列表 |

#### Bucket 相关操作

| 方法 | 说明 |
| -- | -- |
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