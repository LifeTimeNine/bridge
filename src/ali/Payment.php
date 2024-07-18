<?php

declare(strict_types = 1);

namespace lifetime\bridge\ali;

use lifetime\bridge\config\AliPayment;
use lifetime\bridge\exception\InvalidArgumentException;
use lifetime\bridge\exception\InvalidResponseException;
use lifetime\bridge\exception\InvalidSignException;
use lifetime\bridge\Tools;

/**
 * 支付宝支付相关业务
 */
class Payment
{
    /**
     * 配置
     * @var AliPayment
     */
    protected $config;

    /**
     * 当前请求数据
     * @var array
     */
    protected $options;

    /**
     * bizContent数据
     * @var array
     */
    protected $bizContent;

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
     */
    public function __construct($config = [])
    {
        $this->config = new AliPayment($config);

        if ($this->config['sandbox']) {
            $this->gateway = 'https://openapi-sandbox.dl.alipaydev.com/gateway.do';
        } else {
            $this->gateway = 'https://openapi.alipay.com/gateway.do';
        }
    }

    /**
     * 初始化请求参数
     * @access  protected
     * @return void
     */
    protected function initOptions()
    {
        $this->options = [
            'app_id' => $this->config['appid'],
            'version' => '1.0',
            'format' => 'JSON',
            'sign_type' => $this->config['sign_type'],
            'charset' => 'UTF-8',
            'timestamp' => date('Y-m-d H:i:s'),
        ];
        $this->bizContent = [];
    }

