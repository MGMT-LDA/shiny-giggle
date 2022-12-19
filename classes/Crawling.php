<?php
	
	/*
	 * Deals with website crawling.
	 *
	 *
	 */
	class Crawling
	{
		function getSource($url) 
		{
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);//kashish: automatically redirect http to https
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
			$content = curl_exec($ch);
			curl_close($ch);
			return $content;
		}
		
		function getcontent($content,$startString,$frontShift=0,$endString,$lastShift=0,$posToStart=0,$posRequired=1)
		{
			$endStringOriginal=$endString;
			
			if(!empty($content) && !empty($startString))
			{
				$startStringpos=@strpos($content,$startString,$posToStart);
				if($startStringpos !== false)
				{
					$strFrom=strlen($startString);
					$startString=$strFrom+$startStringpos+$frontShift;
					$strToPos = strpos($content,$endString,$startStringpos);
					$endString=$strToPos-$startString-$lastShift;
					if($endString < 0)
						$endString = strlen($content);
					$content=substr($content,$startString,$endString);
					$endPos=$endString+strlen($endStringOriginal)+$startString;
					if ($posRequired == 1)
						return $contentArray=array('value'=>$content,'endPos'=>$endPos);
					else
						return $contentArray=array('value'=>$content);
				}
				else
				{
					if ($posRequired == 1)
						return $contentArray=array('value'=>0,'endPos'=>0);
					else
						return $contentArray=array('value'=>$content);
				}
			}
			else
				return $contentArray=array('value'=>0,'endPos'=>0);
		}
		
		function getUrlContents($url)
		{
			//$options  = array('http' =>array('user_agent' => $_SERVER['HTTP_USER_AGENT'])); //previous approach
			$options  = array('http' =>array('user_agent' => $_SERVER['HTTP_USER_AGENT']),"ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false,));// kashish
			$context  = stream_context_create($options);
			return file_get_contents($url, false, $context);
		}
	}
?>