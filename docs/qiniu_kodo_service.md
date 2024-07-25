# 七牛云对象存储Service相关操作

## bucketList
- 说明: 获取Bucket列表
- 官方文档: [获取 Bucket 列表](https://developer.qiniu.com/kodo/3926/get-service)
- 参数说明
  + `tags`: (array) 过滤空间的标签或标签值['key1'=>'value1','key2'=>'value2']

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Service())->bucketList(['tag1' => 'v1']);
~~~