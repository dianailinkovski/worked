<?php
$i = 0;
foreach ($blocs as $bloc):

	if ($bloc[0]->title_anchor):
		$blocTitle = CHtml::encode($bloc[0]->title);
		
		if ($i == 0):
		?>
		
<nav id="content-index">
	<ul>
		<?php endif; ?>
		
			<li><a data-animate="true" href="#bloc-<?php echo CHtml::encode($bloc[0]->title_url); ?>" title="<?php echo $blocTitle; ?>"><?php echo $blocTitle; ?></a></li>
		<?php 
		$i++;
	endif;
endforeach;

if ($i > 0): 
?>
	</ul>
</nav>
<?php endif;

foreach ($blocs as $bloc): ?>
	
<section class="section-bloc bloc-<?php echo $bloc[0]->bloc_type; ?>" id="bloc-<?php echo CHtml::encode($bloc[0]->title_url); ?>">

	<?php if ($bloc[0]->title_page): ?>
	<h1><?php echo CHtml::encode($bloc[0]->title); ?></h1>
	<?php endif; ?>
	
	<?php $this->controller->renderPartial('application.components.blocs.'.$bloc[2].'.views.bloc', array('bloc'=>$bloc[1])); ?>
	
</section>

<?php endforeach; ?>