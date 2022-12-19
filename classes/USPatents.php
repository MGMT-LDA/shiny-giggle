<?php
	include_once("classes/Utility.php");
	include_once("classes/Crawling.php");
	
	class USPatents
	{
		private $objCrawling;
		
		public $patent_id;
		
		
		function __construct()
		{			
			$this->objCrawling	=	new Crawling();
		}
				
		public function crawlPatent()
		{
			if(strlen($this->patent_id) == 6 || strlen($this->patent_id) == 7 || strlen($this->patent_id) == 11  || strlen($this->patent_id) == 8)
			{
				if(strlen($this->patent_id) == 7 || strlen($this->patent_id) == 6 || strlen($this->patent_id) == 8)
					$url	=	'http://patft.uspto.gov/netacgi/nph-Parser?Sect1=PTO1&Sect2=HITOFF&d=PALL&p=1&u=%2Fnetahtml%2FPTO%2Fsrchnum.htm&r=1&f=G&l=50&s1='.$this->patent_id.'.PN.&OS=PN/'.$this->patent_id.'&RS=PN/'.$this->patent_id;
				else
					$url	=	'http://appft.uspto.gov/netacgi/nph-Parser?Sect1=PTO1&Sect2=HITOFF&d=PG01&p=1&u=%2Fnetahtml%2FPTO%2Fsrchnum.html&r=1&f=G&l=50&s1=%22'.$this->patent_id.'%22.PGNR.&OS=DN/'.$this->patent_id.'&RS=DN/'.$this->patent_id;
				
				$pageContent	=	$this->objCrawling->getSource($url);


				$posToStart		=	0;
				$flag			=	true;
				$contentArray	=	array();
				$linksArray		=	array();
				$pdfArray		=	array();
				$pdfLink		=	'';
				
				while($flag)
				{
					$content	=	$this->objCrawling->getcontent($pageContent,'<TABLE',-6,'* * * * *',11,$posToStart);
					array_push($contentArray,($content['value']));

					if(empty($content['endPos']))
						$flag	=	false;
					else
						$posToStart	=	$content['endPos'];
				}


				$flag			=	true;
				$posToStart		=	0;

				while($flag)
				{
					$content	=	$this->objCrawling->getcontent($pageContent,'<a href=',0,'>',0,$posToStart);
					array_push($linksArray,($content['value']));

					if(empty($content['endPos']))
						$flag	=	false;
					else
						$posToStart	=	$content['endPos'];
				}

				
				foreach($linksArray as $link)
				{
					$link			=	str_replace('"','',$link);
					$linkArray		=	explode(".",$link);

					if(!isset($linkArray[1]) || strtolower($linkArray[1]) != 'html')
					{
						$link			=	str_replace("PageNum=&","PageNum=0&",$link);
						$linkContent	=	$this->objCrawling->getSource(trim($link));

						if(!empty($linkContent))
						{
							$flag			=	true;
							$posToStart		=	0;

							while($flag)
							{
								$pdf	=	$this->objCrawling->getcontent($linkContent,'<embed',0,'</embed>',0,$posToStart);

								$pdfSrc	=	$this->objCrawling->getcontent($pdf['value'],'src="',0,'" ',0,0);

								$pdfSrcArray	=	explode(".",$pdfSrc['value']);

								if(!empty($pdfSrcArray) && strtolower(array_pop($pdfSrcArray)) == 'pdf')
									array_push($pdfArray,($pdfSrc['value']));

								if(empty($pdf['endPos']))
									$flag	=	false;
								else
									$posToStart	=	$pdf['endPos'];
							}
							if(!empty($pdfArray))
								$pdfLink = $pdfArray[0];
						}
					}
				}
				$contentArray	=	array_filter($contentArray);
				$content		=	$this->objCrawling->getcontent(implode(" ",$contentArray),'</TABLE>',0,'name="bottom"',0,0);
				$content['value']	=	$this->formatHtml($content['value']);
				
				return array('content'=>$content['value'], 'pdfLink'=>$pdfLink);
			}
			else
				return array('content'=>'', 'pdfLink'=>'');
		}
		
		function formatHtml($html)
		{
			$html = html_entity_decode($html, ENT_COMPAT, 'UTF-8');
			$html = str_replace("&nbsp"," ",$html);
			$doc = new DOMDocument();
			@$doc->loadHTML($html);
			$html = $doc->saveHTML();
			//$html = preg_replace("/<[^>]*</", "", $html);
			//Utility::debug($html,1);
			return $html;
		}
	}
?>