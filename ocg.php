<?php
header("content-type:text/html; charset=utf-8");

/**
   Plugin Name: 游戏王卡片查询器for微信
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
define("DEFAULT_COVER", "");

// 设置欢迎文案
define("WELCOME" , "It's time to duel!");

// 查询来源网页 ourocg.cn（不需要修改）
define("OUROCG","http://www.ourocg.cn/m/card-");
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
   if( $keyword == 'hi' )
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
<Content><![CDATA[没有搜索到相关卡片，请尝试其他关键词。]]></Content>
</xml> 
<?php
 }  }              
                }else{
                  echo "请输入要查询的游戏王卡片关键词。";
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



function ws_get_article( $keyword ){
		
		$i = 0;
		$sfile = fopen(SEARCHPAGE.$keyword,"r"); //打开查询网页
		
		while(!FEOF($sfile)){ //读出网页源码
		
			$l = trim(fgets($sfile));//读出一行源码		
			
			if ( strpos($l,"main-content carddetails")!==false  ){ //搜到准确结果
			
				$getCard = get_accurate_result( $keyword );

				return $getCard;

				exit;		
			
			}	
		
			elseif ( strpos($l,"info fn-left")!==false  ){ //搜到模糊结果，返回前10个
							
				if($i < 10){ //微信限制一次最多返回10篇文章

					// 用正则表达式填写进需要的过程，获得各种需要内容

					
					$out = preg_replace('/<div class.*height=120 alt="/','',$l);	
					$out = preg_replace('/"\/><\/figure>/','',$out);
			 		$result['title'] = "【".$out."】";// 卡片名称
			 		
					$out = preg_replace('/<div class.*<\/a><\/h1>/','',$l);
					$out = preg_replace('/<\/span><\/div>.*<\/figure>/','',$out);	
			 		$out = trim(strip_tags($out));// 卡片属性
			 		$result['title'] = $result['title']." - ".$out;


					$out = preg_replace('/<div class.*<p class="effect">/','',$l);
					$out = preg_replace('/<\/p>.*<\/figure>/','',$out);	
			 		$result['content'] = mb_strimwidth($out,0,200,'...','UTF-8');// 卡片效果		

					$out = preg_replace('/<div class.*h1.*Cards\/View-/','',$l);	
					$out = preg_replace('/">.*<\/figure>/','',$out);
			 		$result['url'] = OUROCG.$out.".html";// 卡片地址
			 		
					$out = preg_replace('/<div class.*src="/','',$l);	
					$out = preg_replace('/" height.*<\/figure>/','',$out);
			 		$result['pic'] = $out;// 卡片图片			 		
									 		
  		  			$results[] = $result;
  		  			$i++;
				}				
				
			}
		
		}
		
		if( count( $results ) > 0 ) return $results ; 
  		else return false;

}





function get_accurate_result( $keyword ){

//搜到精确结果时，直接呈现结果

		$i = 0;
		$sfile = fopen(SEARCHPAGE.$keyword,"r"); //打开查询网页
		
		while(!FEOF($sfile)){ //读出网页源码
		
			$l = trim(fgets($sfile));//读出一行源码
					
			if ( strpos($l,"<li>中文名：")!==false  ){
			
					$out = preg_replace('/<li>中文名：/','',$l);	
					$out = preg_replace('/<\/li>/','',$out);
			 		$result['title'] = "【".$out."】";// 卡片名称
			}
			
			elseif ( strpos($l,"<li>卡片种类：")!==false  ){

					$out = preg_replace('/<li>卡片种类：/','',$l);	
					$out = preg_replace('/<\/li>/','',$out);
			 		$cardCate = $out;// 卡片种类
			}		
			
			elseif ( strpos($l,"<li>星级：")!==false  ){
					
					$out = preg_replace('/<li>星级：/','',$l);	
					$out = preg_replace('/<\/li>/','',$out);
			 		$cardLevel = " ".$out."星 ";// 卡片星级			
			} 	
				
			elseif ( strpos($l,"<li>阶级：")!==false  ){

					$out = preg_replace('/<li>阶级：/','',$l);	
					$out = preg_replace('/<\/li>/','',$out);
			 		$cardXyz = " ".$out."阶 ";// 卡片阶级
			} 		
			
			elseif ( strpos($l,"<li>攻击：")!==false  ){

					$out = preg_replace('/<li>攻击：/','',$l);	
					$out = preg_replace('/<\/li>/','',$out);
			 		$cardATK = "/ ATK：".$out." ";// ATK
			} 		
			
			elseif ( strpos($l,"<li>防御：")!==false  ){

					$out = preg_replace('/<li>防御：/','',$l);	
					$out = preg_replace('/<\/li>/','',$out);
			 		$cardDEF = "/ DEF：".$out." ";// DEF
			} 		
			
			elseif ( strpos($l,"<li>种族：")!==false  ){

					$out = preg_replace('/<li>种族：/','',$l);	
					$out = preg_replace('/<\/li>/','',$out);
			 		$cardRace = "/ 种族：".$out." ";// 种族
			} 		
			
			elseif ( strpos($l,"<li>属性：")!==false  ){

					$out = preg_replace('/<li>属性：/','',$l);	
					$out = preg_replace('/<\/li>/','',$out);
			 		$cardNature = "/ 属性：".$out;// 属性
			} 		
			
			elseif ( strpos($l,"<li>效果：")!==false  ){

					$out = preg_replace('/<li>效果：/','',$l);	
					$out = preg_replace('/<\/li>/','',$out);
			 		$result['content'] = mb_strimwidth($out,0,200,'...','UTF-8');// 卡片效果
			}
			
			elseif ( strpos($l,'width="160"')!==false  ){

					$out = preg_replace('/.*src="/','',$l);	
					$out = preg_replace('/".*width=.*<\/li>/','',$out);
					
					echo $out;
					
			 		$result['pic'] = $out;// 卡片图片
			}
			
			elseif ( strpos($l,'link rel="canonical')!==false  ){
			
					$out = preg_replace('/.*<link rel="canonical" href=".*View-/','',$l);	
					$out = preg_replace('/">/','',$out);
			 		$result['url'] = OUROCG.$out.".html";// 卡片地址
			}
		
		}


					$result['title'] = $result['title']." - ".$cardCate.$cardLevel.$cardXyz.$cardATK.$cardDEF.$cardRace.$cardNature;
			
				 		
  		  			$results[] = $result;

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