<?php

require_once('weixin.class.php');
$feedbackurl = "https://mp.weixin.qq.com/payfb/payfeedbackindex?appid=".APPID."#wechat_webview_type=1&wechat_redirect";
Header("Location: $feedbackurl");

?>