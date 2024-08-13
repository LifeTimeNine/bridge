<?php

declare(strict_types = 1);

namespace lifetime\bridge\Ali\Alipay;

use lifetime\bridge\Config\AliPayment;
use lifetime\bridge\Exception\AliPaymentResponseException;
use lifetime\bridge\Exception\InvalidArgumentException;
use lifetime\bridge\Exception\InvalidConfigException;
use lifetime\bridge\Exception\InvalidDecodeException;
use lifetime\bridge\Exception\InvalidResponseException;
use lifetime\bridge\exception\InvalidSignException;
use lifetime\bridge\Request;
use lifetime\bridge\Tools;

/**
 * 支付宝基类
 */
abstract class Basic
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
     * 应用私钥Key
     * @var string
     */
    protected $appPrivateKey;

    /**
     * 支付宝公钥Key
     * @var string
     */
    protected $alipayPublicKey;

    /**
     * 应用公钥证书
     * @var string
     */
    protected $appPublicCert;

    /**
     * 支付宝公钥证书
     * @var string
     */
    protected $alipayPublicCert;

    /**
     * 支付宝根证书
     * @var string
     */
    protected $alipayRootCert;

    /**
     * 支付宝根证书SN
     * @var string
     */
    protected $alipayRootCertSn = null;

    /**
     * 应用公钥证书SN
     * @var string
     */
    protected $appPublicCertSn = null;

    /**
     * 支付宝公钥SN
     * @var string
     */
    protected $alipayPublicCertSn = null;

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
            $this->gateway = 'https://openapi-sandbox.dl.alipaydev.com';
        } else {
            $this->gateway = 'https://openapi.alipay.com';
        }
        $this->appPrivateKey = "-----BEGIN RSA PRIVATE KEY-----\n" .
                wordwrap($this->config->appPrivateKey(), 64,"\n", true) .
                "\n-----END RSA PRIVATE KEY-----";
        if (empty($this->config->appPublicCertPath())) {
            $this->alipayPublicKey = "-----BEGIN PUBLIC KEY-----\n" .
                wordwrap($this->config->alipayPublicKey(), 64, "\n", true) .
                "\n-----END PUBLIC KEY-----";
        } else {
            $this->appPublicCert = file_get_contents($this->config->appPublicCertPath());
            $this->alipayPublicCert = file_get_contents($this->config->alipayPublicCertPath());
            $this->alipayRootCert = file_get_contents($this->config->alipayRootCertPath());
            // 提取支付宝根证书SN
            $array = explode('-----END CERTIFICATE-----', $this->alipayRootCert);
            for ($i = 0; $i < count($array) - 1; $i++) {
                $ssl = openssl_x509_parse($array[$i] . "-----END CERTIFICATE-----");
                if (strpos($ssl['serialNumber'], '0x') === 0) {
                    $ssl['serialNumber'] = self::hex2dec($ssl['serialNumberHex']);
                }
                if ($ssl['signatureTypeLN'] == "sha1WithRSAEncryption" || $ssl['signatureTypeLN'] == "sha256WithRSAEncryption") {
                    /** @var array */
                    $sslIssuer = $ssl['issuer'];
                    if ($this->alipayRootCertSn == null) {
                        $this->alipayRootCertSn = md5(self::array2string(array_reverse($sslIssuer)) . $ssl['serialNumber']);
                    } else {
                        $this->alipayRootCertSn .= "_" . md5(self::array2string(array_reverse($sslIssuer)) . $ssl['serialNumber']);
                    }
                }
            }
            // 提取应用公钥SN
            $ssl = openssl_x509_parse($this->appPublicCert);
            /** @var array */
            $sslIssuer = $ssl['issuer'];
            $this->appPublicCertSn = md5(self::array2string(array_reverse($sslIssuer)) . $ssl['serialNumber']);
            // 提取支付宝公钥SN
            $ssl = openssl_x509_parse($this->alipayPublicCert);
            /** @var array */
            $sslIssuer = $ssl['issuer'];
            $this->alipayPublicCertSn = md5(self::array2string(array_reverse($sslIssuer)) . $ssl['serialNumber']);
            // 提取支付宝公钥
            $keyData = openssl_pkey_get_details(openssl_pkey_get_public($this->alipayPublicCert));
            $this->alipayPublicKey = $keyData['key'];
        }
    }

    /**
     * 数组转字符串
     * @access  private
     * @param   array   $array  数组
     * @return  string
     */
    private function array2string(array $array): string
    {
        $string = [];
        foreach ($array as $key => $value) {
            $string[] = $key . '=' . $value;
        }
        return implode(',', $string);
    }

    /**
     * 十六进制转高精度数字
     * @access  
     * @param string    $hex    十六进制字符串
     * @return string
     */
    private function hex2dec(string $hex): string
    {
        $dec = '0';
        $len = strlen($hex);
        for ($i = 1; $i <= $len; $i++) {
            $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
        }
        return $dec;
    }

    /**
     * 填充算法
     * @access  private
     * @param   string  $source 源内容
     * @return  string
     */
    private function addPKCS7Padding($source): string
    {
        $source = trim($source);
        $block = 16;

        $pad = $block - (strlen($source) % $block);
        if ($pad <= $block) {
            $char = chr($pad);
            $source .= str_repeat($char, $pad);
        }
        return $source;
    }

    /**
     * 移去填充算法
     * @access  private
     * @param   string  $source 源内容
     * @return  string
     */
    private function stripPKCS7Padding($source): string
    {
        $char = substr($source, -1);
        $num = ord($char);
        if ($num == 62) return $source;
        $source = substr($source, 0, -$num);
        return $source;
    }

    /**
     * 生成签名
     * @access  protected
     * @param   array   $header     请求头
     * @param   string  $method     请求方法
     * @param   string  $uri        请求地址
     * @param   string  $body       请求内容
     * @return  void
     */
    protected function buildSign(array &$header, string $method, string $uri, string $body = null)
    {
        $appAuthToken = $header['alipay-app-auth-token'] ?? null;
        $nonce = uniqid((string)mt_rand());
        $timestamp = (int)(microtime(true) * 1000);
        $authStringArr = [
            "app_id={$this->config->appId()}",
            "nonce={$nonce}",
            "timestamp={$timestamp}"
        ];
        if (!empty($this->config->appPublicCertPath())) {
            $authStringArr[] = "app_cert_sn={$this->appPublicCertSn}";
        }
        $authString = implode(',', $authStringArr);
        $contentArr = [
            $authString,
            $method,
            $uri
        ];
        if (!empty($body)) $contentArr[] = $body;
        if (!empty($appAuthToken)) $contentArr[] = $appAuthToken;

        if (empty($this->config->appPublicCertPath())) {
            $res = $this->appPrivateKey;
        } else {
            $res = openssl_get_privatekey($this->appPrivateKey);
        }
        if (empty($res)) {
            throw new InvalidConfigException('Private key format is incorrect', 1);
        }
        openssl_sign(implode("\n", $contentArr) . "\n", $sign, $res, OPENSSL_ALGO_SHA256);
        $sign = base64_encode($sign);
        $header['Authorization'] = "ALIPAY-SHA256withRSA {$authString},sign={$sign}";
    }

    /**
     * 验证签名
     * @access  protected
     * @param   string  $content    响应内容
     * @param   string  $timestamp  时间戳
     * @param   string  $nonce      随机字符串
     * @param   string  $sign       响应的签名
     * @return  bool
     */
    protected function verifySign(string $content, string $timestamp, string $nonce, string $sign): bool
    {
        $signStr = "{$timestamp}\n{$nonce}\n{$content}\n";
        return openssl_verify($signStr, base64_decode($sign), $this->alipayPublicKey, OPENSSL_ALGO_SHA256) === 1;
    }

    /**
     * 发起请求
     * @access  protected
     * @param   string  $method     请求方法
     * @param   string  $uri        请求地址
     * @param   array   $query      Query参数
     * @param   array   $body       请求内容
     * @return  array
     * @throws  InvalidResponseException
     * @throws  AliPaymentResponseException
     * @throws  InvalidDecodeException
     * @throws  InvalidSignException
     */
    protected function request(string $method, string $uri, array $query = [], array $body = []): array
    {
        $header = [
            Request::HEADER_CONTENT_TYPE => Request::CONTENT_TYPE_PLAIN,
            'alipay-encrypt-type' => 'AES'
        ];
        $body = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
        if (!empty($this->config->encryptKey())) {
            $body = base64_encode(openssl_encrypt($this->addPKCS7Padding($body), 'AES-128-CBC', base64_decode($this->config->encryptKey()), OPENSSL_NO_PADDING, str_repeat("\0", 16)));
        }
        if (!empty($query)) {
            $uri .= '?' . Tools::arrToUrl($query);
        }
        $this->buildSign($header, $method, $uri, $body);
        $request = new Request("{$this->gateway}{$uri}", $method);
        $request->setHeaders($header)
            ->setBody($body);
        
        $response = $request->send();
        if ($request->getCode() <> 200) {
            if (empty($response)) {
                throw new InvalidResponseException('Request exception', $request->getCode());
            } else {
                $response = json_decode($response, true);
                throw new AliPaymentResponseException($response['message'], $request->getCode(), $response['code']);
            }
        }
        // 验证支付宝公钥证书SN
        if ($this->alipayPublicCertSn <> $request->getHeader('alipay-sn')) {
            throw new InvalidResponseException('Alipay certificate has expired');
        }
        // 验证签名
        if (!$this->verifySign($response, $request->getHeader('alipay-timestamp'), $request->getHeader('alipay-nonce'), $request->getHeader('alipay-signature'))) {
            throw new InvalidSignException('Signature verification failed');
        }
        if (!empty($this->config->encryptKey())) {
            $response = $this->stripPKCS7Padding(openssl_decrypt(base64_decode($response), 'AES-128-CBC', base64_decode($this->config->encryptKey()), OPENSSL_NO_PADDING, str_repeat("\0", 16)));
            if (empty($response)) {
                throw new InvalidDecodeException('Encrypt fail', 1);
            }
        }
        $response = json_decode($response, true);
        if (json_last_error() > 0) {
            throw new InvalidDecodeException(json_last_error_msg(), json_last_error());
        }
        return $response;
    }
    
    /**
     * 验证必须参数
     * @access  protected
     * @param   array   $data   要验证的数据
     * @param   array   $field  必须的字段
     * @param   array   $msg    消息
     * @return void
     * @throws InvalidArgumentException
     */
    protected function checkMustOptions($data = [], $field = [], $msg = [])
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
                    $this->checkMustOptions($data[$k], $field[$k], array_merge($msg, [$k]));
                }
            }
        }
    }
}