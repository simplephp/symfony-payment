<?php
/**
 * 
 * description ********************
 * author: Kevin<askyiwag@gmail.com>
 * date: 2016/4/2516:35
 * created by PhpStorm.
 */
namespace simplephp\Bundle;

class Payment {

    //payment option
    private $payment_option = [];

    // 支付 map
    private $support_map = [
        'alipay' => 'simplephp\\Bundle\\src\\Alipay',
        'payease' => 'simplephp\\Bundle\\src\\Payease'
    ];

    /**
     * 直接简单使用 ReflectionClass
     * @param $payment_type
     */
    public function get($payment_type) {
        if(!$this->isSupport($payment_type)) {
            throw new \Exception('暂不支持该支付方式');
        }
        $namesapce = $this->support_map[$payment_type];

        $class = new \ReflectionClass($namesapce);
        return $class->newInstanceArgs([$this->payment_option[$payment_type]]);
    }

    /**
     * 内部调用初始化参数不要修改
     * @param array $options
     */
    public function setOptions($options = []) {
        $this->payment_option = array_merge($this->payment_option, $options);
    }

    /**
     * 是否支持支付方式
     * @param $payment_type
     * @return bool
     */
    protected function isSupport($payment_type) {
        if(isset($this->support_map[$payment_type])) {
            return true;
        }
        return false;
    }


} 