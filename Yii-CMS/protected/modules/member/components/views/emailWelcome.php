<?php $fontFamily = Yii::app()->params['emailFontFamily']; ?>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="center" colspan="3">
		
			<p style="margin-top:15px; margin-bottom:20px; font-family:<?PHP echo $fontFamily; ?>; font-size:18px; color:#444444;">Bienvenue et merci d'avoir choisi le système <em>Allo Transport</em> pour la gestion de vos transports en commun!</p>
			
		</td>
	</tr>
	<tr>
		<td width="47%" align="center">
		
			<p style="margin-top:5px; margin-bottom:15px; padding-bottom:10px; border-bottom:1px solid #DDDDDD; font-family:<?PHP echo $fontFamily; ?>; font-size:24px; color:#054B91;">Vous êtes passager?</p>
			
			<p style="margin-top:0; margin-bottom:20px; font-family:<?PHP echo $fontFamily; ?>; font-size:14px; color:#55555;">Il est temps de rechercher le transport qui convient à vos besoins en utilisant notre formulaire de recherche facile à utiliser.</p>
			
			<p style="margin-left:auto; margin-right:auto; margin-top:15px; margin-bottom:15px; width:230px; height:60px; background-color:#7EC245;">
				<a href="<?php echo $this->createAbsoluteUrl('/recherche/rechercher'); ?>" style="display:block; width:230px; height:60px; line-height:60px; font-family:<?PHP echo $fontFamily; ?>; font-size:16px; color:#FFFFFF; text-decoration:none;">Recherche un transport</a>
			</p>
		
		</td>
		<td width="6%"></td>
		<td width="47%" align="center">
		
			<p style="margin-top:5px; margin-bottom:15px; padding-bottom:10px; border-bottom:1px solid #DDDDDD; font-family:<?PHP echo $fontFamily; ?>; font-size:24px; color:#054B91;">Vous êtes conducteur?</p>
			
			<p style="margin-top:0; margin-bottom:20px; font-family:<?PHP echo $fontFamily; ?>; font-size:14px; color:#55555;">En tant que conducteur, votre prochaine étape consiste à offrir un transport en créant votre premier trajet.</p>
			
			<p style="margin-left:auto; margin-right:auto; margin-top:15px; margin-bottom:15px; width:230px; height:60px; background-color:#7EC245;">
				<a href="<?php echo $this->createAbsoluteUrl('/trajets/trajets'); ?>" style="display:block; width:230px; height:60px; line-height:60px; font-family:<?PHP echo $fontFamily; ?>; font-size:16px; color:#FFFFFF; text-decoration:none;">Offrir un transport</a>
			</p>
		
		</td>
	</tr>
	<tr>
		<td align="center" colspan="3">
		
			<p style="margin-top:15px; margin-bottom:20px; font-family:<?PHP echo $fontFamily; ?>; font-size:16px; color:#444444;">Pour toute question concernant votre compte ou le fonctionnement du système, <a href="<?php echo $this->createAbsoluteUrl('/site/page', array('view'=>'faq')); ?>" style="color:#054B91; text-decoration:none;">consultez notre FAQ</a> ou <a href="<?php echo $this->createAbsoluteUrl('/site/contact'); ?>" style="color:#054B91; text-decoration:none;">contactez-nous</a>.</p>
			
			<p style="margin-top:15px; margin-bottom:20px; font-family:<?PHP echo $fontFamily; ?>; font-size:16px; color:#444444;">Bonne route!</p>
			
		</td>
	</tr>
</table>