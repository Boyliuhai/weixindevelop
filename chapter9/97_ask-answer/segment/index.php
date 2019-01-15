<?php

/* 
【版权声明】 
    本软件产品的版权归方倍工作室所有，受《中华人民共和国计算机软件保护条例》等知识产权法律及国际条约与惯例的保护。您获得的只是本软件的使用权。 
 
    您不得: 
    * 在未得到授权的情况下删除、修改本软件及其他副本上一切关于版权的信息； 
    * 销售、出租此软件产品的任何部分； 
    * 从事其他侵害本软件版权的行为。 
 
    如果您未遵守本条款的任一约定，方倍工作室有权立即终止本条款的执行，且您必须立即终止使用本软件并销毁本软件产品的任何副本。这项要求对各种拷贝形式有效。 
 
    您同意承担使用本软件产品的风险，在适用法律允许的最大范围内，方倍工作室在任何情况下不就因使用或不能使用本软件产品所发生的特殊的、意外的、非直接或间接的损失承担赔偿责任。即使已事先被告知该损害发生的可能性。 
 
    如使用本软件所添加的任何信息，发生版权纠纷，方倍工作室不承担任何责任。 
 
    方倍工作室对本条款拥有最终解释权。 
 
    CopyRight 2013  方倍工作室  All Rights Reserved 
 
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
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            switch ($RX_TYPE)
            {
                case "text":
                    $resultStr = $this->receiveText($postObj);
                    break;
                case "event":
                    $resultStr = $this->receiveEvent($postObj);
                    break;
                case "voice":
                    $resultStr = $this->receiveVoice($postObj);
                    break;
                default:
                    $resultStr = "";
                    break;
            }
            echo $resultStr;
        }else {
            echo "";
            exit;
        }
    }

    private function receiveText($object)
    {
        if (isset($object->Recognition)){
            $content = strval($object->Recognition);
            $mediaid = trim($object->MediaID);
        }else{
            $content = trim($object->Content);
        }
        if (isset($_SERVER['HTTP_APPNAME'])){
            include("segment.php");
            $result = sinasegment($content);
            if (is_array($result)){
                switch ($result['category'])
                {
                    case "天气":
                        $url = "http://api100.duapp.com/weather/?appkey=book97&city=".urlencode($result['keyword']);
                        $output = file_get_contents($url);
                        $replyContent = json_decode($output, true);
                        break;
                    case "空气":
                        $url = "http://api100.duapp.com/airquality/?appkey=book97&city=".urlencode($result['keyword']);
                        $output = file_get_contents($url);
                        $replyContent = json_decode($output, true);
                        break; 
                    default:
                        $replyContent = "还不支持这一功能：".$result['category'];
                        break;
                }
            }else{
                $replyContent = "不能理解你的内容：".$content;
            }
        }else{
            $replyContent = "只能运行在SAE环境！";
        }
        
        if (is_array($replyContent)){
            $resultStr = $this->transmitNews($object, $replyContent);
        }else{
            $resultStr = $this->transmitText($object, $replyContent);
        }
        return $resultStr;
    }
    
    private function receiveEvent($object)
    {
        $replyContent = "";
        switch ($object->Event)
        {
            case "subscribe":
                $replyContent = "欢迎关注方倍工作室";
            case "unsubscribe":
                break;
            default:
                break;      

        }
        if (is_array($replyContent)){
            $resultStr = $this->transmitNews($object, $replyContent);
        }else{
            $resultStr = $this->transmitText($object, $replyContent);
        }
        return $resultStr;
    }
    

    private function receiveVoice($object)
    {
        $funcFlag = 0;
        
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $content = strval($object->Recognition);
            include("segment.php");
            $result = sinasegment($content);
            if (is_array($result)){
                switch ($result['category'])
                {
                    case "天气":
                        $url = "http://apix.sinaapp.com/weather/?appkey=trialuser&city=".urlencode($result['keyword']);
                        $output = file_get_contents($url);
                        $replyContent = json_decode($output, true);
                        break;
                    default:
                        $replyContent = "还不支持这一功能：".$result['category'];
                        break;
                }
            }else{
                $replyContent = "不能理解你的内容：".$content;
            }
        }else{
            $replyContent = "未开启语音识别功能";
        }
        if (is_array($replyContent)){
            $resultStr = $this->transmitNews($object, $replyContent);
        }else{
            $resultStr = $this->transmitText($object, $replyContent);
        }
        return $resultStr;
    }
    
    
    private function transmitText($object, $content, $flag = 0)
    {
        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
<FuncFlag>%d</FuncFlag>
</xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
        return $resultStr;
    }

    private function transmitNews($object, $arr_item, $flag = 0)
    {
        if(!is_array($arr_item))
            return;

        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
        $item_str = "";
        foreach ($arr_item as $item)
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['Picurl'], $item['Url']);

        $newsTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<Content><![CDATA[]]></Content>
<ArticleCount>%s</ArticleCount>
<Articles>
$item_str</Articles>
<FuncFlag>%s</FuncFlag>
</xml>";

        $resultStr = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($arr_item), $flag);
        return $resultStr;
    }
}
?>