<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="format-detection" content="telephone=no">
		<meta name="description" content="">
		<title>方倍工作室</title>
		<link href="css/activity-style.css" rel="stylesheet" type="text/css">
		<script type="text/javascript">
			document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
				WeixinJSBridge.call('hideOptionMenu');
			});
			document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
				WeixinJSBridge.call('hideToolbar');
			});
		</script>
	</head>

	<body data-role="page" class="activity-scratch-card-winning">
		<script src="js/jquery.js" type="text/javascript"></script>
		<script src="js/wScratchPad.js" type="text/javascript"></script>

		<div class="main">
			<div class="cover">
				<img src="img/activity-scratch-card-bannerbg.png">
				<div id="prize"> </div>
				<div id="scratchpad">
					<div style="position: absolute; width: 150px; height: 40px; cursor: default;">
						<canvas width="150" height="40" style="cursor: default;"></canvas>
					</div>
				</div>
			</div>
			<div class="content">

				<div id="winprize" style="display:none" class="boxcontent boxwhite">
					<div class="box">
						<div class="title-red"><span>恭喜你中奖了</span></div>
						<div class="Detail">
							<p>您中了 <span class="red" id="prizelevel"></span></p>
							<p>奖品为 <span class="red" id="prizename"></span></p>
						</div>
					</div>
				</div>

				<div class="boxcontent boxwhite">
					<div class="box">
						<div class="title-brown">活动说明：
						</div>
						<div class="Detail">
                            <p>活动时间：即日起至2016年12月31日</p>
                            <p>奖品设置：<font color="red"><strong>iPhone 6</strong></font> 手机 1000 部</p>
                            <p>参与方式</p>
                            <p> 第一步：关注微信公众账号 方倍工作室</p>
                            <p> 第二步：回复<strong> 刮刮乐 </strong>三个汉字</p>
                            <p> 第三步：点击图文消息进入刮奖页面</p>
                            <p> 第四步：刮掉涂层查看中奖结果</p>
                           
						</div>
					</div>
				</div>

			</div>
			<div style="clear:both;"></div>
		</div>
		<div style="height:60px;"></div>
		<script src="js/alert.js" type="text/javascript"></script>

		<script type="text/javascript">
			var display = false;
			var num = 0;
			var win = false;
			$(function(){
				$("#scratchpad").wScratchPad({
					width : 150,
					height : 40,
					color : "#a9a9a7",
					scratchMove : function(e, percent){
						num++;
						//80%时自动清除
						if(percent > 80){
							this.clear();
						}
						//开始时请求中奖结果
						if (num == 1) {
							if (Math.floor(Math.random() * (100 + 1)) <  50){
								//需要通过ajax请求判断openid以前是否中过奖
								$getPrizeBefor = false;
								if (!$getPrizeBefor){
									win = true;
								}
							}
						}

						//移动至少10次后，显示结果
						if (num > 10){
							//只显示一次
							if (!display){
								//根据概率显示
								if (win){
									document.getElementById('prize').innerHTML = "一等奖";
									$("#prizelevel").text("一等奖");
									$("#prizename").text('iPhone 6');
									$("#winprize").slideToggle(500);
								}else {
									document.getElementById('prize').innerHTML="谢谢参与";
								}
							}
							display = true;
						}
					}
				});
			});
		</script>
	</body></html>