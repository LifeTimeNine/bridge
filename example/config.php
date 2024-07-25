<?php

return [
    // 阿里云和支付宝相关配置
    'ali' => [
        // 支付宝支付相关配置
        'payment' => [
            // 支付宝APPID
            'app_id' => '',
            // 应用公钥
            'public_key' => '',
            // 应用私钥
            'private_key' => '',
            // 支付宝公钥
            'alipay_public_key' => ''
        ],
    ],
    // 微信相关配置
    'wechat' => [
        // 支付相关配置
        'payment' => [
            // 应用ID
            'app_id'=> '',
            // 商户ID
            'mch_id' => '',
            // 商户支付密钥
            'mch_key' => '',
            // 证书cert.pem路径
            'ssl_cert' => '',
            // 证书key.pem路径
            'ssl_key' =>  ''
        ],
        // 公众号相关配置
        'official' => [
            // 公众号appid
            'app_id' => '',
            // 公众号secret
            'app_secret' => ''
        ],
        // 小程序配置
        'miniapp' => [
            // 小程序APPID
            'app_id' => '',
            // 小程序Secret
            'app_secret' => '',
        ],
    ],
    // 七牛云相关配置
    'qiniu' => [
        // 对象存储配置
        'kodo' => [
            // AccessKey
            'access_key' => '',
            // SecretKey
            'secret_key' => '',
            // 默认Bucket名称
            'bucket' => ''
        ],
    ],
];