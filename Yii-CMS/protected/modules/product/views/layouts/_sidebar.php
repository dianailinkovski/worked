<header class="sidebar-title visible-md visible-lg">
	<h1 class="page-title"><?php echo isset($currentCategory) ? CHtml::encode($currentCategory->name) : Yii::t('productModule.common', 'Promotions'); ?></h1>
</header>
	
<nav id="sidebar-nav">
	<header>
		<h1 class="sectionName"><?php echo Yii::t('productModule.common', 'Catégories'); ?></h1>
	</header>
	
	<ul id="sidebar-menu">
		<?php
		foreach ($categories as $category):
			?>
		
			<li<?php echo (isset($currentCategory) && $currentCategory->id == $category->id) ? " class='current'" : ""; ?>>
				<a href="<?PHP echo $this->createUrl('listing', array('c'=>$category->name_url)); ?>"><?php echo CHtml::encode($category->name); ?></a>
			</li>
			
			<?php
		endforeach;
		?>
	</ul>
	
</nav>