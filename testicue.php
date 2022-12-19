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
		echo '<script>console.log("'.$pdflink.'Your stuff here")</script>';	
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
		<link rel="icon" href="images/logo/favicon2.png" type="image/x-icon">
		
		<link href="style.css" rel="stylesheet" type="text/css" />
		<link href="css/color-picker.min.css" rel="stylesheet" type="text/css" />
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
		 <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
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
			<div class="wrapper1">
				<div class="top-left">
					<?php if(!empty($searchCode)): ?>
						<div class="logo">
							<a href="<?php echo URL; ?>"><img src="images/logo/logo.png" /></a>
						</div>
						<div class="search-button">
								<form method="get" action="<?php echo URL; ?>">
									<div class="selectFieldContainerTop" id="selectContainerTop">
										<select name="patent_code" required>
											<?php
												$objReflection		=	new ReflectionClass ('PatentTypes');
												$patentTypeResult	=	$objReflection->getConstants();
												foreach($patentTypeResult as $val)
												{
													if($patentCode == $val)
														echo '<option value="'.$val.'" selected>'.ucWords($val).'</option>';
													else
														echo '<option value="'.$val.'" >'.ucWords($val).'</option>';
												}
											?>
										</select>
									</div>
									<div class="inputFieldContainerTop">
										 <input id="search-input" name="patent_id" type="text" value="<?php echo ($_GET['patent_code'] == PatentTypes::ALL)?$_GET['patent_id']:$searchCode; ?>" required>
										 <img src="images/search-ico.png" >
									 </div>
									 <input type="hidden" name="msearch" value="1" />
								</form>
						</div>
					<?php endif; ?>
					<div class="clr"></div>
				</div>
				<div class="top-left text-right" id="user-options">
					<?php if(isset($_SESSION['user']) && !empty($_SESSION['user'])): ?>
						<a class="save-changes">Save</a>
						<a class="logout-user">Logout</a>
					<?php else: ?>
						<a class="login-link">Login</a>
						<a class="register-link">Register</a>
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
				<?php if(isset($_SESSION['patentSearchArray'][$_GET['patent_code']]) && count($_SESSION['patentSearchArray'][$_GET['patent_code']]) > 1): ?>
					<div class="searchList">
					<div class="searchList-box">
						<div class="toggleSearchList"><img src="images/double-arrows.png" /></div>
						<ul>
							<?php foreach($_SESSION['patentSearchArray'][$_GET['patent_code']] as $patentId): ?>
								<li><a href="<?php echo URL; ?>?patent_code=<?php echo $_GET['patent_code']; ?>&patent_id=<?php echo $patentId; ?>"><?php echo $patentId; ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div></div>
				<?php endif; ?>
				<div class="main-content">
					<div class="top-div">
						<div class="top-left">
							<ul class="left-sec">
								<li><a href="<?php echo URL.'download.php?file='.$pUrl.'&name='.$searchCode; ?>" target="_blank" title="Download PDF">
								<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20">
                                <path fill="#000000" d="M14.853 9.647c-0.195-0.195-0.512-0.195-0.707 0l-4.146 4.146v-11.293c0-0.276-0.224-0.5-0.5-0.5s-0.5 0.224-0.5 0.5v11.293l-4.146-4.146c-0.195-0.195-0.512-0.195-0.707 0s-0.195 0.512 0 0.707l5 5c0.098 0.098 0.226 0.146 0.354 0.146s0.256-0.049 0.354-0.147l5-5c0.195-0.195 0.195-0.512-0-0.707z"></path>
                                <path fill="#000000" d="M17.5 19h-16c-0.827 0-1.5-0.673-1.5-1.5v-2c0-0.276 0.224-0.5 0.5-0.5s0.5 0.224 0.5 0.5v2c0 0.276 0.224 0.5 0.5 0.5h16c0.276 0 0.5-0.224 0.5-0.5v-2c0-0.276 0.224-0.5 0.5-0.5s0.5 0.224 0.5 0.5v2c0 0.827-0.673 1.5-1.5 1.5z"></path>
                                </svg>
                                </a></li>
								<li><a href="<?php echo $gUrl; ?>" target="_blank" title="Google Patents">G</a></li>
								<li><a href="<?php echo (!empty($eUrl))?$eUrl:$eUrlDefault; ?>" target="_blank" title="EspaceNet">E</a></li>
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
												<li><a href="<?php echo 'http://patft.uspto.gov/netacgi/nph-Parser?Sect2=PTO1&Sect2=HITOFF&p=1&u=/netahtml/PTO/search-bool.html&r=1&f=G&l=50&d=PALL&RefSrch=yes&Query=PN/'.$searchCode.'/'?>" target="_blank" title="Uspto">U</a></li> 
											 <?php
											 }
											 elseif(strlen($searchCode)==10)
										     {
											  $leftpartstr =substr($searchCode, 0, 4);	
                                              $rightpartstr=substr($searchCode, 4, 10);	
                                              $completestr=	$leftpartstr."0".$rightpartstr;									  
									          ?>
											  <li><a href="<?php echo "http://appft.uspto.gov/netacgi/nph-Parser?Sect1=PTO1&Sect2=HITOFF&d=PG01&p=1&u=%2Fnetahtml%2FPTO%2Fsrchnum.html&r=1&f=G&l=50&s1='".$completestr."'.PGNR.&OS=DN/".$completestr."&RS=DN/".$completestr ?>" target="_blank" title="Uspto">U</a></li>	 
											 <?php
											 }
											 else
											 {
											 ?>
											  <li><a href="<?php echo "http://appft.uspto.gov/netacgi/nph-Parser?Sect1=PTO1&Sect2=HITOFF&d=PG01&p=1&u=%2Fnetahtml%2FPTO%2Fsrchnum.html&r=1&f=G&l=50&s1='".$searchCode."'.PGNR.&OS=DN/".$searchCode."&RS=DN/".$searchCode ?>" target="_blank" title="Uspto">U</a></li>		 
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
							
							<span class="togale">
								<div class="togale-show">	<img src="images/togal-2.jpg" /></div>
								<div class="togale-hide">	<img src="images/togal-white.jpg" /></div>
							</span>
							<ul class="right-sec">
							
								<li class="togale-claim"><a id="compare" title="Claims in view of Description">C|D</a></li>
								<li class="togale-claim"><a id="compare2" title="Claims and Description in view of Diagram">C|D|P</a></li>
								<li><a href="#contentTop" class="contentTop scrollTags"><img src="images/up arrow.png" alt="up arrow" title="Top" class="uparrow"></a></li>
								<li><a href="#claims" title="Claims" class="scrollTags">C</a></li>
								<li><a href="#description" title="Description" class="scrollTags">D</a></li>
							</ul>
							<!--kash--->
							<div style="clear:both"></div>
							<div style="margin:0 5px;"><a href="https://portal.uspto.gov/pair/PublicPair" target="_blank" style="text-decoration:none;color: #368EBE;">USPTO PUBLIC PAIR</a></div>
							<!---->
						</div>
						
						
					</div>
					
					<div class="content-section content-section-container active" id="content-section-1">
						<div class="left-side first-child-div">
							<div id="content-2" class="bottom-left content" >
								<?php echo stripslashes($content); ?>
							</div>
						</div>
						<div class="lineSeperator" id="lineSeperator1"></div>
						<div class="right-side">
							<embed src="<?php echo $pdfLink; ?>" width="100%" height="100%" type=application/pdf></embed>
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
						<div class="right-side">
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
							<embed src="<?php echo $pdfLink; ?>" width="100%" height="100%" type=application/pdf></embed>
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
								<a href="<?php echo URL; ?>"><img src="images/logo/logoHome.jpg" /></a>
							</div>
						</div>
						<form method="get" id="frontPageSearch">
							<div class="selectFieldContainer">
								<select name="patent_code" required>
									<?php
										$objReflection		=	new ReflectionClass ('PatentTypes');
										$patentTypeResult	=	$objReflection->getConstants();
										foreach($patentTypeResult as $val)
										{
											echo '<option value="'.$val.'" >'.ucWords($val).'</option>';
										}
									?>
								</select>
							</div>
							<div class="inputFieldContainer">
								<input id="search-input" name="patent_id" type="text" placeholder="Publication Number..." required>
								<input type="submit" value="Search" />
							</div>
							<input type="hidden" name="msearch" value="1" />
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
		<div class="login-form-container">
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
		</div>
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

<footer class="coppywrite-final">
	<p class="coppywrite-final-left"> <b>Copyright</b> iCuerious LLP. All Rights Reserved © 2012-2017</p>
	<p class="coppywrite-final-right">iCueView is a proprietary patent reviewing tool of iCuerious Research Services LLP |
		<a href="http://www.icuerious.com/" target="new">About |</a>
		<a href="<?php echo URL; ?>docs/iCueView_User_Guide.pdf" target="new">User Guide</a>
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
</html>
