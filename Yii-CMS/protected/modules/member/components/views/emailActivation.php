<?php $fontFamily = Yii::app()->params['emailFontFamily']; ?>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="left">
		
			<p style="margin-top:15px; margin-bottom:15px; font-family:<?PHP echo $fontFamily; ?>; font-size:16px; color:#444444;">Bienvenue sur <em>Allo Transport</em>,</p>
			
			<p style="margin-top:15px; margin-bottom:15px; font-family:<?PHP echo $fontFamily; ?>; font-size:16px; color:#444444;">Vous recevez ce courriel suite à la création de votre compte sur <a href="http://allotransport.com">allotransport.com</a>.</p>
			
			<p style="margin-top:15px; margin-bottom:15px; font-family:<?PHP echo $fontFamily; ?>; font-size:16px; color:#444444;">Avant de continuer, vous devez d'abord valider votre inscription en cliquant le bouton ci-dessous :</p>
			
		</td>
	</tr>
	<tr>
		<td align="center">
		
			<p style="margin-left:auto; margin-right:auto; margin-top:15px; margin-bottom:15px; width:200px; height:60px; background-color:#7EC245;">
				<a href="<?php echo $this->createAbsoluteUrl('/site/index', array('ahash'=>$member->activation_hash)); ?>" style="display:block; width:200px; height:60px; line-height:60px; font-family:<?PHP echo $fontFamily; ?>; font-size:16px; color:#FFFFFF; text-decoration:none;">Valider mon inscription</a>
			</p>
		
		</td>
	</tr>
	<tr>
		<td align="center">
		
			<p style="margin-left:auto; margin-right:auto; margin-top:0; margin-bottom:20px; width:320px; text-align:center; font-family:<?PHP echo $fontFamily; ?>; font-size:13px; color:#666666;">Vous serez redirigé vers le site Web d'<em>Allo Transport</em>, où vous pourrez compléter votre profil de membre.</p>
		
		</td>
	</tr>
</table>