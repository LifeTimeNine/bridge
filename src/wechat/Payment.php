<?php

declare(strict_types = 1);

namespace lifetime\bridge\wechat;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use lifetime\bridge\Cache;
use lifetime\bridge\config\WechatPayment;
use lifetime\bridge\exception\InvalidArgumentException;
use lifetime\bridge\exception\InvalidConfigException;
use lifetime\bridge\exception\InvalidResponseException;
use lifetime\bridge\Tools;

/**
 * 微信支付
 * @throws InvalidConfigException
 */
class Payment
{
    /**
     * 配置类
     * @var WechatPayment
     */
    protected $config;

    /**
     * 请求数据
     * @var array
     */
    protected $options;

    /**
     * 请求接口地址
     * @var string
     */
    protected $apiDomain = 'https://api.mch.weixin.qq.com';

    /**
     * 订单必须参数
     * @var array
     */
    protected $mustOptions = [];

    /**
     * 构造函数
     * @access  public
     * @param   array   $config     配置
     * @throws InvalidConfigException
     */
    public function __construct(array $config = [])
    {
        $this->config = new WechatPayment($config);
        Cache::init();
    }

    /**
     * 初始化参数
     * @access  protected
     * @return self
     */
    protected function initOptions(): self
    {
        $this->options = [
            'appid' => $this->config['app_id'],
            'mchid' => $this->config['mch_id'],
        ];
        return $this;
    }

    /**
     * 设置场景必须参数
     * @access  protected
     * @param   array   $data   当前场景必须的参数
     * @return  self
     */
    protected function setMustOptions($data): self
    {
        $this->mustOptions = $data;
        return $this;
    }

    /**
     * 验证参数是否足够
     * @access  protected
     * @param   array   $data   要验证的数据
     * @param   array   $field  必须的字段
     * @param   array   $msg    消息
     * @return void
     * @throws InvalidArgumentException
     */
    protected function checkOptions($data = [], $field = [], $msg = [])
    {
        foreach ($field as $k => $v) {
            if (is_string($v) && !isset($data[$v])) {
                $msg[] = $v;
                throw new InvalidArgumentException("Missing Options [". implode('.', $msg) ."]");
            } elseif (is_array($v)) {
                if (!isset($data[$k])) {
                    $msg[] = $k;
                    throw new InvalidArgumentException("Missing Options [". implode('.', $msg) ."]");
                } else {
                    $this->checkOptions($data[$k], $field[$k], array_merge($msg, [$k]));
                }
            }
        }
    }

    /**
     * 获取API私钥
     * @access  protected
     * @return  string
     */
    protected function getApiPrivateKey(): string
    {
        return file_get_contents($this->config['ssl_key']);
    }

    /**
     * 获取证书序列号
     * @access  protected
     * @return  string
     */
    protected function getSerialNo(): string
    {
        return openssl_x509_parse(file_get_contents($this->config['ssl_cert']))['serialNumberHex'] ?? '';
    }

