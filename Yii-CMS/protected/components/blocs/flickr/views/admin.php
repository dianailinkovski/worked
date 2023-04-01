<?php echo $this->renderPartial('//adminLayouts/_blocHeader', array('form'=>$form, 'model'=>$model, 'formId'=>$formId, 'itemId'=>$itemId)); ?>

<?php
$userIdsData = CHtml::listData(FlickrUser::model()->findAll(), 'user_id', 'user_id');

$userIds = array();
foreach ($userIdsData as $userId)
{
	$curl = curl_init("https://api.flickr.com/services/rest/?method=flickr.people.getInfo&api_key=cd80122ae0a0f805b279d80715dd7861&user_id=".urlencode($userId)."&format=json&nojsoncallback=1");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($curl);
	curl_close($curl);
	$mObject = json_decode($data, false); // stdClass object

	if (isset($mObject->person) && !empty($mObject->person->path_alias))
		$userIds[$userId] = CHtml::encode($mObject->person->path_alias);
	else
		$userIds[$userId] = $userId;
}
?>

<div class="form-group">
	<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']user_id', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
	<div class="col-sm-6 col-xs-12"><?php echo $form->dropDownList($model,'['.$formId.']['.$itemId.']user_id', $userIds, array('empty'=>'', 'class'=>'blocFlickrUserId form-control')); ?></div>
	<?php echo $form->error($model,'['.$formId.']['.$itemId.']user_id', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
</div>

<div class="form-group">
	<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']set_id', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
	<div class="col-sm-6 col-xs-12"><?php echo $form->dropDownList($model,'['.$formId.']['.$itemId.']set_id', array(), array('empty'=>'', 'class'=>'form-control blocFlickrSetId')); ?></div>
	<?php echo CHtml::hiddenField(get_class($model).'['.$formId.']['.$itemId.'][set_id_hidden]', $model->set_id, array('class'=>'blocFlickrSetIdHidden', 'id'=>get_class($model).'_'.$formId.'_'.$itemId.'_set_id_hidden')); ?>
	<?php echo $form->error($model,'['.$formId.']['.$itemId.']set_id', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
</div>

<div class="form-group">
	<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']nbr_images', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
	<div class="col-sm-6 col-xs-12"><?php echo $form->numberField($model,'['.$formId.']['.$itemId.']nbr_images', array('class'=>'form-control')); ?></div>
	<?php echo $form->error($model,'['.$formId.']['.$itemId.']nbr_images', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
</div>

<div class="form-group">
	<?php echo $form->labelEx($model,'['.$formId.']['.$itemId.']show_as_carrousel', array('class'=>'control-label col-sm-3 col-xs-12')); ?>
	<div class="col-sm-6 col-xs-12"><?php echo $form->checkBox($model,'['.$formId.']['.$itemId.']show_as_carrousel', array('class'=>'form-control')); ?></div>
	<?php echo $form->error($model,'['.$formId.']['.$itemId.']show_as_carrousel', array('class'=>'error text-left col-sm-3 col-xs-12')); ?>
</div>

<?php echo $form->hiddenField($model,'['.$formId.']['.$itemId.']rank'); ?>