<?PHP
Yii::app()->clientScript->registerCssFile(Yii::app()->cms->blocs['contact']->assetsUrl.'/css/contact.css');

if ($bloc->google_maps != "" or $bloc->display_contact_form):

	if ($bloc->google_maps != ""):
		preg_match('/src="([^"]+)/u', $bloc->google_maps, $url); 
		
		if (isset($url[1])):
			$url = CHtml::encode($url[1]);
		endif;
	endif;
	?>

<div class="row">

	<div class="<?PHP echo ($bloc->display_contact_form) ? 'col-sm-7' : 'col-md-5'; ?>">
	
<?PHP endif; ?>

		<dl class="vcard">
			<dt class="fn org"><?php echo CHtml::encode($bloc->name); ?></dt>
			
			<?PHP if ($bloc->address != "" or $bloc->city != "" or $bloc->province != "" or $bloc->postal_code != "" or $bloc->country != ""): ?>
			<dd class="adr">
				<?PHP if ($bloc->address != ""): ?><span class="street-address"><?php echo CHtml::encode($bloc->address); ?></span><br><?PHP endif; ?>
				<?PHP if ($bloc->city != ""): ?><span class="locality"><?php echo CHtml::encode($bloc->city); ?></span>, <span class="region"><?php echo CHtml::encode($bloc->province); ?></span><br><?PHP endif; ?>
				<?PHP if ($bloc->postal_code != ""): ?><span class="postal-code"><?php echo CHtml::encode($bloc->postal_code); ?></span><?PHP endif; ?>
<?PHP if ($bloc->country != ""): ?>, <span class="country-name"><?php echo CHtml::encode($bloc->country); ?></span><?PHP endif; ?>
			</dd>
			<?PHP endif; ?>
			
			<?PHP if ($bloc->phone1 != ""): ?>
			<dd class="tel phone1"><span class="vcard-label"><?PHP echo Yii::t('blocs', 'Téléphone'); ?> : </span><?php echo CHtml::encode($bloc->phone1); ?></dd>
			<?PHP endif; ?>
			
			<?PHP if ($bloc->phone2 != ""): ?>
			<dd class="tel phone2"><?php echo CHtml::encode($bloc->phone2); ?></dd>
			<?PHP endif; ?>
			
			<?PHP if ($bloc->phone_toll_free != ""): ?>
			<dd class="tel phone1"><span class="vcard-label"><?PHP echo Yii::t('blocs', 'Sans frais'); ?> : </span><?php echo CHtml::encode($bloc->phone_toll_free); ?></dd>
			<?PHP endif; ?>
			
			<?PHP if ($bloc->fax != ""): ?>
			<dd class="fax"><span class="vcard-label"><?PHP echo Yii::t('blocs', 'Télécopieur'); ?> : </span><?php echo CHtml::encode($bloc->fax); ?></dd>
			<?PHP endif; ?>
			
			<?PHP 
			if ($bloc->email != ""):
				$personEmail = CHtml::encode($bloc->email);
				$part1 = mb_substr($personEmail, 0, strpos($personEmail, "@"));
				$part2 = mb_substr($personEmail, strpos($personEmail, "@"));
				$encodedEmail = $part1."nospam".$part2;
				?>
			<dd class="email">
				<span class="vcard-label"><?PHP echo Yii::t('blocs', 'Courriel'); ?> :</span>
				<script type="application/javascript">document.write('<a class="email-link" href="mailto:<?php echo $encodedEmail; ?>"><?php echo $encodedEmail; ?></a>'.replace(/nospam/g, ''));</script>
			</dd>
			<?PHP endif; ?>
			
			<?PHP if ($bloc->comment != ""): ?>
			<dd class="note"><?php echo CHtml::encode($bloc->comment); ?></dd>
			<?PHP endif; ?>
			
			<?PHP
			if ($bloc->image != ""):
				$image = '/'.$bloc->imageHandler->dir."/".Helper::encodeFileName(Helper::fileSuffix($bloc->image, 'm'));
				$xtraClass = ($bloc->google_maps == "" and !$bloc->display_contact_form) ? " floated" : "";
				$imageTitle = CHtml::encode($bloc->image_title);
				?>
			<dd class="picture<?PHP echo $xtraClass; ?>">
				<img class="photo img-responsive" src="<?PHP echo Yii::app()->baseUrl.$image; ?>" alt="<?php echo $imageTitle; ?>" title="<?php echo $imageTitle; ?>">
			</dd>
			<?PHP endif; ?>
		</dl>
		
		<?PHP if ($bloc->google_maps != "" and $bloc->display_contact_form): ?>
		<div class="map-container">
			
			<div class="overlay" onClick="style.pointerEvents='none'"></div>
			<iframe src="<?php echo $url; ?>"></iframe>
			<div class="enlarge-link"><small><a href="<?php echo $url; ?>" target="_blank"><?php echo Yii::t('blocs', 'Agrandir le plan'); ?></a></small></div>
		
		</div>
		<?PHP endif; ?>

<?PHP if ($bloc->google_maps != "" or $bloc->display_contact_form): ?>

	</div>
	
	<div class="<?PHP echo ($bloc->display_contact_form) ? 'col-sm-5 col-lg-4 col-lg-offset-1' : 'col-md-7'; ?>">
	
	<?php 
	if ($bloc->display_contact_form):
	?>
		
		<div class="contact-form-container">
		
			<h2><?php echo Yii::t('blocs', 'Question ou commentaire'); ?></h2>
			
			<?PHP
			$this->widget('application.components.widgets.ContactUs.ContactUsWidget', array(
				'emailSubject' => Yii::app()->params['contactUsWidgetSubject'],
				'emailTo' => Yii::app()->params['contactUsWidgetEmail'],
			));
			?>
		
		</div>
		
	<?PHP
	else:
	?>
			
		<div class="map-container">
			
			<div class="overlay" onClick="style.pointerEvents='none'"></div>
			<iframe src="<?php echo $url; ?>"></iframe>
			<div class="enlarge-link"><small><a href="<?php echo $url; ?>" target="_blank"><?php echo Yii::t('blocs', 'Agrandir le plan'); ?></a></small></div>
		
		</div>
		
	<?PHP
	endif;
	?>
		
	</div>
	
</div>

<?PHP endif; ?>
