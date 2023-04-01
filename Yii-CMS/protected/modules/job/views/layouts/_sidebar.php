<nav>
	<header>
		<h1 class="sectionName"><?php echo Yii::t('jobModule.common', 'Dans le même secteur...'); ?></h1>
	</header>
	
	<ul id="sidebar_menu">
		<?php
		foreach ($jobs as $job):
			if ($job->title != "Banque de candidatures"  || $job->title != "Application bank"):
			?>
		
			<li<?php echo ($currentJobId == $job->id) ? " class='current'" : ""; ?>>
				<a href="<?PHP echo $this->createUrl('detail', array('t'=>$job->title_url)); ?>"><?php echo CHtml::encode($job->title); ?></a>
			</li>
			
			<?php
			endif;
		endforeach;
		?>
	</ul>
	
</nav>