<?php

declare(strict_types = 1);

namespace lifetime\bridge\ali;

use lifetime\bridge\config\AliPayment;
use lifetime\bridge\exception\AliPaymentResponseException;
use lifetime\bridge\exception\InvalidArgumentException;
use lifetime\bridge\exception\InvalidConfigException;
use lifetime\bridge\exception\InvalidDecodeException;
use lifetime\bridge\exception\InvalidResponseException;
use lifetime\bridge\exception\InvalidSignException;
use lifetime\bridge\Request;
use lifetime\bridge\Tools;

/**
 * 支付宝支付相关业务
 * @throws InvalidConfigException
 */
class Payment
{
    /**
     * 配置
     * @var AliPayment
     */
    protected $config;

    /**
     * 请求网关
     * @var string
     */
    protected $gateway;

    /**
     * 订单必须参数
     * @var array
     */
    protected $orderMustOptions = ['out_trade_no', 'total_amount', 'subject'];

    /**
     * 构造函数
     * @access  public
     * @param   array   $config     配置信息
     * @throws InvalidConfigException
     */
    public function __construct($config = [])
    {
        $this->config = new AliPayment($config);

        if ($this->config->sandbox()) {
            $this->gateway = 'https://openapi-sandbox.dl.alipaydev.com/gateway.do';
        } else {
            $this->gateway = 'https://openapi.alipay.com/gateway.do';
        }
    }

