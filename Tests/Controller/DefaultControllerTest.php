<?php

namespace simplephp\payment\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertContains('Hello World', $client->getResponse()->getContent());
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $payment = $this->get('payment')->get('alipay');
        $order_no = date('ymdhis').substr(microtime(),2,4);
        $option = [
            'order_no' => $order_no,//  订单ID
            'subject' => '测试充值主题',  //  订单标题
            'body' => '测试充值具体内容',  //  订单内容
            'money' => '0.01',      //  money
            'notify_url' => $this->generateUrl('alipaynotify', [], 0),  //支付宝同步调用地址
            'return_url' => $this->generateUrl('alipayreturn', [], 0),  //支付宝异步调用地址
        ];
        echo $payment->pay($option);
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * 首信易
     * @Route("/payease", name="payease")
     * @param Request $request
     */
    public function payeaseAction(Request $request) {
        $payease_mid = $this->getParameter('payment.payease.mid');
        $order_ymd = date('Ymd');
        $v_date = date('His');
        // 首信易订单号是有格式的
        $order_no = $order_ymd.'-' . $payease_mid.'-'.$v_date;

        $option = [
            'pmode'         => 3,                       //银行ID
            'order_no'      => $order_no,               //订单
            'rcvname'	    => '1001',                  //收货人姓名,建议用商户编号uid代替或者是英文数字uname。因为首信平台的编号是gb2312的
            'rcvaddr'	    => '2001',                  //收货人地址，可用商户编号代替
            'rcvtel'	    => '15683272574',           //收货人电话
            'rcvpost'	    => '401228',                //收货人邮编
            'money'	        => 0.01,                    //订单金额
            'ymd'	        => $order_ymd,              //订单时间
            'orderstatus'	=> 1,                       //配货状态:0-未配齐，1-已配
            'ordername'	    => '12',                    //产品ID
            'moneytype'	    => 0,                       //0为人民币，1为美元，2为欧元，3为英镑，4为日元，5为韩元，6为澳大利亚元，7为卢布(内卡商户币种只能为人民币)
            'return_url'	=> $this->generateUrl('payeasereturn', [], 0),   //首信易只能自定义同步地址，异步地址需要联系首信易官网指定
        ];
        $payment = $this->get('payment')->get('payease');
        echo $payment->pay($option);
        return $this->render('default/payease.html.twig');
    }

    /**
     * 首信易同步回调
     * @Route("/payeasereturn", name="payeasereturn")
     * @param Request $request
     */
    public function payeasereturn(Request $request) {

    }
    /**
     * @Route("/alipayreturn", name="alipayreturn")
     */
    public function alipayreturn(Request $request) {
        $payment = $this->get('payment')->get('alipay');
        $verify_result = $payment->verifyReturn();

        if($verify_result) {//验证成功
            //请在这里加上商户的业务逻辑程序代码

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

            //商户订单号
            $out_trade_no = $request->get('out_trade_no');

            //支付宝交易号
            $trade_no = $request->get('trade_no');

            //交易状态
            $trade_status = $request->get('trade_status');

            if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
            } else {
                echo "trade_status=".$trade_status;
            }

            echo "验证成功<br />";

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

        } else {
            //验证失败
            //如要调试，请看alipay_notify.php页面的verifyReturn函数
            echo "验证失败";
        }
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/alipaynotify", name="alipaynotify")
     */
    public function alipaynotify(Request $request) {
        $payment = $this->get('payment')->get('alipay');
        $verify_result = $payment->verifyNotify();
        if($verify_result) {
            ### 业务逻辑处理
            //商户订单号
            $out_trade_no = $request->request->get('out_trade_no');

            //支付宝交易号
            $trade_no =  $request->request->get('trade_no');

            //交易状态
            $trade_status =  $request->request->get('trade_status');

            if($_POST['trade_status'] == 'TRADE_FINISHED') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
                //如果有做过处理，不执行商户的业务程序

                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            } else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
                //如果有做过处理，不执行商户的业务程序

                //注意：
                //付款完成后，支付宝系统发送该交易状态通知

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            echo "success";		//请不要修改或删除

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
            echo "fail";

            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
}
