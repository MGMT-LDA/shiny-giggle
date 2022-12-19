<?php
	include_once("Utility.php");
	include_once("DbHelper.php");
	include_once("Crawling.php");
	include_once("USPatents.php");
	include_once("WOPatents.php");
	
	class Patents extends DbHelper
	{
		private $dbconnection;
		private $objCrawling;
		
		public $patent_id;
		public $content;
		public $pdfLink;
		public $claim;
		public $claim1;
		public $description;
		public $description1;
		public $patentFamily;
		public $espacenetUrl;
		
		public $patentCode;
		public $userId;
		
		
		function __construct()
		{
			parent::__construct();
			
			$this->objCrawling	=	new Crawling();
		}
		
		public function insertPatent()
		{
			$data	=	array(
							'patent_id'=>$this->patent_id,
							'patent_code'=>$this->patentCode,
							'content'=>$this->content,
							'pdf_link'=>$this->pdfLink,
							'claim'=>$this->claim,
							'claim_1'=>$this->claim1,
							'description'=>$this->description,
							'description_1'=>$this->description1,
							'espacenet_url'=>$this->espacenetUrl,
							'patent_family'=>$this->patentFamily,
							'user_id'=>$this->userId
							);
							
			return $this->insert('patents',$data);
		}
		
		public function updatePatent()
		{
			$data	=	array(
							'patent_id'=>$this->patent_id,
							'patent_code'=>$this->patentCode,
							'content'=>$this->content,
							'pdf_link'=>$this->pdfLink,
							'claim'=>$this->claim,
							'claim_1'=>$this->claim1,
							'description'=>$this->description,
							'description_1'=>$this->description1,
							'espacenet_url'=>$this->espacenetUrl,
							'patent_family'=>$this->patentFamily,
							'user_id'=>$this->userId
							);
			$where	=		array('patent_id'=>$this->patent_id);
			if(!empty($this->userId))
				$where['user_id']	=	$this->userId;
			
			if(!empty($this->patentCode))
				$where['patent_code']	=	$this->patentCode;
			
			return $this->update('patents',$data, $where);
		}
		
		public function updatePatentClaimAndDescription()
		{
			$data	=	array(
							'patent_id'=>$this->patent_id,
							'claim'=>$this->claim,
							'description'=>$this->description
							);
							
			return $this->update('patents',$data,array('patent_id'=>$this->patent_id));
		}
		
		public function updatePatentEspacenetUrl()
		{
			$data	=	array(
							'espacenet_url'=>$this->espacenetUrl
							);
							
			return $this->update('patents',$data,array('patent_id'=>$this->patent_id));
		}
		
		public function updatePatentFamilies()
		{
			$data	=	array(
							'patent_family'=>$this->patentFamily
							);
							
			return $this->update('patents',$data,array('patent_id'=>$this->patent_id));
		}
		
		public function selectPatent()
		{
			$data	=	array(
							'patent_id'=>$this->patent_id,
							'patent_code'=>$this->patentCode
							);
			
			if(!empty($this->userId))
				$data['user_id']	=	$this->userId;
			
			return $this->show('patents',$data);
		}
				
		public function crawlPatent()
		{
			$fxn				=	$this->patentCode.'Patents';
			$obj				=	new $fxn();
			$obj->patent_id		=	$this->patent_id;
			$crawlingContent	=	$obj->crawlPatent();
			return $crawlingContent;
			Utility::debug($crawlingContent);die;
		}
		
		public function getEspaceLink($url)
		{
			$content			=	$this->objCrawling->getSource($url);
			if(empty($content))
				//$content		=	file_get_contents($url);
			
			if(!empty($content))
			{
				$anchorContent		=	$this->objCrawling->getcontent($content,'publicationLinkClass',0,'</a>',0);
				$link				=	$this->objCrawling->getcontent($anchorContent['value'],'href="',0,'">',0);
				
				if(!empty($link['value']))
					return "https://worldwide.espacenet.com".$link['value'];
				else
					return '';
			}
			else
				return $url;
		}
		
		public function getClaimAndDescription($contentArray)
		{
			$result		=	$this->objCrawling->getcontent($contentArray,'id="claims"',-17,'id="description"',20,0);
			$result1	=	$this->objCrawling->getcontent($contentArray,'id="description"',-22,'xxx',0,0);
			
			return array('claim'=>$result['value'],'description'=>$result1['value']);
		}
		

		public function savePatentForUser()
		{
			$result		=	$this->selectPatent();
			print_r($result);
			if(is_array($result) && !empty($result))
				return $this->updatePatent();
			else
				return $this->insertPatent();
		}
		
		public function getPatentFamily($url)
		{
			$patentFamilies	=	array();
			$domain			=	'https://worldwide.espacenet.com';
			$link			=	'';
			$url 			=	str_replace($domain,$domain.'/data',$url);
			
			/* $content		=	$this->objCrawling->getSource($url);
			
			if(empty($content)) */
				$content	=	@file_get_contents(htmlspecialchars_decode($url));
			
			if(!empty($content))
			{
				$lists		=	$this->objCrawling->getcontent($content,'epoContentNav',0,'epoAccordionContainer',0);
				
				$posToStart		=	0;
				$flag			=	true;
				$contentArray	=	array();
				
				while($flag)
				{
					$content	=	$this->objCrawling->getcontent($lists['value'],'<li>',0,'</li>',0,$posToStart);
					
					if(strpos($content['value'],'INPADOC patent family',0) !== false)
						array_push($contentArray,($content['value']));

					if(empty($content['endPos']))
						$flag	=	false;
					else
						$posToStart	=	$content['endPos'];
				}
				
				
				if(!empty($contentArray))
				{
					$link		=	$this->objCrawling->getcontent($contentArray[0],'href="',0,'">',0,0);
					$urlPatentFamilies	=	$domain.'/data'.$link['value'];
				}
				
				if(!empty($link))
				{
					/* $content		=	$this->objCrawling->getSource($urlPatentFamilies);
					
					if(empty($content)) */
						$content	=	file_get_contents(htmlspecialchars_decode($urlPatentFamilies));
					
					$patentFamilies	=	$this->extractPatentFamilies($content);
				}
			}
			
			return $patentFamilies;
		}
		
		
		function extractPatentFamilies($content)
		{
			$posToStart			=	0;
			$flag				=	true;
			$patents			=	array();
			
			while($flag)
			{
				$patentLinkContent	=	$this->objCrawling->getcontent($content,'titleRowClass',0,'</tr>',0,$posToStart);
				$patentLink		=	$this->objCrawling->getcontent($patentLinkContent['value'],'publicationLinkClass',0,'>',0,0);
				$patentLink		=	$this->objCrawling->getcontent($patentLink['value'],'href="',0,'"',0,0);
				
				$patentContent	=	$this->objCrawling->getcontent($content,'publicationInfoColumn',0,'</td>',0,$posToStart);
				
				if(empty($patentContent['endPos']))
					$flag	=	false;
				else
					$posToStart	=	$patentContent['endPos'];
				
				$patentContent	=	$this->objCrawling->getcontent($patentContent['value'],'</h4>',0,'globalDossier',0,0);
				
				$patentFamilyArray	=	explode("<br />",$patentContent['value']);
				$patentFamilyArray	=	array_map('trim',$patentFamilyArray);
				$patentFamilyArray	=	array_map('strip_tags',$patentFamilyArray);
				$patentFamilyArray	=	array_map('trim',$patentFamilyArray);
				
				foreach($patentFamilyArray as $item)
				{
					if($this->validateDate($item) === false && !empty($item))
					{
						$item	=	'<a href="https://worldwide.espacenet.com'.$patentLink['value'].'">'.$item.'</a>';
						array_push($patents,$item);
					}
				}
				$patents	=	array_filter($patents);
			}
			
			return $patents;
		}
		
		function validateDate($date)
		{
			return strtotime($date);
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
		
		function getPatentCode($code)
		{
			switch($code)
			{
				case 'WO':
				{
					return PatentTypes::WIPO;
				}
				default:
				{
					return PatentTypes::US;
				}
			}
		}
	}
?>