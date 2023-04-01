<?php $fontFamily = Yii::app()->params['emailFontFamily']; ?>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="left" colspan="3">
			
			<p style="margin-top:15px; margin-bottom:15px; font-family:<?PHP echo $fontFamily; ?>; font-size:16px; color:#444444;">Bonjour,</p>
			
			<p style="margin-top:15px; margin-bottom:15px; font-family:<?PHP echo $fontFamily; ?>; font-size:16px; color:#444444;">Un visiteur du site Web de la Ville de Saint-Félicien a participé au <?PHP echo mb_strtolower(Yii::app()->cms->currentAlias->title, 'UTF-8'); ?> &laquo;<?PHP echo CHtml::encode($contest->title); ?>&raquo;.</p>
			
			<p style="margin-top:15px; margin-bottom:15px; font-family:<?PHP echo $fontFamily; ?>; font-size:16px; color:#1F8385;">
				<a href="<?php echo $this->createAbsoluteUrl('adminresults/view', array('id'=>$contest->id, 'view_id'=>$entry->id)); ?>" title="Voir les détails">Voir les détails</a>
			</p>
			
		</td>
	</tr>
</table>

