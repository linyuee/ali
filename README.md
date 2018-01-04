阿里工具包，包括支付宝支付
===============================

### 1、支付支付
```
$pay = new \Linyuee\AliPay('appid');
$biz_content = array('subject'=>'标题','out_trade_no'=>'订单号','total_amount'=>'金额单位分');
$pay->setFormat($biz_content);
$pay->setAppPrivateKey('app密钥');
$pay->setAliPayPublicKey('支付宝公钥');
$pay->setNotifyUrl('回调地址');
$res = $pay->unifiedorder(); 
```
返回示例：

"alipay_sdk=alipay-sdk-php-20161101&amp;app_id=20020302550&amp;
biz_content=%7B%22subject%22%3A%22%5Cu9510%5Cu516c%5Cu8003%5Cu8bfe%5Cu7a0b%5Cu8d2d%5Cu4e70%22%2C%22out_trade_no%22%3A14885469%2C%22total_amount%22%3Anull%7D&amp;
charset=UTF-8&amp;format=json&amp;method=alipay.trade.app.pay&amp;
notify_url=api.test.com%2Fapi%2Ftest&amp;sign_type=RSA2&amp;
timestamp=2017-12-21+15%3A14%3A51&amp;version=1.0&amp;
sign=vPmIQUNxzYKDqb24udR2RNERpr6J8Kvd12XNc7i0q3iSv8l9QY12dWp3cVym4w6QA3rhDJesPcZw94I4s%2BA%3D%3D"

无需处理直接作为APP的请求参数直接请求支付宝的sdk