<?php
/**
 * 首信易支付展示
 */
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PayeaseController extends Controller
{
    /**
     * @Route("/payease", name="payease")
     */
    public function indexAction(Request $request)
    {
        $payease_mid = $this->getParameter('payment.payease.mid');
        $order_ymd = date('Ymd');
        $v_date = date('His');
        // 首信易订单号是有格式的
        $order_no = $order_ymd.'-' . $payease_mid.'-'.$v_date;

        $option = [
            'pmode'         => 126,                       //银行ID Payease::$blank
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
    public function payeasereturnAction(Request $request) {

        $option['v_oid'] = $_REQUEST['v_oid'];                        //支付提交时的订单编号，此时返回
        $option['v_pstatus'] = $_REQUEST['v_pstatus'];                //1 待处理,20 支付成功,30 支付失败
        $option['v_pstring'] = urldecode($_REQUEST['v_pstring']);     //支付结果信息返回。当v_pstatus=1时-已提交。20-支付完成。30-支付失败
        $option['v_pmode'] = urldecode($_REQUEST['v_pmode']);       //支付方式。
        $option['v_amount'] = $_REQUEST['v_amount'];                //订单金额
        $option['v_moneytype'] = $_REQUEST['v_moneytype'];          //币种
        $option['v_md5info'] = $_REQUEST['v_md5info'];
        $option['v_md5money'] = $_REQUEST['v_md5money'];
        $option['v_sign'] = $_REQUEST['v_sign'];

        $payment = $this->get('payment')->get('payease');
        $verify_result = $payment->verifyReturn($option);
        if($verify_result) {
            // 业务处理
            echo 'send';
        } else {
            //失败处理
            echo 'error';
        }
        return $this->render('default/payease.html.twig');
    }

    /**
     * @Route("/payeasenotify", name="payeasenotify")
     */
    public function payeasenotifyAction(Request $request) {
        //接收返回的参数
        $option['v_oid'] = $_REQUEST['v_oid'];//订单编号组
        $option['v_pmode'] = urldecode($_REQUEST['v_pmode']);//支付方式组
        $option['v_pstatus'] = $_REQUEST['v_pstatus'];//支付状态组
        $option['v_pstring'] = urldecode($_REQUEST['v_pstring']);//支付结果说明
        $option['v_amount'] = $_REQUEST['v_amount'];//订单支付金额
        $option['v_count'] = $_REQUEST['v_count'];//订单个数
        $option['v_moneytype'] = $_REQUEST['v_moneytype'];//订单支付币种
        $option['v_mac'] = $_REQUEST['v_mac'];//数字指纹（v_mac）
        $option['v_md5money'] = $_REQUEST['v_md5money'];//数字指纹（v_md5money）
        $option['v_sign'] = $_REQUEST['v_sign'];//验证商城数据签名（v_sign）

        //拆分参数
        $sp = '|_|';
        $a_oid = explode($sp, $option['$v_oid']);
        $a_pmode = explode($sp, $option['$v_pmode']);
        $a_pstatus = explode($sp, $option['$v_pstatus']);
        $a_pstring = explode($sp, $option['$v_pstring']);
        $a_amount = explode($sp, $option['$v_amount']);
        $a_moneytype = explode($sp, $option['$v_moneytype']);

        $payment = $this->get('payment')->get('payease');
        $verify_result = $payment->verifyNotify($option);
        if($verify_result) {
            // 业务处理
            //echo 'send/error' 接口要求
            echo 'send';
            //通过for循环查看该笔通知有几笔订单,并对于更改数据库状态
            if($option['v_count'] > 1) {
                for($i = 0; $i < $option['v_count']; $i++) {
                    // todo
                }
            } else {
                // 单条订单处理 todo
            }

        } else {
            echo 'error';
            //失败处理
        }
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
}