    /**
     * 初始化请求参数
     * @access  protected
     * @return  array
     */
    protected function initOptions(): array
    {
        return [
            'app_id' => $this->config->appId(),
            'version' => '1.0',
            'format' => 'JSON',
            'sign_type' => $this->config->signType(),
            'charset' => 'UTF-8',
            'timestamp' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * 获取数据签名
     * @access  public
     * @param   array   $data   签名的数据
     * @return string
     */
    protected function getSign(array $data): string
    {
        $content = wordwrap(preg_replace(['/\s+/', '/\-{5}.*?\-{5}/'], '', $this->config->privateKey()), 64, "\n", true);
        $string = "-----BEGIN RSA PRIVATE KEY-----\n{$content}\n-----END RSA PRIVATE KEY-----";
        if ($data['sign_type'] === 'RSA2') {
            openssl_sign($this->handlerSignData($data, true), $sign, $string, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($this->handlerSignData($data, true), $sign, $string, OPENSSL_ALGO_SHA1);
        }
        return base64_encode($sign);
    }

    /**
     * 处理签名数据
     * @access  public
     * @param array     $data           需要进行签名数据
     * @param bool      $needSignType   是否需要sign_type字段
     * @return string
     */
    protected function handlerSignData(array $data, bool $needSignType = false): string
    {
        list($attrs,) = [[], ksort($data)];
        if (isset($data['sign'])) unset($data['sign']);
        if (empty($needSignType)) unset($data['sign_type']);
        foreach ($data as $key => $value) {
            if ($value === '' || is_null($value)) continue;
            array_push($attrs, "{$key}={$value}");
        }
        return implode('&', $attrs);
    }

    /**
     * 验证签名
     * @access  public
     * @param   array   $data   阿里返回数据
     * @return  void
     */
    protected function verify($data)
    {
        if (empty($data)) return false;

        if (empty($data['sign']) || empty('sing_type')) return false;
        $sign = $data['sign'];
        $signType = $data['sign_type'];
        $signData = $this->handlerSignData($data);

        if (!$this->checkSign($signData, $sign, $signType)) {
            throw new InvalidSignException('signature verification failed', 1, $data);
        }
    }

    /**
     * 验证sign
     * @access  public
     * @param   string  $data               sign源数据
     * @param   string  $sign               sign
     * @param   string  $signType           sign类型
     * @return  bool
     */
    protected function checkSign($data, $sign, $signType = 'RSA'): bool
    {
        $alipayPublicKey = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($this->config->alipayPublicKey(), 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";

        if ($signType == "RSA2") {
            return openssl_verify($data, base64_decode($sign), $alipayPublicKey, OPENSSL_ALGO_SHA256) === 1;
        } else {
            return openssl_verify($data, base64_decode($sign), $alipayPublicKey) === 1;
        }
    }

    /**
     * 生成支付HTML代码
     * @access  public
     * @param   array   $options        请求参数
     * @param   array   $bizContent     订单参数
     * @return string
     */
    protected function buildPayHtml(array $options, array $bizContent): string
    {
        $options['biz_content'] = json_encode($bizContent, JSON_UNESCAPED_UNICODE);
        $options['sign'] = $this->getSign($options);

        $html = "<form id='alipaysubmit' name='alipaysubmit' action='{$this->gateway}' method='post'>";
        foreach ($options as $key => $value) {
            $value = str_replace("'", '&apos;', $value);
            $html .= "<input type='hidden' name='{$key}' value='{$value}'/>";
        }
        $html .= "<input type='submit' value='ok' style='display:none;'></form>";
        return "{$html}<script>document.forms['alipaysubmit'].submit();</script>";
    }

    /**
     * 验证订单必须参数
     * @param   array   $data   订单数据
     * @return  void
     */
    protected function checkOrder($data)
    {
        if (!is_array($data)) throw new InvalidArgumentException("Missing Options type");

        foreach ($this->orderMustOptions as $v) {
            if (empty($data[$v])) throw new InvalidArgumentException("Miss Options [{$v}]");
        }
    }

    /**
     * 发起请求
     * @access  protected
     * @param   array       $options    请求参数
     * @param   array       $bizContent 接口参数
     * @return  array
     */
    protected function request(array $options, array $bizContent): array
    {
        $options['biz_content'] = json_encode($bizContent, JSON_UNESCAPED_UNICODE);
        $options['sign'] = $this->getSign($options);

        $request = Request::get("{$this->gateway}?" . http_build_query($options));
        $resData = json_decode($request->send(), true);

        if ($request->getCode() <> 200 && $request->getCode() <> 204) {
            throw new InvalidResponseException('Request exception', 1);
        }

        if (json_last_error() != JSON_ERROR_NONE) throw new InvalidDecodeException(json_last_error_msg(), json_last_error());

        if (empty($resData['sign'])) throw new InvalidResponseException("Missing Response data");
        $sign = $resData['sign'];

        $methodName = str_replace('.', '_', $options['method']) . '_response';

        if (empty($resData[$methodName])) throw new InvalidResponseException("Missing Response data");
        $data = $resData[$methodName];

        if (!$this->checkSign(json_encode($data, JSON_UNESCAPED_UNICODE), $sign, $options['sign_type'])) throw new InvalidSignException('Signature verification failed', 1, $data);

        if ($data['code'] <> 10000 || $data['sub_code'] <> 'ACQ.TRADE_HAS_SUCCESS') {
            throw new AliPaymentResponseException($data['msg'], (int)$data['code'], $data['sub_code'], $data['sub_msg']);
        }

        return $data;
    }

    /**
     * Web 页面支付
     * @access  public
     * @param   array   $order      订单信息(out_trade_no-订单编号,subject-订单名称,total_amount-订单金额)
     * @param   string  $notifyUrl  异步回调地址
     * @param   string  $returnUrl  同步跳转地址
     * @return   string
     * @throws InvalidArgumentException
     */
    public function page(array $order, string $notifyUrl, string $returnUrl = null): string
    {
        $this->checkOrder($order);
        $options = $this->initOptions();

        $options['method'] = 'alipay.trade.page.pay';
        $options['notify_url'] = $notifyUrl;
        $options['return_url'] = $returnUrl;

        $order['product_code'] = 'FAST_INSTANT_TRADE_PAY';
        
        return $this->buildPayHtml($options, $order);
    }

    /**
     * 手机网站支付
     * @access  public
     * @param   array   $order      订单信息(out_trade_no-订单编号,subject-订单名称,total_amount-订单金额)
     * @param   string  $notifyUrl  异步回调地址
     * @param   string  $returnUrl  同步跳转地址
     * @return  string
     * @throws InvalidArgumentException
     */
    public function wap(array $order, string $notifyUrl, string $returnUrl = null): string
    {
        $this->checkOrder($order);
        $options = $this->initOptions();

        $options['method'] = 'alipay.trade.wap.pay';
        $options['notify_url'] = $notifyUrl;
        $options['return_url'] = $returnUrl;

        $order['product_code'] = 'QUICK_WAP_WAY';

        return $this->buildPayHtml($options, $order);
    }

    /**
     * APP 支付
     * @access  public
     * @param   array   $order      订单信息(out_trade_no-订单编号,subject-订单名称,total_amount-订单金额)
     * @param   string  $notifyUrl 异步回调地址(为空可能会支付失败)
     * @return   string
     * @throws InvalidArgumentException
     */
    public function app(array $order, string $notifyUrl): string
    {
        $this->checkOrder($order);
        $options = $this->initOptions();

        $order['product_code'] = 'QUICK_MSECURITY_PAY';

        $options['method'] = 'alipay.trade.app.pay';
        $options['notify_url'] = $notifyUrl;
        $options['biz_content'] = json_encode($order, JSON_UNESCAPED_UNICODE);
        $options['sign'] = $this->getSign($options);
        
        return http_build_query($options);
    }

    /**
     * 异步通知处理
     * @access  public
     * @param callable  $callback   验证完成闭包函数(参数: $data-数据) 如果返回false将会给支付宝返回失败的消息
     * @param string    给支付宝返回的消息
     * @throws InvalidSignException
     */
    public function notify(callable $callback= null): string
    {
        $data = $_POST;
        if (!$this->verify($data)) {
            return 'fail';
        }

        $res = call_user_func_array($callback, [$data]);
        return $res === false ? 'fail' : 'success';
    }

    /**
     * 订单查询
     * @access  public
     * @param   string  $outTradeNo     商户订单号
     * @param   string  $tradeNo        支付宝订单号
     * @param   array   $queryOptions   查询选项
     * @param   string  $orgPid         双联通过该参数指定需要查询的交易所属收单机构的pid
     * @return  array
     * @throws InvalidArgumentException
     * @throws InvalidSignException
     * @throws InvalidResponseException
     */
    public function query(string $outTradeNo = null, string $tradeNo = null, array $queryOptions = [], string $orgPid = null): array
    {
        if (empty($outTradeNo) && empty($tradeNo)) {
            throw new InvalidArgumentException("Missing Options [out_trade_no OR trade_no]");
        }
        $options = [];
        if (!empty($outTradeNo)) $options['out_trade_no'] = $outTradeNo;
        if (!empty($tradeNo)) $options['trade_no'] = $tradeNo;
        if (!empty($queryOptions)) $options['query_options'] = $queryOptions;
        if (!empty($orgPid)) $options['org_pid'] = $orgPid;
        $requestOptions = $this->initOptions();
        $requestOptions['method'] = 'alipay.trade.query';

        return $this->request($requestOptions, $options);
    }

    /**
     * 退款
     * @access  public
     * @param   array       $options    请求参数 (out_trade_no-商户订单号 || trade_no-支付宝订单号, refund_amount-退款金额, out_request_no-退款请求号)
     * @return  array
     * @throws InvalidArgumentException
     * @throws InvalidSignException
     * @throws InvalidResponseException
     */
    public function refund(array $options): array
    {
        if (empty($options['out_trade_no']) && empty($options['trade_no'])) {
            throw new InvalidArgumentException("Missing Options [out_trade_no OR trade_no]");
        }

        if (empty($options['refund_amount']) || $options['refund_amount'] <= 0) {
            throw new InvalidArgumentException("Missing Options [refund_amount]");
        }

        if (empty($options['out_request_no'])) {
            throw new InvalidArgumentException("Missing options [out_request_no]");
        }
        $requestOptions = $this->initOptions();
        $requestOptions['method'] = 'alipay.trade.refund';

        return $this->request($requestOptions, $options);
    }

    /**
     * 退款查询
     * @access  public
     * @param   string  $outRequestNo   退款请求号
     * @param   string  $outTradeNo     商户订单号
     * @param   string  $tradeNo        支付宝订单号
     * @param   array   $queryOptions   查询选项
     * @return  array
     * @throws InvalidArgumentException
     * @throws InvalidSignException
     * @throws InvalidResponseException
     */
    public function refundQuery(string $outRequestNo, string $outTradeNo = null, string $tradeNo = null, array $queryOptions = []): array
    {
        $options = ['out_request_no' => $outRequestNo];
        if (empty($outTradeNo) && empty($tradeNo)) {
            throw new InvalidArgumentException("Missing Options [out_trade_no OR trade_no]");
        }
        if (!empty($outTradeNo)) $options['out_trade_no'] = $outTradeNo;
        if (!empty($tradeNo)) $options['trade_no'] = $tradeNo;
        if (!empty($queryOptions)) $options['query_options'] = $queryOptions;

        $requestOptions = $this->initOptions();
        $requestOptions['method'] = 'alipay.trade.fastpay.refund.query';

        return $this->request($requestOptions, $options);
    }

    /**
     * 交易关闭
     * @access  public
     * @param   string  $outTradeNo     商户订单号
     * @param   string  $tradeNo        支付宝订单号
     * @param   string  $operatorId     商家操作员编号
     * @return  array
     * @throws InvalidArgumentException
     * @throws InvalidSignException
     * @throws InvalidResponseException
     */
    public function tradeClose(string $outTradeNo = null, string $tradeNo = null, string $operatorId = null)
    {
        if (empty($outTradeNo) && empty($tradeNo)) {
            throw new InvalidArgumentException("Missing Options [out_trade_no OR trade_no]");
        }
        $options = [];
        if (!empty($outTradeNo)) $options['out_trade_no'] = $outTradeNo;
        if (!empty($tradeNo)) $options['trade_no'] = $tradeNo;
        if (!empty($operatorId)) $options['operator_id'] = $operatorId;

        $requestOptions = $this->initOptions();
        $requestOptions['method'] = 'alipay.trade.close';

        return $this->request($requestOptions, $options);
    }
}