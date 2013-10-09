<?php

header("content-type:text/html; charset=utf-8");

/**********************************************************

   Plugin Name: 游戏王微信卡片查询器
   Plugin URI: http://www.fanbing.net
   Description: 在微信中查询游戏王卡片，技术支持来自ourocg.cn
   Version: 1.5
   Author: XDash
   Email: fanbingx@gmail.com
   Author URI: http://www.fanbing.net
   License: BSD
   Lastupdate:2013.10.09
   
***********************************************************/



// ------ Settings & Includes ----------


require_once("settings.php");
require_once("strings.php");
require_once("functions.php");
require_once("duelterminal.php");



// ------ BODY ----------


$wechatObj = new wechatCallbackapiTest(); // 微信消息接口实例，用于返回各类消息

if( isset($_REQUEST['echostr']) )

	$wechatObj->valid();

elseif( isset( $_REQUEST['signature'] ) ){

    $wechatObj->responseMsg();
    
}



class wechatCallbackapiTest{

// ********   微信消息主要交互流程用类   ********** //

	public $fromUsername;
	public $toUsername;
	public $msgType;
	public $keyword;
	public $event;
	

	public function responseMsg(){
	/*** 微信主要消息类 */
	
		//$dt = new DuelTerminal(); // DuelTerminal的实例，用于发送各种游戏王卡片查询请求
		
		$this->fetchData(); // 读取用户发送的消息，获取toUser、keyword等信息
		
		switch ($this->msgType){ // 根据不同的消息类型，发送回复响应
		
			case "event": // 用户事件
			
				$this->onEvent(); 
			
			case "text": // 文本
				
				$this->onText(); 
				
			case "location": // 位置
				
				exit;
				
			case "image": // 图片
			
				exit;	
							
			default:
			
				exit;
		
		}
	}



	private function onText(){
	/*** 处理用户发送的文本 */
	
	
		if(!empty( $this->keyword )){ // 关键词
	
		$dt = new DuelTerminal(); // DuelTerminal的实例，用于发送各种游戏王卡片查询请求		
        
        	switch (strtolower($this->keyword)){  // 判断是否输入了特殊关键词 or 普通搜索
        	
				case "hi": 		// 欢迎信息
						
					echo $this->respondPlainText(WELCOME);
								
				case "help": 	// 全部指令列表
						
					echo $this->respondPlainText(ALLCOMMANDS);
						
				case "roll": 	// 抛骰子

					echo $this->respondPlainText(rand(1,6));
   					
				case "coin":	// 抛硬币
	
					echo $this->respondPlainText(rand(1,2)==1?"正面":"反面");
   					
				case "jz":		// 禁止卡表
				
					echo $this->respondPlainText(FORBIDDEN_CARDS);

				case "xz":		// 限制卡表
					
					echo $this->respondPlainText(LIMIT_CARDS);
   				   			
				case "zxz":		// 准限制卡表
				
					echo $this->respondPlainText(QUASI_LIMIT_CARDS);					

				case "wxz":		// 无限制卡表
				
					echo $this->respondPlainText(NO_LIMIT_CARDS);

				case "yf":		// 高级搜索语法
				
					echo $this->respondPlainText(ADVANCED_COMMANDS);
					
				case "nw":		// 任天堂世界最新帖子
				               		
					$test = $dt->getNwbbsTopics();
					$this->respondMultipleNews($test);					

				case "tb":		// 百度游戏王吧最新帖子
				               		
					$test = $dt->getTiebaTopics();
					$this->respondMultipleNews($test);	
               		
				case "r":		// 随机一张卡（图文版）

					ob_start(); 			
				
					$randomCard = $dt->get_random_result();
					$this->respondSingleNews($randomCard);

				case "rr":		// 随机一张中文卡（纯文本）
				
					$randomCard = rand(1,TOTAL_CARD_COUNT);
					echo $this->respondPlainText("【".$dt->getCardNameByID($randomCard)."】".$dt->getEffectByID($randomCard)."\n\n".OUROCG_MOBILE_PAGE.$randomCard.".html");

				case "rrr":		// 随机一张日文卡（纯文本）
				
					$randomCard = rand(1,TOTAL_CARD_COUNT);
					echo $this->respondPlainText("【".$dt->getCardJapaneseNameByID($randomCard)."】".$dt->getJapaneseEffectByID($randomCard)."\n\n".OUROCG_MOBILE_PAGE.$randomCard.".html");		
								 				
				
				default: // 不属于任何一种关键词，按搜索卡片处理
           																
                	if( $searchCard = $dt->getCardByAPI($this -> keyword )){ // 搜到
                              
               			ob_start(); 
						$this->respondMultipleNews($searchCard);
						
					}else{ // 没搜索到

  						echo $this->respondPlainText(NORESULT);
  						
  					}

  				} // END SIGN for Switch
  				
						     
        } // END SIGN OF if (!empty($keyword))
	
	
	}