    /**
     * 获取数据签名
     * @access  public
     * @return string
     */
    protected function getSign(): string
    {
        $content = wordwrap(preg_replace(['/\s+/', '/\-{5}.*?\-{5}/'], '', $this->config['private_key']), 64, "\n", true);
        $string = "-----BEGIN RSA PRIVATE KEY-----\n{$content}\n-----END RSA PRIVATE KEY-----";
        if ($this->options['sign_type'] === 'RSA2') {
            openssl_sign($this->getSignContent($this->options, true), $sign, $string, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($this->getSignContent($this->options, true), $sign, $string, OPENSSL_ALGO_SHA1);
        }
        return base64_encode($sign);
    }

    /**
     * 数据签名处理
     * @access  public
     * @param array $data 需要进行签名数据
     * @param boolean $needSignType 是否需要sign_type字段
     * @return string
     */
    protected function getSignContent(array $data, $needSignType = false): string
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
        $signData = $this->getSignContent($data);

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
            wordwrap($this->config['alipay_public_key'], 64, "\n", true) .
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
     * @param   array   $order  订单参数
     * @return string
     */
    protected function buildPayHtml($order)
    {
        $this->options['biz_content'] = json_encode(array_merge($this->bizContent, $order), JSON_UNESCAPED_UNICODE);
        $this->options['sign'] = $this->getSign();

        $html = "<form id='alipaysubmit' name='alipaysubmit' action='{$this->gateway}' method='post'>";
        foreach ($this->options as $key => $value) {
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
     * 请求支付宝
     * @access  protected
     * @param  array    $options    请求参数
     * @return array    [相应数据， 验证签名结果]
     */
    protected function requestAli($options): array
    {
        $this->options['biz_content'] = json_encode(array_merge($this->bizContent, $options), JSON_UNESCAPED_UNICODE);
        $this->options['sign'] = $this->getSign();

        try {
            $res = Tools::request('get', $this->gateway, [
                'query' => $this->options
            ]);
        } catch (\Exception $e) {
            throw new InvalidResponseException($e->getMessage(), $e->getCode(), $this->options);
        }

        $resData = json_decode($res, true);
        if (json_last_error() != JSON_ERROR_NONE) throw new InvalidResponseException("The request result resolution failed", 200, $this->options);

        if (empty($resData['sign'])) throw new InvalidResponseException("Missing Response data");
        $sign = $resData['sign'];

        $methodName = str_replace('.', '_', $this->options['method']) . '_response';

        if (empty($resData[$methodName])) throw new InvalidResponseException("Missing Response data");
        $data = $resData[$methodName];

        if (!$this->checkSign(json_encode($data, JSON_UNESCAPED_UNICODE), $sign, $this->options['sign_type'])) throw new InvalidSignException('Signature verification failed', 1, $data);

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
        $this->initOptions();

        $this->bizContent['product_code'] = 'FAST_INSTANT_TRADE_PAY';

        $this->options['method'] = 'alipay.trade.page.pay';
        $this->options['notify_url'] = $notifyUrl;
        $this->options['return_url'] = $returnUrl;
        
        return $this->buildPayHtml($order);
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
        $this->initOptions();

        $this->bizContent['product_code'] = 'QUICK_WAP_WAY';

        $this->options['method'] = 'alipay.trade.wap.pay';
        $this->options['notify_url'] = $notifyUrl;
        $this->options['return_url'] = $returnUrl;

        return $this->buildPayHtml($order);
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
        $this->initOptions();

        $this->bizContent['product_code'] = 'QUICK_MSECURITY_PAY';

        $this->options['method'] = 'alipay.trade.app.pay';
        $this->options['notify_url'] = $notifyUrl;
        $this->options['biz_content'] = json_encode(array_merge($this->bizContent, $order), JSON_UNESCAPED_UNICODE);
        $this->options['sign'] = $this->getSign();
        
        return http_build_query($this->options);
    }

    /**
     * 异步通知处理
     * @access  public
     * @param callable  $callback   验证完成闭包函数(参数: $data-数据，$checkRes-验证结果) 如果返回false强制给阿里云返回失败的消息
     * @param string    给支付宝返回的消息
     * @throws InvalidSignException
     */
    public function notify(callable $callback= null): string
    {
        $data = $_POST;
        $checkRes = $this->verify($data);

        if (is_callable($callback)) {
            $res = call_user_func_array($callback, [$data, $checkRes]);
            return $res === false ? 'fail' : 'success';
        } else {
            return $checkRes ? 'success' : 'fail';
        }
    }

    /**
     * 订单查询
     * @access  public
     * @param   array     $options    请求参数 (out_trade_no-商户订单号 || trade_no-支付宝订单号)
     * @return  array
     * @throws InvalidArgumentException
     * @throws InvalidSignException
     * @throws InvalidResponseException
     */
    public function query(array $options): array
    {
        if (empty($options['out_trade_no']) && empty($options['trade_no'])) {
            throw new InvalidArgumentException("Missing Options [out_trade_no OR trade_no]");
        }
        $this->initOptions();
        $this->options['method'] = 'alipay.trade.query';

        return $this->requestAli($options);
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
        $this->initOptions();
        $this->options['method'] = 'alipay.trade.refund';

        return $this->requestAli($options);
    }

    /**
     * 退款查询
     * @access  public
     * @param   array     $options    请求参数 (out_trade_no-商户订单号 || trade_no-支付宝订单号, out_request_no-退款请求号)
     * @return  array
     * @throws InvalidArgumentException
     * @throws InvalidSignException
     * @throws InvalidResponseException
     */
    public function refundQuery(array $options): array
    {
        if (empty($options['out_trade_no']) && empty($options['trade_no'])) {
            throw new InvalidArgumentException("Missing Options [out_trade_no OR trade_no]");
        }

        if (empty($options['out_request_no'])) {
            throw new InvalidArgumentException("Missing options [out_request_no]");
        }
        $this->initOptions();
        $this->options['method'] = 'alipay.trade.fastpay.refund.query';

        return $this->requestAli($options);
    }

    /**
     * 交易关闭
     * @access  public
     * @param   array   $options    请求参数 (out_trade_no-商户订单号 || trade_no-支付宝订单号)
     * @return  array
     * @throws InvalidArgumentException
     * @throws InvalidSignException
     * @throws InvalidResponseException
     */
    public function tradeClose(array $options)
    {
        if (empty($options['out_trade_no']) && empty($options['trade_no'])) {
            throw new InvalidArgumentException("Missing Options [out_trade_no OR trade_no]");
        }
        $this->initOptions();
        $this->options['method'] = 'alipay.trade.close';

        return $this->requestAli($options);
    }
}