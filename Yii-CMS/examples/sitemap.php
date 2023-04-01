//================================================================================
// CSS
//================================================================================

/* sitemap */
#sitemap {
	font-size:1.4em;
}
	#sitemap ul.root {
		padding-left:0;
		list-style-image:none;
		list-style:none;
	}
	#sitemap ul {
		margin-top:10px;
		list-style-image:url(../images/layout/bullet.jpg);
	}
		#sitemap ul li {
			font-size:inherit;
		}
		#sitemap ul.root li.level_1 {
			padding-top:15px;
		}
			#sitemap li.level_1 > span,
			#sitemap li.level_1 > a{
				font-size:1.4em;
				line-height:1.8em;
			}

//================================================================================
// Controller
//================================================================================

<?php
public function actionSitemap()
{
	$aliases = CmsAlias::model()->findAll(array('order'=>'lft ASC', 'with'=>'routes', 'together'=>true));
	$this->render('sitemap', array('aliases'=>$aliases));
}
?>

//================================================================================
// View file
//================================================================================

<?php
$this->breadcrumbs = Helper::breadcrumbsFromAlias();
$this->pageTitle = Helper::titleFromBreadcrumbs();

Yii::app()->clientScript->registerMetaTag('sitemap-meta', 'description');
?>

<article>
		
	<header>
		<h3 class="page-title">Sitemap</h3>
	</header>
	
	<section id="sitemap">			
		<div>
			<ul class="root">
			
				<li class="level_1"><a href="/">Home</a></li>
				
				<?php 
				$level = 1;
				$parentLevel = 1;
				$path = array();
				
				foreach ($aliases as $alias): 
					if ($alias->level == 1):
						continue;
					endif;

					if ($alias->level <= $level && $alias->level <= $parentLevel): 
						$parentLevel = $alias->level-1;
						array_pop($path);
						?>
						
					</li>
					
						<?php
						for ($i = 0; $i < $level-$alias->level; $i++):
							array_pop($path);
							?>
							
						</ul>
					</li>
					
						<?php
						endfor;
						
					elseif ($alias->level == $level): 
						array_pop($path);
						?>
						
					</li>
						
					<?php 
					elseif ($alias->level > 2): 
						$parentLevel = $alias->level-1;
						?>
						
						<ul>
						
					<?php 
					endif;
					
					$path[] = $alias->alias;
					$level = $alias->level;
					?>
					
					<li class="level_<?php echo ($level-1); ?>">
					
					<?php if (empty($alias->routes)): ?>
					
					<span><?php echo CHtml::encode($alias->title); ?></span>
					
					<?php 
					else: 
						$url = '/';
						if (Yii::app()->languageManager->multilang):
							$url .= Yii::app()->language.'/';
						endif;
						
						foreach ($path as $pathElem):
							$url .= $pathElem.'/';
						endforeach;
						
						substr($url, 0, -1);
						
						echo CHtml::link(CHtml::encode($alias->title), $url); 
					endif; 
					?>

				<?php endforeach; ?>
				
				<?php if ($level > 1): ?>
						</li>
				<?php endif; ?>
				
				<?php for ($i = 0; $i < $level-2; $i++): ?>
					</ul>
				</li>
				<?php endfor; ?>
			</ul>
		</div>
	</section>
	
</article>