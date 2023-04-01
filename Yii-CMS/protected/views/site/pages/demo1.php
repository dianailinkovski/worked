<?PHP
$this->pageTitle = "Demo 1 | ".Yii::app()->name;
$this->breadcrumbs[] = "Demo 1";
?>

<div class="row">

	<div id="column1" class="col-sm-8">
	
		<article class="main mod-content mod-type-detail">
		
			<header>
				<h1 class="page-title">Titre de la page</h1>
			</header>
			
			<nav id="content-index">
				<ul>				
					<li><a data-animate="true" href="#bloc1" title="">Titre d'un bloc texte</a></li>	
					<li><a data-animate="true" href="#bloc2" title="">Titre d'un bloc texte</a></li>
				</ul>
			</nav>
			
			<section id="bloc1" class="section-bloc bloc-editor">
			
				<h1>Titre d'un bloc texte</h1>
				   
				<p><strong>Pellentesque habitant morbi tristique</strong> senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. <em>Aenean ultricies mi vitae est.</em> Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, <code>commodo vitae</code>, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. <a href="#">Donec non enim</a> in turpis pulvinar facilisis. Ut felis.</p>
				
				<h2>Header Level 2</h2>
						   
				<ol>
				   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
				   <li>Aliquam tincidunt mauris eu risus.</li>
				</ol>
				
				<blockquote><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus magna. Cras in mi at felis aliquet congue. Ut a est eget ligula molestie gravida. Curabitur massa. Donec eleifend, libero at sagittis mollis, tellus est malesuada tellus, at luctus turpis elit sit amet quam. Vivamus pretium ornare est.</p></blockquote>
				
				<h3>Header Level 3</h3>
				
				<ul>
				   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
				   <li>Aliquam tincidunt mauris eu risus.</li>
				</ul>
									
			</section>
			
			<section id="bloc2" class="section-bloc bloc-editor">
			
				<h1>Titre d'un bloc texte</h1>
				
				<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>
					
				<dl>
				   <dt>Definition list</dt>
				   <dd>Consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna 
				aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea 
				commodo consequat.</dd>
				   <dt>Lorem ipsum dolor sit amet</dt>
				   <dd>Consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna 
				aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea 
				commodo consequat.</dd>
				</dl>
				
				<table>
					<thead>
						<tr>
							<th>Header 1</th>
							<th>Header 2</th>
							<th>Header 3</th>
							<th>Header 4</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>A</td>
							<td>B</td>
							<td>C</td>
							<td>D</td>
						</tr>
						<tr>
							<td>E</td>
							<td>F</td>
							<td>G</td>
							<td>H</td>
						</tr>
					</tbody>
				</table>
									
			</section>
			
			<section id="bloc3" class="section-bloc bloc-timeline">
			
				<div class="timeline">
				  <dl>
					  <dt>Avril 2014</dt>
					  <dd class="pos-right clearfix">
						  <div class="circ"></div>
						  <div class="time">12 avril</div>
						  <div class="events">
							  <div class="pull-left">
								  <img src="/images/layout/placeholder-600x400.jpg" style="max-width:5em;" class="events-object img-rounded">
							  </div>
							  <div class="events-body">
								  <h4 class="events-heading">Bootstrap</h4>
								  <p>Raw denim you probably haven't heard of them jean shorts Austin. Nesciunt tofu stumptown aliqua, retro synth master cleanse. Mustache cliche tempor, williamsburg carles vegan helvetica.</p>
							  </div>
						  </div>
					  </dd>
					  <dd class="pos-left clearfix">
						  <div class="circ"></div>
						  <div class="time">10 avril</div>
						  <div class="events">
							  <div class="pull-left">
								   <img src="/images/layout/placeholder-600x400.jpg" style="max-width:5em;" class="events-object img-rounded">
							  </div>
							  <div class="events-body">
								  <h4 class="events-heading">Bootflat</h4>
								  <p>Raw denim you probably haven't heard of them jean shorts Austin. Nesciunt tofu stumptown aliqua, retro synth master cleanse. Mustache cliche tempor, williamsburg carles vegan helvetica.</p>
							  </div>
						  </div>
					  </dd>
					  
					  <dt>Septembre 2014</dt>
					  <dd class="pos-right clearfix">
						  <div class="circ"></div>
						  <div class="time">15 septembre</div>
						  <div class="events">
							  <div class="pull-left">
								   <img src="/images/layout/placeholder-600x400.jpg" style="max-width:5em;" class="events-object img-rounded">
							  </div>
							  <div class="events-body">
								  <h4 class="events-heading">Flat UI</h4>
								  <p>Raw denim you probably haven't heard of them jean shorts Austin. Nesciunt tofu stumptown aliqua, retro synth master cleanse. Mustache cliche tempor, williamsburg carles vegan helvetica.</p>
							  </div>
						  </div>
					  </dd>
					  <dd class="pos-left clearfix">
						  <div class="circ"></div>
						  <div class="time">8 mars</div>
						  <div class="events">
							  <div class="pull-left">
								   <img src="/images/layout/placeholder-600x400.jpg" style="max-width:5em;" class="events-object img-rounded">
							  </div>
							  <div class="events-body">
								  <h4 class="events-heading">UI design</h4>
								  <p>Raw denim you probably haven't heard of them jean shorts Austin. Nesciunt tofu stumptown aliqua, retro synth master cleanse. Mustache cliche tempor, williamsburg carles vegan helvetica.</p>
							  </div>
						  </div>
					  </dd>

				  </dl>
			  </div>
			
			</section>
			
		</article>
		
	</div>

	<div id="column2" class="col-sm-4">
	
		<nav id="context-menu" role="menu">
		
			<header>
				<h1 data-toggle="collapse" data-target="#context-menu-items" aria-expanded="true" aria-controls="context-menu-items">
					<span class="glyphicon glyphicon-th-list"></span>Section parent
				</h1>
			</header>
			
			<ul id="context-menu-items" class="collapse">
				<li><a href="#">Autre page de la même section</a></li>
				<li><a href="#">Autre page de la même section</a></li>
				<li><a href="#">Autre page de la même section</a></li>
				<li><a href="#">Autre page de la même section</a></li>
			</ul>
		</nav>
	
	</div>

</div>