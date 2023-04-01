<?php
$this->pageTitle=Yii::t('memberModule.common', 'Récupération de mot de passe').$this->titleSeparator.Yii::app()->name;
$this->breadcrumbs=array(
	Yii::t('memberModule.common', 'Récupération de mot de passe') => array('/member/default/recover'),
	Yii::t('memberModule.common', 'Expiration'),
);
?>

<article>

	<header>
		<h1 class="mainTitle"><?PHP echo Yii::t('memberModule.common', 'Récupération de mot de passe'); ?></h1>
	</header>

	<section>

		<p><?PHP echo Yii::t('memberModule.common', 'Le lien de récupération de mot de passe est expiré, veuillez réessayer de nouveau.'); ?></p>
		
		<p><?php echo CHtml::link(Yii::t('memberModule.common', 'Aller au formulaire'), $this->createUrl('/member/default/recover')); ?></p>

	</section>
	
</article>