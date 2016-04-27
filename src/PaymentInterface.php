<?php
/**
 * 
 * description ********************
 * author: Kevin<askyiwag@gmail.com>
 * date: 2016/4/2516:35
 * created by PhpStorm.
 */

namespace simplephp\Bundle\src;

interface PaymentInterface {

    /**
     * 检查配置文件
     * @return mixed
     */
    public function checkConfig();

    /**
     * 支付方法
     * @param $option
     * @return mixed
     */
    public function pay($option);

    /**
     * 支付宝同步通知
     * @return mixed
     */
    public function verifyReturn();

    /**
     * 支付宝异步通知
     * @return mixed
     */
    public function verifyNotify();

} 