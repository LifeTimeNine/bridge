# 微信小程序用户信息

## getPluginOpenPid
- 说明: 获取插件用户OpenID
- 官方文档: [用户信息/用户信息/获取插件用户openpid](https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/user-info/basic-info/getPluginOpenPId.html)
- 参数说明
  + `code`: (string) 通过 wx.pluginLogin 获得的插件用户标志凭证 code

示例
~~~php
$result = (new \lifetime\bridge\wechat\miniapp\User())->getPluginOpenPid('code');
~~~

## checkEncryptedData
- 说明: 检查加密信息
- 官方文档: [用户信息/用户信息/检查加密信息](https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/user-info/basic-info/checkEncryptedData.html)
- 参数说明
  + `encryptedMsgHash`: (string) 加密数据的sha256，通过Hex（Base16）编码后的字符串

示例
~~~php
$result = (new \lifetime\bridge\wechat\miniapp\User())->checkEncryptedData('657edd868c9715a9bebe42b833269a557a48498785397a796f1568c29a200b2c');
~~~

## getPaidUnionId
- 说明: 支付后获取 Unionid
- 官方文档: [用户信息/用户信息/支付后获取 Unionid](https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/user-info/basic-info/getPaidUnionid.html)
- 参数说明
  + `openid`: (string) 用户OpenID
  + `transactionId`: (string) 微信支付订单号
  + `mchId`: (string) 微信支付分配的商户号，和商户订单号配合使用
  + `outTradeNo`: (string) 微信支付商户订单号，和商户号配合使用

示例
~~~php
$result = (new \lifetime\bridge\wechat\miniapp\User())->getPaidUnionId('openid');
~~~

> 注意事项  
> - 调用前需要用户完成支付，且在支付后的五分钟内有效。  
> - 使用微信支付订单号（transactionId）和微信支付商户订单号和微信支付商户号（outTradeNo 及 mchId），二选一

## getUserEncryptKey
- 说明: 获取用户encryptKey
- 官方文档: [用户信息/网络/获取用户encryptKey](https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/user-info/internet/getUserEncryptKey.html)
- 参数说明
  + `openid`: (string) 用户OpenID
  + `signature`: (string) 用户登录态签名(hash('sha256', $sessionKey))
  + `sigMethod`: (string) 签名方法，只支持 hmac_sha256

示例
~~~php
$result = (new \lifetime\bridge\wechat\miniapp\User())->getUserEncryptKey('openid', 'signature');
~~~

## getPhoneNumber
- 说明: 获取手机号
- 官方文档: [用户信息/手机号/手机号快速验证](https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/user-info/phone-number/getPhoneNumber.html)
- 参数说明
  + `code`: (string) 通过 手机号获取凭证
  + `openid`: (string) 用户OpenID

示例
~~~php
$result = (new \lifetime\bridge\wechat\miniapp\User())->getPhoneNumber('code');
~~~

## check
- 说明: 验证用户信息
- 官方文档: [开放能力/用户信息/开放数据校验与解密](https://developers.weixin.qq.com/miniprogram/dev/framework/open-ability/signature.html)
- 参数说明
  + `rawData`: (string) 数据字符串
  + `signature`: (string) 签名字符串
  + `sessionKey`: (string) session_key

示例
~~~php
$result = (new \lifetime\bridge\wechat\miniapp\User())->check('raw_data', 'signature', 'session_key');
~~~

## decodeUserInfo
- 说明: 用户信息解密
- 官方文档: [开放能力/用户信息/开放数据校验与解密](https://developers.weixin.qq.com/miniprogram/dev/framework/open-ability/signature.html)
- 参数说明
  + `encryptedData`: (string) 密文
  + `iv`: (string) 解密算法初始向量
  + `sessionKey`: (string) session_key

示例
~~~php
$result = (new \lifetime\bridge\wechat\miniapp\User())->decodeUserInfo('encrypted_data', 'iv', 'session_key');
~~~
