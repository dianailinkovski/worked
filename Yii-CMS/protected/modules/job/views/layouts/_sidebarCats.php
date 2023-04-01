<nav id="contextMenu">
	<header>
		<h1 class="sectionName"><?php echo Yii::t('jobModule.common', 'Secteurs d\'emploi'); ?></h1>
	</header>
	
	<ul id="sidebar_menu">
		<?php foreach ($categories as $category): ?>
		
			<li<?php echo ($currentCategory == $category->name_url) ? ' class="current"' : ''; ?>><?php echo CHtml::link(CHtml::encode($category->name), array('index', 'category'=>$category->name_url)); ?></li>
			
		<?php endforeach; ?>
	</ul>
</nav>