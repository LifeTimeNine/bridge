# 微信公众号用户相关

## createTag
- 说明: 创建标签
- 官方文档: [用户标签管理/创建标签](https://developers.weixin.qq.com/doc/offiaccount/User_Management/User_Tag_Management.html)
- 参数说明
  + `name`: (string) 标签名称

示例
~~~php
$result = (new \lifetime\bridge\wechat\official\User())->createTag('name');
~~~

## getTag
- 说明: 获取已经创建的标签
- 官方文档: [用户标签管理/获取公众号已创建的标签](https://developers.weixin.qq.com/doc/offiaccount/User_Management/User_Tag_Management.html)
- 参数说明: 无

示例
~~~php
$result = (new \lifetime\bridge\wechat\official\User())->getTag();
~~~

## updateTag
- 说明: 更新标签信息
- 官方文档: [用户标签管理/编辑标签](https://developers.weixin.qq.com/doc/offiaccount/User_Management/User_Tag_Management.html)
- 参数说明
  + `tagId`: (int) 标签ID
  + `name`: (string) 标签名称

示例
~~~php
$result = (new \lifetime\bridge\wechat\official\User())->updateTag(1, 'name');
~~~

## deleteTag
- 说明: 删除标签
- 官方文档: [用户标签管理/删除标签](https://developers.weixin.qq.com/doc/offiaccount/User_Management/User_Tag_Management.html)
- 参数说明
  + `tagId`: (int) 标签ID

示例
~~~php
$result = (new \lifetime\bridge\wechat\official\User())->deleteTag(1);
~~~

## getTagUser
- 说明: 获取某个标签下的用户列表
- 官方文档: [用户标签管理/获取标签下粉丝列表](https://developers.weixin.qq.com/doc/offiaccount/User_Management/User_Tag_Management.html)
- 参数说明
  + `tagId`: (int) 标签ID
  + `nextOpenid`: (string) 第一个拉取的OPENID，不填默认从头开始拉取

示例
~~~php
$result = (new \lifetime\bridge\wechat\official\User())->getTagUser(1, 'openid');
~~~

## batchBindTag
- 说明: 批量为用户绑定标签
- 官方文档: [用户标签管理/批量为用户打标签](https://developers.weixin.qq.com/doc/offiaccount/User_Management/User_Tag_Management.html)
- 参数说明
  + `tagId`: (int) 标签ID
  + `openidList`: (array) 用户OpenID列表

示例
~~~php
$result = (new \lifetime\bridge\wechat\official\User())->batchBindTag(1, ['openid1','openid2']);
~~~

## batchUnBindTag
- 说明: 批量为用户解绑标签
- 官方文档: [用户标签管理/批量为用户取消标签](https://developers.weixin.qq.com/doc/offiaccount/User_Management/User_Tag_Management.html)
- 参数说明
  + `tagId`: (int) 标签ID
  + `openidList`: (array) 用户OpenID列表

示例
~~~php
$result = (new \lifetime\bridge\wechat\official\User())->batchUnBindTag(1, ['openid1','openid2']);
~~~

## getUserTag
- 说明: 获取用户绑定的标签
- 官方文档: [用户标签管理/获取用户身上的标签列表](https://developers.weixin.qq.com/doc/offiaccount/User_Management/User_Tag_Management.html)
- 参数说明
  + `openid`: (string) 用户OpenID

示例
~~~php
$result = (new \lifetime\bridge\wechat\official\User())->getUserTag('openid1');
~~~

## updateRemark
- 说明: 设置用户备注名
- 官方文档: [用户标签管理/设置用户备注名](https://developers.weixin.qq.com/doc/offiaccount/User_Management/Configuring_user_notes.html)
- 参数说明
  + `openid`: (string) 用户OpenID
  + `remark`: (string) 备注名

示例
~~~php
$result = (new \lifetime\bridge\wechat\official\User())->updateRemark('openid', 'remark');
~~~

## getUserInfo
- 说明: 获取用户基本信息(UnionID机制)
- 官方文档: [用户标签管理/创建标获取用户基本信息(UnionID机制)](https://developers.weixin.qq.com/doc/offiaccount/User_Management/Get_users_basic_information_UnionID.html#UinonId)
- 参数说明
  + `openid`: (string) 用户OpenID

示例
~~~php
$result = (new \lifetime\bridge\wechat\official\User())->getUserInfo('openid');
~~~

## batchGetUserInfo
- 说明: 批量获取用户基本信息
- 官方文档: [用户标签管理/批量获取用户基本信息](https://developers.weixin.qq.com/doc/offiaccount/User_Management/Get_users_basic_information_UnionID.html#UinonId)
- 参数说明
  + `openidList`: (array) 用户OpenID列表

示例
~~~php
$result = (new \lifetime\bridge\wechat\official\User())->batchGetUserInfo(['openid1']);
~~~

## getUserList
- 说明: 获取用户列表
- 官方文档: [用户标签管理/获取用户列表](https://developers.weixin.qq.com/doc/offiaccount/User_Management/Getting_a_User_List.html)
- 参数说明
  + `nextOpenid`: (string) 第一个拉取的OPENID，不填默认从头开始拉取

示例
~~~php
$result = (new \lifetime\bridge\wechat\official\User())->getUserList('openid');
~~~

## getBlackList
- 说明: 获取黑名单列表
- 官方文档: [用户标签管理/获取公众号的黑名单列表](https://developers.weixin.qq.com/doc/offiaccount/User_Management/Manage_blacklist.html)
- 参数说明
  + `beginOpenid`: (string) 第一个拉取的OPENID，不填默认从头开始拉取

示例
~~~php
$result = (new \lifetime\bridge\wechat\official\User())->getBlackList('openid');
~~~

## batchBlack
- 说明: 批量拉黑用户
- 官方文档: [用户标签管理/拉黑用户](https://developers.weixin.qq.com/doc/offiaccount/User_Management/Manage_blacklist.html)
- 参数说明
  + `openidList`: (array) 用户OpenID列表

示例
~~~php
$result = (new \lifetime\bridge\wechat\official\User())->batchBlack(['openid1']);
~~~

## batchUnBlack
- 说明: 批量取消拉黑用户
- 官方文档: [用户标签管理/取消拉黑用户](https://developers.weixin.qq.com/doc/offiaccount/User_Management/Manage_blacklist.html)
- 参数说明
  + `openidList`: (array) 用户OpenID列表

示例
~~~php
$result = (new \lifetime\bridge\wechat\official\User())->batchUnBlack(['openid1']);
~~~

