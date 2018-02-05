<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2018/2/5
 * Time: 下午6:24
 */

namespace Linyuee;

use Linyuee\aop\AopClient;
use Linyuee\Exception\ApiException;

abstract class AliPayBase
{
    private $appid;
    protected $format;
    protected $signType = 'RSA2';
    protected $aop;
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


    public function setAppPrivateKey($app_private_key){
        $this->aop->rsaPrivateKey = $app_private_key;
    }

    public function setAliPayPublicKey($ali_pay_public_key){
        $this->aop->alipayrsaPublicKey = $ali_pay_public_key;
    }

    //设置支付biz_content
    public function setPayFormat(array $format){
        $this->format = $format;
    }

    protected function getPayFormat(){
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

    protected function getReturnUrl(){
        if (empty($this->returnUrl)){
            throw new ApiException('H5支付没有设置支付完成跳转地址');
        }
        return $this->returnUrl;
    }
}