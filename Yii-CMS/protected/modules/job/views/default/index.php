<?php
if (!isset($currentCategory)):
	$this->breadcrumbs = Helper::breadcrumbsFromAlias();
	$this->pageTitle = Helper::titleFromBreadcrumbs();
else:
	$this->breadcrumbs = Helper::breadcrumbsFromAlias(true);
	$this->pageTitle = Helper::titleFromBreadcrumbs();
endif;
?>

<article class="mod-job mod-type-listing">
	
	<header>
		<h1 class="page-title"><?php echo Yii::app()->cms->currentAlias->title; ?></h1>
	</header>
	
	<?php 
	foreach ($jobProvider->getData() as $job):
		if(!empty($job->id))
		$jobExist = true;
		break;
	endforeach;
		
	if(isset($jobExist) && $jobExist === true):
	?>
		<section class="section-bloc bloc-editor">
			<header><h1><?php echo Yii::t('jobModule.common', 'Pour postuler'); ?></h1></header>
			<ol>
				<li><?php echo Yii::t('jobModule.common', 'Sélectionnez le ou les emplois sur lesquels vous désirez appliquer parmi ceux listés ci-dessous.'); ?></li>
				<li><?php echo Yii::t('jobModule.common', 'Utilisez le bouton "Choisir un fichier" ou "Parcourir", au bas de la page, pour joindre votre curriculum vitae.'); ?></li>
				<li><?php echo Yii::t('jobModule.common', 'Cliquez le bouton "Postuler" pour transmettre votre candidature.'); ?></li>
			</ol>
		</section>
	<?php 
	endif;
	?>

	<section class="section-bloc bloc-editor">
		<header><h1><?php echo Yii::t('jobModule.common', 'Offres demplois'); ?></h1></header>
	
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'job-form',
			'enableAjaxValidation'=>false,
			'htmlOptions' => array('enctype' => 'multipart/form-data'),
			'action' => array('index', '#'=>'job-form'),
		)); ?>
	
		<?php echo $form->errorSummary(array_merge(array($formModel), array($cvModel))); ?>
		
		<?PHP
		$i=1;
		if(!isset($categories) || empty($categories)):
		?>
			<p><?php echo Yii::t('jobModule.common', 'Aucune offre demplois nest disponible pour le moment.'); ?></p>
		<?php 
		else:
		
			foreach ($categories as $category):
				
				if ($i > 1)
					echo "</fieldset>";
					?>
			
			<fieldset id="cat<?PHP echo $category->id; ?>">				
				<h2 class="form-subtitle"><?PHP echo CHtml::encode($category->name); ?></h2>
		
				<?PHP
				$j=0;
				$hasCandidateBank = false;
				$jobId = "";
				$jobTitle = "";
				$jobUrl = "";
				foreach ($jobProvider->getData() as $job):
				
					if ($job->category_id == $category->id):
					
						if ($job->type == 4):
							$hasCandidateBank = true;
							$jobId = $job->id;
							$jobTitle = CHtml::encode($job->title);
							$jobUrl = $job->title_url;
						else:
						?>
						
					<div class="form-group" style="margin-bottom:1.4em;">
						<div class="checkbox">
							<?php echo $form->checkbox($formModel, $job->id); ?>
							<label for="JobForm_<?PHP echo $job->id; ?>"><?php echo CHtml::encode($job->title); ?>&nbsp;&nbsp;<span>[<a href="<?PHP echo $this->createUrl('detail', array('t'=>$job->title_url)); ?>"><?php echo Yii::t('jobModule.common', 'détails'); ?></a>]</span></label>
						</div>
					</div>
		
						<?PHP
						endif;
						
						$j++;
					endif;
				endforeach;
				
				if ($j == 0 or ($j == 1 and $hasCandidateBank)):
				?>
				
				<p><?php echo Yii::t('jobModule.common', 'Aucune offre d\'emploi n\'est présentement disponible dans ce secteur.'); ?></p>
				
				<?PHP
				endif;
				
				if ($j == 1 and $hasCandidateBank): ?>
				
				<p><?php echo Yii::t('jobModule.common', 'Vous pouvez tout de même nous faire parvenir votre CV pour faire partie de notre banque de candidats dans ce secteur.'); ?></p>
				
				<?PHP
				endif;
				
				if ($hasCandidateBank):
				?>
				
				<div class="form-group">
					<div class="checkbox">
						<?php echo $form->checkbox($formModel, $jobId); ?>
						<label for="JobForm_<?PHP echo $jobId; ?>"><?php echo $jobTitle; ?>&nbsp;&nbsp;<span>[<a href="<?PHP echo $this->createUrl('detail', array('t'=>$jobUrl)); ?>"><?php echo Yii::t('jobModule.common', 'détails'); ?></a>]</span></label>
					</div>
				</div>
				
				<?PHP
				endif;
				
				$i++;
			endforeach;
			?>
		
			</fieldset>
			
			<?php 
			if(isset($jobExist) && $jobExist === true):
			?>
			<fieldset>
				<h2 class="form-subtitle"><?php echo Yii::t('jobModule.common', 'Votre curriculum vitae'); ?></h2>
				
				<div class="form-group">
					<label class="control-label required" for="JobCv_cv"><?php echo Yii::t('jobModule.common', 'Sélectionner un fichier au format Word ou PDF'); ?> <span class="required">*</span></label>
					<?php echo $cvModel->cvHandler->makeField($form, null, array('class'=>'form-control')); ?>
					<?php echo $form->error($cvModel, 'cv'); ?>
				</div>
				
			</fieldset>
			
			<fieldset>
				<div class="form-group">
					<?php echo CHtml::submitButton(Yii::t('jobModule.common', 'Postuler'), array('class'=>'btn btn-success')); ?>
				</div>
			</fieldset>
			<?php endif; ?>
		<?php endif; ?>
		<?php $this->endWidget(); ?>
		
	</section>
	
</article>
