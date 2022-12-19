<?php
	error_reporting(E_ALL);
	session_start();
	include_once('private/settings.php');
	include_once('classes/Patents.php');


	$objPatents						=		new Patents();

	$searchCode						=		'';
	$patentFamily					=		array();
	$resultPatent					=		array();
	
	$claim_1						=		'';
	$description_1					=		'';

	if(isset($_GET['patent_id']) && !empty($_GET['patent_id']))
	{
		$searchCode					=		$_GET['patent_id'];
		$patentCode					=		$_GET['patent_code'];
        $searchCode	                =       str_replace(',','',$searchCode); //kashish
		$searchCode					=		preg_split('/[;, |]/',$searchCode);
		$searchCode					=		array_filter($searchCode);
		$searchCode					=		array_values($searchCode);
		
		if(is_array($searchCode) && count($searchCode) > 1)
		{
			$searchDiff				=		array();
			
			if(isset($_GET['msearch']) && !empty($_GET['msearch']))
			{
				$_SESSION['patentSearchArray'][$patentCode]	=	$searchCode;
			}
		}
		elseif(isset($_GET['msearch']) && !empty($_GET['msearch']))
			$_SESSION['patentSearchArray'][$_GET['patent_code']]	=	array();
			
		$searchCode					=		$searchCode[0];
		
		
		//if($patentCode	==	PatentTypes::ALL)
		{
			$patentCode				=		$objPatents->getPatentCode(substr($searchCode, 0, 2));
			
			if (preg_match('/[a-z]/i', substr($searchCode, 0, 2)))
				$searchCode			=		substr($searchCode, 2);
			elseif(isset($_GET['patent_code']) && !empty($_GET['patent_code']))
				$patentCode			=		$_GET['patent_code'];
			else
				$patentCode			=		PatentTypes::US;
		}
		
		$showContent				=		1;
		$objPatents->patent_id		=		$searchCode;
		$objPatents->patentCode		=		$patentCode;
		
		if(isset($_SESSION['user']['id']) && !empty($_SESSION['user']['id']))
			$objPatents->userId		=		$_SESSION['user']['id'];
		$resultPatent				=		$objPatents->selectPatent();
		
		if(!is_array($resultPatent) || empty($resultPatent))
		{
			$objPatents->userId		=		'';
			$resultPatent			=		$objPatents->selectPatent();
		}
		$_SESSION['patentSearch']	=		array();
		$_SESSION['patentSearch']['patent_id']		=	$objPatents->patent_id;
		$_SESSION['patentSearch']['patentCode']		=	$objPatents->patentCode;
		
		$objPatents->userId			=		'';
	}
	
	if(!empty($resultPatent))
	{
		$content					=		str_replace("&Acirc;","",$resultPatent[0]->content);
		$pdfLink					=		$resultPatent[0]->pdf_link;
		$claim						=		$resultPatent[0]->claim;
		$description				=		$resultPatent[0]->description;
		$claim_1					=		$resultPatent[0]->claim_1;
		$description_1				=		$resultPatent[0]->description_1;
		$eUrl						=		$resultPatent[0]->espacenet_url;
		$patentFamily				=		unserialize($resultPatent[0]->patent_family);
		$saveToDb					=		0;
		
		if(empty($claim) || empty($description))
		{
			$resultClaimAndDesc			=		$objPatents->getClaimAndDescription($content);
			$claim						=		$resultClaimAndDesc['claim'];
			$description				=		$resultClaimAndDesc['description'];
			
			$objPatents->claim			=	$claim;
			$objPatents->description	=	$description;
			$objPatents->updatePatentClaimAndDescription();
		}
	}
	elseif(!empty($searchCode))
	{
		$resultPatent			=		$objPatents->crawlPatent();

		$pdfLink				=		$resultPatent['pdfLink'];
		$content				=		$resultPatent['content'];
		$content				=		str_ireplace("<i>Claims",'<i><span id="claims">Claims</span>',$content);
		$content				=		str_ireplace("<i>Description",'<i><span id="description">Description</span>',$content);
		$content				=		'<div id="contentTop"></div>'.$content;
		$saveToDb				=		1;
		
		$resultClaimAndDesc		=		$objPatents->getClaimAndDescription($content);
		
		$claim					=		$resultClaimAndDesc['claim'];
		$description			=		$resultClaimAndDesc['description'];
	}
	
	if(empty($content))
	{
		$showContent			=		0;
	}
	elseif($patentCode == PatentTypes::WIPO)
	{
		$pUrl					=		$pdfLink;
		$gUrl					=		'https://www.google.com/patents/WO'.$searchCode;
	}
	else
	{
		$pUrl					=		$pdfLink;//'https://docs.google.com/viewer?url=patentimages.storage.googleapis.com/pdfs/US'.$searchCode.'.pdf';
		
		$gUrl					=		'https://www.google.com/patents/US'.$searchCode;
	}
	
	if(empty($eUrl))
	{
		if(!empty($searchCode) && $patentCode == PatentTypes::WIPO)
		{	
			$eUrl					=		$objPatents->getEspaceLink('https://worldwide.espacenet.com/searchResults?ST=singleline&locale=en_EP&submitted=true&DB=&query=WO'.$searchCode.'+&Submit=Search');
		}
		elseif(strlen($searchCode) > 8)
		{
			$searchCode				=		str_split($searchCode, 1);
			array_splice($searchCode, 4, 1);
			$searchCode				=		implode("",$searchCode);
			//$eUrl					=		'https://worldwide.espacenet.com/publicationDetails/biblio?CC=US&NR='.$searchCode.'A1&KC=A1&FT=D';
			$eUrl					=		$objPatents->getEspaceLink('https://worldwide.espacenet.com/searchResults?submitted=true&locale=en_EP&DB=EPODOC&ST=advanced&TI=&AB=&PN=US'.$searchCode.'&AP=&PR=&PD=&PA=&IN=&CPC=&IC=');
		}
		elseif(!empty($searchCode))
		{
			//$eUrl					=		'https://worldwide.espacenet.com/publicationDetails/biblio?CC=US&NR='.$searchCode.'B2&KC=B2&FT=D';
			$eUrl					=		$objPatents->getEspaceLink('https://worldwide.espacenet.com/searchResults?submitted=true&locale=en_EP&DB=EPODOC&ST=advanced&TI=&AB=&PN=US'.$searchCode.'&AP=&PR=&PD=&PA=&IN=&CPC=&IC=');
			
			
		}
		
		if(!empty($resultPatent))
		{
			$objPatents->espacenetUrl	=	$eUrl;
			$objPatents->updatePatentEspacenetUrl();
		}
	}
	
	if(empty($patentFamily) && !empty($searchCode))
	{
		$patentFamily			=		$objPatents->getPatentFamily($eUrl);
		
		if(!empty($resultPatent))
		{
			$objPatents->patentFamily	=	serialize($patentFamily);
			$objPatents->updatePatentFamilies();
		}
	}

	$patentFamily				=		array_filter($patentFamily);
	
	if(!empty($eUrl))
		$_SESSION['patentSearch']['espacenetUrl']	=	$eUrl;
	
	if(!empty($pdfLink))
		$_SESSION['patentSearch']['pdfLink']		=	$pdfLink;
	
	if(!empty($patentFamily))
		$_SESSION['patentSearch']['patentFamily']	=	serialize($patentFamily);


	$eUrlDefault	=	'https://worldwide.espacenet.com/searchResults?submitted=true&locale=en_EP&DB=EPODOC&ST=advanced&TI=&AB=&PN='.$searchCode.'&AP=&PR=&PD=&PA=&IN=&CPC=&IC=';
	
