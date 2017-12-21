<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/12/20
 * Time: 上午11:08
 */

namespace Linyuee;

use Linyuee\aop\request\AlipayTradeAppPayRequest;
use App\aop\AopClient;
use Linyuee\Exception\ApiException;

class AliPay
{
    protected $appid;
    protected $appPrivatekey;
    protected $aliPayPublicKey;
    protected $format;
    protected $signType = 'RSA2';
    protected $notifyUrl;
    public function __construct($appid)
    {
        $this->appid = $appid;
    }

    public function unifiedorder(){
        $aop = new AopClient;
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = $this->appid;
        $aop->rsaPrivateKey = $this->getAppPrivateKey();
        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = $this->signType;
        $aop->alipayrsaPublicKey = $this->getAliPayPublicKey();
//实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        $request = new AlipayTradeAppPayRequest();
//SDK已经封装掉了公共参数，这里只需要传入业务参数
        $bizcontent = $this->format;
        $request->setNotifyUrl($this->notifyUrl);
        $request->setBizContent($bizcontent);
//这里和普通的接口调用不同，使用的是sdkExecute
        $response = $aop->sdkExecute($request);
//htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
        return htmlspecialchars($response);//就是orderString 可以直接给客户端请求，无需再做处理。
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
        return $this->appPrivatekey;
    }

    public function setFormat($format){
        $this->format = $format;
    }

    public function setSignType($signType){
        $this->signType = $signType;
    }

    public function setNotifyUrl($notify_url){
        $this->notifyUrl = $notify_url;
    }

}