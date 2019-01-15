<?php
/*
    方倍工作室
    http://www.fangbei.org/
    CopyRight 2014 All Rights Reserved
*/

define("TOKEN", "weixin");

$wechatObj = new wechatCallbackapiTest();
if (!isset($_GET['echostr'])) {
    $wechatObj->responseMsg();
}else{
    $wechatObj->valid();
}

class wechatCallbackapiTest
{
    //验证签名
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if($tmpStr == $signature){
            echo $echoStr;
            exit;
        }
    }

    //响应消息
    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $this->logger("R ".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
             
            //消息类型分离
            switch ($RX_TYPE)
            {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->receiveText($postObj);
                    break;
                // case "image":
                    // $result = $this->receiveImage($postObj);
                    // break;
                // case "location":
                    // $result = $this->receiveLocation($postObj);
                    // break;
                // case "voice":
                    // $result = $this->receiveVoice($postObj);
                    // break;
                // case "video":
                    // $result = $this->receiveVideo($postObj);
                    // break;
                // case "link":
                    // $result = $this->receiveLink($postObj);
                    // break;
                // default:
                    // $result = "unknown msg type: ".$RX_TYPE;
                    // break;
            }
            $this->logger("T ".$result);
            echo $result;
        }else {
            echo "";
            exit;
        }
    }

    //接收事件消息
    private function receiveEvent($object)
    {
        $content = "";
        switch ($object->Event)
        {
            case "subscribe":
                $content = "欢迎关注 ";
                break;
            case "CLICK":
                switch ($object->EventKey)
                {
                    default:
                        $content = "点击菜单：".$object->EventKey;
                        break;
                }
                break;
            case "merchant_order":
				$orderid = strval($object->OrderId);
				$openid = strval($object->FromUserName);
                require_once('weixin.class.php');
                $weixin = new class_weixin();
				$orderArr0 = $weixin->get_detail_by_order_id($orderid);

                $orderArr  = $orderArr0["order"];
				
				//客服接口发送
				/*
                $orderInfo = "【订单信息】\n单号：".$orderArr["order_id"]."\n时间：".date("Y-m-d H:i:s", ($orderArr["order_create_time"]));
                $goodsInfo = "【商品信息】\n名称：".$orderArr["product_name"].
                "\n总价：￥".($orderArr["product_price"] / 100)." × ".$orderArr["product_count"]." + ￥".($orderArr["order_express_price"] / 100)." = ￥".($orderArr["order_total_price"] / 100);
                $buyerInfo = "【买家信息】\n昵称：".$orderArr["buyer_nick"].
                "\n地址：".$orderArr["receiver_province"].$orderArr["receiver_city"].$orderArr["receiver_zone"].$orderArr["receiver_address"].
                "\n姓名：".$orderArr["receiver_name"]." 电话：".((isset($orderArr["receiver_phone"]) && !empty($orderArr["receiver_phone"]))?($orderArr["receiver_phone"]):($orderArr["receiver_mobile"]));
                $data[] = array("title"=>urlencode("订单通知"), "description"=>"", "picurl"=>"", "url" =>"");
                $data[] = array("title"=>urlencode($orderInfo), "description"=>"", "picurl"=>"", "url" =>"");
                $data[] = array("title"=>urlencode($goodsInfo), "description"=>"", "picurl"=>$orderArr["product_img"], "url" =>"");
                $data[] = array("title"=>urlencode($buyerInfo), "description"=>"", "picurl"=>"", "url" =>"");
                $result2 = $weixin->send_custom_message($openid, "news", $data);
				*/
				
				//模版消息发送 消费品 - 消费品 - 购买成功通知
				$template = array('touser' => $openid,
				'template_id' => "C-OG2tfhVcPrOj9rKPvhPlb6MTUciNcWnoh3RSbVpvI",
				'url' => "",
				'topcolor' => "#7B68EE",
				'data' => array('first'		=> array('value' => urlencode("您好，欢迎来到微信小店购物。"),
													 'color' => "#000000",
													),
							    'product'	=> array('value' => urlencode($orderArr["product_name"]),
													 'color' => "#000093",
													),
							    'price' 	=> array('value' => urlencode("￥".($orderArr["order_total_price"] / 100)),
													 'color' => "#FF0000",
													),
							    'time' 		=> array('value' => urlencode(date("Y-m-d H:i:s", ($orderArr["order_create_time"]))),
													 'color' => "#006000",
													),
							    'remark' 	=> array('value' => urlencode("订单单号：".$orderArr["order_id"]),
													 'color' => "#000000",
													),
						       )
				);
				$weixin->send_template_message(urldecode(json_encode($template)));	
				
                $content = "";
                break;
            default:
                $content = "";
                break;
            
        }
        if(is_array($content)){
            if (isset($content[0])){
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }
        }else{
            $result = $this->transmitText($object, $content);
        }

        return $result;
    }

    //接收文本消息
    private function receiveText($object)
    {
        $keyword = trim($object->Content);
		$content = "";
        //多客服人工回复模式
        if (strstr($keyword, "您好") || strstr($keyword, "你好") || strstr($keyword, "在吗")){
            // $result = $this->transmitService($object);
        }
        //自动回复模式
        else{
            if (strstr($keyword, "文本")){
                $content = "这是个文本消息\n".$object->FromUserName;
            }else if (strstr($keyword, "单图文")){
                $content = array();
                $content[] = array("Title"=>"单图文标题",  "Description"=>"单图文内容", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
            }else if (strstr($keyword, "图文") || strstr($keyword, "多图文")){
                $content = array();
                $content[] = array("Title"=>"多图文1标题", "Description"=>"", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
                $content[] = array("Title"=>"多图文2标题", "Description"=>"", "PicUrl"=>"http://d.hiphotos.bdimg.com/wisegame/pic/item/f3529822720e0cf3ac9f1ada0846f21fbe09aaa3.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
                $content[] = array("Title"=>"多图文3标题", "Description"=>"", "PicUrl"=>"http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
            }else if (strstr($keyword, "音乐")){
                $content = array();
                $content = array("Title"=>"最炫民族风", "Description"=>"歌手：凤凰传奇", "MusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3", "HQMusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3");
            }else if (strstr($keyword, "?")){
                $content = date("Y-m-d H:i:s",time())."\n".$object->FromUserName."";
            }
            
            if(is_array($content)){
                if (isset($content[0]['PicUrl'])){
                    $result = $this->transmitNews($object, $content);
                }else if (isset($content['MusicUrl'])){
                    $result = $this->transmitMusic($object, $content);
                }
            }else{
                $result = $this->transmitText($object, $content);
            }
        }

        return $result;
    }

    //接收图片消息
    private function receiveImage($object)
    {
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitImage($object, $content);
        return $result;
    }

    //接收位置消息
    private function receiveLocation($object)
    {
        $content = "你发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //接收语音消息
    private function receiveVoice($object)
    {
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $content = "你刚才说的是：".$object->Recognition;
            $result = $this->transmitText($object, $content);
        }else{
            $content = array("MediaId"=>$object->MediaId);
            $result = $this->transmitVoice($object, $content);
        }
        return $result;
    }

    //回复文本消息
    private function transmitText($object, $content)
    {
		if (!isset($content) || empty($content)){
			return "";
		}
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    //回复图文消息
    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return "";
        }
        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>%s</ArticleCount>
<Articles>
$item_str</Articles>
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }

    //回复音乐消息
    private function transmitMusic($object, $musicArray)
    {
        $itemTpl = "<Music>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <MusicUrl><![CDATA[%s]]></MusicUrl>
    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
</Music>";

        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[music]]></MsgType>
$item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复多客服消息
    private function transmitService($object)
    {
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[transfer_customer_service]]></MsgType>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //日志记录
    private function logger($log_content)
    {
        if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
            sae_set_display_errors(false);
            sae_debug($log_content);
            sae_set_display_errors(true);
        }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.2"){ //LOCAL
            $max_size = 10000;
            $log_filename = "log.xml";
            if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
            file_put_contents($log_filename, date('H:i:s')." ".$log_content."\r\n", FILE_APPEND);
        }
    }
}
?>