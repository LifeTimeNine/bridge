# 微信小程序登录

## code2session
- 说明: 小程序登录
- 官方文档: [小程序登录/小程序登录](https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/user-login/code2Session.html)
- 参数说明
  + `jsCode`: (string) 登录时获取的 code，可通过wx.login获取
  + `grantType`: (string) 授权类型，此处只需填写 authorization_code

示例
~~~php
$result = (new \lifetime\bridge\wechat\miniapp\Login())->code2session('js_code');
~~~

## checkSession
- 说明: 检验登录态
- 官方文档: [小程序登录/检验登录态](https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/user-login/checkSessionKey.html)
- 参数说明
  + `openid`: (string) 用户OpenID
  + `signature`: (string) 用户登录态签名(hash('sha256', $sessionKey))
  + `sigMethod`: (string) 用户登录态签名的哈希方法，目前只支持 hmac_sha256

示例
~~~php
$result = (new \lifetime\bridge\wechat\miniapp\Login())->checkSession('openid', hash('sha256', 'session_key'));
~~~

## resetSession
- 说明: 重置登录态
- 官方文档: [小程序登录/重置登录态](https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/user-login/ResetUserSessionKey.html)
- 参数说明
  + `openid`: (string) 用户OpenID
  + `signature`: (string) 用户登录态签名(hash('sha256', $sessionKey))
  + `sigMethod`: (string) 用户登录态签名的哈希方法，目前只支持 hmac_sha256

示例
~~~php
$result = (new \lifetime\bridge\wechat\miniapp\Login())->resetSession('openid', hash('sha256', 'session_key'));
~~~
