<?php
function getExpressInfo($keyword)
{
    $expresses = array(
        "AAE"=>"aae","安捷"=>"anjie","安信达"=>"anxinda","ARAMEX"=>"aramex","巴伦支"=>"balunzhi","宝通达"=>"baotongda","成都奔腾"=>"benteng","CCES"=>"cces","长通"=>"changtong","程光"=>"chengguang","城际"=>"chengji","城市100"=>"chengshi100","传喜"=>"chuanxi","传志"=>"chuanzhi","出口易"=>"chukouyi","CITYLINK"=>"citylink","东方"=>"coe","城市之星"=>"cszx","大田"=>"datian","大洋"=>"dayang","德邦"=>"debang","德创"=>"dechuang","DHL"=>"dhl","店通"=>"diantong","递达"=>"dida","叮咚"=>"dingdong","递四方"=>"disifang","DPEX"=>"dpex","D速"=>"dsu","百福东方"=>"ees","EMS"=>"ems","凡宇"=>"fanyu","FARDAR"=>"fardar","FEDEX"=>"fedex","FEDEX国内"=>"fedexcn","飞邦"=>"feibang","飞豹"=>"feibao","原飞航"=>"feihang","飞狐"=>"feihu","飞特"=>"feite","飞远"=>"feiyuan","丰达"=>"fengda","飞康达"=>"fkd","广东邮政"=>"gdyz","国内小包"=>"gnxb","共速达"=>"gongsuda","国通"=>"guotong","山东海红"=>"haihong","海盟"=>"haimeng","昊盛"=>"haosheng","河北建华"=>"hebeijianhua","恒路"=>"henglu","华诚"=>"huacheng","华翰"=>"huahan","华企"=>"huaqi","华夏龙"=>"huaxialong","天地华宇"=>"huayu","汇强"=>"huiqiang","汇通"=>"huitong","海外环球"=>"hwhq","佳吉快运"=>"jiaji","佳怡"=>"jiayi","加运美"=>"jiayunmei","金大"=>"jinda","京广"=>"jingguang","晋越"=>"jinyue","急先达"=>"jixianda","嘉里大通"=>"jldt","康力"=>"kangli","顺鑫"=>"kcs","快捷"=>"kuaijie","宽容"=>"kuanrong","跨越"=>"kuayue","乐捷递"=>"lejiedi","联昊通"=>"lianhaotong","立即送"=>"lijisong","龙邦"=>"longbang","民邦"=>"minbang","明亮"=>"mingliang","闽盛"=>"minsheng","尼尔"=>"nell","港中能达"=>"nengda","OCS"=>"ocs","平安达"=>"pinganda","邮政"=>"pingyou","品速心达"=>"pinsu","全晨"=>"quanchen","全峰"=>"quanfeng","全际通"=>"quanjitong","全日通"=>"quanritong","全一"=>"quanyi","保时达"=>"rpx","如风达"=>"rufeng","赛澳递"=>"saiaodi","三态"=>"santai","伟邦"=>"scs","圣安"=>"shengan","盛丰"=>"shengfeng","盛辉"=>"shenghui","申通"=>"shentong","顺丰"=>"shunfeng","穗佳"=>"suijia","速尔"=>"sure","天天"=>"tiantian","TNT"=>"tnt","通成"=>"tongcheng","通和天下"=>"tonghe","UPS"=>"ups","USPS"=>"usps","万博"=>"wanbo","万家"=>"wanjia","微特派"=>"weitepai","祥龙运通"=>"xianglong","新邦"=>"xinbang","信丰"=>"xinfeng","希优特"=>"xiyoute","源安达"=>"yad","亚风"=>"yafeng","一邦"=>"yibang","银捷"=>"yinjie","音素"=>"yinsu","亿顺航"=>"yishunhang","优速"=>"yousu","一统飞鸿"=>"ytfh","远成"=>"yuancheng","圆通"=>"yuantong","元智捷诚"=>"yuanzhi","越丰"=>"yuefeng","誉美捷"=>"yumeijie","韵达"=>"yunda","运通中港"=>"yuntong","宇鑫"=>"yuxin","源伟丰"=>"ywfex","宅急送"=>"zhaijisong","郑州建华"=>"zhengzhoujianhua","芝麻开门"=>"zhima","中天万运"=>"zhongtian","中通"=>"zhongtong","忠信达"=>"zhongxinda","中邮"=>"zhongyou"
    );
    $contentStr = "";
    for($len = 3; $len <= 12; $len++)
    {
        if (array_key_exists(substr($keyword, 0, $len), $expresses) || array_key_exists(strtoupper(substr($keyword, 0, $len)), $expresses)){
            if (preg_match("/[a-zA-Z]/",substr($keyword, 0, $len))){
                $companyEn = $expresses[strtoupper(substr($keyword, 0, $len))];
            }else{
                $companyEn = $expresses[substr($keyword, 0, $len)];
            }
            $number = trim(substr($keyword, $len, strlen($keyword)));
            if (!preg_match("/^\w{8,14}$/",$number)){
                return "请发送'快递名称'+'单号'，不要加快递两个字。例如“汇通210157777433”";
            }
            $contentStr = getIckdExpressInfo($companyEn, $number);
            return $contentStr;
        }
    }
    if ($contentStr == ""){
        $contentStr = "没有匹配到快递公司！";
        return $contentStr;
    }
}

function getIckdExpressInfo($companyEn, $number)
{
    $appid = "103223";
    $secret = "sssssssssssssss";
    $url= "http://api.ickd.cn/?id=".$appid."&secret=".$secret."&com=".$companyEn."&nu=".$number."&type=json&encode=utf8";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($ch);

	if(curl_errno($ch))
	{ echo 'CURL ERROR Code: '.curl_errno($ch).', reason: '.curl_error($ch);}
	curl_close($ch);
	
	$AllInfo = json_decode($output, true);

    if($AllInfo['message'] != "" ){
		return $AllInfo['message'];
	}else{
		$result = "";
		foreach ($AllInfo["data"] as $singleStep)
		{
			$result .= $singleStep["time"]." ".$singleStep["context"]."\n";
		}
		return trim($result);
    }
}

?>