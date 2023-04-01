<?PHP
$this->pageTitle = Yii::app()->name;
Yii::app()->clientScript->registerMetaTag(Yii::t('layout', 'accueil_meta_description'), 'description');
?>
	
<main id="content" class="homepage" role="main">

	<!-- SECTION A -->
	<div id="section-a">
	
		<div class="container-fluid">
							
			<section class="section-bloc">
			
				<div class="row">
				
					<div class="col-md-7">
				
						<h1 class="page-title">Lorem ipsum dolor sit amet</h1>

						<p class="lead">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vestibulum pellentesque bibendum. Praesent elit erat, ornare at ligula nec, lobortis pulvinar tellus. Mauris ligula felis, tempor quis elit ac, finibus mollis diam.</p>
						
						<ul>
							<li><a href="<?php echo $this->createUrl('/site/page', array('view'=>'demo1')); ?>">Exemple de page de contenu (blocs)</a></li>
						</ul>
				
					</div>
				
					<div class="col-md-4 col-md-push-1">
	
						<p>Curabitur eget libero ac ex porttitor consectetur. Aliquam vel accumsan massa. Sed augue dui, auctor id tristique non, iaculis a lacus. Cras et ante leo. Ut gravida vulputate augue, at cursus neque posuere quis. Duis vel hendrerit augue. Nulla facilisi. Phasellus pharetra interdum tempus.</p>
				
					</div>
					
				</div>
				
			</section>
			
		</div>
	
	</div>
	
	
	<!-- SECTION B -->
	<div id="section-b">
	
		<div class="container-fluid">
	
			<section class="section-bloc">
			
				<h1 class="page-title">Nulla vitae turpis purus eget ullamcorper arcu.</h1>
				
				<p class="lead">Nam lacinia sed nunc vel blandit. Aliquam et tellus eget eros tempor hendrerit sed maximus turpis. Donec a massa dolor.</p>
				
				<p><a href="#" title="" class="btn btn-primary">En savoir plus</a></p>
				
				<div class="row">
				
					<div class="col-sm-6 col-md-3">
					
						<div class="thumbnail">
							<img src="/images/layout/placeholder-600x400.jpg" alt="...">
							<div class="caption">
								<h3>Thumbnail label</h3>
								<p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
								<p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
							</div>
						</div>
						
					</div>
					
					<div class="col-sm-6 col-md-3">
					
						<div class="thumbnail">
							<img src="/images/layout/placeholder-600x400.jpg" alt="...">
							<div class="caption">
								<h3>Thumbnail label</h3>
								<p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
								<p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
							</div>
						</div>
					
					</div>
					
					<div class="col-sm-6 col-md-3">
					
						<div class="thumbnail">
							<img src="/images/layout/placeholder-600x400.jpg" alt="...">
							<div class="caption">
								<h3>Thumbnail label</h3>
								<p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
								<p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
							</div>
						</div>
					
					</div>
					
					<div class="col-sm-6 col-md-3">
					
						<div class="thumbnail">
							<img src="/images/layout/placeholder-600x400.jpg" alt="...">
							<div class="caption">
								<h3>Thumbnail label</h3>
								<p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
								<p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
							</div>
						</div>
					
					</div>
				
				</div>
				
			</section>
			
		</div>
		
	</div>
	
</main>