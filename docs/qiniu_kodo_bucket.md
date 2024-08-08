# 七牛云对象存储 Bucket 相关操作

## getRegionList
- 说明: 获取存储区域列表
- 官方文档: [存储区域](https://developer.qiniu.com/kodo/1671/region-endpoint-fq)
- 参数说明: 无

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Bucket())->getRegionList();
~~~

> 如果发现返回的数据与官方文档不符时，请提交issue！

## list
- 说明: 获取Bucket列表
- 官方文档: [获取 Bucket 列表](https://developer.qiniu.com/kodo/3926/get-service)
- 参数说明
  + `tags`: (array) 过滤空间的标签或标签值['key1'=>'value1','key2'=>'value2']

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Bucket())->list(['tag1' => 'v1']);
~~~

## create
- 说明: 创建Bucket
- 官方文档: [创建 Bucket](https://developer.qiniu.com/kodo/1382/mkbucketv3)
- 参数说明
  + `regionId`: (string) 区域ID, 具体请参考`getRegionList`的返回值

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Bucket())
  ->setBucketName('bucket')
  ->create('z1');
~~~

## delete
- 说明: 删除Bucket
- 官方文档: [删除 Bucket](https://developer.qiniu.com/kodo/1601/drop-bucket)
- 参数说明: 无

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Bucket())
  ->setBucketName('bucket')
  ->delete();
~~~

## getDomain
- 说明: 获取Bucket空间域名
- 官方文档: [获取 Bucket 空间域名](https://developer.qiniu.com/kodo/3949/get-the-bucket-space-domain)
- 参数说明： 无

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Bucket())->getDomain();
~~~

## setImageSource
- 说明: 设置镜像源
- 官方文档: [设置 Bucket 镜像源](https://developer.qiniu.com/kodo/3966/bucket-image-source)
- 参数说明
  + `accessUrl`: (string) 镜像源的访问域名
  + `host`: (string) 回源时使用的Host头部值

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Bucket())->setImageSource('http://xxx.com');
~~~

## setAccessAuth
- 说明: 设置访问权限
- 官方文档: [设置 Bucket 访问权限](https://developer.qiniu.com/kodo/3946/set-bucket-private)
- 参数说明
  + `private`: (bool) 是否是私有

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Bucket())->setAccessAuth(true);
~~~

## setTag
- 说明: 设置空间标签
- 官方文档: [设置空间标签](https://developer.qiniu.com/kodo/6314/put-bucket-tagging)
- 参数说明
  + `tagList`: (array) 标签列表['key1'=>'value1','key2'=>'value2']

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Bucket())->setTag(['a' => 1, 'b' => 2]);
~~~

## getTag
- 说明: 获取空间标签
- 官方文档: [查询空间标签](https://developer.qiniu.com/kodo/6315/get-bucket-tagging)
- 参数说明: 无

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Bucket())->getTag();
~~~

## deleteTag
- 说明: 删除空间标签
- 官方文档: [删除空间标签](https://developer.qiniu.com/kodo/6316/delete-bucket-tagging)
- 参数说明: 无

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Bucket())->deleteTag();
~~~
