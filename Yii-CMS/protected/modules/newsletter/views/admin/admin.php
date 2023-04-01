<?php
$this->breadcrumbs=array(
	$this->sectionLabel,
);

$this->title=$this->sectionLabel;
?>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm'); ?>
	
	<?php echo $form->errorSummary($model); ?>
	
	<p>Notez que seulement les items ayant été publiés après le dernier envoi de l'infolettre sont inclus.</p>
	
	<div class="row">
		<?php echo $form->labelEx($model,'frequency'); ?>
		<?php echo $form->dropDownList($model,'frequency', array(1=>'Jamais', (24*60*60)=>'Chaque jour', (3*24*60*60)=>'3 jours', (7*24*60*60)=>'7 jours', (30*24*60*60)=>'30 jours', (90*24*60*60)=>'90 jours', (180*24*60*60)=>'180 jours', (365*24*60*60)=>'Anuellement'), array('empty'=>'')); ?>
		<?php echo $form->error($model,'frequency'); ?>
	</div>
	
	

	<div class="row">
	
		<h2>Aperçu de la prochaine infolettre :</h2>
		
		<div style="margin: 20px; padding: 20px; border: 1px solid black;">
		
		<?php if ($newsletter === false): ?>
		
			<p>Aucune mise à jour depuis le dernier envoi</p>
		
		<?php else: ?>
		
			<?php echo $newsletter; ?>
			
		<?php endif; ?>
		
		</div>
		
	</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton(Yii::t('admin', 'Save')); ?>
		<?php echo CHtml::submitButton('Sauvegarder et envoyer l’infolettre', array(
			'confirm'=>'Attention, l’infolettre sera envoyée ajourd’hui (minuit) à tous les destinataires. Voulez-vous poursuivre?',
		)); ?>
	</div>
	
<?php $this->endWidget(); ?>

</div>
