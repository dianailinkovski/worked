<?php
$this->breadcrumbs=array(
	$contest->title=>array('/contest/admin/update', 'id'=>(int)$_GET['id']),
	'Résultats'
);

$this->menu=array(
	array('label'=>'Statistiques', 'url'=>array('stats', 'id'=>(int)$_GET['id'])),
);

$this->title='Résultats';
?>

<?php 
$column = array(
	array('name'=>'id', 'header'=>'#'),
	array('name'=>'created_at', 'header'=>'Date / Heure de l’inscription'),
);

foreach (Yii::app()->controller->fields as $fieldId => $fieldArr)
{
	$column[] = array('type'=>'raw', 'name'=>'field_'.$fieldId, 'header'=>mb_strlen($fieldArr['title']) > 30 ? mb_substr(CHtml::encode($fieldArr['title']), 0, 30).'&hellip;' : CHtml::encode($fieldArr['title']), 'value'=>"mb_strlen(\$data->field_".$fieldId.") > 200 ? mb_substr(CHtml::encode(\$data->field_".$fieldId."), 0, 200).'&hellip;' : CHtml::encode(\$data->field_".$fieldId.")");
}

$column[] = array(
	'class'=>'CButtonColumn',
	'template'=>'{view} {delete}',
	'viewButtonUrl'=>'Yii::app()->controller->createUrl(\'view\', array(\'id\'=>\''.(int)$_GET['id'].'\', \'view_id\'=>$data[\'id\']))',
);

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'content-entry-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>$column
)); ?>

<hr/>

<h2>Tirage</h2>

<form>
	<label>Nombre de gagnants : </label><input name="nbWinners" id="nbWinners" type="text" value=""></input>
	<?php echo CHtml::ajaxSubmitButton('Tirer', $this->createUrl('winners', array('id'=>(int)$_GET['id'])), array(
			'update'=>'#winnersResult',
			'beforeSend'=>"function(){
				$('#winnersResult').html('Chargement...');
			}",
		)); ?>
</form>

<div id="winnersResult"></div>