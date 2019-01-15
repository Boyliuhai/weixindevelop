<?php

/*
    方倍工作室
    CopyRight 2014 All Rights Reserved
*/
require_once('config.php');   //引用配置

class class_weixin
{
	var $appid = APPID;
	var $appsecret = APPSECRET;

    //构造函数，获取Access Token
	public function __construct($appid = NULL, $appsecret = NULL)
	{
        if($appid && $appsecret){
            $this->appid = $appid;
			$this->appsecret = $appsecret;
        }

        //HARDCODE
        $this->lasttime = 1316888645;
        $this->access_token = "0a7Ewf0QNGkS5gRZbJeDSUHRBXGMat85dxpYFsatJQiwT32AYu_EGBj6FJNYNxWevm05Pb2lUnpJ_AN-sZKM42Ukf1y2hjCGY9yJfHQfYUI";

        if (time() > ($this->lasttime + 7200)){
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
            $res = $this->http_request($url);
            $result = json_decode($res, true);
            //save to Database or Memcache
            $this->access_token = $result["access_token"];
            $this->lasttime = time();

            // var_dump($this->lasttime);
            // var_dump($this->access_token);
        }
	}

    //生成OAuth2的URL
	public function oauth2_authorize($redirect_url, $scope, $state = NULL)
    {
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appid."&redirect_uri=".$redirect_url."&response_type=code&scope=".$scope."&state=".$state."#wechat_redirect";
        return $url;
	}
    //生成OAuth2的Access Token
	public function oauth2_access_token($code)
    {
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->appid."&secret=".$this->appsecret."&code=".$code."&grant_type=authorization_code";
        $res = $this->http_request($url);
        return json_decode($res, true);
	}
	
	//获取用户基本信息
	public function get_user_info($openid)
    {
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->access_token."&openid=".$openid."&lang=zh_CN";
		$res = $this->http_request($url);
        return json_decode($res, true);
	}

    //创建菜单
    public function create_menu($data)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->access_token;
        $res = $this->http_request($url, $data);
        return json_decode($res, true);
    }

    //根据订单ID获取订单详情
	public function get_detail_by_order_id($id)
    {
        $data = array('order_id' =>$id);
		$url = "https://api.weixin.qq.com/merchant/order/getbyid?access_token=".$this->access_token;
        $res = $this->http_request($url, json_encode($data));
        return json_decode($res, true);
	}

    //根据订单状态/创建时间获取订单详情
	public function get_detail_by_filter($data = null)
    {
		$url = "https://api.weixin.qq.com/merchant/order/getbyfilter?access_token=".$this->access_token;
        $res = $this->http_request($url, $data);
        return json_decode($res, true);
	}

    //发送客服消息，已实现发送文本，其他类型可扩展
	public function send_custom_message($touser, $type, $data)
    {
        $msg = array('touser' =>$touser);
        $msg['msgtype'] = $type;
        switch($type)
        {
			case 'text':
				$msg[$type]    = array('content'=>urlencode($data));
				break;
			case 'news':
				$msg[$type]    = array('articles'=>$data);
				break;
            default:
                $msg['text']   = array('content'=>urlencode("不支持的消息类型 ".$type));
                break;
        }
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$this->access_token;
		return $this->http_request($url, urldecode(json_encode($msg)));
	}

	/*
	  $template = array('touser' => "owddJuAiiQpXZedAWxjpp3pkZTzU",
	  'template_id' => "jD1Jfu0ElKcyEK0CfJ2JjTy4U1fjYI09l6eax9BBu9U",
	  'url' => "",
	  'topcolor' => "#7B68EE",
	  'data' => array('first'	=> array('value' => urlencode("您好，方倍，欢迎使用模版消息！"),
										 'color' => "#743A3A",
										  ),
					  'product' => array('value' => urlencode("微信公众平台开发最佳实践"),
										 'color' => "#FF0000",
										  ),
					  'price' 	=> array('value' => urlencode("69.00元"),
										 'color' => "#C4C400",
										  ),
					  'time' 	=> array('value' => urlencode("2014年6月1日"),
										 'color' => "#0000FF",
										  ),
					  'remark' 	=> array('value' => urlencode("\\n你的订单已提交，我们将尽快发货。祝您生活愉快！"),
										 'color' => "#008000",
										  ),

					  )
	  );
	  $weixin->send_template_message(urldecode(json_encode($template)));
	*/
	//发送模版消息
	public function send_template_message($data)
    {
		$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$this->access_token;
        $res = $this->http_request($url, $data);
        return json_decode($res, true);
	}
	
    //https请求（支持GET和POST）
    protected function http_request($url, $data = null)
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
