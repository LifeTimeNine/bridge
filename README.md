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