	private function onEvent(){
	/*** 响应特殊用户事件 */	
	
		if(!empty( $this->event )){ // 事件
   			
			switch ($this->event){
   			
			case "subscribe": // 关注事件
   				
				echo $this->respondPlainText(WELCOME);
   			
			case "unsubscribe":// 取消关注事件
   				
				echo $this->respondPlainText(SAYBYE);
   				
   			}
		}
	}



	private function respondPlainText($text){
	/*** 微信回复纯文本信息 */
	
		$returnText = "<xml><ToUserName><![CDATA[".$this->fromUsername."]]></ToUserName><FromUserName><![CDATA[".$this->toUsername."]]></FromUserName><CreateTime><?=time()?></CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[".$text."]]></Content></xml>";
		return $returnText;
	}



	private function respondSingleNews($news){
	/*** 微信回复单条图文信息 */

	?>								
		<xml>
		<ToUserName><![CDATA[<?=$this->fromUsername?>]]></ToUserName>
		<FromUserName><![CDATA[<?=$this->toUsername?>]]></FromUserName>
		<CreateTime><?=time()?></CreateTime>
		<MsgType><![CDATA[news]]></MsgType>
		<ArticleCount>1</ArticleCount>
		<Articles><?php foreach( $news as $item ): ?>
		<item> 
			<Title><![CDATA[<?=$item['title']?>]]></Title>
			<Description><![CDATA[<?=$item['content']?>]]></Description>
			<PicUrl><![CDATA[<?=$item['pic']?>]]></PicUrl>							
			<Url><![CDATA[<?=$item['url']?>]]></Url>
		</item>
		<?php endforeach; ?></Articles>
		<FuncFlag>0</FuncFlag>
		</xml>		
	<?php		
		$xml = ob_get_contents();
		//file_put_contents('xml.txt', $xml);
		header('Content-Type: text/xml');
		echo trim($xml); 
	}




	private function respondMultipleNews($news){
	/*** 微信回复多条图文信息 */

	?>				
		<xml>
		<ToUserName><![CDATA[<?=$this->fromUsername?>]]></ToUserName>
		<FromUserName><![CDATA[<?=$this->toUsername?>]]></FromUserName>
		<CreateTime><?=time()?></CreateTime>
		<MsgType><![CDATA[news]]></MsgType>
		<ArticleCount><?=count($news)?></ArticleCount>
		<Articles><?php foreach( $news as $item ): ?>
		<item> 
			<Title><![CDATA[<?=$item['title']?>]]></Title>
			<Description><![CDATA[<?=$item['content']?>]]></Description>
			<PicUrl><![CDATA[<?=$item['pic']?>]]></PicUrl>							
			<Url><![CDATA[<?=$item['url']?>]]></Url>
		</item>
		<?php endforeach; ?></Articles>
		<FuncFlag>0</FuncFlag>
		</xml>			
	<?php			
		$xml = ob_get_contents();
		//file_put_contents('xml.txt', $xml);
		header('Content-Type: text/xml');

		echo trim($xml); 

	}



	private function fetchData(){
	/*** 解析用户的消息请求 */	
	
	    $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
	    if (!empty($postStr)){
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$this->fromUsername = $postObj->FromUserName;
			$this->toUsername = $postObj->ToUserName;		
			$this->keyword = trim($postObj->Content);
			$this->msgType = trim($postObj->MsgType);			
			$this->event = trim($postObj->Event);
			$time = time();
		}
	}




	public function valid(){
	/*** 如果消息的用户签名正确，则返回消息 */
	
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
          echo $echoStr;
          exit;
        }
	}
	
	
	
	private function checkSignature(){
	/*** 检查消息的用户签名 */

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



} // End Sign of class wechatCallbackapiTest