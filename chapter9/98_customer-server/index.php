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
                    $result = $this->receiveText($postObj);
                    break;
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                default:
                    $result = "";
                    break;
            }
            echo $result;
        }else {
            echo "";
            exit;
        }
    }

    private function receiveText($object)
    {
        $keyword = trim($object->Content);
        $mmc = memcache_init();
        $service = memcache_get($mmc, "service");
		
        //当前有人工客服对话
        if ($service){
            $relation = json_decode($service, true);
            //用户处在人工客服交互中
            if (in_array($object->FromUserName, $relation)){
                require_once('weixin.class.php');
                $to = ($relation['from'] == strval($object->FromUserName))?$relation['to']:$relation['from'];
                $weixin = new class_weixin();
                $weixin->send_custom_msg($to, "text", $keyword);
                return;
            }
        }
        
        $content = "22";
        if (is_array($content)){
            $result = $this->transmitNews($object, $content);
        }else{
            $result = $this->transmitText($object, $content);
        }
        return $result;


    }

    private function receiveEvent($object)
    {
        $content = "";
        switch ($object->Event)
        {
            case "subscribe":
                $content = "欢迎关注方倍工作室";
            case "unsubscribe":
                break;
            case "CLICK":
                switch ($object->EventKey)
                {
                    case "在线客服":
                        $staffs = array("oQW8FuFKL0530Qhp3rh0HciHkTL0");
                        $mmc=memcache_init();
                        require_once('weixin.class.php');
                        $weixin = new class_weixin();

                        //客服点击，结束会话
                        if (in_array($object->FromUserName, $staffs)) {
                            $relation = json_decode(memcache_get($mmc, "service"), true);
                            $from = strval($object->FromUserName);
                            $to = $relation['from'];
                            $weixin->send_custom_msg($from, "text", "你已结束本次对话！");
                            $weixin->send_custom_msg($to, "text", "感谢您的咨询。期待下次再会！");
                            memcache_delete($mmc,"service");
                        }

                        //用户点击，开始会话
                        else {
                            $from = strval($object->FromUserName);
                            $to = $staffs[0];
                            memcache_set($mmc, "service", '{"from":"'.$from.'","to":"'.$to.'"}');
                            $weixin->send_custom_msg($from, "text", "正在为您接入客服，请稍候...");
                            $weixin->send_custom_msg($to, "text", "有用户请求接入，请响应！");
                        }
                        return;
                    default:
                        $content = "菜单默认回复";
                        break;
                }
                break;
            default:
                break;

        }
        if (is_array($content)){
            $result = $this->transmitNews($object, $content);
        }else{
            $result = $this->transmitText($object, $content);
        }
        return $result;
    }

    private function transmitText($object, $content, $funcFlag = 0)
    {
        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
<FuncFlag>%d</FuncFlag>
</xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $funcFlag);
        return $result;
    }

    private function transmitNews($object, $arr_item, $funcFlag = 0)
    {
        //首条标题28字，其他标题39字
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
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);

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

        $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($arr_item), $funcFlag);
        return $result;
    }
}
?>