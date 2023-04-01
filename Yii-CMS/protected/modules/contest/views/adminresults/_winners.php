<table cellspacing="0" cellpadding="0">

	<tr><td><p class="red" style="margin-top:1em;">ATTENTION : Le résultat du tirage n'est pas sauvegardé. Il est conseillé de prendre le(s) numéro(s) gagnant(s) en note et ce, avant même de cliquer le(s) lien(s) "afficher".</p></td></tr>
	
	<?php foreach ($winners as $winner): ?>
	
	<tr><td>#<?php echo $winner['id']; ?> <?php echo CHtml::link('(afficher)', array('view', 'id'=>(int)$_GET['id'], 'view_id'=>$winner['id'])); ?></td></tr>
		
	<?php endforeach; ?>

	
</table>