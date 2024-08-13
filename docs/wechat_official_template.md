# 微信公众号模板消息

官方文档: [模板消息接口](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Template_Message_Interface.html)

## setIndustry
- 说明: 设置所属行业
- 参数说明
  + `industryId1`: (string) 行业编号1
  + `industryId2`: (string) 行业编号2

示例
~~~php

$result = (new \lifetime\bridge\Wechat\Official\Template())->setIndustry('industryId1', 'industryId1');

~~~

## getIndustry
- 说明: 获取所属行业
- 参数说明: 无

示例
~~~php

$result = (new \lifetime\bridge\Wechat\Official\Template())->getIndustry();

~~~

## addTemplate
- 说明: 添加模板
- 参数说明
  + `templateId`: (string) 板库中模板的编号
  + `keywordNameList`: (string) 选用的类目模板的关键词

示例
~~~php

$result = (new \lifetime\bridge\Wechat\Official\Template())->addTemplate('templateId', ['keyword1', 'keyword2']);

~~~

## getAllPrivateTemplate
- 说明: 获取模板列表
- 参数说明: 无

示例
~~~php

$result = (new \lifetime\bridge\Wechat\Official\Template())->getAllPrivateTemplate();

~~~

## deletePrivateTemplate
- 说明: 删除模板
- 参数说明
  + `templateId`: (string) 模板ID

示例
~~~php

$result = (new \lifetime\bridge\Wechat\Official\Template())->deletePrivateTemplate('templateId');

~~~

## send
- 说明: 发送模板消息
- 参数说明
  +  `toUser`: (string) 接收着OpenID
  + `templateId`: (string) 模板ID
  + `data`: (array) 数据['keyword1'=>['value'=>'xxx'],'keyword2'=>['value'=>'xxx']]
  + `url`: (string) 跳转连接
  + `miniProgram`: (array) 小程序所需参数['appid' => 'xxx','pagepath' => '']
  +  `clientMsgId`: (string) 防重入ID

示例
~~~php

$result = (new \lifetime\bridge\Wechat\Official\Template())->send(
  'openid',
  'templateId',
  [
    'keyword1' => ['value' => 'value1'],
    'keyword2' => ['value' => 'value2'],
  ]
);

~~~
