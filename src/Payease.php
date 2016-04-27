<?php

/**
 * @Created by kevin(askyiwang@gmail.com).
 * @User: kevin
 * @Date: 2016/4/25
 * @Time: 23:33
 * @description
 */
namespace simplephp\payment\src;


class Payease implements \simplephp\payment\src\PaymentInterface
{
    /**
     * 首信易支付网关
     * @var string
     */
    private $payease_gateway = 'https://pay.yizhifubj.com/customer/gb/pay_bank.jsp';

    /**
     * 银行map
     * @var array
     */
    public static $blank = array(
        3   => '招商银行一网通',
        4   => '北京建设银行龙卡',
        9   => '工商银行牡丹卡',
        14  => '平安银行',
        28  => '民生银行卡',
        33  => '兴业银行卡',
        43  => '农业银行金穗卡',
        44  => '广东发展银行',
        50  => '北京银行',
        59  => '中国邮政',
        60  => '华夏银行',
        67  => '交通银行',
        69  => '浦发银行',
        74  => '光大银行',
        75  => '北京农村商业银行',
        83  => '渤海银行',
        84  => '中信银行',
        85  => '中国银行',
        121 => '上海银行',
        126 => '银联支付',
    );

    /**
     * @var array
     */
    private $payease_config = [

    ];

    public function __construct($option = []) {
        $this->payease_config = array_merge($this->payease_config, $option);
    }

    public function checkConfig() {
        return true;
    }

    /**
     * @param array $option
     *
     */
    public function pay($option = []) {

        $parameter = array(
            'v_mid'         => $this->payease_config['mid'],
            'v_pmode'       => $option['pmode'],        //银行ID
            'v_oid'         => $option['order_no'],     //订单
            'v_rcvname'	    => $option['rcvname'],      //收货人姓名,建议用商户编号代替或者是英文数字。因为首信平台的编号是gb2312的
            'v_rcvaddr'	    => $option['rcvaddr'],      //收货人地址，可用商户编号代替
            'v_rcvtel'	    => $option['rcvtel'],       //收货人电话
            'v_rcvpost'	    => $option['rcvpost'],      //收货人邮箱
            'v_amount'	    => $option['money'],        //订单金额
            'v_ymd'	        => $option['ymd'],          //订单时间
            'v_orderstatus'	=> $option['orderstatus'],  //配货状态:0-未配齐，1-已配
            'v_ordername'	=> $option['ordername'],    //收货人地址，可用商户编号代替
            'v_moneytype'	=> $option['moneytype'],    //0为人民币，1为美元，2为欧元，3为英镑，4为日元，5为韩元，6为澳大利亚元，7为卢布(内卡商户币种只能为人民币)
            'v_url'	        => $option['return_url'],   //首信易只能自定义同步地址，异步地址需要联系首信易官网指定
            'v_md5info'	    => $this->hmac($this->payease_config['security_code'],($option['moneytype'].$option['ymd'].$option['money'].$option['rcvname'].$option['order_no'].$this->payease_config['mid'].$option['return_url'])),
        );
        $html_text = $this->buildRequestForm($parameter, 'POST');
        return $html_text;
    }
    /**
     * 此处摘抄 支付宝的方法 可以使用任何形式的 http 请求，例如 curl
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @param $method 提交方式。两个值可选：post、get
     * @param $button_name 确认按钮显示文字
     * @return 提交表单HTML文本
     */
    private function buildRequestForm($para_temp, $method) {
        $sHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">';
        $sHtml .= '<html xmlns="http://www.w3.org/1999/xhtml">';
        $sHtml .= '<head>';
        $sHtml .= '<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />';
        $sHtml .= '<title>首信易支付</title>';
        $sHtml .= '</head>';
        $sHtml .= '<body>';
        $sHtml .= "<form id='payease_form' name='payease_form' action='".$this->payease_gateway."' method='".$method."' target='_parent'>";
        while (list ($key, $val) = each ($para_temp)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
        $sHtml .= $sHtml."<script>payease_form.submit();</script>";
        $sHtml .= '</body></html>';
        return $sHtml;
    }
    /**
     * v_md5info的计算
     * @param $key
     * @param $data
     * @return string
     */
    private function hmac($key, $data) {
        // 创建 md5的HMAC
        $b = 64; // md5加密字节长度
        if (strlen($key) > $b) {
            $key = pack("H*",md5($key));
        }
        $key  = str_pad($key, $b, chr(0x00));
        $ipad = str_pad('', $b, chr(0x36));
        $opad = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;
        return md5($k_opad  . pack("H*",md5($k_ipad . $data)));
    }

    /**
     * 根据银行ID 获取银行名称
     * @param $bid 银行ID
     * return string
     */
    public static function getBlankById($bid) {
        if(isset(self::$blank[$bid])) {
            return self::$blank[$bid];
        } else {
            return false;
        }
    }

    /**
     * 首信易同步
     * @param array $option
     * @return bool|mixed
     */
    public function verifyReturn($option = array()) {
        //计算得出通知验证结果

        $data1 = $option['v_oid']. $option['v_pstatus']. $option['v_pstring']. $option['v_pmode'];
        $md5info = $this->hmac($this->payease_config['payease_security_code'], $data1);

        $data2 = $option['v_amount'].$option['v_moneytype'];
        $md5money= $this->hmac($this->payease_config['payease_security_code'], $data2);

        if($md5info == $option['v_md5info'] && $md5money == $option['v_md5money']) {
            if($option['v_pstatus'] == '20') {
                return true;
            } else if($option['v_pstatus']=='30') {
                return false;
            }  else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 首信易异步
     * @param array $option
     * @return bool|mixed
     */
    public function verifyNotify($option) {
        $data1 = $option['v_oid'].$option['v_pmode'].$option['v_pstatus'].$option['v_pstring'].$option['v_count'];
        $mac = $this->hmac($this->payease_config['payease_security_code'], $data1);

        $data2 = $option['v_amount'].$option['v_moneytype'];
        $md5money= $this->hmac($this->payease_config['payease_security_code'], $data2);

        if($mac == $option['v_mac'] or $md5money == $option['v_md5money']) {
            if($option['v_pstatus'] == '1') {
                return true;
            } else if($option['v_pstatus'] == '3') {
                return false;
            }  else {
                return false;
            }
        } else {
            return false;
        }
    }
}