<?php

require_once('weixin.class.php');
$weixin = new class_weixin();
$openid = "";
if (!isset($_GET["code"])){
	$redirect_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$jumpurl = $weixin->oauth2_authorize($redirect_url, "snsapi_base", "123");
	Header("Location: $jumpurl");
}else{
	$access_token = $weixin->oauth2_access_token($_GET["code"]);
	$openid = $access_token['openid'];
}
	
	
function getOrderItem($openid)
{
	$weixin = new class_weixin();
	$orderArr = $weixin->get_detail_by_filter("{}");
	$content = $orderArr["errcode"];
	if ($orderArr["errcode"] == -1){
		return '
			<tr>
				<td>系统繁忙，请稍后再试！</td>
			</tr>
		';
	}
	else if (count($orderArr["order_list"]) == 0){
		return '
			<tr>
				<td>没有查询到订单记录！</td>
			</tr>
		';
	}else{
		$content = "";
		$data = array();
		$data[] = array("title"=>urlencode("我的订单"), "description"=>"", "picurl"=>"", "url" =>"");
		foreach ($orderArr["order_list"] as $index => $item){
			if($item["buyer_openid"] == $openid){
				$title = "编号：".$item["order_id"]."\n时间：".date("Y-m-d H:i:s",$item["order_create_time"])
						."\n名称：".$item["product_name"]."\n总价：￥".($item["product_price"] / 100)." × ".$item["product_count"]." + ￥".($item["order_express_price"] / 100)." = ￥".($item["order_total_price"] / 100);
				switch ($item["order_status"])
				{
					case 2:
					  $orderstatus = "待发货";
					  break;
					case 3:
					  $orderstatus = "已发货";
					  break;
					case 5:
					  $orderstatus = "已完成";
					  break;
					case 8:
					  $orderstatus = "维权中";
					  break;
					default:
					  $orderstatus = "未知状态码".$item["order_status"];
					  break;
				}
				$title .= "\n状态：".$orderstatus;
				$url = "";
				if ($item["order_status"] == 3 && !empty($item["delivery_company"])){
					switch ($item["delivery_company"])
					{
						case "Fsearch_code":
						  $expressName = "邮政EMS";
						  break;
						case "002shentong":
						  $expressName = "申通快递";
						  break;
						case "066zhongtong":
						  $expressName = "中通速递";
						  break;
						case "056yuantong":
						  $expressName = "圆通速递";
						  break;
						case "042tiantian":
						  $expressName = "天天快递";
						  break;
						case "003shunfeng":
						  $expressName = "顺丰速运";
						  break;
						case "059Yunda":
						  $expressName = "韵达快运";
						  break;
						case "064zhaijisong":
						  $expressName = "宅急送";
						  break;
						case "020huitong":
						  $expressName = "汇通快运";
						  break;
						case "zj001yixun":
						  $expressName = "易迅快递";
						  break;
						default:
						  $expressName = "未知物流公司，ID：".$item["delivery_company"];
						  break;
					}
					$title .= "\n物流：".$expressName." ".$item["delivery_id"];
					if(preg_match("/^\d{3}[A-Za-z]{2,10}$/",$item["delivery_company"])){
						$companyEn = trim(substr($item["delivery_company"],3,strlen($item["delivery_company"])));
						$url = "http://m.kuaidi100.com/result.jsp?com=".strtolower($companyEn)."&nu=".$item["delivery_id"];
					}
				}
				$data[] = array("title"=>urlencode($title), "description"=>"", "picurl"=>"", "url" =>$url);
				$content .= '
					<tr>
						<td>'.str_replace("\n", "<br>", $title).'</td>
					</tr>
				';
			}
		}
		
		if (count($data) == 1){
			return '
				<tr>
					<td>没有查询到你的订单记录！</td>
				</tr>
			';
		}else{
			return $content;
		}
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="format-detection" content="telephone=no">
		<title>微信订单查询</title>
        <script type="text/javascript">
            document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
                WeixinJSBridge.call('hideOptionMenu');
            });
        </script>
		<link href="css/fangbei.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<div class="cardexplain">
			<ul class="round">
				<li class="title">
					<span class="none smallspan"><font size="3px">我的订单</font></span>
				</li>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="cpbiaoge">
					<tbody>
						<?php echo getOrderItem($openid);?>
                       
					</tbody>
				</table>
			</ul>
		</div>	
	</body>
</html>