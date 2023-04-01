<?php
$this->breadcrumbs=array(
	$model->contest->title=>array('/contest/admin/update', 'id'=>(int)$_GET['id']),
	'Résultats'=>array('admin', 'id'=>(int)$_GET['id']),
	$model->id
);

$this->menu=array(
	array('label'=>'Liste des entrées', 'url'=>array('admin', 'id'=>(int)$_GET['id'])),
	array('label'=>'Statistiques', 'url'=>array('stats', 'id'=>(int)$_GET['id'])),
);

$this->title='Entrée # '.$model->id;

$items = array();
foreach ($model->items as $id => $item):
	if (isset($items[$item->contest_field_id])):
		if (!is_array($items[$item->contest_field_id])):
			$items[$item->contest_field_id] = array($items[$item->contest_field_id]);
		endif;
		$items[$item->contest_field_id][] = $item->content;
	else:
		$items[$item->contest_field_id] = $item->content;
	endif;
endforeach;

foreach ($model->contest->fields as $field): ?>

	<?php switch ($field->type): 

		case 'text': ?>
		
		<?php if (isset($items[$field->id])): ?>
		
		<div><h2><?php echo CHtml::encode($field->title); ?></h2> : <p><?php echo CHtml::encode($items[$field->id]); ?></p></div>
		
		<?php endif; ?>
		
		<?php break;
		
		case 'email': ?>
		
		<?php if (isset($items[$field->id])): ?>
		
		<div><h2><?php echo CHtml::encode($field->title); ?></h2> : <p><?php echo CHtml::encode($items[$field->id]); ?></p></div>
		
		<?php endif; ?>
		
		<?php break; ?>
		
		<?php case 'textarea': ?>

		<?php if (isset($items[$field->id])): ?>
		
		<h2><?php echo CHtml::encode($field->title); ?> : </h2>
		<p><?php echo CHtml::encode($items[$field->id]); ?></p>
		
		<?php endif; ?>
		
		<?php break; ?>
		
		<?php case 'radio': ?>
		
		<?php if (isset($items[$field->id])): ?>
	
		<div><h2><?php echo CHtml::encode($field->title); ?></h2> : <p><?php echo isset($field->multi[$items[$field->id]]) ? CHtml::encode($field->multi[$items[$field->id]]->title) : ''; ?></p></div>
		
		<?php endif; ?>

		<?php break; ?>
		
		<?php case 'checkbox': ?>
				
		<?php if (isset($items[$field->id])): ?>
		
		<h2><?php echo CHtml::encode($field->title); ?> : </h2>
		
			<?php
			if (!is_array($items[$field->id]))
				$items[$field->id] = array($items[$field->id]);
			
			$i = 0;
			foreach ($items[$field->id] as $checkboxItem):
				$i++;
			?>

		<p>Choix <?php echo $i; ?> : <?php echo isset($field->multi[$checkboxItem]) ? CHtml::encode($field->multi[$checkboxItem]->title) : ''; ?></p>
		
		<?php 
			endforeach;
		endif; ?>

		<?php break; ?>
		
		<?php case 'file': ?>

		<?php if (isset($items[$field->id])): ?>
		
		<div><h2><?php echo CHtml::encode($field->title); ?></h2> : <p><?php echo CHtml::link('Télécharger le fichier', '/files/_user/contest_entries/'.Helper::encodeFileName($items[$field->id]), array('target'=>'_blank')); ?></p></div>

		<?php endif; ?>

		<?php break; ?>
		
		<?php case 'title': ?>
		
		<h2><?php echo CHtml::encode($field->title); ?></h2>
		
		<?php break; ?>
		
	<?php endswitch; ?>

<?php endforeach; ?>