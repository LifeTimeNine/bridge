## jsapi
- 说明: JSAPI下单
- 官方文档: [JSAPI下单](https://pay.weixin.qq.com/docs/merchant/apis/jsapi-payment/direct-jsons/jsapi-prepay.html)
- 参数说明
  + `order`: (array) 订单参数，仅列出必选参数，其他参数请参考官方文档
    - `out_trade_no`: (string) 商户订单号
    - `description`: (string) 订单描述
    - `amount`: (array)
      + `total`: (int) 订单金额，分
    - `payer`: (array)
      + `openid`: (string) 用户openid
  + `notifyUrl`: (string) 异步通知地址

请求示例
~~~php
<?php

$result = (new \lifetime\bridge\wechat\Payment)->jsapi([
    'out_trade_no' => 'order_2',
    'description' => '测试订单',
    'amount' => ['total' => 1],
    'payer' => ['openid' => 'obaxI5YDv-3r4VEkSzKbOAq0Dw94'],
], 'https://xxx.com');
~~~

## app
- 说明: APP下单
- 官方文档: [APP下单](https://pay.weixin.qq.com/docs/merchant/apis/in-app-payment/direct-jsons/app-prepay.html)
- 参数说明
  + `order`: (array) 订单参数，仅列出必选参数，其他参数请参考官方文档
    - `out_trade_no`: (string) 商户订单号
    - `description`: (string) 订单描述
    - `amount`: (array)
      + `total`: (int) 订单金额，分
  + `notifyUrl`: (string) 异步通知地址

请求示例
~~~php
<?php

$result = (new \lifetime\bridge\wechat\Payment)->app([
    'out_trade_no' => 'order_2',
    'description' => '测试订单',
    'amount' => ['total' => 1],
], 'https://xxx.com');
~~~

## h5
- 说明: H5下单
- 官方文档: [H5下单](https://pay.weixin.qq.com/docs/merchant/apis/h5-payment/direct-jsons/h5-prepay.html)
- 参数说明
  + `order`: (array) 订单参数，仅列出必选参数，其他参数请参考官方文档
    - `out_trade_no`: (string) 商户订单号
    - `description`: (string) 订单描述
    - `amount`: (array)
      + `total`: (int) 订单金额，分
    - `scene_info`: (array)
      + `payer_client_ip`: (string) 用户终端IP
      + `h5_info`: (array)
        - `type`: (string) 场景类型
  + `notifyUrl`: (string) 异步通知地址

请求示例
~~~php
<?php

$result = (new \lifetime\bridge\wechat\Payment)->h5([
  'out_trade_no' => 'order_1',
  'amount' => ['total' => 1],
  'description' => '测试订单',
  'scene_info' => [
      'payer_client_ip' => '127.0.0.1',
      'h5_info' => [
          'type' => 'android'
      ]
  ]
], 'https://xxx.com');
~~~

## native
- 说明: Native下单
- 官方文档: [Native下单](https://pay.weixin.qq.com/docs/merchant/apis/native-payment/direct-jsons/native-prepay.html)
- 参数说明
  + `order`: (array) 订单参数，仅列出必选参数，其他参数请参考官方文档
    - `out_trade_no`: (string) 商户订单号
    - `description`: (string) 订单描述
    - `amount`: (array)
      + `total`: (int) 订单金额，分
  + `notifyUrl`: (string) 异步通知地址

请求示例
~~~php
<?php

$result = (new \lifetime\bridge\wechat\Payment)->native([
  'out_trade_no' => 'order_1',
  'amount' => ['total' => 1],
  'description' => '测试订单',
], 'https://xxx.com');
~~~

## miniApp
- 说明: 小程序下单
- 官方文档: [小程序下单](https://pay.weixin.qq.com/docs/merchant/apis/mini-program-payment/mini-prepay.html)
- 参数说明
  + `order`: (array) 订单参数，仅列出必选参数，其他参数请参考官方文档
    - `out_trade_no`: (string) 商户订单号
    - `description`: (string) 订单描述
    - `amount`: (array)
      + `total`: (int) 订单金额，分
    - `payer`: (array)
      + `openid`: (string) 用户openid
  + `notifyUrl`: (string) 异步通知地址

请求示例
~~~php
<?php

$result = (new \lifetime\bridge\wechat\Payment)->miniApp([
    'out_trade_no' => 'order_2',
    'description' => '测试订单',
    'amount' => ['total' => 1],
    'payer' => ['openid' => 'obaxI5YDv-3r4VEkSzKbOAq0Dw94'],
], 'https://xxx.com');
~~~

## query
- 说明: 订单号查询订单
- 官方文档: [微信支付订单号查询订单](https://pay.weixin.qq.com/docs/merchant/apis/jsapi-payment/query-by-wx-trade-no.html) [商户订单号查询订单](https://pay.weixin.qq.com/docs/merchant/apis/jsapi-payment/query-by-out-trade-no.html)
- 参数说明:
  + 以下参数二选一
    - `outRefundNo`: (string) 商户订单号
    - `transactionId`: (string) 微信支付订单号

请求示例
~~~php
<?php

$result = (new \lifetime\bridge\wechat\Payment)->query([
    'out_trade_no' => 'order_2',
]);
~~~

## close
- 说明: 关闭订单
- 官方文档: [关闭订单](https://pay.weixin.qq.com/docs/merchant/apis/jsapi-payment/close-order.html)
- 参数说明:
  + `outTradeNo`: (string) 商户订单号

请求示例
~~~php
<?php

(new \lifetime\bridge\wechat\Payment)->clse('order_2');
~~~

> 如果成功将返回 `null`, 如果失败将抛出异常 `InvalidResponseException`

## refund
- 说明: 退款申请
- 官方文档: [退款申请](https://pay.weixin.qq.com/docs/merchant/apis/jsapi-payment/create.html)
- 参数说明
  + `options`: (array) 请求参数
    - 以下参数二选一
      + `transaction_id`: (string) 微信支付订单号
      + `out_trade_no`: (string) 商户订单号
    - `out_refund_no`: (string) 商户退款单号
    - `amount`: (array)
      + `refund`: (int) 退款金额, 单位: 分
      + `total`: (int) 原订单金额, 单位: 分
      + `currency`: (int) 退款币种, 只支持人民币：CNY

请求示例
~~~php
<?php

$result = (new \lifetime\bridge\wechat\Payment)->refund([
  'out_trade_no' => 'order_1',
  'out_refund_no' => 'refund_1',
  'amount' => ['refund' => 1, 'total' => 1,'currency' => 'CNY'],
]);
~~~

## refundQuery
- 说明: 查询单笔退款（通过商户退款单号）
- 官方文档: (查询单笔退款（通过商户退款单号）)[https://pay.weixin.qq.com/docs/merchant/apis/jsapi-payment/query-by-out-refund-no.html]
- 参数说明:
  + `outRefundNo`: (string) 商户退款单号

请求示例
~~~php
<?php

(new \lifetime\bridge\wechat\Payment)->refundQuery('refund_2');
~~~

## notify
- 说明: 支付通知
- 官方文档: [支付通知](https://pay.weixin.qq.com/docs/merchant/apis/jsapi-payment/payment-notice.html)

示例
~~~php
<?php

echo (new \lifetime\bridge\wechat\Payment)->notify(function($data) {
  /** 
   * 当签名验证成功后会进入此闭包函数
   * 
   * 返回false，表示处理失败，会向微信服务返回失败的消息
   * 返回任何非false的结果，会向微信服务返回成功的消息
   */

});
~~~