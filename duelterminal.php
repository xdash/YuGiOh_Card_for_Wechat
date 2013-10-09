<?php
/**
 * 专用于游戏王微信卡查的各种查询类
 * 包括搜索卡片、随机显示卡片、显示游戏王贴吧等
 * by @XDash http://www.fanbing.net
 */


session_start();


//require_once("settings.php");
//require_once("strings.php");
//require_once("functions.php");
//require_once("duelterminal.php");


//$test = new DuelTerminal();
//$test->get_random_result();



class DuelTerminal{

/*** 核心查询类 用于各种与卡片相关的查询、解析、论坛新帖数据读取 */


	public function getCardByAPI($keyword){
	/*** 根据关键词搜索卡片2.0（API来自ourocg.cn） ***/

			//查询卡片获取JSON格式结果
			$json = json_decode(request_by_curl(OUROCG_API,"Key=".$keyword));
			$jsonCount = $json->totalResult;
	   
	    	//取得最多10条记录（微信的图文显示限制），若不够10条则全取
	   		if ($jsonCount > 10){
	   			$countMax = 10-1;
	   		} elseif ($jsonCount == 1){
	   			$countMax = 1-1;
	   		} elseif ($jsonCount == 0){
	   			return false;
	   		} else {
	   			$countMax = $jsonCount-1;
	   		}  	
	   	
	   		//从JSON读取卡片详细数据
			for ($i=0;$i <= $countMax;$i++){
				$result['name']	= $json->data[$i]->name;
				$result['tribe']	= $json->data[$i]->tribe;
				$result['element']	= $json->data[$i]->element;
				$result['level']	= $json->data[$i]->level;
				$result['atk'] = $json->data[$i]->atk;
				$result['def'] = $json->data[$i]->def;
				$result['cardType'] = $json->data[$i]->cardType;
				$result['ID'] = $json->data[$i]->ID;
				$result['pic'] = OUROCG_PIC.$result['ID'].".jpg"; //图片	
				$result['url'] = $_SERVER['HTTP_HOST']."/".OUROCG_REDIRCT_MOBILE.$result['ID']; //网址	

				//如果只有一张卡的结果，获取效果
				if ($jsonCount == 1){
					$result['content'] = $this->getEffectByID($result['ID']) ;
				}	 

				//判断是否为XYZ怪兽，显示星数或阶数
				if ($result['cardType']=="XYZ怪兽"){
					$isXYZMonster = "阶";
				}else{
					$isXYZMonster = "星";
				}
			
				//生成用于输出到微信的图文消息的标题
				if (isset($result['atk'])){ //怪兽卡
					$result['title'] = "【".$result['name']."】".$result['cardType']."/".$result['level'].$isXYZMonster."/ATK:".$result['atk']."/DEF:".$result['def']."/种族：".$result['tribe']."/属性：".$result['element']."/ID:".$result['ID'];
				} else { //魔法陷阱卡
					$result['title'] = "【".$result['name']."】".$result['cardType']."/ID:".$result['ID'];
				}		
			
				//追加1条记录，最多10条
				$results[] = $result;																   
			}//end of 'for ($i=0;$i <= $countMax;$i++)'
		
	   		return $results;
	   
	   		//echo $json->totalResult;
			//echo $json->data[0]->ID;
	   
	}



	public function getCardNameByID($cardID){
	/*** 根据卡片ID获取一张卡的中文名称（抓取版） ***/
			$content = file_get_contents(OUROCG_WIKI_PREFIX.$cardID);
			$arr = explode('<title>',$content);
			$arr = explode(' - WIKI/调整',$arr[1]);
			$result = trim($arr[0]);
			return $result;
	}



	public function getCardJapaneseNameByID($cardID){
	/*** 根据卡片ID获取一张卡的日文名称（抓取版） ***/
			$content = file_get_contents(OUROCG_WIKI_PREFIX.$cardID);
			$arr = explode('<h1 class="title">《',$content);
			$arr = explode('》  </h1>',$arr[1]);
			$result = trim(strip_tags($arr[0]));
			return $result;
	}



	public function getEffectByID($cardID){
	/*** 根据卡片ID获取一张卡的中文效果（抓取版） ***/
	
			$sfile = fopen(OUROCG_WEB_PAGE.$cardID,"r"); //打开查询网页		
			while(!FEOF($sfile)){ //读出网页源码
				$l = trim(fgets($sfile));//读出一行源码
				if ( strpos($l,"<li>效果：")!==false  ){
					$out = preg_replace('/<li>效果：/','',$l);	
					$out = preg_replace('/<\/li>/','',$out);
			 		$cardEffect = mb_strimwidth($out,0,200,'...','UTF-8');//卡片效果
			 		break;
				}
			}
  			return $cardEffect;	   
	}



