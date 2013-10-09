<?php
require_once("strings.php");
?>


<?php

	//$content = file_get_contents("http://www.ourocg.cn/m/card-".$_GET["cardid"].".html");
	$content = file_get_contents(OUROCG_MOBILE_PAGE.$_GET["cardid"].".html");
	
		//抓取卡片名称
		$arr = explode('<strong style="font-size:18px;color:blue">',$content);
		$arr = explode('</strong><br/>',$arr[1]);
		$cardName = trim($arr[0]);
		
		//抓取卡片图片
		//$arr = explode('<img src="',$content);
		//$arr = explode('" alt="',$arr[1]);
		//$cardPic = trim($arr[0]);		
		//$cardPicIP = str_replace("http://p.ocgsoft.cn/","http://122.0.65.71:9310/",$cardPic);
		$cardPic = OWN_PIC_BED.$_GET["cardid"].".jpg";

		//抓取卡片资料
		$arr = explode('<br/><br/><br/>',$content);
		$arr = explode('<br/><br/><br /></div>',$arr[1]);
		$cardDes = trim($arr[0]);	

?>


<html>
<head>
<meta charset="utf-8">
<title>「<?php echo $cardName ?>」游戏王卡片</title>
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1, user-scalable=yes">
<link href="style.css" rel="stylesheet">
<link href="/img/favicon.png" rel="Shortcut Icon">
</head>
<body>

<div align="center"><img src="<?php echo $cardPic ?>" /></div>
<br/>
<?php echo $cardDes ?>

<a href="http://www.51.la/?16351683" target="_blank"><img alt="&#x6211;&#x8981;&#x5566;&#x514D;&#x8D39;&#x7EDF;&#x8BA1;" src="http://img.users.51.la/16351683.asp" style="border:none" width="0" height="0"/></a>

</body>
</html>





