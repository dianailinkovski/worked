<!doctype html>
<html lang="<?php echo Yii::app()->language; ?>" id="top">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="author" content="Gnetix Solutions Web + Mobiles">
	<meta name="robots" content="index, follow">
	
	<?PHP if (isset(Yii::app()->params['googleFont'])) : ?>
	<link href='<?PHP echo Yii::app()->params['googleFont']; ?>' rel='stylesheet' type='text/css'>
	<?PHP endif; ?>
	
	<link href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.png" rel="shortcut icon">
	
	<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap/bootstrap.min.css" rel="stylesheet">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
		<script src="<?php echo Yii::app()->request->baseUrl; ?>/javascript/html5shiv.js"></script>
		<script src="<?php echo Yii::app()->request->baseUrl; ?>/javascript/respond.min.js"></script>
    <![endif]-->
	
	<link rel="profile" href="http://microformats.org/profile/hcard">
	
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/framework.css">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css">
	
</head>
<body id="page-<?php echo isset(Yii::app()->cms->currentAlias->keyword) ? Yii::app()->cms->currentAlias->keyword : preg_replace('/[^A-Za-z0-9_\-\.]/', '-', Yii::app()->controller->route); ?>">
	
	<!-- HEADER -->
	<header id="site-header">
		
		<div class="container-fluid">
				
			<div class="row">
			
				<div class="col-sm-4">
				
					<!-- LOGO -->
					<div id="logo">
						<h1>
							<span>Site Name</span>
							<a href="<?PHP echo $this->createUrl('/site/index'); ?>" title="Site Name">
								<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo-site-name.png" alt="Site Name">
							</a>
						</h1>
					</div>
					
				</div>
				
				<div class="col-sm-8">
					
					<!-- SECONDARY MENU -->
					<ul id="top-nav" class="nav navbar-nav navbar-right">
						<li class="hidden-xs"><a href="#" title="">Item 1</a></li>
						<li class="hidden-xs"><a href="#" title="">Item 2</a></li>
						<li class="hidden-xs"><a href="#" title="">Item 3</a></li>
					</ul>
				
				</div>
				
			</div>
			
		</div>
				
		<div id="main-nav-wrapper">
		
			<!-- MAIN MENU -->
			<nav id="main-navbar" class="navbar navbar-default" role="navigation">
				
				<div class="container-fluid">
			
					<div class="navbar-header">
						<div class="navbar-header">
						  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-nav">
							<span class="glyphicon glyphicon-align-justify"></span>&nbsp;<span >Menu</span>
						  </button>
						</div>
					</div>
					
					<div class="collapse navbar-collapse" id="main-nav">
						<ul class="nav navbar-nav">
							<li class="visible-xs-block"><a href="#">Item 1</a></li>
							<li class="visible-xs-block"><a href="#">Item 2</a></li>
							<li class="visible-xs-block"><a href="#">Item 3</a></li>
							<li><a href="#">Item 4</a></li>
							<li><a href="#">Item 5</a></li>
							<li><a href="#">Item 6</a></li>
							<li><a href="#">Item 7</a></li>
							<li><a href="#">Item 8</a></li>
							<li><a href="#">Item 9</a></li>
						</ul>
					</div>
		
				</div>
			
			</nav>
			
		</div>

	</header>
	<!-- /HEADER -->
	
	
	<!-- CONTENT -->
	<?php echo $content; ?>
	<!-- /CONTENT -->
	
	
	<!-- FOOTER -->
	<footer id="site-footer">
	
		<div class="container-fluid">
		
			<div class="row">
				
				<!-- ADDRESS -->
				<div class="col-md-6">
				
					<section id="footer-contact" class="section-bloc bloc-contact">

						<dl class="vcard">
							<dt class="fn org" title="Arianne Phosphate Inc.">Nom entreprise</dt>
							<dd class="adr">
								<span class="type">Succursale</span><br>
								<span class="street-address">Adresse</span><br>
								<span class="locality">Ville</span>, <span class="region">Province</span><br>
								<span class="postal-code">Code postal</span>
							</dd>
							<dd class="tel" title="Téléphone">Label : <span class="value">999-999-9999</span></dd>
							<dd class="fax" title="Télécopieur">Label  : <span class="value">999-999-9999</span></dd>
							<dd>
								<script type="application/javascript">document.write('<a class="email" href="mailto:infonospam@business.com" title="Contactez-nous par courriel">infonospam@business.com</a>'.replace(/nospam/g, ''));</script>
							</dd>
						</dl>
						
					</section>
					
				</div>
				
				
				<div class="col-md-6">
					
					
					
				</div>
			
		</div>
	
	</footer>
	<!-- /FOOTER -->
	
	
	<!-- COPYRIGHT -->
	<div id="footer-copy">
		
		<div class="container-fluid">
	
			<div class="row">
			
				<div class="col-sm-7" id="copy">
					<p>&copy;<?PHP echo date("Y"); ?> Nom du site.  <span><?PHP echo Yii::t('layout', 'Tous droits réservés.'); ?></span></p>
				</div>
				
				<div class="col-sm-5" id="credits">
					<p><?PHP echo Yii::t('layout', 'Conçu et développé par'); ?> <a target="_blank" title="<?PHP echo Yii::t('layout', 'Conçu et développé par'); ?> Web Solutions" href="#">Web Solutions</a></p>
				</div>
			
			</div>
		
		</div>
	
	</div>
	<!-- /COPYRIGHT -->
	
	<a id="btn-btt" class="glyphicon glyphicon-chevron-up" data-animate="true" href="#top" title="<?PHP echo Yii::t('layout', 'Haut de la page'); ?>"></a>
	
</body>
</html>