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
<?php

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