?>
<html>
	<head>
		
		<style>
			body
			{
				font-size:16px;
				font-family: "Source Sans Pro","Helvetica Neue",Helvetica,Arial,sans-serif;
				
				margin:0;
				overflow:hidden;
				
			}
			table, body font
			{
				font-size:14px;
			}
			
			hr
			{
				margin-top: 10px;
				margin-bottom: 10px;
				border: 0;
				border-top: 1px solid #eee;
			}

			.main-container
			{
				height:100%;
				overflow:hidden;
			}
			.topBar
			{
				background:#a4a4a4;
				height:100px;
				width:100%;
			}

			.headerLeft
			{
				float: left;
				padding: 7px;
				width: 6%;
			}

			.headerLeft img
			{
				width:100%;
			}

			.headerRight
			{
				float:right;
			}

			.container
			{
				margin:0 4px;
			}

			.left
			{
				width:50%;
				float:left;
				height:90%;
				overflow-y: auto;
				position:absolute;
			}

			.left hr:first-child
			{
				display:none;
			}

			.right
			{
				width:50%;
				float:right;
			}
			.clr
			{
				clear:both;
			}
			.hide
			{
				display:none;
			}
		</style>
		<meta charset="utf-8">
		<link rel="icon" href="images/logo/favicon.png" type="image/x-icon">
		<meta content="width=device-width, initial-scale=1" name="viewport" />

		<link href="style.css" rel="stylesheet" type="text/css" />
		<link href="css/color-picker.min.css" rel="stylesheet" type="text/css" />
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
		 <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
		 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<title>iCUEVIEW</title>
		<meta name="description" content=" iCueView is a patent tool or a web-based dashboard, developed by iCuerious, for making patent read convenient and interesting."/>
         <meta name="keywords" content="iCueView, icueview, icuerious tool"/>

			<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-38089819-1', 'auto');
  ga('send', 'pageview');

