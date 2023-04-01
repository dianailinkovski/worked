<?php
$this->pageTitle=Yii::t('memberModule.common', 'Récupération de mot de passe').$this->titleSeparator.Yii::app()->name;
$this->breadcrumbs=array(
	Yii::t('memberModule.common', 'Récupération de mot de passe') => array('/member/default/recover'),
	Yii::t('memberModule.common', 'Succès'),
);
?>

<article>

	<header>
		<h1 class="mainTitle"><?PHP echo Yii::t('memberModule.common', 'Récupération de mot de passe'); ?></h1>
	</header>

	<section>

		<p><?PHP echo Yii::t('memberModule.common', 'Votre mot de passe a été changé.'); ?></p>
		
		<p><?php echo CHtml::link(Yii::t('memberModule.common', 'Aller au formulaire de connexion.'), $this->createUrl('/member/default/login')); ?></p>

	</section>
	
</article>