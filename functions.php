<?php
/**
 * 各种公用模块和函数
 * by @XDash http://www.fanbing.net
 */



function request_by_curl($remote_server,$post_string){
/*** 发送POST请求，返回数据 ***/
/*** $post_string = "app=request&version=beta"; ***/
/*** request_by_curl('http://facebook.cn/restServer.php',$post_string); ***/

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $remote_server);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$post_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Wechat Request');
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;

}



function get_redirect_mobile($cardID){
/*** 根据 ourocg.cn 原移动页面中心定向到自己适配的页面 ***//
/*** 自己适配的页面地址为 redirect_mobile.php?cardid=xxx ***//
/*** 其中 cardid 就是原本卡片的ID 号 ***//

	return $_SERVER['HTTP_HOST'].OUROCG_REDIRCT_MOBILE.$cardID;

}





?>