</script>
	</head>
	<body>
		<div class="header">
			<div class="top-header">
				<ul>
					<li><a href="tel:+91-(988)-873-2426">+91-(988)-873-2426 (Ind)</a></li>
					
					<li><a href="tel:+1-(339)-237-3075">+1-(339)-237-3075 (USA)</a></li>
				</ul>
				<ul class="maillist">
					<li><a href="mailto:info@icuerious.com">info@icuerious.com</a></li>
				</ul>
			</div>
			<div class="wrapper1">
				<div class="top-left">
					<?php if(!empty($searchCode)): ?>
						<div class="logo">
							<a href="<?php echo URL; ?>"><img src="images/home/logo.png" /></a>
						</div>
						<div class="search-button"  >
								<form method="get" class="backPageSearch d-flex in-form-box" action="<?php echo URL; ?>" >
									<div class="selectFieldContainerTop" id="selectContainerTop">
										<select class="select_patent" name="patent_code" required>
											<?php
												$objReflection		=	new ReflectionClass ('PatentTypes');
												$patentTypeResult	=	$objReflection->getConstants();
												foreach($patentTypeResult as $val)
												{$count++;
													if($patentCode == $val)
														echo '<option val="'.$count.'" value="'.$val.'" selected>'.ucWords($val).'</option>';
														
													else
														echo '<option val="'.$count.'" value="'.$val.'" >'.ucWords($val).'</option>';
													
												}
											?>
										</select>
										
									</div>
									<div class="select_arrow" 	>
										<div class="up_arrow" style=""><img class="img1" src="images/top.jpg"  style="    left: 83px;top: 41px; padding: 0;"></div>
										<div class="down_arrow"><img src="images/bottom.jpg" class="img2" style="   top: 53px;left: 83px;padding: 0;"></div>
									</div>

									<div class="inputFieldContainerTop" >
										 <input id="search-input" name="patent_id" type="text" value="<?php echo ($_GET['patent_code'] == PatentTypes::ALL)?$_GET['patent_id']:$searchCode; ?>" required>
										 <a class="btn-submit" href="" type="submit"> <img class="search-input" src="images/icon.png"></a>
									 </div>
									 
									
									 <input type="hidden" name="msearch" value="1" />
								</form>




								<?php if(isset($_SESSION['patentSearchArray'][$_GET['patent_code']]) && count($_SESSION['patentSearchArray'][$_GET['patent_code']]) > 1): ?>
					<div class="searchList">
					<div class="searchList-box">
						<div class="toggleSearchList"></div>
						<ul>
							<?php foreach($_SESSION['patentSearchArray'][$_GET['patent_code']] as $patentId): ?>
								<li><input type="radio" id="<?php echo $patentId; ?>" name="patent_id" /><a href="<?php echo URL; ?>?patent_code=<?php echo $_GET['patent_code']; ?>&patent_id=<?php echo $patentId; ?>"><?php echo $patentId; ?></a>
							</li>
							<?php endforeach; ?>
						</ul>
					</div></div>
				<?php endif; ?>



				
						</div>
					<?php endif; ?>
					<div class="clr"></div>
				</div>
				<div class="top-left text-right" id="user-options">
					<?php if(isset($_SESSION['user']) && !empty($_SESSION['user'])): ?>
						<a class="save-changes"><img src="images/home/file.png"/></a>
						<a class="logout-user"><img src="images/home/logout.png"/></a>
					<?php else: ?>
						<a class="login-link">Login</a>
						<a class="register-link">Register</a>
						<a href="#" class="user" data-toggle="modal" data-target="#exampleModalLong"><img src="images/home/user.png" /></a>
						<a href="#" class="guide"><img src="images/home/guide.png" /> User<br>Guide</a>
					<?php endif; ?>
					<div class="clr"></div>
				</div>
				<div class="clr"></div>
			</div>
		</div>
		<div class="wrapper1" id="contentBody">
			<?php if(!empty($content) && !empty($showContent)): 
					
					$content					=		str_replace("&Acirc;","",$content);
					$content					=		preg_replace("/<img[^>]+\>/i", "", $content); 
					$description				=		preg_replace("/<img[^>]+\>/i", "", $description);
				?>
				<div class="main-content">
					<div class="top-div">
						<div class="top-left">
							<ul class="left-sec">
								
								<li><a href="<?php echo $gUrl; ?>" target="_blank" title="Google Patents"><img class="g-btn" src="images/home/googlepatent.png" alt=""></i></a></li>
								<li><a href="<?php echo (!empty($eUrl))?$eUrl:$eUrlDefault; ?>" target="_blank" title="EspaceNet"><img class="g-btn" src="images/home/fingerprint.png" alt=""></a></li>
								<?php 
                                /* ----------kashish-----------*/								
								if(isset($_GET['patent_id']) && !empty($_GET['patent_id']))
	                              {
		                              //$searchCode =	$_GET['patent_id'];
		                              //$patentCode =	$_GET['patent_code'];
								         if($patentCode=="US")
										 {
											 if(strlen($searchCode)<=8)
											 {?>
												<li><a href="<?php echo 'https://patft.uspto.gov/netacgi/nph-Parser?Sect2=PTO1&Sect2=HITOFF&p=1&u=/netahtml/PTO/search-bool.html&r=1&f=G&l=50&d=PALL&RefSrch=yes&Query=PN/'.$searchCode.'/'?>" target="_blank" title="Uspto"><img class="g-btn" src="images/home/u.png" alt=""></a></li> 

											 <?php
											 }
											 elseif(strlen($searchCode)==10)
										     {
											  $leftpartstr =substr($searchCode, 0, 4);	
                                              $rightpartstr=substr($searchCode, 4, 10);	
                                              $completestr=	$leftpartstr."0".$rightpartstr;									  
									          ?>
											  <li><a href="<?php echo "https://appft.uspto.gov/netacgi/nph-Parser?Sect1=PTO1&Sect2=HITOFF&d=PG01&p=1&u=%2Fnetahtml%2FPTO%2Fsrchnum.html&r=1&f=G&l=50&s1='".$completestr."'.PGNR.&OS=DN/".$completestr."&RS=DN/".$completestr ?>" target="_blank" title="Uspto">U</a></li>	 
											 <?php
											 }
											 else
											 {
											 ?>
											  <li><a href="<?php echo "https://appft.uspto.gov/netacgi/nph-Parser?Sect1=PTO1&Sect2=HITOFF&d=PG01&p=1&u=%2Fnetahtml%2FPTO%2Fsrchnum.html&r=1&f=G&l=50&s1='".$searchCode."'.PGNR.&OS=DN/".$searchCode."&RS=DN/".$searchCode ?>" target="_blank" title="Uspto">U</a></li>		 
											 <?php
											 }
											  
											  if(strlen($searchCode)==11 && substr($searchCode, 4, 1)==0)
											  {
												  $leftpartstr =substr($searchCode, 0, 4);	
                                                  $rightpartstr=substr($searchCode, 5, 6);	
                                                  $completestr=	$leftpartstr.$rightpartstr;	
												?>
											     <li><a href="<?php echo "https://www.icuerious.com/icuemapnew/?pubno=US".$completestr ?>" target="_blank" title="Patent Family Tree"><img class="g-btn" src="images/home/usericon.png" alt=""></a></li>
										       <?php  
											  }
											  else
											  {
												?>
											     <li><a href="<?php echo "https://www.icuerious.com/icuemapnew/?pubno=US".$searchCode ?>" target="_blank" title="Patent Family Tree"><img class="g-btn" src="images/home/usericon.png" alt=""></a></li>
											 	<li class="pdf"><a href="<?php echo URL.'download.php?file='.$pUrl.'&name='.$searchCode; ?>" target="_blank" title="Download PDF" ><img class="g-btn download-btn" src="images/home/download.png" alt="" ></a></li>


										       <?php  
											  }
					                         											 
										 }
										 if($patentCode=="WO")
										 {
											 ?>
											 <li><a href="<?php echo "https://patentscope.wipo.int/search/en/detail.jsf?docId=WO".$searchCode ?>" target="_blank" title="PatentScope">W</a></li>
										     <?php
										 }
								  }
								  /*-----------------------------------------*/
								?>
							     
								<?php if(!empty($patentFamily)): ?>
									<li>
										<a href="#" class="patentFamilies" title="Patent Families">F</a>
										<ul class="patentFamiliesList hide">
											<?php foreach($patentFamily as $patentNumber): ?>
												<li><?php echo $patentNumber; ?></li>
											<?php endforeach; ?>
										</ul>
									</li>
								<?php endif; ?>
							 
							</ul>
							<div class="uspto"><a href="https://portal.uspto.gov/pair/PublicPair" target="_blank" style="text-decoration:none;color: #368EBE;">USPTO Public Pair</a></div>
							<div class="downloadpdf">

							<!-- <span class="togale">
								<div class="togale-show">	<img src="images/togal-2.jpg" /></div>
								<div class="togale-hide">	<img src="images/togal-white.jpg" /></div>
							</span> -->
							
							<li class="pdf">
								
								<!-- <a href="<?php echo URL.'download.php?file='.$pUrl.'&name='.$searchCode; ?>" target="_blank" title="Download PDF" >
								<svg version="1.1" xmlns="https://www.w3.org/2000/svg" xmlns:xlink="https://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20">
                                <path fill="#000000" d="M14.853 9.647c-0.195-0.195-0.512-0.195-0.707 0l-4.146 4.146v-11.293c0-0.276-0.224-0.5-0.5-0.5s-0.5 0.224-0.5 0.5v11.293l-4.146-4.146c-0.195-0.195-0.512-0.195-0.707 0s-0.195 0.512 0 0.707l5 5c0.098 0.098 0.226 0.146 0.354 0.146s0.256-0.049 0.354-0.147l5-5c0.195-0.195 0.195-0.512-0-0.707z"></path>
                                <path fill="#000000" d="M17.5 19h-16c-0.827 0-1.5-0.673-1.5-1.5v-2c0-0.276 0.224-0.5 0.5-0.5s0.5 0.224 0.5 0.5v2c0 0.276 0.224 0.5 0.5 0.5h16c0.276 0 0.5-0.224 0.5-0.5v-2c0-0.276 0.224-0.5 0.5-0.5s0.5 0.224 0.5 0.5v2c0 0.827-0.673 1.5-1.5 1.5z"></path>
                                </svg><img class="g-btn" src="images/home/download.png" alt="" >
                                </a> --></div>
								
								<ul class="right-sec" style="margin-top: 13px;">
							<li class="togale-claim"><a id="compare" title="Claims in view of Description">C | D</a></li>
							<li class="togale-claim"><a id="compare2" title="Claims and Description in view of Diagram">C | D | P</a></li>
						</ul>
							
							
							</li>
							
							<!--kash--->
							<div style="clear:both"></div>
							
							<!---->
						</div>
					</div>
					
					<div class="content-section content-section-container active" id="content-section-1">
						<div class="left-side first-child-div" style="">
							<div id="content-2" class="bottom-left content" >
							<ul class="right-sec w-100 m-0">
							<li><a href="#togale-show" class="togale-show"><img src="images/togal-2.png" alt="togale-show" title="togale-show" class="togale-show" style="width:35px !important; padding:16px 0 0px 15px;"></a></li>
							<li><a href="#contentTop" class="contentTop scrollTags"><img src="images/up arrow.png" alt="up arrow" title="Top" class="uparrow"></a></li>
							<li><a href="#claims" title="Claims" class="scrollTags">C</a></li>
							<li><a href="#description" title="Description" class="scrollTags">D</a></li>
						</ul>
								<?php echo stripslashes($content); ?>
							</div>
						</div>
						<div class="lineSeperator" id="lineSeperator1"></div>
						<div class="right-side" style="">
							<embed src="
							<?php 
							//echo $pdfLink.'#page=2'; /*kashish*/
							  if(substr($pdfLink, 0, 4)=='/fdd')
								{
									echo "https://pdfaiw.uspto.gov/".$pdfLink;
								}
								else
								{
									echo $pdfLink;
								}
							
							?>
							" width="100%" height="100%" style="width: 100%; " type=application/pdf></embed>
						</div>
						<div class="clr"></div>
					</div>
					<div class="content-section-2 hide content-section-container" >
						<div class="left-side first-child-div">
							<div id="content-1-1" class="bottom-left content" >
								<?php echo stripslashes($claim); ?>
							</div>
						</div>
						<div class="lineSeperator" id="lineSeperator2"></div>
						<div class="right-side" id="right-scroll" style="height: 50% !important;">
							<?php echo stripslashes($description); ?>
						</div>
						<div class="clr"></div>
					</div>
					<div class="content-section-3 hide content-section-container" >
						<div class="left-side first-child-div" id="claim1">
							<div id="content-3" class="bottom-left content" >
								<?php echo (empty($claim_1))?stripslashes($claim):stripslashes($claim_1); ?>
							</div>
						</div>
						<div class="lineSeperator-group" id="lineSeperator3"></div>
						<div class="left-side" id="description1">
							<div id="content-4" class="bottom-left content" >
								<?php echo (empty($description_1))?stripslashes($description):stripslashes($description_1); ?>
							</div>
						</div>
						<div class="lineSeperator-group" id="lineSeperator4"></div>
						<div class="right-side" id="pdf1">
							<embed src="
							<?php 
							//echo $pdfLink.'#page=2'; /*kashish*/
							    if(substr($pdfLink, 0, 4)=='/fdd')
								{
									echo "https://pdfaiw.uspto.gov/".$pdfLink;
								}
								else
								{
									echo $pdfLink;
								}
							?>" 
							width="100%" height="91%" type=application/pdf></embed>
						</div>
						<div class="clr"></div>
					</div>
				</div>
				<div class="clr"></div>
				<?php
					if(!empty($saveToDb))
					{
						$objPatents->content		=	$content;
						$objPatents->pdfLink		=	$pdfLink;
						$objPatents->claim			=	$claim;
						$objPatents->description	=	$description;
						$objPatents->espacenetUrl	=	$eUrl;
						$objPatents->patentFamily	=	serialize($patentFamily);
						$objPatents->insertPatent();
					}
				?>
			<?php elseif(empty($searchCode)): ?>
				<div class="wrapper-home">
					<div class="search-logo">
						<div class="logo">
							<div class="logoInnerContainer">
								<a href="<?php echo URL; ?>"><img src="images/home/logo.png" /></a>
							</div>
						</div>
						<form method="get" id="frontPageSearch">
							<div class="form-box">
							<div class="selectFieldContainer">
								<select class="select_patent" name="patent_code" required style=";">
									<?php
										$objReflection		=	new ReflectionClass ('PatentTypes');
										$patentTypeResult	=	$objReflection->getConstants();
										$val= 1;
										foreach($patentTypeResult as $val)
										{
											$count++;
											echo '<option style="padding:2px;" val="'.$count.'" value="'.$val.'" >'.ucWords($val).'</option>';
										}
									?>
								</select>
							</div>
							<div class="select_arrow">
								<div class="up_arrow"><img src="images/top.jpg"></div>
								<div class="down_arrow"><img src="images/bottom.jpg"></div>
							</div>
							<div class="inputFieldContainer">
								<input id="search-input" name="patent_id" type="text" placeholder="Publication Number" required>
							</div>
							<input type="hidden" name="msearch" value="1" />
							</div>
							<input type="submit" value="Search" />
						</form>
						
					</div>
				</div>
			<?php else: ?>
				<h2>Result Not Found</h2>
			<?php endif; ?>
		</div>
		<ul class='highlight-menu'>
		  <li data-action = "first">First thing</li>
		  <li data-action = "second">Second thing</li>
		  <li data-action = "third">Third thing</li>
		</ul>
		
		<div class="bg-overlay"></div>
		<!-- <div class="login-form-container">
			<div class="popup-header">
            <div class="close-popup">X</div>
            	<div class="popup-title">Login</div>
                
            </div>
            <form name="login-form" class="login-form">
				<div class="input-container">
					<input name="email" type="text" placeholder="Enter your email" />
					<label class="error hide-div"></label>
				</div>
				<div class="input-container">
					<input name="password" type="password" placeholder="Enter password" />
					<label class="error hide-div"></label>
					
				</div>
				
				<button id="login-button">Login</button> 
				<a href="#" class="register-now-link">Not Registered? Register Now.</a>
			</form>
			<a href="#" class="forgot-password-link">Forgot Password</a>
			<div class="error-box"></div>
		</div> -->
		<div class="forgot-pwd-form-container">
			<div class="popup-header">
            <div class="close-popup">X</div>
            	<div class="popup-title">Forgot Password</div>
            </div>
            <form name="forgot-pwd-form" class="forgot-pwd-form">
				<div class="input-container">
					<input name="email" type="text" placeholder="Enter your email" />
					<label class="error hide-div"></label>
				</div>
				<button id="forgot-pwd-button">Get Password Link</button>
			</form>
			<a href="#" class="login-now-link">Back to Login.</a>
			<div class="error-box"></div>
		</div>
		<div class="register-form-container">
        	<div class="popup-header">
            <div class="close-popup">X</div>
            	<div class="popup-title">Register</div>
                
            </div>
			<form name="register-form" class="register-form">
				<div class="input-container">
					<input name="firstName" type="text" placeholder="Enter first name" />
					<label class="error hide-div"></label>
				</div>
				<div class="input-container">
					<input name="lastName" type="text" placeholder="Enter last name" />
					<label class="error hide-div"></label>
				</div>
				<div class="input-container">
					<input name="email" type="text" placeholder="Enter your email" />
					<label class="error hide-div"></label>
				</div>
				<div class="input-container">	
					<input name="password" type="password" placeholder="Enter password" />
					<label class="error hide-div"></label>
				</div>
				<div class="input-container">
					<input name="confirmPassword" type="password" placeholder="Confirm password" />
					<label class="error hide-div"></label>
				</div>
				<button id="register-button">Register</button> <a href="#" class="already-registered-now-link">Already registered? Login Here.</a>
			</form>
			<div class="error-box"></div>
		</div>
		
		<div class="message-box-container">
         <div class="close-popup">X</div>
			<div class="message-box">
            You are sucessfully logged in. Please wait for a moment.
			</div>
		</div>
		<div class="forget_password_form" style="display: none">
			<form name="forget-password-form" class="forgot-pwd-form">
				<div class="input-container">
					<h2>Forgot Password</h2>
					<input name="email" type="text" placeholder="Enter your email" />
					<label class="error hide-div"></label>
				</div>
				<div class="btn-forget">
					<button type="submit" id="forgot-pwd-button">Get Password Link</button>
					<button id="back_to_login">Back to Login</button>
				</div>
				<div class="error-box"></div>
			</form>
		</div>
