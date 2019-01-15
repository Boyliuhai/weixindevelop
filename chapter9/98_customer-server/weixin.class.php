<?php

// $weixin = new class_weixin();
// $openid = "oQW8FuFKL0530Qhp3rh0HciHkTL0";
// $weixin->send_custom_msg($openid, "text", "ttttt");

class class_weixin
{
	var $appid = "";
	var $appsecret = "";

    //构造函数
	public function __construct($appid = NULL, $appsecret = NULL)
	{
        if($appid){
            $this->appid = $appid;
        }
        if($appsecret){
            $this->appsecret = $appsecret;
        }
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
        $res = $this->https_request($url);
        $result = json_decode($res, true);
        $this->access_token = $result["access_token"];
	}

    //发送客服消息，文本形式
	public function send_custom_msg($touser, $type, $data)
    {
        $msg = array('touser' =>$touser);
        switch($type)
        {
			case 'text':
				$msg['msgtype'] = 'text';
				$msg['text']    = array('content'=> urlencode($data));
				break;
        }
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$this->access_token;
		return $this->https_request($url, urldecode(json_encode($msg)));
	}

    protected function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}

