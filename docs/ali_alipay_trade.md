## app
- 说明: APP支付
- 官方文档: [APP支付接口2.0](https://opendocs.alipay.com/open/cd12c885_alipay.trade.app.pay)
- 参数说明
 + `order`: (array) 订单参数，仅列出必选参数，其他参数请参考官方文档
    - `out_trade_no`: (string) 商户网站唯一订单号
    - `total_amount`: (string) 订单总金额，单位为元，精确到小数点后两位
    - `subject`: (string) 订单标题
  + `notifyUrl`: (string) 异步回调地址

请求示例
~~~php
<?php

$result = (new \lifetime\bridge\Ali\Trade)->app([
  'out_trade_no' => 'order_1',
  'total_amount' => '0.01',
  'subject' => '测试订单'
], 'https://xxx/');
~~~

## wap
- 说明: 手机网站支付
- 官方文档: [手机网站支付接口2.0](https://opendocs.alipay.com/open/29ae8cb6_alipay.trade.wap.pay)
- 参数说明
 + `order`: (array) 订单参数，仅列出必选参数，其他参数请参考官方文档
    - `out_trade_no`: (string) 商户网站唯一订单号
    - `total_amount`: (string) 订单总金额，单位为元，精确到小数点后两位
    - `subject`: (string) 订单标题
  + `notifyUrl`: (string) 异步回调地址
  + `returnUrl`: (string) 同步跳转地址

请求示例
~~~php
<?php

$result = (new \lifetime\bridge\Ali\Trade)->wap([
  'out_trade_no' => 'order_1',
  'total_amount' => '0.01',
  'subject' => '测试订单'
], 'https://xxx/', 'https://xxx');
~~~

## page
- 说明:  电脑网站支付
- 官方文档: [电脑网站支付统一收单下单并支付页面接口](https://opendocs.alipay.com/open/59da99d0_alipay.trade.page.pay)
- 参数说明
 + `order`: (array) 订单参数，仅列出必选参数，其他参数请参考官方文档
    - `out_trade_no`: (string) 商户网站唯一订单号
    - `total_amount`: (string) 订单总金额，单位为元，精确到小数点后两位
    - `subject`: (string) 订单标题
  + `notifyUrl`: (string) 异步回调地址
  + `returnUrl`: (string) 同步跳转地址

请求示例
~~~php
<?php

$result = (new \lifetime\bridge\Ali\Trade)->page([
  'out_trade_no' => 'order_1',
  'total_amount' => '0.01',
  'subject' => '测试订单'
], 'https://xxx/', 'https://xxx');
~~~

## notify
- 说明: 对支付后异步的通知进行处理

示例
~~~php
<?php

echo (new \lifetime\bridge\Ali\Trade)->notify(function($data) {
  /** 
   * 当签名验证成功后会进入此闭包函数
   * 
   * 返回false，表示处理失败，会向支付宝服务返回失败的消息
   * 返回任何非false的结果，会向支付宝服务返回成功的消息
   */
});
~~~

## query
- 说明: 订单查询
- 官方文档: [统一收单交易查询](https://opendocs.alipay.com/open/4e2d51d1_alipay.trade.query)
- 参数说明
  + `options`: (array) 请求参数，仅列出必选参数，其他参数请参考官方文档
    - 以下参数二选一，不能同时为空
      + `out_trade_no`: (string) 商户订单号
      + `trade_no`: (string) 支付宝订单号

示例
~~~php
<?php

$result = (new \lifetime\bridge\Ali\Trade)->query([
  'out_trade_no' => 'order_1'
]);
~~~

## refund
- 说明: 退款
- 官方文档: [统一收单交易退款接口](https://opendocs.alipay.com/open/4b7cc5db_alipay.trade.refund)
- 参数说明
  + `options`: (array) 请求参数，仅列出必选参数，其他参数请参考官方文档
    - 以下参数二选一，不能同时为空
      + `out_trade_no`: (string) 商户订单号
      + `trade_no`: (string) 支付宝订单号
  + `refund_amount`: (float)退款金额
  + `out_request_no`: (string)退款请求号

示例
~~~php
<?php

$result = (new \lifetime\bridge\Ali\Trade)->refund([
  'out_trade_no' => 'order_1',
  'refund_amount' => '0.01',
  'out_request_no' => 'refund_1'
]);
~~~

## refundQuery
- 说明: 退款查询
- 官方文档: [统一收单交易退款查询](https://opendocs.alipay.com/open/8c776df6_alipay.trade.fastpay.refund.query)
- 参数说明
  + `options`: (array) 请求参数，仅列出必选参数，其他参数请参考官方文档
    - 以下参数二选一，不能同时为空
      + `out_trade_no`: (string) 商户订单号
      + `trade_no`: (string) 支付宝订单号
  + `out_request_no`: (string)退款请求号

示例
~~~php
<?php

$result = (new \lifetime\bridge\Ali\Trade)->refundQuery([
  'out_trade_no' => 'order_1',
  'out_request_no' => 'refund_1'
]);
~~~

## tradeClose
- 说明: 交易关闭
- 官方文档: [统一收单交易关闭接口](https://opendocs.alipay.com/open/ce0b4954_alipay.trade.close)
- 参数说明
  + `options`: (array) 请求参数，仅列出必选参数，其他参数请参考官方文档
    - 以下参数二选一，不能同时为空
      + `out_trade_no`: (string) 商户订单号
      + `trade_no`: (string) 支付宝订单号

示例
~~~php
<?php

$result = (new \lifetime\bridge\Ali\Trade)->tradeClose([
  'out_trade_no' => 'order_1'
]);
~~~