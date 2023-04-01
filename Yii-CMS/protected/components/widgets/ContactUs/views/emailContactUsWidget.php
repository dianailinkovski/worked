<?php $fontFamily = Yii::app()->params['emailFontFamily']; ?>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="left" colspan="3">
			
			<p style="margin-top:15px; margin-bottom:15px; font-family:<?PHP echo $fontFamily; ?>; font-size:16px; color:#444444;">Bonjour,</p>
			
			<p style="margin-top:15px; margin-bottom:15px; font-family:<?PHP echo $fontFamily; ?>; font-size:16px; color:#444444;">Vous avez recu un nouveau message envoyé à partir du site Web :</p>
			
			<p style="margin-top:15px; margin-bottom:15px; font-family:<?PHP echo $fontFamily; ?>; font-size:16px; color:#444444;">
				<strong>Nom de la personne</strong><br/><?php echo CHtml::encode($personName); ?><br/><br/>
				<strong>Adresse courriel</strong><br/><?php echo CHtml::encode($email); ?><br/><br/>
				<strong>Message</strong><br/><?php echo str_replace("\n", '<br/>', CHtml::encode($message)); ?>
			</p>
			
		</td>
	</tr>
</table>
