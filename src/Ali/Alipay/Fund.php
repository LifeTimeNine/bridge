<?php

declare(strict_types = 1);

namespace lifetime\bridge\Ali\Alipay;

use lifetime\bridge\Exception\InvalidArgumentException;
use lifetime\bridge\Request;

/**
 * 支付宝资金相关业务
 * @throws InvalidConfigException
 */
class Fund extends Basic
{
    /**
     * 资产查询
     * @access  public
     * @param   string  $accountType    账号类型(ACCTRANS_ACCOUNT-余额户,TRUSTEESHIP_ACCOUNT-托管账户)
     * @param   string  $alipayOpenId   支付宝openId
     * @param   string  $alipayUserId   支付宝会员 id
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  AliPaymentResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidSignException
     */
    public function accountQuery(string $accountType, string $alipayOpenId = null, string $alipayUserId = null): array
    {
        if (empty($alipayOpenId) && empty($alipayUserId)) {
            throw new InvalidArgumentException("Missing Options [alipay_open_id OR alipay_user_id]");
        }
        $query = ['account_type' => $accountType];
        if (!empty($alipayOpenId)) $query['alipay_open_id'] = $alipayOpenId;
        if (!empty($alipayUserId)) $query['alipay_user_id'] = $alipayUserId;
        return $this->request(Request::METHOD_GET, '/v3/alipay/fund/account/query', $query);
    }

    /**
     * 转账额度查询
     * @access  public
     * @param   string  $productCode    产品编码(TRANS_ACCOUNT_NO_PWD-单笔转账到支付宝账户,STD_RED_PACKET-收发现金红包,TRANS_BANKCARD_NO_PWD-单笔付款到卡,DEFAULT-接口转账)
     * @param   string  $bizScene       业务场景(DIRECT_TRANSFER-单笔无密转账到支付宝，单笔无密转账到银行卡，现金红包,DEFAULT-转账到户)
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  AliPaymentResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidSignException
     */
    public function quotaQuery(string $productCode, string $bizScene): array
    {
        $query = [
            'product_code' => $productCode,
            'biz_scene' => $bizScene
        ];
        return $this->request(Request::METHOD_GET, '/v3/alipay/fund/quota/query', $query);
    }

    /**
     * 单笔转账
     * @access  public
     * @param   string  $outBizNo       商家侧唯一订单号
     * @param   float   $transAmount    订单总金额，单位为元
     * @param   string  $bizScene       业务场景。单笔无密转账固定为 DIRECT_TRANSFER
     * @param   string  $productCode    销售产品码。单笔无密转账固定为 TRANS_ACCOUNT_NO_PWD
     * @param   string  $orderTitle     转账业务的标题
     * @param   array   $payeeInfo      收款方信息[identity-标识,identity_type-标识类型]
     * @return  array
     * @throws  InvalidArgumentException
     * @throws  InvalidResponseException
     * @throws  AliPaymentResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidSignException
     */
    public function transfer(string $outBizNo, float $transAmount, string $bizScene, string $productCode, string $orderTitle, array $payeeInfo): array
    {
        $this->checkMustOptions($payeeInfo, ['identity', 'identity_type'], ['payeeInfo']);
        return $this->request(Request::METHOD_POST, '/v3/alipay/fund/trans/uni/transfer', [], [
            'out_biz_no' => $outBizNo,
            'trans_amount' => $transAmount,
            'biz_scene' => $bizScene,
            'product_code' => $productCode,
            'order_title' => $orderTitle,
            'payee_info' => $payeeInfo
        ]);
    }
}