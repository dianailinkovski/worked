<?php
$contestTitle = CHtml::encode($contest->title);
$contestImagePath = Yii::app()->request->baseUrl."/".$contest->imageHandler->dir."/";

$this->breadcrumbs = Helper::breadcrumbsFromAlias(true);

if ($contest->status == "archived"):
	$this->breadcrumbs[Yii::t('contestModule.common', 'Archives')] = array('index', 'archives'=>$this->module->archivesVarName[Yii::app()->language]);
endif;

$this->breadcrumbs[] = $contest->title;

$this->pageTitle = Helper::titleFromBreadcrumbs();

Yii::app()->clientScript->registerMetaTag($contest->summary, 'description');

// facebook OG Meta
Yii::app()->facebook->ogTags['og:site_name'] = Yii::app()->name;
Yii::app()->facebook->ogTags['og:title'] = $contest->title;
Yii::app()->facebook->ogTags['og:type'] = "article";
Yii::app()->facebook->ogTags['og:description'] = $contest->summary;
if ($contest->image != "") Yii::app()->facebook->ogTags['og:image'] = "http://".Yii::app()->request->serverName.Yii::app()->request->baseUrl.$contestImagePath.Helper::encodeFileName(Helper::fileSuffix($contest->image, 'm'));
?>

<article class="mod-contest mod-type-detail<?php echo ($contest->image != "" ? " article-image-layout" : ""); ?>">

	<header>
		<h3 class="page-title">
			<?php echo $contestTitle; ?>
			<?PHP
			if ($contest->status == "active"
				&& $contest->start_date <= $currentDate
				&& ($contest->max_participation == null || ($contest->max_participation !== null && ContestEntry::model()->countByAttributes(array('contest_id'=>$contest->id)) < $contest->max_participation))
				&& ($contest->multiple_entries || !$contest->multiple_entries && ContestEntry::model()->countByAttributes(array('contest_id'=>$contest->id, 'ip'=>$_SERVER['REMOTE_ADDR'])) < 1)
				&& ($contest->fields)):
				?>
				<span><a href="#formulaire" class="btn btn-primary"><?PHP echo Yii::t('contestModule.common', 'Participer'); ?></a></span>
			<?PHP endif; ?>
		</h3>
	</header>
	
	<?PHP if ($contest->image != ""): ?>
	<div class="article-image clearfix">
		
		<?PHP
		$this->beginWidget('ext.prettyPhoto.PrettyPhoto', array(
		  'id'=>'pretty_photo',
		  'options'=>Yii::app()->params['prettyPhotoOptions'],
		));
		?>

		<a href="<?PHP echo $contestImagePath.Helper::encodeFileName(Helper::fileSuffix($contest->image, 'l')); ?>" title="<?PHP echo $contestTitle; ?>">
			<img src="<?PHP echo $contestImagePath.Helper::encodeFileName(Helper::fileSuffix($contest->image, 'l')); ?>" alt="<?PHP echo $contestTitle; ?>" title="<?php echo Yii::t('contestModule.common', 'Cliquez pour agrandir limage'); ?>" class="img-responsive" />
		</a>
		
		<?PHP
		$this->endWidget('ext.prettyPhoto.PrettyPhoto');
		?>
		
	</div>

	<?PHP
	endif;

	$this->widget('application.widgets.BlocsWidget', array(
		'parentId'=>$contest->id,
		'uniqueId'=>'contest',
	));
	?>
	
	<?php if ($contest->fields):	// Pas de formulaire. ?>
	
	<section class="section-bloc" id="formulaire">
	
		<h4><?PHP echo Yii::t('contestModule.common', 'Formulaire de participation'); ?></h4>
		
		<?php if ($contest->start_date > $currentDate):	// Contest is not started. ?>
		
		<p class="alert alert-info"><?PHP echo Yii::t('contestModule.common', 'Le formulaire de participation sera accessible à partir du').Helper::formatDate($contest->start_date, "reg").Yii::t('contestModule.common', 'à').substr($contest->start_date, 11, 5); ?>.</p>
		
		<?PHP elseif ($contest->max_participation !== null && ContestEntry::model()->countByAttributes(array('contest_id'=>$contest->id)) >= $contest->max_participation):	// Max. participation has been reached. ?>
		
		<p class="alert alert-danger"><?PHP echo Yii::t('contestModule.common', 'Le nombre maximum de participations a été atteint pour ce concours'); ?>.</p>
		
		<?PHP elseif (!($contest->multiple_entries) && ContestEntry::model()->countByAttributes(array('contest_id'=>$contest->id, 'ip'=>$_SERVER['REMOTE_ADDR'])) > 0): ?>
		
		<p class="alert alert-danger"><?PHP echo Yii::t('contestModule.common', 'Vous ne pouvez participer plus d’une fois à ce concours/sondage'); ?>.</p>
		
		<?PHP
		else:
		
			$form=$this->beginWidget('CActiveForm', array(
				'id'=>'contest-form',
				'enableAjaxValidation'=>false,
				'errorMessageCssClass'=>'alert alert-danger',
				'action'=>array('detail', 'n'=>$contest->title_url, '#'=>'contest-form'),
				'htmlOptions' => array('enctype' => 'multipart/form-data'),
			)); ?>
			
			<p class="text-small"><span class="required">*</span> = <?php echo Yii::t('common', 'champs obligatoires'); ?></p>
			
			<?php echo $form->errorSummary($entry);
			
			$fieldsetInitiated = false;
			
			foreach ($contest->fields as $field): ?>
			
				<?php
				$fieldAttributes = ($field->required == 1) ? array('required'=>'required') : array();
				
				switch ($field->type): 
					
					case 'title':
						if ($fieldsetInitiated):
							echo "</fieldset>";
							echo "<fieldset>";
						else :
							echo "<fieldset>";
							$fieldsetInitiated = true;
						endif;
						?>
					
					<h5 class="form-subtitle"><?php echo CHtml::encode($field->title); ?></h5>
					
					<?php break;
					
					case 'text': ?>
					
					<div class="form-group">
						<?php echo $form->labelEx($entry,$field->id, array('class'=>'control-label')); ?>
						<?php echo $form->textField($entry,$field->id, array_merge(array('class'=>'form-control'), $fieldAttributes)); ?>
						<?php echo $form->error($entry,$field->id); ?>
					</div>
					
					<?php break;
					
					case 'email': ?>
					
					<div class="form-group">
						<?php echo $form->labelEx($entry,$field->id, array('class'=>'control-label')); ?>
						<?php echo $form->emailField($entry,$field->id, array_merge(array('class'=>'form-control'), $fieldAttributes)); ?>
						<?php echo $form->error($entry,$field->id); ?>
					</div>
					
					<?php break; ?>
					
					<?php case 'textarea': ?>
		
					<div class="form-group">
						<?php echo $form->labelEx($entry,$field->id, array('class'=>'control-label')); ?>
						<?php echo $form->textArea($entry,$field->id, array_merge(array('class'=>'form-control'), $fieldAttributes)); ?>
						<?php echo $form->error($entry,$field->id); ?>
					</div>
					
					<?php break; ?>
					
					<?php case 'radio': ?>
					
						<?php 
						$radioList = array();
						foreach ($field->multi as $multi): 
							$radioList[$multi->id] = CHtml::encode($multi->title);
						endforeach;
						?>
						
					<div class="form-group">
						<?php echo $form->labelEx($entry,$field->id, array('class'=>'control-label')); ?>
						<div class="radio">
							<?php echo $form->radioButtonList($entry,$field->id, $radioList, array_merge(array('template'=>'{input} {label}', 'separator'=>''), $fieldAttributes)); ?>
						</div>
						<?php echo $form->error($entry,$field->id); ?>
					</div>
		
					<?php break; ?>
					
					<?php case 'checkbox': ?>
					
						<?php 
						$checkboxList = array();
						foreach ($field->multi as $multi): 
							$checkboxList[$multi->id] = CHtml::encode($multi->title);
						endforeach;
						?>
						
					
					<div class="form-group">
						<?php echo $form->labelEx($entry,$field->id, array('class'=>'control-label')); ?>
						<div class="checkbox">
							<?php echo $form->checkBoxList($entry,$field->id, $checkboxList, array('template'=>'{input} {label}', 'separator'=>'')); ?>
						</div>
						<?php echo $form->error($entry,$field->id); ?>
					</div>
		
					<?php break; ?>
					
					<?php case 'file': ?>
		
					<div class="form-group">
						<?php echo $form->labelEx($entry,$field->id, array('class'=>'control-label')); ?>
						<?php echo $entry->{$field->id.'Handler'}->makeField($form, null, $fieldAttributes); ?>
						<?php echo $form->error($entry,$field->id); ?>
					</div>
			
					<?php break; ?>
					
				<?php endswitch; ?>
			
			<?php endforeach; ?>
			
			<?PHP if ($fieldsetInitiated) 
					echo "</fieldset>"; ?>
						
			<?php if(CCaptcha::checkRequirements()): ?>
			<div class="form-group">
		  		<?php echo $form->labelEx($entry,'verify_code', array('class'=>'control-label', 'style'=>'display:block;')); ?>
				<?php $this->widget('CCaptcha',array('clickableImage' => true, 'captchaAction'=>'/site/captcha')); ?>
				<?php echo $form->textField($entry,'verify_code'); ?>
				<div class="hint">Veuillez saisir les caractères affichés dans l'image ci-dessus.<br/>Les caractères ne sont pas sensibles à la casse.</div>
				<?php echo $form->error($entry,'verify_code'); ?>
			</div>
			<?php endif; ?>
			
			<fieldset>
			
				<div class="form-group">
					<?php echo CHtml::submitButton(Yii::t('contestModule.common', 'Envoyer'), array('class'=>'btn btn-success')); ?>
				</div>
				
			</fieldset>
					
			<?php
			$this->endWidget();
			
		endif;
		?>
		
	</section>
	
	<?php endif; ?>

</article>
