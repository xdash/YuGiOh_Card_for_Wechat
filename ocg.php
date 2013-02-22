<?php
header("content-type:text/html; charset=utf-8");

/**
   Plugin Name: 游戏王卡片查询器for微信
   Plugin URI: http://www.fanbing.net
   Description: 在微信中查询游戏王卡片，技术支持来自ourocg.cn
   Version: 1.3
   Author: XDash
   Email: fanbingx@gmail.com
   Author URI: http://www.fanbing.net
   License: BSD
   Lastupdate:2013.02.22
**/
 


// ------ Settings ----------

// 设置token
// 必须和微信公众平台中的设置保持一致；设置页面 http://mp.weixin.qq.com/cgi-bin/callbackprofile?t=wxm-callbackapi&type=info&lang=zh_CN
define("TOKEN", "ocgcardfdasfjlsdjou88fadt");

// 设置默认图片 
define("DEFAULT_COVER", "");

// 设置欢迎文案
define("WELCOME" , "It's time to duel!");

// 查询来源网页 ourocg.cn（不需要修改）
define("OUROCG","http://www.ourocg.cn/m/card-");
define("SEARCHPAGE","http://www.ourocg.cn/S.aspx?key=");


$Commands=<<<EOF
全部指令列表——

输入关键词（如“人渣”）搜索卡片；
【t 关键词】 - 搜索结果无图模式；
【yf】 - 高级搜索语法指令；
【roll】 - 抛骰子；
【coin】 - 抛硬币；
【jz】 - 禁止卡表；
【xz】 - 限制卡表；
【zxz】 - 准限制卡表；
【wxz】 - 无限制卡表；
【help】 - 本指令列表；

更多指令陆续追加中…
EOF;




$LatestCardList_Forbidden=<<<EOF
【20130301 禁止卡】
交换蛙
胜利龙
混沌帝龙 -终焉的使者-
杀人蛇
三眼怪 前限制
成长的鳞茎
黑森林的魔女
御用守护者
混沌之黑魔术师
电子壶
千眼纳祭神
处刑人-摩休罗
神圣魔术师
发条空母 发条巨舰 前限制
暗黑俯冲轰炸机
命运英雄 圆盘人
恶魔 弗兰肯
同族感染病毒
冰结界之龙 三叉龙
冰结界之龙 光枪龙
纤维壶
电子鱼人-枪手
魔导科学家
精神脑魔
八汰乌
救援猫
噩梦之蜃气楼
爱恶作剧的双子恶魔
王家的神殿
收押
苦涩的选择
强引的番兵
强夺
强欲之壶
心变
雷击
次元融合
生还的宝札
洗脑
大寒波
蝶之短剑-回音
天使的施舍
鹰身女妖的羽毛扫
过早的埋葬
飓风
质量加速器
未来融合
突然变异
遗言状
王宫的弹压
王宫的敕命
现世与冥界的逆转
死之卡组破坏病毒
第六感
滑槽
刻之封印
破坏轮
最终一战！
EOF;



$LatestCardList_Limit=<<<EOF
【20130301 限制卡】
邪遗式幽风乌贼怪
甲虫装机 豆娘
甲虫装机 大黄蜂
元素英雄 天空侠
欧尼斯特
混沌巫师
混沌战士 -开辟的使者-
剑斗兽 枪斗
真六武众-紫炎
发条魔术师 前无限制
僵尸带菌者
暗黑武装龙
蒲公英狮
科技属 突击兵
科技属 超图书馆员
深渊的暗杀者
新宇宙侠·大地鼹鼠
死灵之颜
被封印的艾克佐迪亚
被封印者的右足
被封印者的右腕
被封印者的左足
被封印者的左腕
方程式同调士
黑羽-疾风之盖尔
冥府之使者 格斯
马头鬼
变形壶
真红眼暗铁龙
孤火花
来自异次元的埋葬
一时休战 前无限制
永火炮
大风暴
愚蠢的埋葬
黑旋风
原初之种
死者苏生
替罪羊
精神操作
增援
月之书
手札抹杀
贪欲之壶
光之援军
黑洞
怪兽之门
暗之诱惑
限制解除
六武之门
一对一
来自异次元的归还
神之警告 前准限制
神之宣告
血之代偿
停战协定
转生的预言
光之护封壁
魔力爆发
EOF;



