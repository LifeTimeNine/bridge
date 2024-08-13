## accountQuery
- 说明: 资产查询
- 官方文档: [支付宝资金账户资产查询接口](https://opendocs.alipay.com/open-v3/77e2b925_alipay.fund.account.query)
- 参数说明
  + `accountType`: (string) 账号类型(ACCTRANS_ACCOUNT-余额户,TRUSTEESHIP_ACCOUNT-托管账户)
  + `alipayOpenId`: (string) 支付宝openId
  + `alipayUserId`: (string) 支付宝会员 id

示例
~~~php
$result = (new \lifetime\bridge\Ali\Alipay\Fund())->accountQuery('ACCTRANS_ACCOUNT', 'alipay-open-id');
~~~

## quotaQuery
- 说明: 转账额度查询
- 官方文档: [转账额度查询接口](https://opendocs.alipay.com/open-v3/05708ce0_alipay.fund.quota.query)
- 参数说明
  + `productCode`: (string) 产品编码(TRANS_ACCOUNT_NO_PWD-单笔转账到支付宝账户,STD_RED_PACKET-收发现金红包,TRANS_BANKCARD_NO_PWD-单笔付款到卡,DEFAULT-接口转账)
  + `bizScene`: (string) 业务场景(DIRECT_TRANSFER-单笔无密转账到支付宝，单笔无密转账到银行卡，现金红包,DEFAULT-转账到户)

示例
~~~php
$result = (new \lifetime\bridge\Ali\Alipay\Fund())->quotaQuery('DEFAULT', 'DEFAULT');
~~~

## transfer
- 说明: 单笔转账
- 官方文档: [单笔转账接口](https://opendocs.alipay.com/open-v3/08e7ef12_alipay.fund.trans.uni.transfer)
- 参数说明
  + `outBizNo`: (string) 商家侧唯一订单号
  + `transAmount`: (float) 订单总金额，单位为元
  + `bizScene`: (string) 业务场景。单笔无密转账固定为 DIRECT_TRANSFER
  + `productCode`: (string) 销售产品码。单笔无密转账固定为 TRANS_ACCOUNT_NO_PWD
  + `orderTitle`: (string) 转账业务的标题
  + `payeeInfo`: (array) 收款方信息
      - `identity`: (string) 标识
      - `identity_type`: (string) 标识类型

示例
~~~php
$result = (new \lifetime\bridge\Ali\Alipay\Fund())->transfer('transfer_1', 1, 'DIRECT_TRANSFER', 'TRANS_ACCOUNT_NO_PWD', '测试转账', ['identity' => 'alipay-open-id', 'identity_type' => 'ALIPAY_OPEN_ID']);
~~~
