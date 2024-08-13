# 微信公众号网页授权

官方文档：[网页授权](https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/Wechat_webpage_authorization.html)

## authorize
- 说明: 这是网页授权的第一步, 跳转到微信授权, 获取Code
- 官方文档: [第一步：用户同意授权，获取code](https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/Wechat_webpage_authorization.html#0)
- 参数说明
  + `redirectUri`: (string) 授权后跳转的地址
  + `scope`: (bool) 是否获取用户详情信息
  + `state`: (string) 重定向后会带上state参数

示例
~~~php
(new \lifetime\bridge\Wechat\Official\Oauth())->authorize('http://xxx.com');
~~~

> 注意调用此方法之后前端会跳转，因此不可以有任何返回数据

## getUserAccessToken
- 说明: 这是网页授权第二步，通过Code获取用户访问Token
- 官方文档: [第二步：通过code换取网页授权access_token](https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/Wechat_webpage_authorization.html#1)
- 参数说明: 无

示例
~~~php
$result = (new \lifetime\bridge\Wechat\Official\Oauth())->getUserAccessToken();
~~~

## refreshAccessToken
- 说明: 刷新访问Token
- 官方文档: [刷新access_token](https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/Wechat_webpage_authorization.html#2)
- 参数说明
  + `refreshToken`: (string) `getUserAccessToken`中返回的刷新Token

示例
~~~php
$result = (new \lifetime\bridge\Wechat\Official\Oauth())->refreshAccessToken('refresh_token');
~~~

## getUserInfo
- 说明: 获取用户个人信息（UnionID机制）
- 官方文档: [拉取用户信息(需scope为 snsapi_userinfo)](https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/Wechat_webpage_authorization.html#3)
- 参数说明
  + `accessToken`: (string) 访问Token
  + `openid`: (string) 用户OpenId

示例
~~~php
$result = (new \lifetime\bridge\Wechat\Official\Oauth())->getUserInfo('access_token', 'openid');
~~~

## checkAccessToken
- 说明: 校验授权凭证是否有效
- 官方文档: [检验授权凭证（access_token）](https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/Wechat_webpage_authorization.html#4)
- 参数说明
  + `accessToken`: (string) 访问Token
  + `openid`: (string) 用户OpenId

示例
~~~php
$result = (new \lifetime\bridge\Wechat\Official\Oauth())->checkAccessToken('access_token', 'openid');
~~~

## getJsSdkSign
- 说明: 获取JS-SDK使用权限
- 官方文档: [JS-SDK说明文档](https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/JS-SDK.html)
- 参数说明
  + `url`: (string) 当前网页的URL，不包含#及其后面部分

示例
~~~php
$result = (new \lifetime\bridge\Wechat\Official\Oauth())->getJsSdkSign('url');
~~~