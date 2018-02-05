<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/12/20
 * Time: 上午11:08
 */
namespace Linyuee;

use Linyuee\aop\request\AlipayTradeAppPayRequest;
use Linyuee\aop\request\AlipayTradePagePayRequest;
use Linyuee\aop\request\AlipayTradeRefundRequest;
use Linyuee\aop\request\AlipayTradeWapPayRequest;
use Linyuee\Exception\ApiException;

class AliPay extends AliPayBase
{


    protected $notifyUrl;
    protected $returnUrl;


    //统一下单
    public function appPay(){
        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        $request = new AlipayTradeAppPayRequest();
        //SDK已经封装掉了公共参数，这里只需要传入业务参数
        $bizcontent = json_encode($this->getPayFormat());
        $request->setNotifyUrl($this->notifyUrl);
        $request->setBizContent($bizcontent);
        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $this->aop->sdkExecute($request);
        return $response;
        //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题

        //return htmlspecialchars($response);//就是orderString 可以直接给客户端请求，无需再做处理。
    }

    public function wapPay(){
        $request = new AlipayTradeWapPayRequest();
        $bizcontent = json_encode($this->getPayFormat());
        $request->setNotifyUrl($this->notifyUrl);
        $request->setReturnUrl($this->getReturnUrl());
        $request->setBizContent($bizcontent);
        $result = $this->aop->sdkExecute($request);
        $result = $this->aop->gatewayUrl.'?'.$result;
        return $result;
    }


    public function pagePay(){
        $request = new AlipayTradePagePayRequest();
        $bizcontent = json_encode($this->getPayFormat());
        $request->setNotifyUrl($this->notifyUrl);
        $request->setReturnUrl($this->getReturnUrl());
        $request->setBizContent($bizcontent);
        // 首先调用支付api
        $result = $this->aop->sdkExecute($request);
        $result = $this->aop->gatewayUrl.'?'.$result;
        return $result;
    }

    //统一退款
    public function refund(){
        $request = new AlipayTradeRefundRequest();
        $bizcontent = json_encode($this->format);
        $request->setBizContent($bizcontent);
        $result = $this->aop->execute ( $request);
        return json_decode(json_encode($result->alipay_trade_refund_response),true);
        //return $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
    }



}