<?php
$openid = $_GET["openid"];
$cardid = fromOpenID2CardID($openid);

function fromOpenID2CardID($openid)
{
    $mysql_host = SAE_MYSQL_HOST_M;
    $mysql_host_s = SAE_MYSQL_HOST_S;
    $mysql_port = SAE_MYSQL_PORT;
    $mysql_user = SAE_MYSQL_USER;
    $mysql_password = SAE_MYSQL_PASS;
    $mysql_database = SAE_MYSQL_DB;

	$mysql_table = "member";
    $mysql_state = "SELECT * FROM ".$mysql_table." WHERE `openid` = '".$openid."'";
    
	$con = mysql_connect($mysql_host.':'.$mysql_port, $mysql_user, $mysql_password, true);
	if (!$con){
		die('Could not connect: ' . mysql_error());
	}
	mysql_query("SET NAMES 'UTF8'");
	mysql_select_db($mysql_database, $con);
	$result = mysql_query($mysql_state);
    $cardID = "";
    while($row = mysql_fetch_array($result))
    {
        $cardID = $row['cardid']; 
        break;
    }
	mysql_close($con);
	return $cardID;
}
?>
<!DOCTYPE html>
<html lang="zh-CN" manifest="/misc/wei_cache.manifest">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="Generator" content="Fortune v1.0.0">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;">
    <meta name="format-detection" content="telephone=no">
    <title>番茄田</title>
    <link href="./css/style.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="./js/_meishi_wei_html5_v3.2.9.js"></script>
    <style>
      abbr, article, aside, audio, canvas, datalist, details, dialog, eventsource, figure, figcaption, footer, header, hgroup, mark, menu, meter, nav, output, progress, section, small, time, video {
        display:block;
      }
    </style>
  </head>
  <body id="" class="">
    <div id="loading" style="display: none; ">
      <div class="bk"></div>
      <div class="cont">
      <img src="./img/loading.gif" alt="loading...">正在加载...</div>
    </div>
    
    <div id="mappContainer">
      <section id="card_ctn">
        <div class="bk1"></div>
        <div class="cont">
          <div class="card">
            <div class="front">
              <figure class="fg" style="background-image:url(./img/4f.jpg);">
                <img src="./companylogo.png">
                <figcaption class="fc">
                  <span class="cname" style="color:#957426;">微信会员卡</span>
                  <span class="t" style="color:#aaa;text-shadow:#000 0 -1px;"></span>
                  <span class="n" style="color: rgb(170, 170, 170); text-shadow: rgb(0, 0, 0) 0px -1px; ">NO.<?php echo $cardid;?></span>
                </figcaption>
              </figure>
            </div>
            <div class="back">
              <figure class="fg" style="background-image:url(./img/4b.jpg);">
                <div class="info">
                  <p class="addr">广东省深圳市南山区滨海大道深圳湾体育中心</p>
                  <p class="tel"><a class="autotel" href="tel:075586280000">0755-86280000</a></p>
                </div>
                <p class="keywords">番茄田</p>
              </figure>
            </div>
          </div>
        </div>
      </section>

      <div id="vip" style=""> 
        <small><em>尊贵会员, 尽享如下特权:</em></small>
        <ul class="round">
          <li data-ajax-params="" data-ajax-act="" class="only"><a href=""><b>9折优惠券</b></a></li>
        </ul>
      </div>
    </div>
  </body>
</html>