<div class="login-form-start" style="display: none">
		<form id="login-form" method="POST" class="login-form">
			<h3>LOGIN</h3>
			<div class="input-container">
				<input name="email" id="user_email" type="text" placeholder="Enter your email id" required/>
				<label class="error hide-div"></label>
			</div>
			<div class="form-group input-container">
				<input type="password"id="user_passeord" class="form-control" id="exampleInputPassword1" name="password" placeholder="Password" required>
				<label class="error hide-div"></label>
			</div>
			<div class="checkboxinput-container">
				<div class="checkbox">
					<input type="checkbox" id="showPassword" value="0" />
					<label for="showPassword">Show password</label>
				</div>
				|
				<a href="#" class="forgot_password" style="padding-top: 2px;">Forgot password</a>
			</div>
			<button type="submit" id="login-button" class="btn btn-sm btn-primary">Login</button>
		</form>
<h6 class="register-text">or</h6>
<div class="Divider">
    <div class="Divider-text">Not registerd?</div>
</div>
<!-- register_in_form -->

<form name="register-form" class="register-form">
<h3>REGISTER HERE</h3>
<div class="error-box"></div>
    <div class="input-container">
		<input name="firstName" id="firstName" type="text" placeholder="Enter your first name" required />
		<label class="error hide-div"></label>
	</div>
	<div class="input-container">
		<input name="lastName"  id="lastName" type="text" placeholder="Enter your last name" required/>
		<label class="error hide-div"></label>
	</div>
	<div class="input-container">
		<input name="email" id="email" type="text" placeholder="Enter your Email Id" required/>
		<label class="error hide-div"></label>
	</div>
	<div class="input-container">	
		<input name="password" id="password" type="password" placeholder="Enter password" required/>
		<label class="error hide-div"></label>
	</div>
	<div class="input-container">
		<input name="confirmPassword" id="confirmPassword" type="password" placeholder="Confirm password" required/>
		<label class="error hide-div"></label>
	</div>
	<button id="register-button">Register Now</button> 
