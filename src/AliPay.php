<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/12/20
 * Time: 上午11:08
 */
namespace Linyuee;

use Linyuee\aop\request\AlipayTradeAppPayRequest;
use Linyuee\aop\AopClient;
use Linyuee\aop\request\AlipayTradeRefundRequest;
use Linyuee\aop\request\AlipayTradeWapPayRequest;
use Linyuee\Exception\ApiException;

class AliPay
{
    protected $appid;
    protected $appPrivatekey;
    protected $aliPayPublicKey;
    protected $format;
    protected $signType = 'RSA2';
    protected $notifyUrl;
    protected $returnUrl;
    private $aop;
    public function __construct($appid)
    {
        $this->appid = $appid;

        $this->aop = new AopClient;
        $this->aop->appId = $this->appid;
        $this->aop->format = "json";
        $this->aop->charset = "UTF-8";
        $this->aop->signType = $this->signType;
        $this->aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
    }
    //统一下单
    public function appPay(){

        $this->aop->rsaPrivateKey = $this->getAppPrivateKey();
        $this->aop->alipayrsaPublicKey = $this->getAliPayPublicKey();
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
        $this->aop->rsaPrivateKey = $this->getAppPrivateKey();
        $this->aop->alipayrsaPublicKey = $this->getAliPayPublicKey();
        $request = new AlipayTradeWapPayRequest();
        $bizcontent = json_encode($this->getPayFormat());
        $request->setNotifyUrl($this->notifyUrl);
        $request->setReturnUrl($this->getReturnUrl());
        $request->setBizContent($bizcontent);
        $result = $this->aop->sdkExecute($request);
        $result = $this->aop->gatewayUrl.'?'.$result;
        return $result;
    }

    //统一退款
    public function refund(){
        $this->aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $this->aop->rsaPrivateKey = $this->getAppPrivateKey();
        $this->aop->alipayrsaPublicKey = $this->getAliPayPublicKey();
        $request = new AlipayTradeRefundRequest();
        $bizcontent = json_encode($this->format);
        $request->setBizContent($bizcontent);
        $result = $this->aop->execute ( $request);
        return json_decode(json_encode($result->alipay_trade_refund_response),true);
        //return $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
    }

    public function setAppPrivateKey($app_private_key){
        $this->appPrivatekey = $app_private_key;
    }

    public function getAppPrivateKey(){
        if(empty($this->appPrivatekey)){
            throw new ApiException('APP_PRIVATE_KEY不能为空');
        };
        return $this->appPrivatekey;
    }

    public function setAliPayPublicKey($ali_pay_public_key){
        $this->aliPayPublicKey = $ali_pay_public_key;
    }

    public function getAliPayPublicKey(){
        if(empty($this->aliPayPublicKey)){
            throw new ApiException('ALIPAY_PUBLIC_KEY不能为空');
        };
        return $this->aliPayPublicKey;
    }
    //设置支付biz_content
    public function setPayFormat(array $format){

        $this->format = $format;
    }

    public function getPayFormat(){
        $need = ['subject','out_trade_no','total_amount'];
        foreach ($need as $key=>$item) {
            if (!array_key_exists($item,$this->format)){
                throw new ApiException('缺少业务参数'.$item);
            }
        }
        return $this->format;
    }
    //设置退款参数
    public function setRefundFormat(array $format){
        if (!array_key_exists('out_trade_no',$format)&&!array_key_exists('trade_no',$format)){
            throw new ApiException('out_trade_no和trade_no不能同时为空');
        }
        if (!array_key_exists('refund_amount',$format)){
            throw new ApiException('refund_amount不能为空');
        }
        $this->format = $format;
    }

    public function setSignType($signType){
        $this->signType = $signType;
    }

    public function setNotifyUrl($notify_url){
        $this->notifyUrl = $notify_url;
    }

    public function setReturnUrl($url){
        $this->returnUrl = $url;
    }

    public function getReturnUrl(){
        return $this->returnUrl;
    }

}