    /**
     * 获取sign
     * @access  protected
     * @param   array   $data   要签名的数据
     * @param   int     $type   签名类型
     * @return  string
     */
    protected function getSign(array $data, int $type = OPENSSL_ALGO_SHA256): string
    {
        $dataStr = '';
        foreach ($data as $v) $dataStr .= "{$v}\n";
        $sign = '';
        if ($type == OPENSSL_ALGO_SHA256) {
            openssl_sign($dataStr, $sign, $this->getApiPrivateKey(), OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($dataStr, $sign, $this->getApiPrivateKey(), 'sha256WithRSAEncryption');
        }
        return base64_encode($sign);
    }

    /**
     * 获取Authorization字符串
     * @access  protected
     * @param   string  $requestMethod     请求方法
     * @param   string  $requestUrl        请求地址
     * @param   string  $requestBody       请求报文主体
     * @return  string
     */
    protected function getAuthorization(string $requestMethod, string $requestUrl, string $requestBody = null)
    {
        $time = time();
        $nonce_str = Tools::createRandomStr();

        $signStr = $this->getSign([$requestMethod, $requestUrl, $time, $nonce_str, $requestBody]);

        $data = [
            'mchid' => $this->config['mch_id'],
            'serial_no' => $this->getSerialNo(),
            'nonce_str' => $nonce_str,
            'timestamp' => $time,
            'signature' => $signStr
        ];
        $data2 = [];
        foreach ($data as $k => $v) $data2[] = "{$k}=\"{$v}\"";

        return 'WECHATPAY2-SHA256-RSA2048 ' . implode(',', $data2);
    }

    /**
     * 发起请求
     * @access  protected
     * @param   string  $method 请求方法
     * @param   string  $url    请求地址
     * @param   array   $body   请求主体
     * @param   array   $query  请求query参数
     * @return  array
     * @throws InvalidResponseException
     */
    protected function request(string $method, string $url, array $body = null, array $query = []): array
    {
        if (empty($query)) {
            $signUrl = $url;
        } else {
            $signUrl = "{$url}?" . http_build_query($query);
        }
        if (empty($body)) {
            $body = '';
        } else {
            $body = json_encode($body, 256);
        }
        $result = json_decode(Tools::request($method, "{$this->apiDomain}{$url}", [
            'headers' => [
                "Authorization: {$this->getAuthorization($method,$signUrl,$body)}",
                'Content-Type: application/json',
                'Accept: application/json',
                "User-Agent: php-curl/" . PHP_VERSION
            ],
            'data' => $body,
            'query' => $query
        ]), true);
        if (!empty($result['code'])) {
            throw new InvalidResponseException($result['message'], $result['code']);
        }
        return $result ?: null;
    }

    /**
     * 解密AEAD_AES_256_GCM
     * @access  protected
     * @param   string  $cipherText     密文
     * @param   string  $nonceStr       随机字符串
     * @param   string  $associatedData 附加数据包
     * @return  string
     * @throws InvalidArgumentException
     */
    protected function decodeAes256Gcm(string $cipherText, string $nonceStr, string $associatedData): string
    {
        if (empty($this->config['mch_key_v3'])) throw new InvalidArgumentException("Missing Config [mch_key_v3]");
        
        $cipherText = base64_decode($cipherText);
        $ctext = substr($cipherText, 0, -16);
        $authTag = substr($cipherText, -16);
        return openssl_decrypt($ctext, 'aes-256-gcm', $this->config['mch_key_v3'], OPENSSL_RAW_DATA, $nonceStr, $authTag, $associatedData);
    }

    /**
     * 获取微信平台证书
     * @access  protected
     * @return  string
     */
    protected function getCert(): string
    {
        $wechatPublicCert = Cache::get("wechat_public_cert_{$this->config['mch_id']}");
        if (!empty($wechatPublicCert)) return $wechatPublicCert;

        $url = "/v3/certificates";

        $result = $this->request('GET', $url);
        $cretData = $result['data'][count($result['data']) - 1];
        $wechatPublicCert = $this->decodeAes256Gcm($cretData['encrypt_certificate']['ciphertext'],  $cretData['encrypt_certificate']['nonce'], $cretData['encrypt_certificate']['associated_data']);
        Cache::set("wechat_public_cert_{$this->config['mch_id']}", $wechatPublicCert, 3600 * 11);
        return $wechatPublicCert;
    }

    /**
     * 验证sign
     * @access  protected
     * @param   string  $data               sign源数据
     * @param   string  $sign               sign
     * @return  bool
     */
    protected function checkSign(string $data, string $sign, $signType = OPENSSL_ALGO_SHA256): bool
    {
        if ($signType == OPENSSL_ALGO_SHA256) {
            return openssl_verify($data, base64_decode($sign), $this->getCert(), OPENSSL_ALGO_SHA256) === 1;
        } else {
            return openssl_verify($data, base64_decode($sign), $this->getCert()) === 1;
        }
    }

    /**
     * 统一下单
     * @access  protected
     * @param   string  $url        请求url
     * @param   array   $options    订单参数
     * @return  array
     */
    protected function createOrder(string $url, array $options): array
    {
        $this->options = array_merge($this->options, $options);
        $this->checkOptions($this->options, $this->mustOptions);
        return $this->request('POST', $url, $this->options);
    }

    /**
     * JSAPI下单
     * @access  public
     * @param   array   $options    订单参数[out_trade_no-商户订单号, amount.total-订单金额, description-订单描述, payer.openid-用户openid]
     * @param   string  $notify_url 异步通知地址
     * @return  array
     * @throws InvalidArgumentException
     * @throws InvalidResponseException
     */
    public function jsapi(array $options, string $notifyUrl): array
    {
        $this->setMustOptions(['out_trade_no', 'amount' => ['total'], 'description', 'payer' => ['openid']]);
        $this->initOptions();

        $this->options['notify_url'] = $notifyUrl;

        $order = $this->createOrder('/v3/pay/transactions/jsapi', $options);
        $time = time();
        $nonceStr = Tools::createRandomStr();

        return [
            'appId' => $this->config['app_id'],
            'timeStamp' => $time,
            'onoceStr' => $nonceStr,
            'package' => "prepay_id={$order['prepay_id']}",
            'signType' => 'RSA',
            'paySign' => $this->getSign([$this->config['app_id'], $time, $nonceStr, "prepay_id={$order['prepay_id']}"], 1)
        ];
    }

    /**
     * APP下单
     * @access  public
     * @param   array   $options    订单参数[out_trade_no-商户订单号, amount.total-订单金额, description-订单描述]
     * @param   string  $notify_url 异步通知地址
     * @return  array
     * @throws InvalidArgumentException
     * @throws InvalidResponseException
     */
    public function app(array $options, string $notifyUrl): array
    {
        $this->setMustOptions(['out_trade_no', 'amount' => ['total'], 'description']);
        $this->initOptions();

        $this->options['notify_url'] = $notifyUrl;

        $order = $this->createOrder('/v3/pay/transactions/app', $options);
        $time = time();
        $nonceStr = Tools::createRandomStr();

        return [
            'appid' => $this->config['app_id'],
            'partnerid' => $this->config['mch_id'],
            'prepayid' => $order['prepay_id'],
            'package' => "Sign=WXPay",
            'onoceStr' => $nonceStr,
            'timestamp' => $time,
            'paySign' => $this->getSign([$this->config['app_id'], $time, $nonceStr, "prepay_id={$order['prepay_id']}"], 1)
        ];
    }

     /**
     * H5下单
     * @access  public
     * @param   array   $options    订单参数[out_trade_no-商户订单号, amount.total-订单金额, description-订单描述, scene_info.payer_client_ip-用户终端IP, scene_info.h5_info.type-场景类型]
     * @param   string  $notify_url 异步通知地址
     * @return  string
     * @throws InvalidArgumentException
     * @throws InvalidResponseException
     */
    public function h5(array $options, string $notifyUrl): string
    {
        $this->setMustOptions([
            'out_trade_no',
            'amount' => ['total'],
            'description',
            'scene_info' => [
                'payer_client_ip',
                'h5_info' => [
                    'type'
                ]
            ]
        ]);
        $this->initOptions();

        $this->options['notify_url'] = $notifyUrl;

        $order = $this->createOrder('/v3/pay/transactions/h5', $options);

        return $order['h5_url'];
    }

    /**
     * Native下单
     * @access  public
     * @param   array   $options    订单参数[out_trade_no-商户订单号, amount.total-订单金额, description-订单描述]
     * @param   string  $notify_url 异步通知地址
     * @param   int     $qrcodeSize 二维码大小
     * @return  string
     * @throws InvalidArgumentException
     * @throws InvalidResponseException
     */
    public function native(array $options, string $notifyUrl, int $qrcodeSize = 200): string
    {
        $this->setMustOptions(['out_trade_no', 'amount' => ['total'], 'description']);
        $this->initOptions();

        $this->options['notify_url'] = $notifyUrl;

        $order = $this->createOrder('/v3/pay/transactions/native', $options);

        $qrcode = QrCode::create($order['code_url'])
            ->setSize($qrcodeSize);
        $writer = (new PngWriter)->write($qrcode);
        
        return $writer->getDataUri();
    }

    /**
     * 小程序下单
     * @access  public
     * @param   array   $options    订单参数[out_trade_no-商户订单号, amount.total-订单金额, description-订单描述, payer.openid-用户openid]
     * @param   string  $notify_url 异步通知地址
     * @return  array
     * @throws InvalidArgumentException
     * @throws InvalidResponseException
     */
    public function miniApp(array $options, string $notifyUrl): array
    {
        $this->setMustOptions(['out_trade_no', 'amount' => ['total'], 'description', 'payer' => ['openid']]);
        $this->initOptions();

        $this->options['notify_url'] = $notifyUrl;

        $order = $this->createOrder('/v3/pay/transactions/jsapi', $options);
        $time = time();
        $nonceStr = Tools::createRandomStr();

        return [
            'appId' => $this->config['app_id'],
            'timeStamp' => $time,
            'onoceStr' => $nonceStr,
            'package' => "prepay_id={$order['prepay_id']}",
            'signType' => 'RSA',
            'paySign' => $this->getSign([$this->config['app_id'], $time, $nonceStr, "prepay_id={$order['prepay_id']}"], 1)
        ];
    }

    /**
     * 订单查询
     * @access  public
     * @param   array   $options    查询参数[二选其一: transaction_id-微信支付订单号, out_trade_no-商户订单号]
     * @return  array
     * @throws InvalidArgumentException
     * @throws InvalidResponseException
     */
    public function query(array $options): array
    {
        $url = '';
        if (!empty($options['transaction_id'])) {
            $url = "/v3/pay/transactions/id/{$options['transaction_id']}";
        } elseif (!empty($options['out_trade_no'])) {
            $url = "/v3/pay/transactions/out-trade-no/{$options['out_trade_no']}";
        } else {
            throw new InvalidArgumentException("Missing Options [transaction_id  OR out_trade_no]");
        }

        return $this->request('GET', $url, null, ['mchid' => $this->config['mch_id']]);
    }

    /**
     * 关闭订单
     * @access  public
     * @param   string  $outTradeNo     商户订单号
     * @return  array
     * @throws InvalidArgumentException
     * @throws InvalidResponseException
     */
    public function close(string $outTradeNo): array
    {
        $this->initOptions();
        if (empty($outTradeNo)) throw new InvalidArgumentException('Missing Options [out_trade_no]', 1, $this->options);

        $url = "/v3/pay/transactions/out-trade-no/{$outTradeNo}/close";

        return $this->request('POST', $url, ['mchid' => $this->config['mch_id']]);
    }

    /**
     * 退款
     * @access  public
     * @param   array   $options    请求参数(transaction_id-微信支付订单号|out_trade_no-商户订单号,out_refund_no-商户退款单号,amount.refund-退款金额,amount.total-原订单金额,amount.currency-退款币种只支持人民币：CNY)
     * @return  array
     */
    public function refund(array $options): array
    {
        $this->setMustOptions([
            'out_refund_no',
            'amount' => ['refund','total','currency']
        ]);
        $this->options = [];

        $this->options = array_merge($this->options, $options);

        if (empty($this->options['transaction_id']) && empty($this->options['out_trade_no'])) {
            throw new InvalidArgumentException('Missing Options [transaction_id | out_trade_no]', 1, $this->options);
        }

        $this->checkOptions($this->options, $this->mustOptions);

        return $this->request('POST', '/v3/refund/domestic/refunds', $this->options);
    }

    /**
     * 退款查询
     * @access  public
     * @param   string  $outRefundNo    商户退款单号
     * @return  array
     */
    public function refundQuery(string $outRefundNo): array
    {
        if (empty($outRefundNo)) {
            throw new InvalidArgumentException('Missing Options [out_refund_no]');
        }

        return $this->request('GET', "/v3/refund/domestic/refunds/{$outRefundNo}");
    }

    /**
     * 异步回调通知
     * @access  public
     * @param   callable    $callable   回调方法（可以接收的参数：$data-解析到的数据）
     * @return  string  给微信返回的消息,如果回调方法返回false则直接给微信返回失败的消息
     */
    public function notify(callable $callable): string
    {
        $postData = !empty($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        if (empty($_SERVER['HTTP_WECHATPAY_TIMESTAMP']) || empty($_SERVER['HTTP_WECHATPAY_NONCE']) || empty($_SERVER['HTTP_WECHATPAY_SIGNATURE'])) {
            return json_encode(['code' => 'FAIL', 'message' => 'Signature verification failed']);
        }
        $checkSign = $this->checkSign("{$_SERVER['HTTP_WECHATPAY_TIMESTAMP']}\n{$_SERVER['HTTP_WECHATPAY_NONCE']}\n{$postData}\n", $_SERVER['HTTP_WECHATPAY_SIGNATURE']);
        if (!$checkSign) {
            return json_encode(['code' => 'FAIL', 'message' => 'Signature verification failed']);
        }
        $postData = json_decode($postData, true);
        $data = $this->decodeAes256Gcm($postData['resource']['cipherText'], $postData['resource']['nonce'], $postData['resource']['associated_data']);
        $result = call_user_func_array($callable, [json_decode($data, true)]);
        if ($result !== false) {
            return json_encode(['code' => 'SUCCESS', 'message' => 'success']);
        } else {
            return json_encode(['code' => 'FAIL', 'message' => 'Business failure']);
        }
    }

}