</form>
</div>


<footer class="coppywrite-final">
	<p class="coppywrite-final-left"> <b>Copyright</b> iCuerious LLP. All Rights Reserved © 2012-2020</p>
	<p class="coppywrite-final-right">iCueView is a proprietary patent reviewing tool of iCuerious Research Services LLP &nbsp; &nbsp;
		<a href="https://www.icuerious.com/" target="new">www.icuerious.com</a>
		<!-- <a href="<?php echo URL; ?>docs/iCueView_User_Guide.pdf" target="new">User Guide</a> -->
	</p>
</footer>
	</body>

	
<script src="https://code.jquery.com/jquery-1.9.1.js"></script>
<script src="https://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
<script src="js/clipboard.min.js"></script>
<script src="https://swisnl.github.io/jQuery-contextMenu/dist/jquery.contextMenu.js" type="text/javascript"></script>
<script type="text/javascript" src="js/color-picker.min.js"></script>
<script src="js/main.js"></script>
<!-- kashish--->
<script src="js/pdfobject.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>




<script src="https://code.jquery.com/jquery-1.9.1.js"></script>
<script src="https://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
<script src="js/clipboard.min.js"></script>
<script src="https://swisnl.github.io/jQuery-contextMenu/dist/jquery.contextMenu.js" type="text/javascript"></script>
<script type="text/javascript" src="js/color-picker.min.js"></script>

