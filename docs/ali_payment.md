## app
- 说明: APP支付
- 官方文档: [APP支付接口2.0](https://opendocs.alipay.com/open/cd12c885_alipay.trade.app.pay?scene=20&pathHash=ab686e33)
- 参数说明
 + `order`: (array) 订单参数，仅列出必选参数，其他参数请参考官方文档
    - `out_trade_no`: (string) 商户网站唯一订单号
    - `total_amount`: (string) 订单总金额，单位为元，精确到小数点后两位
    - `subject`: (string) 订单标题
  + `notifyUrl`: (string) 异步回调地址

请求示例
~~~php
<?php

$result = (new \lifetime\bridge\ali\Payment)->app([
  'out_trade_no' => 'order_1',
  'total_amount' => '0.01',
  'subject' => '测试订单'
], 'https://xxx/');
~~~

## wap
- 说明: 手机网站支付
- 官方文档: [手机网站支付接口2.0](https://opendocs.alipay.com/open/29ae8cb6_alipay.trade.wap.pay?scene=21&pathHash=1ef587fd)
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

$result = (new \lifetime\bridge\ali\Payment)->wap([
  'out_trade_no' => 'order_1',
  'total_amount' => '0.01',
  'subject' => '测试订单'
], 'https://xxx/', 'https://xxx');
~~~

## page
- 说明:  电脑网站支付
- 官方文档: [电脑网站支付统一收单下单并支付页面接口](https://opendocs.alipay.com/open/59da99d0_alipay.trade.page.pay?scene=22&pathHash=e26b497f)
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

$result = (new \lifetime\bridge\ali\Payment)->page([
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

echo (new \lifetime\bridge\ali\Payment)->notify(function($data, $checkRes) {
  if ($checkRes) {
    // 如果签名验证成功
    /**
     * 业务处理
     * xxxxx
    */
    return true;
  } else {
    // 如果签名验证失败
    return false;
  }
});
~~~

## query
- 说明: 订单查询
- 官方文档: [统一收单交易查询](https://opendocs.alipay.com/open/4e2d51d1_alipay.trade.query?scene=common&pathHash=8abc6ffe)
- 参数说明
  + `options`: (array) 请求参数，仅列出必选参数，其他参数请参考官方文档
    - 以下参数二选一，不能同时为空
      + `out_trade_no`: (string) 商户订单号
      + `trade_no`: (string) 支付宝订单号

示例
~~~php
<?php

$result = (new \lifetime\bridge\ali\Payment)->query([
  'out_trade_no' => 'order_1'
]);
~~~

## refund
- 说明: 退款
- 官方文档: [统一收单交易退款接口](https://opendocs.alipay.com/open/4b7cc5db_alipay.trade.refund?scene=common&pathHash=d98b006d)
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

$result = (new \lifetime\bridge\ali\Payment)->query([
  'out_trade_no' => 'order_1',
  'refund_amount' => '0.01',
  'out_request_no' => 'refund_1'
]);
~~~

## refundQuery
- 说明: 退款查询
- 官方文档: [统一收单交易退款查询](https://opendocs.alipay.com/open/8c776df6_alipay.trade.fastpay.refund.query?scene=common&pathHash=fb6e1894)
- 参数说明
  + `options`: (array) 请求参数，仅列出必选参数，其他参数请参考官方文档
    - 以下参数二选一，不能同时为空
      + `out_trade_no`: (string) 商户订单号
      + `trade_no`: (string) 支付宝订单号
  + `out_request_no`: (string)退款请求号

示例
~~~php
<?php

$result = (new \lifetime\bridge\ali\Payment)->refundQuery([
  'out_trade_no' => 'order_1',
  'out_request_no' => 'refund_1'
]);
~~~

## tradeClose
- 说明: 交易关闭
- 官方文档: [统一收单交易关闭接口](https://opendocs.alipay.com/open/ce0b4954_alipay.trade.close?scene=common&pathHash=7b0fdae1)
- 参数说明
  + `options`: (array) 请求参数，仅列出必选参数，其他参数请参考官方文档
    - 以下参数二选一，不能同时为空
      + `out_trade_no`: (string) 商户订单号
      + `trade_no`: (string) 支付宝订单号

示例
~~~php
<?php

$result = (new \lifetime\bridge\ali\Payment)->tradeClose([
  'out_trade_no' => 'order_1'
]);
~~~