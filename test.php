<?php
header("content-type:text/html; charset=utf-8");

		$code = file_get_contents("http://www.nwbbs.com/forum.php?mod=forumdisplay&fid=8&mobile=yes&simpletype=no");
		$code = explode('<div class="bm_c bt">',$code);
		$code = explode('<span class="xg2">',$code[1]);
		$code = explode('<div class="bm_c">',$code[0]);
		//$code = explode('<br/>',$code[1]);
		
		foreach ($code as $line){
			
			$line = str_replace(" ","",$line);
			
			//echo $line;
			
 			if (preg_match ("/forum.php.*mobile=yes/", $line, $m)){	

		        //echo $m[0]; // 帖子URL
		    
		        $result['url'] = $m[0];

		     }

			$line = preg_replace('/\[<ahref.*<\/a>\]/','',$line);
 			$line = preg_replace('/<ahref.*&mobile=yes">/','',$line);
 			$line = substr($line,0,strpos($line,"</a>"));
 			
 			$title = trim($line); // 帖子标题
 			
 			$result['title'] = $title;
 			
 			
 			
 			$results[] = $result;
 			
 			
		}
		
		var_dump($results);		
	
?>