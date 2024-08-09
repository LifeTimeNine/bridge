# 七牛云对象存储 Object 相关操作

## upload
- 说明: 直传文件
- 官方文档: [直传文件](https://developer.qiniu.com/kodo/1312/upload)
- 参数说明
  + `filename`: (string) 文件名
  + `data`: (string) 数据
  + `storageType`: (int) 存储类型(0-标准存储,1-低频存储,2-归档存储,3-深度归档存储,4-归档直读存储)
  + `customList`: (array) 自定义变量列表['key1' => 'value1', 'key2' => 'value2']
  + `metaList`: (array) 自定义元数据['key1' => 'value1', 'key2' => 'value2']
  + `expire`: (int) 有效时间

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->upload('a.txt','a');
~~~

> 此方法只对接了常用的参数，如果不满足需求，请继承`\lifetime\bridge\qiniu\kodo\Basic`类重新封装

## clientUpload
- 说明: 客户端直传文件
- 官方文档: [直传文件](https://developer.qiniu.com/kodo/1312/upload)
- 参数说明
  + `filename`: (string) 文件名
  + `storageType`: (int) 存储类型(0-标准存储,1-低频存储,2-归档存储,3-深度归档存储,4-归档直读存储)
  + `customList`: (array) 自定义变量列表['key1' => 'value1', 'key2' => 'value2']
  + `metaList`: (array) 自定义元数据['key1' => 'value1', 'key2' => 'value2']
  + `expire`: (int) 有效时间

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->clientUpload('a.txt','a');
~~~

> 此方法只对接了常用的参数，如果不满足需求，请继承`\lifetime\bridge\qiniu\kodo\Basic`类重新封装

## initPart
- 说明: 初始化分片上传
- 官方文档: [初始化任务](https://developer.qiniu.com/kodo/6365/initialize-multipartupload)
- 参数说明
  + `filename`: (string) 文件名称
  + `storageType`: (int) 存储类型(0-标准存储,1-低频存储,2-归档存储,3-深度归档存储,4-归档直读存储)
  + `expire`: (int) 有效时间

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->initPart('a.txt');
~~~

## uploadPart
- 说明: 分片上传数据
- 官方文档: [分块上传数据](https://developer.qiniu.com/kodo/6366/upload-part)
- 参数说明
  + `filename`: (string) 文件名称
  + `uploadId`: (string) 任务Id
  + `partNumber`: (int) 上传标记(0-1000,大小1MB-1GB)
  + `data`: (string) 上传的数据
  + `expire`: (int) 有效时间

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->initPart('a.txt', 'upload_id', 1, 'a');
~~~

## clientUploadPart
- 说明: 客户端分片上传数据
- 官方文档: [分块上传数据](https://developer.qiniu.com/kodo/6366/upload-part)
- 参数说明
  + `filename`: (string) 文件名称
  + `uploadId`: (string) 任务Id
  + `partNumber`: (int) 上传标记(0-1000,大小1MB-1GB)
  + `expire`: (int) 有效时间

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->clientUploadPart('a.txt', 'upload_id', 1);
~~~

## completePart
- 说明: 完成分片上传
- 官方文档: [完成分片上传](https://developer.qiniu.com/kodo/6368/complete-multipart-upload)
- 参数说明
  + `filename`: (string) 文件名称
  + `uploadId`: (string) 任务Id
  + `data`: (array) 参数['partNumber1' => 'etag1', 'partNumber2' => 'etag2']
  + `expire`: (int) 有效时间

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->completePart('a.txt', 'upload_id', ['1' => 'etag1', '2' => 'etag2']);
~~~

## stopPart
- 说明: 终止分片上传任务
- 官方文档: [终止上传](https://developer.qiniu.com/kodo/6367/abort-multipart-upload)
- 参数说明
  + `filename`: (string) 文件名称
  + `uploadId`: (string) 任务Id

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->stopPart('a.txt', 'upload_id');
~~~

## partList
- 说明: 列举已经上传的分片
- 官方文档: [列举已上传分片](https://developer.qiniu.com/kodo/6858/listparts)
- 参数说明
  + `filename`: (string) 文件名称
  + `uploadId`: (string) 任务Id
  + `partNumberMarker`: (int) 指定列举的起始位置
  + `maxParts`: (int) 响应中的最大 Part 数目

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->partList('a.txt', 'upload_id');
~~~

## list
- 说明: 资源列举
- 官方文档: [资源列举](https://developer.qiniu.com/kodo/1284/list)
- 参数说明
  + `marker`: (string) 上一次列举返回的位置标记,作为本次列举的起点信息
  + `limit`: (int) 本次列举的条目数,范围为1-1000
  + `prefix`: (string) 指定前缀,只有资源名匹配该前缀的资源会被列出。
  + `delimiter`: (string) 指定目录分隔符,列出所有公共前缀(模拟列出目录效果)

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->list();
~~~

## getMetaData
- 说明: 获取资源元信息
- 官方文档: [资源元信息查询](https://developer.qiniu.com/kodo/1308/stat)
- 参数说明
  + `filename`: (string) 文件名称

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->getMetaData('a.txt');
~~~

## setMetaData
- 说明: 修改资源元信息
- 官方文档: [资源元信息修改](https://developer.qiniu.com/kodo/1252/chgm)
- 参数说明
  + `filename`: (string) 文件名称
  + `mimeType`: (string) 新的 mimeType
  + `metaList`: (array) 新的Meta数据['key1' => 'value1', 'key2' => 'value2']
  + `cond`: (array) 自定义条件信息['hash' => 'xxxx', 'mime' => 'text/plain']

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->setMetaData('a.txt', 'text/plain', ['a' => '1']);
~~~

## move
- 说明: 移动资源
- 官方文档: [资源移动／重命名](https://developer.qiniu.com/kodo/1288/move)
- 参数说明
  + `sourceFilename`: (string) 源文件名
  + `targetFilename`: (string) 目标文件名
  + `targetBucket`: (string) 目标空间(留空表示与源文件同一空间)
  + `forceCover`: (bool) 强制覆盖目标资源

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->move('a.txt', 'b.txt');
~~~

## copy
- 说明: 复制资源
- 官方文档: [资源复制](https://developer.qiniu.com/kodo/1254/copy)
- 参数说明
  + `sourceFilename`: (string) 源文件名
  + `targetFilename`: (string) 目标文件名
  + `targetBucket`: (string) 目标空间(留空表示与源文件同一空间)
  + `forceCover`: (bool) 强制覆盖目标资源

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->copy('a.txt', 'b.txt');
~~~

## delete
- 说明: 删除资源
- 官方文档: [资源删除](https://developer.qiniu.com/kodo/1257/delete)
- 参数说明
  + `filename`: (string) 文件名称

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->delete('a.txt');
~~~

## setStatus
- 说明: 修改文件状态
- 官方文档: [修改文件状态](https://developer.qiniu.com/kodo/4173/modify-the-file-status)
- 参数说明
  + `filename`: (string) 文件名称
  + `disable`: (bool) 禁用

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->setStatus('a.txt', false);
~~~

## setStorageType
- 说明: 修改文件存储类型
- 官方文档: [修改文件存储类型](https://developer.qiniu.com/kodo/3710/chtype)
- 参数说明
  + `filename`: (string) 文件名称
  + `storageType`: (int) 存储类型(0-标准存储,1-低频存储,2-归档存储,3-深度归档存储,4-归档直读存储)

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->setStorageType('a.txt', false);
~~~

## thaw
- 说明: 解冻归档/深度归档存储文件
- 官方文档: [解冻归档/深度归档存储文件](https://developer.qiniu.com/kodo/6380/restore-archive)
- 参数说明
  + `filename`: (string) 文件名称
  + `duration`: (int) 解冻时长(1-7天)

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->thaw('a.txt', 1);
~~~

## setExpireDeleteDuration
- 说明: 修改文件过期删除时间
- 官方文档: [修改文件过期删除时间](https://developer.qiniu.com/kodo/1732/update-file-lifecycle)
- 参数说明
  + `filename`: (string) 文件名称
  + `duration`: (int) 时长（天，设置为 0 表示取消过期删除设置）

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->setExpireDeleteDuration('a.txt', 1);
~~~

## setLifecycle
- 说明: 修改文件生命周期
- 官方文档: [修改文件生命周期](https://developer.qiniu.com/kodo/8062/modify-object-life-cycle)
- 参数说明
  + `filename`: (string) 文件名称
  + `toIAAfterDays`: (int) 上传后N天转换为低频存储类型, 设置-1表示取消设置
  + `toArchiveIRAfterDays`: (int) 上传后N天转换为归档直读存储类型, 设置-1表示取消设置
  + `toArchiveAfterDays`: (int) 上传后N天转换为归档存储类型, 设置-1表示取消设置
  + `toDeepArchiveAfterDays`: (int) 上传后N天转换为深度归档存储类型, 设置-1表示取消设置
  + `deleteAfterDays`: (int) 上传后N天删除, 设置-1表示取消设置

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->setLifecycle('a.txt', null, null, 1);
~~~

## imageSourceUpdate
- 说明: 镜像资源更新
- 官方文档: [镜像资源更新](https://developer.qiniu.com/kodo/1293/prefetch)
- 参数说明
  + `filename`: (string) 文件名称

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->imageSourceUpdate('a.txt');
~~~

## createAsyncFetchTask
- 说明: 发起异步抓取任务
- 官方文档: [异步第三方资源抓取](https://developer.qiniu.com/kodo/4097/asynch-fetch)
- 参数说明
  + `url`: (string) 需要抓取的 url,支持设置多个用于高可用,以';'分隔,
  + `options`: (array) 参数(除url,bucket以外的参数)

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->createAsyncFetchTask('https://www.xxx.com/a.png', ['key' => 'a.png']);
~~~

## queryAsyncFetchTask
- 说明: 查询异步抓取任务
- 官方文档: [镜像资源更新](https://developer.qiniu.com/kodo/1293/prefetch)
- 参数说明
  + `taskId`: (string) 任务ID

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->queryAsyncFetchTask('task_id');
~~~

## batch
- 说明: 批量操作, 支持 查询元信息、修改元信息、移动、复制、删除、修改状态、修改存储类型、修改生命周期 和 解冻 操作，所有操作名称和参数名称参考具体的方法
- 官方文档: [批量操作](https://developer.qiniu.com/kodo/1250/batch)
- 参数说明
  + `operationList`: (array) 操作列表([['move', ['a.txt', 'b.txt', null, true]], ['delete', ['a.txt']]])

示例
~~~php
$result = (new \lifetime\bridge\qiniu\kodo\Objects())->batch([
    ['setMetaData', ['test.txt', null, ['b' => '3']]],
    ['getMetaData', ['test.txt']],
    ['copy', ['test3.txt', 'test2.txt']],
    ['delete', ['test3.txt']]
]);
~~~
