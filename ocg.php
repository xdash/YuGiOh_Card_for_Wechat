<?php
header("content-type:text/html; charset=utf-8");

/**
   Plugin Name: 游戏王卡片查询器for微信公众平台
   Plugin URI: http://www.fanbing.net
   Description: 在微信中查询游戏王卡片，技术支持来自ourocg.cn
   Version: 1.0
   Author: fanbingx@gmail.com
   Author URI: http://www.fanbing.net
   License: BSD
**/
 


// ------ Settings ----------

// 设置token
// 必须和微信公众平台中的设置保持一致；设置页面 http://mp.weixin.qq.com/cgi-bin/callbackprofile?t=wxm-callbackapi&type=info&lang=zh_CN
define("TOKEN", "ocgcardfdasfjlsdjou88fa");

// 设置默认图片 
define("DEFAULT_COVER", "/wp-content/uploads/2012/12/search_cover.png");

// 设置欢迎文案
define("WELCOME" , "Hello there!");

// 查询来源网页 ourocg.cn（不需要修改）
define("OUROCG","http://www.ourocg.cn");
define("SEARCHPAGE","http://www.ourocg.cn/S.aspx?key=");

// ------ BODY ----------

$wechatObj = new wechatCallbackapiTest();



if( isset($_REQUEST['echostr']) )
  $wechatObj->valid();
elseif( isset( $_REQUEST['signature'] ) )
{
    $wechatObj->responseMsg();
}
  


class wechatCallbackapiTest
{
  public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
          echo $echoStr;
          exit;
        }
    }

    public function responseMsg()
    {
    //get post data, May be due to the different environments
    $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
    if (!empty($postStr)){
                
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                       
        if(!empty( $keyword ))
                {
                  //file_put_contents( 'keyword.txt' , $keyword );
                  
                  if($articles = ws_get_article( $keyword  ))
{
                   ob_start(); 
                  ?><xml>
<ToUserName><![CDATA[<?=$fromUsername?>]]></ToUserName>
<FromUserName><![CDATA[<?=$toUsername?>]]></FromUserName>
<CreateTime><?=$time?></CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<Content><![CDATA[搜索结果]]></Content>
<ArticleCount><?=count($articles)?></ArticleCount>
<Articles><?php foreach( $articles as $item ): ?>
<item> 
  <Title><![CDATA[<?=$item['title']?>]]></Title>
  <Description><![CDATA[<?=$item['content']?>]]></Description>
  <PicUrl><![CDATA[<?=$item['pic']?>]]></PicUrl>
  <Url><![CDATA[<?=$item['url']?>]]></Url>
</item>
<?php endforeach; ?></Articles>
<FuncFlag>0</FuncFlag>
</xml><?php
$xml = ob_get_contents();
//file_put_contents('xml.txt', $xml);
header('Content-Type: text/xml');
echo trim($xml); 

 }else
 {
   if( $keyword == 'Hello2BizUser' )
 {?>
<xml>
<ToUserName><![CDATA[<?=$fromUsername?>]]></ToUserName>
<FromUserName><![CDATA[<?=$toUsername?>]]></FromUserName>
<CreateTime><?=time()?></CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[<?=WELCOME?>]]></Content>
</xml> 
<?php }
else{
?>
<xml>
<ToUserName><![CDATA[<?=$fromUsername?>]]></ToUserName>
<FromUserName><![CDATA[<?=$toUsername?>]]></FromUserName>
<CreateTime><?=time()?></CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[没有找到包含关键字的文章，试试其他关键字？]]></Content>
</xml> 
<?php
 }  }              
                }else{
                  echo "请输入关键字，我们将返回对应的文章...";
                }

        }else {
          echo "";
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
}



function ws_get_article( $keyword , $limit = 10 ){
		
		$i = 0;
		$sfile = fopen(SEARCHPAGE.$keyword,"r"); //打开查询网页
		
		while(!FEOF($sfile)){ //读出网页源码
		
			$l = trim(fgets($sfile));//读出一行源码
		
			if ( strpos($l,"info fn-left")!==false  ){ //搜到一条结果
							
				if($i < 10){ //微信限制一次最多返回10篇文章

					// 用正则表达式填写进需要的过程，获得各种需要内容
					//-------  EDIT HERE ---------

					$regTitle = '/href="(.+?)"/is';//卡片名称
			 		//preg_match( $regTitle , $l , $out);
			 		//$result['title'] = $out[0];
					$result['title'] = "测试标题A";
					
					$regContent = '/href="(.+?)"/is';//卡片效果
			 		//preg_match( $regContent , $l , $out );
			 		//$result['content'] = mb_strimwidth($out[0],0,200,'...','UTF-8');
					$result['content'] = mb_strimwidth("测试说明B",0,200,'...','UTF-8');
					
					$regURL = '/href="(.+?)"/is';//URL
			 		//preg_match( $regURL , $l , $out );
			 		//$result['url'] = OUROCG.$out[0];
					$result['url'] = "http://www.ourocg.cn/Cards/View-5598";
			 		
					$regPic = '/http*.jpg/';//图片
			 		//preg_match( $regPic , $l , $out );
			 		//$result['pic'] = OUROCG.$out[0];
			 		$result['pic'] = "http://p.ocgsoft.cn/5342.jpg";
			 		
  		  			$results[] = $result;
  		  			$i++;
				}
				
				
			}
		
		
		}
		
		if( count( $results ) > 0 ) return $results ; 
  		else return false;

}



function thumbnail_url( $html )
{
  $reg = '/src="(.+?)"/is';
  if(preg_match( $reg , $html , $out ))
  {
    return $out[1];
  }

  return false;
}