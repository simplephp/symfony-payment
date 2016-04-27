<?php
/**
 * 
 * description ********************
 * author: Kevin<askyiwag@gmail.com>
 * date: 2016/4/2516:35
 * created by PhpStorm.
 */

namespace simplephp\payment\src;

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
     * 同步通知
     * @param $option
     * @return mixed
     */
    public function verifyReturn($option);

    /**
     * 异步通知
     * @return mixed
     */
    public function verifyNotify($option);

} 