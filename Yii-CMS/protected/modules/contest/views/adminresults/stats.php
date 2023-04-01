<?php
$this->breadcrumbs=array(
	$model->title=>array('/contest/admin/update', 'id'=>(int)$_GET['id']),
	'Résultats'=>array('admin', 'id'=>(int)$_GET['id']),
	'Statistiques'
);

$this->menu=array(
	array('label'=>'Liste des entrées', 'url'=>array('admin', 'id'=>(int)$_GET['id'])),
	array('label'=>'Statistiques', 'url'=>array('stats', 'id'=>(int)$_GET['id'])),
);

$this->title='Statistiques concours "'.CHtml::encode($model->title).'"';
?>

<p>Cette page présente les statistiques de réponse aux questions de type "cases à cocher" et "boutons radio".</p>

<?php 
foreach ($model->fields as $field): ?>

	<?php if ($field->type == 'radio' || $field->type == 'checkbox'): ?>
	
	<div><h4 style="margin-top:2em;"><?php echo $field->title; ?></h4></div>

	<?php endif; ?>

	<?php switch ($field->type): 

		case 'radio':

		$counts = array();
		$countsTotal = 0;
		foreach ($field->multi as $choice):
			$counts[$choice->id] = Yii::app()->db->createCommand('SELECT COUNT(*) FROM contest_entry_item WHERE contest_field_id = :field_id AND content = :choice_id')->queryScalar(array(':field_id'=>$field->id, ':choice_id'=>$choice->id));
			$countsTotal += $counts[$choice->id];
		endforeach;
		?>

		<?php
		foreach ($field->multi as $choice): ?>
		
		<div>
			<p><?php echo CHtml::encode($choice->title); ?></p>
			<div style="position:relative; margin-bottom:2em;" class="progress-bar" progressbarvalue="<?php echo $counts[$choice->id]; ?>" progressbartotal="<?php echo $countsTotal; ?>">
				<span style="position:absolute; font-size:1.2em; font-weight:600; left:50%; top:5px; margin-left:-1em;"><?php echo $counts[$choice->id]; ?>/<?php echo $countsTotal; ?> (<?php echo round(100*$counts[$choice->id]/$countsTotal, 2); ?>%)</span>
			</div>
		</div>
		
		<?php endforeach; ?>

		<?php break; ?>
		
		<?php case 'checkbox': ?>
				
		<?php
		$counts = array();
		$countsTotal = 0;
		foreach ($field->multi as $choice):
			$counts[$choice->id] = Yii::app()->db->createCommand('SELECT COUNT(*) FROM contest_entry_item WHERE contest_field_id = :field_id AND content = :choice_id')->queryScalar(array(':field_id'=>$field->id, ':choice_id'=>$choice->id));
			$countsTotal += $counts[$choice->id];
		endforeach;

		foreach ($field->multi as $choice): ?>
		
		<div>
			<p><?php echo CHtml::encode($choice->title); ?></p>
			<div style="position:relative; margin-bottom:2em;" class="progress-bar" progressbarvalue="<?php echo $counts[$choice->id]; ?>" progressbartotal="<?php echo $countsTotal; ?>">
				<span style="position:absolute; font-size:1.2em; font-weight:600; left:50%; top:5px; margin-left:-1em;"><?php echo $counts[$choice->id]." (".round(100*$counts[$choice->id]/$countsTotal, 2); ?>%)</span>
			</div>
		</div>
		
		<?php endforeach; ?>

		<?php break; ?>
		
	<?php endswitch; ?>

<?php endforeach; ?>

<?php Yii::app()->clientScript->registerScript('progressBars', "
$('.progress-bar').each(function(){
	var value = parseInt($(this).attr('progressbarvalue'));
	var total = parseInt($(this).attr('progressbartotal'));

	$(this).progressbar({
		value: (value / total) * 100,
	});
});
", CClientScript::POS_READY); ?>