	public function getJapaneseEffectByID($cardID){
	/*** 根据卡片ID获取一张卡的日文效果（抓取版） ***/
			$content = file_get_contents(OUROCG_WIKI_PREFIX.$cardID);
			$arr = explode("<pre>",$content);
			$arr = explode("</pre>",$arr[1]);
			$result = $arr[0];
			return $result;
	}



	public function ws_get_article( $keyword ){
	/*** 根据关键词搜索卡片1.0（抓取版） ***/
		
			$i = 0;
			$sfile = fopen(SEARCHPAGE.$keyword,"r"); 
		
			while(!FEOF($sfile)){
				$l = trim(fgets($sfile));		
				if ( strpos($l,"main-content carddetails")!==false  ){ //精确搜到一张卡，调用get_accurate_result
					$getCard = $this->get_accurate_result( $keyword );
					return $getCard;
					exit;
				}	
		
				elseif ( strpos($l,"info fn-left")!==false  ){ //搜到模糊结果，返回前10个		
					if($i < 10){ //微信限制一次最多返回10篇文章

						$out = preg_replace('/<div class.*height=120 alt="/','',$l);	
						$out = preg_replace('/"\/><\/figure>/','',$out);
			 			$result['title'] = "【".$out."】";//卡片名称
			 		
						$out = preg_replace('/<div class.*<\/a><\/h1>/','',$l);
						$out = preg_replace('/<\/span><\/div>.*<\/figure>/','',$out);	
			 			$out = trim(strip_tags($out));//卡片属性
			 			$result['title'] = $result['title']." - ".$out;

						$out = preg_replace('/<div class.*<p class="effect">/','',$l);
						$out = preg_replace('/<\/p>.*<\/figure>/','',$out);	
			 			$result['content'] = mb_strimwidth($out,0,200,'...','UTF-8');//卡片效果		

						$out = preg_replace('/<div class.*h1.*Cards\/View-/','',$l);	
						$out = preg_replace('/">.*<\/figure>/','',$out);
			 			$result['url'] = OUROCG.$out.".html";//卡片地址
			 		
						$out = preg_replace('/<div class.*src="/','',$l);	
						$out = preg_replace('/" height.*<\/figure>/','',$out);
			 			$result['pic'] = $out;//卡片图片			 		
									 		
  		  				$results[] = $result;
  		  				$i++;
					}//end of 'if($i < 10)'
				}

		
			}//end of 'while(!FEOF($sfile))'
		
			if( count( $results ) > 0 ) return $results ; 
  			else return false;

	}



	public function get_random_result(){
	/*** 随机搜索显示一张卡片（抓取版） ***/
	
			$randomCardID = rand(1,TOTAL_CARD_COUNT);
			$sfile = fopen(CARDPAGE.$randomCardID,"r"); //打开随机一张卡片网页
			$result = $this->parseCardInfo($sfile,$randomCardID);//通过打开的网页解析卡片具体信息
			var_dump($result);
			$results[] = $result;
			if( count( $results ) > 0 ) return $results ; 
  			else return false;
	}
		
	
	