$LatestCardList_Quasi_Limit=<<<EOF
【20130301 准限制卡】
卡片炮击士
召唤僧
神秘之代行者 厄斯
大天使 克里斯提亚
月读命 前限制
命运英雄 魔性人
星骸龙
特拉戈迪亚
冰结界的虎王 雪虎
由魔界到现世的死亡导游
雷王 前无限制
轮回天狗
救援兔
E-紧急呼唤
王家的牲祭
高等仪式术 前限制
强欲而谦虚之壶
召集之圣刻印
连锁爆击
英雄到来
魔法石采掘
名推理
扰乱三人组
激流葬
神圣防护罩 -反射镜力-
奈落的落穴
EOF;



$LatestCardList_No_Limit=<<<EOF
【20130301 无限制卡】
孢子 前限制
黑羽-月影之卡鲁特 前准限制
光道召唤师 露米娜丝 前准限制
紫炎的狼烟 前准限制
心灵崩坏 前准限制
EOF;



$Advanced_Commands=<<<EOF
【高级搜索语法】

语法概要：
+() 里面的内容必须包含
-() 里面的内容必须不包含

AND(and) 两个条件同时成立
OR(or)	 两个条件只要成立一个

条件查询：(括号中用/分割的中任选一个即可，下面的范例就是从中选择一个做范例，实际上都可以使用) 

(中文名/卡名/name): 名称查询
举例： 中文名:变形

(日文名/japName): 日文名查询
举例： japName:コー

(简称/俗称/缩写/shortName):
举例： 俗称:囧

(卡种/卡片种类/cardType):卡种查询
举例： cardType:魔法

(种族/tribe):	 种族查询
举例： 种族:龙

(属性/element):
举例： 属性:暗

(卡包/package):
举例： package:(606 or 605)

(编号/序号/ID):
举例： ID:(5 or 10)

(攻/攻击力/atkValue):
举例： 攻:500

(防/防御力/defValue):
举例： 防御力:500

(星级/星数/等级/level):
举例： 等级:5

limit:
举例： limit:1

数字还可以使用区间语法
比如：
atkValue:400-500

多条件查询举例：

查询 战士族 4星 效果怪兽
+(cardType:效果怪兽) +(tribe:战士) +(level:4)
EOF;





