<?php
if (Yii::app()->cms->currentAlias->isLeaf()):
	$parent = Yii::app()->cms->currentAlias->parent()->find();
	$menuItems = $parent->children()->with('routes')->findAll(array('together'=>true));
	$header = CHtml::encode($parent->title);
else:
	$menuItems = Yii::app()->cms->currentAlias->children()->with('routes')->findAll(array('together'=>true));
	$header = CHtml::encode(Yii::app()->cms->currentAlias->title);
endif;
?>

<nav id="context-menu" role="menu">

	<?php if (!empty($header)): ?>
	<header>
		<h1 data-toggle="collapse" data-target="#context-menu-items" aria-expanded="true" aria-controls="context-menu-items">
			<span class="hidden">Menu contextuel de la section </span><span class="glyphicon glyphicon-th-list"></span><?php echo $header; ?>
		</h1>
	</header>
	<?php endif; ?>
	
	<ul id="context-menu-items"<?php echo (!empty($header)) ? ' class="collapse"' : ''; ?>>
		<?php 
		foreach ($menuItems as $alias):
			if (is_object($alias)):
			?>
			<li<?php echo ($alias->id == Yii::app()->cms->currentAlias->id ? ' class="current"' : ''); ?>>
				<?php echo empty($alias->routes) ? CHtml::link(CHtml::encode($alias->title), array('#'), array('class'=>'dropdown-toggle', 'data-toggle'=>'dropdown')) : CHtml::link(CHtml::encode($alias->title), array('/', 'keyword'=>$alias->keyword)); ?>
				<?php 
				$childrenAliases = $alias->children()->findAll();
				$i = 0;
				foreach ($childrenAliases as $childAlias):
					if (is_object($childAlias)):
						?>
						<?php if ($i == 0): ?><ul class="dropdown-menu"><?php endif; ?>
						<li>
							<?php echo empty($childAlias->routes) ? CHtml::encode($childAlias->title) : CHtml::link(CHtml::encode($childAlias->title), array('/', 'keyword'=>$childAlias->keyword)); ?>
						</li>
				<?php 
						$i++;
					endif;
				endforeach; ?>
				<?php if ($i > 0): ?></ul><?php endif; ?>
			</li>
		<?php 
			endif;
		endforeach; ?>
	</ul>
</nav>