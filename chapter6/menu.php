<?php
require_once('weixin.class.php');
$weixin = new class_weixin();

$wxshopurl = "http://mp.weixin.qq.com/bizmall/mallshelf?id=&t=mall/list&biz=MzA3NjA3NTA3OA==&shelf_id=1&showwxpaytitle=1#wechat_redirect";



$callbackurl = dirname('http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"])."/order.php";
$orderurl =  "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=".$callbackurl."&response_type=code&scope=snsapi_base&state=123#wechat_redirect";

$button[] = array('type' => "view",
                  'name' => urlencode("微信小店"),
                  'url'  => $wxshopurl,
                 );
$button[] = array('name' => urlencode("我的交易"),
                  'sub_button' => array(
										array('type' => "view",
                                              'name' => urlencode("我的订单"),
                                              'url'  => $orderurl
                                             ),
                                        array('type' => "view",
                                              'name' => urlencode("维权"),
                                              'url'  => "https://mp.weixin.qq.com/payfb/payfeedbackindex?appid=".APPID."#wechat_webview_type=1&wechat_redirect"
                                             ),
                                        )
                  );

$menu = urldecode(json_encode(array('button' => $button)));

var_dump($weixin->create_menu($menu));
?>
