Symfony2 Alipay bundle

This bundle permits you to create, modify and read excel objects.

## Installation

**1**  Add to composer.json to the `require` key

``` yml
    "require" : {
        "simplephp/payment": "dev",
    }
``` 

**2** Register the bundle in ``app/AppKernel.php``

``` php
    $bundles = array(
        // ...
         new simplephp\payment\PaymentBundle(),
    );
```

## How to used? Just Three steps

- First! get service:

``` php
$payment = $this->get('payment')->get('alipay');
#alipay is pay method
```
- Second! configuration parameters in your your yml(app\config\config.yml) like this:
``` yml
# payment Configuration
payment:
    alipay:
        partner: ***************
        key: ***************
    payease:
        security_code: ***************
        mid: ***************
    paypal:
        client_id: ***************
        secret: ***************
```

- Third! generate order and configuration parameters:

``` php
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
```

- In our Controller:

```php
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
```

- Handle their own business logic:

```php
    /**
     * @Route("/alipayreturn", name="alipayreturn")
     */
    public function alipayreturnAction(Request $request) {
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
    public function alipaynotifyAction(Request $request) {
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
```