	public function parseCardInfo($sfile,$CardID){
	/*** 根据卡片网页解析出需要的卡片数据（服务于抓取版） ***/
		
			while(!FEOF($sfile)){ //读出网页源码
		
				$l = trim(fgets($sfile));//读出一行源码
				if ( strpos($l,"</figure><ul><li>中文名：")!==false  ){// 适配部分卡片页面的代码将卡片名一栏混合到上一行
						$out = preg_replace('/.*<li>中文名：/','',$l);	
						$out = preg_replace('/<\/li>/','',$out);
			 			$result['title'] = "【".$out."】";//卡片名称
			 			if ( strpos($l,'src="http://p.ocgsoft.cn')!==false  ){
							$out = preg_replace('/.*<figure class="fn-right"><img src="/','',$l);	
							$out = preg_replace('/".*width=.*<\/li>/','',$out);					
			 				$result['pic'] = $out;
			 			}//卡片图片
				}elseif ( strpos($l,"<li>中文名：")!==false  ){
						$out = preg_replace('/<li>中文名：/','',$l);	
						$out = preg_replace('/<\/li>/','',$out);
			 			$result['title'] = "【".$out."】";//卡片名称			 		
				}elseif ( strpos($l,"<li>卡片种类：")!==false  ){
						$out = preg_replace('/.*<li>卡片种类：/','',$l);	
						$out = preg_replace('/<\/li>/','',$out);
			 			$cardCate = $out;//卡片种类
				}elseif ( strpos($l,"<li>星级：")!==false  ){
						$out = preg_replace('/.*<li>星级：/','',$l);	
						$out = preg_replace('/<\/li>/','',$out);
			 			$cardLevel = " ".$out."星 ";//卡片星级			
				}elseif ( strpos($l,"<li>阶级：")!==false  ){
						$out = preg_replace('/<li>阶级：/','',$l);	
						$out = preg_replace('/<\/li>/','',$out);
			 			$cardXyz = " ".$out."阶 ";// 卡片阶级
				}elseif ( strpos($l,"<li>攻击：")!==false  ){
						$out = preg_replace('/<li>攻击：/','',$l);	
						$out = preg_replace('/<\/li>/','',$out);
			 			$cardATK = "/ ATK：".$out." ";// ATK
				}elseif ( strpos($l,"<li>防御：")!==false  ){
						$out = preg_replace('/<li>防御：/','',$l);	
						$out = preg_replace('/<\/li>/','',$out);
			 			$cardDEF = "/ DEF：".$out." ";// DEF
				}elseif ( strpos($l,"<li>种族：")!==false  ){
						$out = preg_replace('/<li>种族：/','',$l);	
						$out = preg_replace('/<\/li>/','',$out);
			 			$cardRace = "/ 种族：".$out." ";// 种族
				}elseif ( strpos($l,"<li>属性：")!==false  ){
						$out = preg_replace('/<li>属性：/','',$l);	
						$out = preg_replace('/<\/li>/','',$out);
			 			$cardNature = "/ 属性：".$out;// 属性
				}elseif ( strpos($l,"<li>效果：")!==false  ){
						$out = preg_replace('/<li>效果：/','',$l);	
						$out = preg_replace('/<\/li>/','',$out);
			 			$result['content'] = mb_strimwidth($out,0,200,'...','UTF-8');// 卡片效果
				}elseif ( strpos($l,'link rel="canonical')!==false  ){
						$out = preg_replace('/.*<link rel="canonical" href=".*View-/','',$l);	
						$out = preg_replace('/">/','',$out);
						$result['url'] = $_SERVER['HTTP_HOST']."/".OUROCG_REDIRCT_MOBILE.$CardID; //网址
				}
			}

			$result['title'] = $result['title']." - ".$cardCate.$cardLevel.$cardXyz.$cardATK.$cardDEF.$cardRace.$cardNature;
			
			return $result;
			
			var_dump($result);
	}



	public function getNwbbsTopics(){
	/*** 获取任天堂世界游戏王BBS新帖（抓取版） ***/
	
			$code = file_get_contents(NWBBS_YUGIOH);
			$code = explode('<div class="bm_c bt">',$code);
			$code = explode('<span class="xg2">',$code[1]);
			$code = explode('<div class="bm_c">',$code[0]);
		
			foreach ($code as $line){
			
				$line = str_replace(" ","",$line);
 				if (preg_match ("/forum.php.*mobile=yes/", $line, $m)){	
		        	$result['url'] = NWBBS_HOST.html_entity_decode($m[0]);
		     	}
				$line = preg_replace('/\[<ahref.*<\/a>\]/','',$line);
 				$line = preg_replace('/<ahref.*&mobile=yes">/','',$line);
 				$line = substr($line,0,strpos($line,"</a>"));
 				
 				$title = html_entity_decode(trim($line)); // 帖子标题
 				$title = str_replace("打飞机","",$title); // 敏感关键词过滤
 				$result['title'] = $title;
 				
 				$results[] = $result; // 输出成数组		
			}
		
			$results[0]['pic'] = NWBBS_LOGO;
		
			do{array_pop($results);} while (count($results)>10);
		
			return $results;
		
	}



	public function getTiebaTopics(){
	/*** 获取百度游戏王吧新帖（抓取版） ***/
	
			$code = file_get_contents(TIEBA_YUGIOH);
			$code = explode('刷新</a></div>',$code);
			$code = explode('</p></div><form action=',$code[1]);	
			$code = explode('<div class="i">',$code[0]);
		
			foreach ($code as $line){
				$line = str_replace(" ","",$line);
				if (preg_match ('/\/mo\/.*=\d{10}/', $line, $m)){	
		        $result['url'] = TIEBA_HOST.html_entity_decode($m[0]); // 帖子URL
		     	}

				$line = preg_match('/&#160.*<\/a>/',$line,$n);
				$line = str_replace('&#160;','',$n[0]);			
				$line = str_replace('</a>','',$line);
			 			
 				$title = html_entity_decode(trim($line)); // 帖子标题
 				$title = str_replace("打飞机","",$title); // 敏感关键词过滤

 				$result['title'] = $title;
 			 			
 				$results[] = $result; // 输出成数组
 			     				
			}
		
			do{array_shift($results);} while (count($results)>10);
			$results[0]['pic'] = TIEBA_LOGO;
			return $results;
		
	}
	


} // End Sign of public class DuelTerminal


?>