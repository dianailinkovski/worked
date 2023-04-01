<?php foreach ($contests as $contest): ?>

<div><?php echo $contest->title; ?></div>

<?php endforeach; ?>

<div><?php echo CHtml::link(Yii::t('contestModule.common', 'Voir les concours et sondages archivés', array('/contest/default/detail', 'n'=>$contest->title_url))); ?></div>