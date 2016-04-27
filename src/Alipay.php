<?php

/**
 * @Created by kevin(askyiwang@gmail.com).
 * @User: kevin
 * @Date: 2016/4/25
 * @Time: 23:33
 * @description
 */
namespace simplephp\payment\src;

use simplephp\payment\src\Alipay\AlipaySubmit;
use simplephp\payment\src\Alipay\AlipayNotify;

class Alipay implements \simplephp\payment\src\PaymentInterface
{
    private $alipay_config = [
        'service' => 'create_direct_pay_by_user',
        'payment_type' => '1',
        'transport' => 'http',
        'sign_type' => 'MD5',
        'input_charset' => 'utf-8',
    ];

    public function __construct($option = []) {
        $this->alipay_config = array_merge($this->alipay_config, $option);
    }

    public function checkConfig() {
        return true;
    }

    /**
     * @param array $option
     *
     */
    public function pay($option = []) {

        $this->alipay_config['notify_url'] = $option['notify_url'];
        $this->alipay_config['return_url'] = $option['return_url'];

        $parameter = array(
            "service" => "create_direct_pay_by_user",
            "partner" => trim($this->alipay_config['partner']),
            "seller_id"	=> trim($this->alipay_config['partner']),
            "payment_type"	=> $this->alipay_config['payment_type'],
            "notify_url"	=> $option['notify_url'],
            "return_url"	=> $option['return_url'],

            "out_trade_no"	=> $option['order_no'],
            "subject"	=> $option['subject'],
            "body"	=> isset($option['body']) ? $option['body'] : '',
            "total_fee"	=> $option['money'],
            "anti_phishing_key"	=> isset($option['anti_phishing_key']) ? $option['anti_phishing_key'] : '',
            "exter_invoke_ip"	=> isset($option['exter_invoke_ip']) ? $option['exter_invoke_ip'] : '',
            "_input_charset"	=> trim(strtolower($this->alipay_config['input_charset']))
        );

        $alipaySubmit = new AlipaySubmit($this->alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确定");
        return $html_text;
    }

    /**
     * 支付宝同步
     * @param $option 无效参数
     * @return mixed|void
     */
    public function verifyReturn($option = []) {
        $alipayNotify = new AlipayNotify($this->alipay_config);
        return $alipayNotify->verifyReturn();
    }

    /**
     * 支付宝异步
     * @param $option 无效参数
     * @return mixed|void
     */
    public function verifyNotify($option = []) {
        $alipayNotify = new AlipayNotify($this->alipay_config);
        return $alipayNotify->verifyNotify();
    }
}