define("LatestCardList_Forbidden",$LatestCardList_Forbidden);
define("LatestCardList_Limit",$LatestCardList_Limit);
define("LatestCardList_Quasi_Limit",$LatestCardList_Quasi_Limit);
define("LatestCardList_No_Limit",$LatestCardList_No_Limit);
define("Advanced_Commands",$Advanced_Commands);
define("Commands",$Commands);

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
    if (!empty($postStr)){//发送有效信息
                
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                
               
        if(!empty( $keyword )){//开始解析关键词

				if( $keyword == 'hi' ){
						?>
						<xml>
						<ToUserName><![CDATA[<?=$fromUsername?>]]></ToUserName>
						<FromUserName><![CDATA[<?=$toUsername?>]]></FromUserName>
						<CreateTime><?=time()?></CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[<?=WELCOME?>]]></Content>
						</xml> 
						<?php 
						exit;}
						
						
				elseif( $keyword == 'help' ){ //全部指令列表
						?>
						<xml>
						<ToUserName><![CDATA[<?=$fromUsername?>]]></ToUserName>
						<FromUserName><![CDATA[<?=$toUsername?>]]></FromUserName>
						<CreateTime><?=time()?></CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[<?=Commands?>]]></Content>
						</xml> 
						<?php
						exit; }						
						
				elseif( $keyword == 'roll' ){
						?>
						<xml>
						<ToUserName><![CDATA[<?=$fromUsername?>]]></ToUserName>
						<FromUserName><![CDATA[<?=$toUsername?>]]></FromUserName>
						<CreateTime><?=time()?></CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[<?=rand(1,6)?>]]></Content>
						</xml> 
						<?php
						exit; }
						
				elseif( $keyword == 'Roll' ){
						?>
						<xml>
						<ToUserName><![CDATA[<?=$fromUsername?>]]></ToUserName>
						<FromUserName><![CDATA[<?=$toUsername?>]]></FromUserName>
						<CreateTime><?=time()?></CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[<?=rand(1,6)?>]]></Content>
						</xml> 
						<?php
						exit; }						
						
				elseif( $keyword == 'ROLL' ){
						?>
						<xml>
						<ToUserName><![CDATA[<?=$fromUsername?>]]></ToUserName>
						<FromUserName><![CDATA[<?=$toUsername?>]]></FromUserName>
						<CreateTime><?=time()?></CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[<?=rand(1,6)?>]]></Content>
						</xml> 
						<?php
						exit; }								
						
				elseif( $keyword == 'coin' ){
						?>
						<xml>
						<ToUserName><![CDATA[<?=$fromUsername?>]]></ToUserName>
						<FromUserName><![CDATA[<?=$toUsername?>]]></FromUserName>
						<CreateTime><?=time()?></CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[<?=rand(1,2)==1?"正面":"反面";?>]]></Content>
						</xml> 
						<?php
						exit; }
						
				elseif( $keyword == 'Coin' ){
						?>
						<xml>
						<ToUserName><![CDATA[<?=$fromUsername?>]]></ToUserName>
						<FromUserName><![CDATA[<?=$toUsername?>]]></FromUserName>
						<CreateTime><?=time()?></CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[<?=rand(1,2)==1?"正面":"反面";?>]]></Content>
						</xml> 
						<?php
						exit; }
						
				elseif( $keyword == 'COIN' ){
						?>
						<xml>
						<ToUserName><![CDATA[<?=$fromUsername?>]]></ToUserName>
						<FromUserName><![CDATA[<?=$toUsername?>]]></FromUserName>
						<CreateTime><?=time()?></CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[<?=rand(1,2)==1?"正面":"反面";?>]]></Content>
						</xml> 
						<?php
						exit; }						

				elseif( $keyword == 'jz' ){ //禁止卡表
						?>
						<xml>
						<ToUserName><![CDATA[<?=$fromUsername?>]]></ToUserName>
						<FromUserName><![CDATA[<?=$toUsername?>]]></FromUserName>
						<CreateTime><?=time()?></CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[<?=LatestCardList_Forbidden?>]]></Content>
						</xml> 
						<?php
						exit; }

				elseif( $keyword == 'xz' ){ //限制卡表
						?>
						<xml>
						<ToUserName><![CDATA[<?=$fromUsername?>]]></ToUserName>
						<FromUserName><![CDATA[<?=$toUsername?>]]></FromUserName>
						<CreateTime><?=time()?></CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[<?=LatestCardList_Limit?>]]></Content>
						</xml> 
						<?php
						exit; }


				elseif( $keyword == 'zxz' ){ //准限制卡表
						?>
						<xml>
						<ToUserName><![CDATA[<?=$fromUsername?>]]></ToUserName>
						<FromUserName><![CDATA[<?=$toUsername?>]]></FromUserName>
						<CreateTime><?=time()?></CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[<?=LatestCardList_Quasi_Limit?>]]></Content>
						</xml> 
						<?php
						exit; }


				elseif( $keyword == 'wxz' ){ //无限制卡表
						?>
						<xml>
						<ToUserName><![CDATA[<?=$fromUsername?>]]></ToUserName>
						<FromUserName><![CDATA[<?=$toUsername?>]]></FromUserName>
						<CreateTime><?=time()?></CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[<?=LatestCardList_No_Limit?>]]></Content>
						</xml> 
						<?php
						exit; }


				elseif( $keyword == 'yf' ){ //高级搜索语法
						?>
						<xml>
						<ToUserName><![CDATA[<?=$fromUsername?>]]></ToUserName>
						<FromUserName><![CDATA[<?=$toUsername?>]]></FromUserName>
						<CreateTime><?=time()?></CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[<?=Advanced_Commands?>]]></Content>
						</xml> 
						<?php
						exit; }


				elseif (substr( $keyword,0,2)=="t "){ //无图模式，语句是“t 卡片关键词”
				
		
						$keyword = substr($keyword,2);//获取真正的关键词
						$articles = ws_get_article( $keyword );
				
               			
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
               	
               	
               	
				elseif (substr( $keyword )=="tv"){ // 看动画		
               			
               			ob_start(); 
                  		?><xml>
						<ToUserName><![CDATA[<?=$fromUsername?>]]></ToUserName>
						<FromUserName><![CDATA[<?=$toUsername?>]]></FromUserName>
						<CreateTime><?=$time?></CreateTime>
						<MsgType><![CDATA[news]]></MsgType>
						<Content><![CDATA[搜索结果]]></Content>
						<ArticleCount>10</ArticleCount>
						<Articles>1
						<item> 
  							<Title><![CDATA[游戏王DM]]></Title>
  							<Description><![CDATA[CV:武藤游戏]]></Description>
  							<Url><![CDATA[http://www.soku.com/detail/show/XODAzNDg=]]></Url>
						</item>
						</Articles>
						<FuncFlag>0</FuncFlag>
					</xml>
					
					<?php
					$xml = ob_get_contents();
					//file_put_contents('xml.txt', $xml);
					header('Content-Type: text/xml');
					echo trim($xml);       			
               			
               	}               	
               	
               	
               																
                elseif( $articles = ws_get_article( $keyword )){// 普通搜索
                              
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
					</xml>
					
					<?php
					$xml = ob_get_contents();
					//file_put_contents('xml.txt', $xml);
					header('Content-Type: text/xml');
					echo trim($xml); 
				}
				
			   else{//没搜索到结果
						
  						?>
						<xml>
						<ToUserName><![CDATA[<?=$fromUsername?>]]></ToUserName>
						<FromUserName><![CDATA[<?=$toUsername?>]]></FromUserName>
						<CreateTime><?=time()?></CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[没有搜索到相关卡片，请尝试其他关键词，或输入 help 查看全部指令。]]></Content>
						</xml>
					<?php
				}
				     
        }else{//发送空关键词
                  	echo "请输入要查询的游戏王卡片关键词。输入 help 查看全部指令。";
        	}


	}else {//未发送有效信息
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

		$sfile = fopen(SEARCHPAGE.$keyword,"r"); //打开查询网页
		
		while(!FEOF($sfile)){ //读出网页源码
		
			$l = trim(fgets($sfile));//读出一行源码
					
			if ( strpos($l,"</figure><ul><li>中文名：")!==false  ){//适配部分卡片页面的代码将卡片名一栏混合到上一行
			
					$out = preg_replace('/.*<li>中文名：/','',$l);	
					$out = preg_replace('/<\/li>/','',$out);
			 		$result['title'] = "【".$out."】";// 卡片名称
			 		
			 		
			 		if ( strpos($l,'src="http://p.ocgsoft.cn')!==false  ){
					$out = preg_replace('/.*<figure class="fn-right"><img src="/','',$l);	
					$out = preg_replace('/".*width=.*<\/li>/','',$out);					
			 		$result['pic'] = $out;}// 卡片图片
			}
			 		


			elseif ( strpos($l,"<li>中文名：")!==false  ){
			
					$out = preg_replace('/<li>中文名：/','',$l);	
					$out = preg_replace('/<\/li>/','',$out);
			 		$result['title'] = "【".$out."】";// 卡片名称
			 		
			}
			
			elseif ( strpos($l,"<li>卡片种类：")!==false  ){

					$out = preg_replace('/.*<li>卡片种类：/','',$l);	
					$out = preg_replace('/<\/li>/','',$out);
			 		$cardCate = $out;// 卡片种类
			}		
			
			elseif ( strpos($l,"<li>星级：")!==false  ){
					
					$out = preg_replace('/.*<li>星级：/','',$l);	
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