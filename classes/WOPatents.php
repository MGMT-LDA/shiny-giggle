<?php
	include_once("classes/Utility.php");
	include_once("classes/Crawling.php");
	
	class WOPatents
	{
		private $objCrawling;
		
		public $patent_id;
		
		
		function __construct()
		{			
			$this->objCrawling	=	new Crawling();
		}
				
		public function crawlPatent()
		{
			$bibilioData	=	$this->getBiblioData();
			$claims			=	$this->getClaims();
			$description	=	$this->getDescription();
			$pdfLink		=	$this->getPdfLink();
			
			return array('content'=>$bibilioData.'       <span id="claims">Claims</span>'.$claims.'               <span id="description">Description</span>'.$description, 'pdfLink'=>$pdfLink);
		}
		
		
		private function getBiblioData()
		{
			$url		=	'https://patentscope.wipo.int/search/en/detail.jsf?docId=WO'.$this->patent_id.'&recNum=1&maxRec=&office=&prevFilter=&sortOption=&queryString=&tab=PCT+Biblio';
				
			$pageContent	=	$this->objCrawling->getUrlContents($url);
			
			//$content		=	$this->objCrawling->getcontent($pageContent,'detailMainForm:PCTBiblio:content',-60,'detailMainForm:PCTDescription',15,0);
			$content		=	$this->objCrawling->getcontent($pageContent,'<tr> <td COLSPAN="2" ALIGN="LEFT" VALIGN="TOP"> <HR /> </td> </tr>',0,'</div></div></div></div><script id="detailMainForm:detailsTabMenu_content_s" type="text/javascript">',0,0);
			$pageContent		=	$content['value'];
			
			
			$pageContent		=	preg_replace("/<div class=\"permaLink\">(.*?)<\/div>/i s", "", $pageContent);
			$pageContent		=	preg_replace("/<img[^>]+\>/i", "", $pageContent); 
			
			return $pageContent;
		}
		
		private function getClaims()
		{
			$url		=	'https://patentscope.wipo.int/search/en/detail.jsf?docId=WO'.$this->patent_id.'&recNum=1&tab=PCTClaims&maxRec=&office=&prevFilter=&sortOption=&queryString=';
				
			$pageContent	=	$this->objCrawling->getUrlContents($url);
			
			//$content		=	$this->objCrawling->getcontent($pageContent,'detailMainForm:PCTClaims:content',-60,'detailMainForm:NationalPhase',15,0); //previous approach
			$content		=	$this->objCrawling->getcontent($pageContent,'<td><span class="searchhit">',0,'</html></span></td>',0,0);
			$pageContent		=	$content['value'];
			
			return $pageContent;
		}
		
		private function getDescription()
		{
			$url		=	'https://patentscope.wipo.int/search/en/detail.jsf?docId=WO'.$this->patent_id.'&recNum=1&maxRec=&office=&prevFilter=&sortOption=&queryString=&tab=PCTDescription';
				
			$pageContent	=	$this->objCrawling->getUrlContents($url);
			
			//$content		=	$this->objCrawling->getcontent($pageContent,'detailMainForm:PCTDescription:content',-65,'detailMainForm:PCTClaims',15,0);
            $content		=	$this->objCrawling->getcontent($pageContent,'<td><span class="searchhit">',0,'</html></span></td>',0,0);
			$pageContent		=	$content['value'];
			
			return $pageContent;
		}
		
		private function getPdfLink()
		{
			$url		=	'https://patentscope.wipo.int/search/en/detail.jsf?docId=WO'.$this->patent_id.'&recNum=1&tab=PCTDocuments&maxRec=&office=&prevFilter=&sortOption=&queryString=';
				
			$pageContent	=	$this->objCrawling->getUrlContents($url);
			
			//$content		=	$this->objCrawling->getcontent($pageContent,'Published International Application',0,'<table',0,0);
			$content		=	$this->objCrawling->getcontent($pageContent,'Published International Application',0,'</table',0,0);
			$content		=	$this->objCrawling->getcontent($content['value'],'<a href="',0,'">',0,0);
			
			$pageContent	=	$content['value'];
			
			$pageContent	=	explode(";",$pageContent);	
			// Utility::debug($pageContent,1);
			return "https://patentscope.wipo.int/".$pageContent[0];
		}
	}
?>