</html>
<script>
	$('.login-form-start .login-form').on('submit', function () {
		var errorCounter	=	0;
		var testEmail		=	/^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
		var email			=	$('.login-form').find('input[name=email]');
		var password		=	$('.login-form').find('input[name=password]');
		
		
		if(email.val().trim() == '')
		{
			email.parent('.input-container').find('.error').html('Please enter email address').show();
			errorCounter++;
		}
		else if(!testEmail.test(email.val()))
		{
			email.parent('.input-container').find('.error').html('Please enter a valid email address').show();
			errorCounter++;
		}
		else
			email.parent('.input-container').find('.error').html('').hide();
		
		if(password.val().trim() == '')
		{
			password.parent('.input-container').find('.error').html('Please enter password').show();
			errorCounter++;
		}
		else
			password.parent('.input-container').find('.error').html('').show();
			console.log(errorCounter);
			
			if(errorCounter == 0)
			{
				$(".login-form-start").hide();
				var url_new = "../ajax/manageUsers.php";
				console.log(url_new);
				$.ajax({
					type: "POST",
					url: "ajax/manageUsers.php",
					data: $(this).serialize()+'&action=login',
					cache: false,
					dataType:'json',
					success: function(response)
					{
						if(response.status == 'ok')
						{
							if(savePage == 1)
							{
								$('.save-changes').trigger('click');
							}
							// <a class="save-changes"><img src="images/home/file.png"/></a>
							console.log("777");
							// $(".save-changes").append("<img src='images/home/file.png'/>")
							$('#user-options').html('<a class="save-changes"></a><a class="logout-user"><img src="images/home/logout.png"></a>');
							reloadPageContent();
							$('.login-form-container').hide();
							$('.message-box').html(response.message);
							$('.message-box-container').show();
							hideMessagePopUp();
							window.location.reload();
						}
						else if(response.status == 'error')
						{
							$('.login-form-container .error-box').html(response.message);
						}
					} 
				});
			}
		return false;
	});
</script>
