<?php $fontFamily = Yii::app()->params['emailFontFamily']; ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>Allo Transport</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,400' rel='stylesheet' type='text/css'>
	
	<style type="text/css">
		/* Client-specific Styles */
		#outlook a { padding: 0; }	/* Force Outlook to provide a "view in browser" button. */
		body { width: 100% !important; }
		/* Reset Styles */
		body { background-color: #EEEEEE; margin: 0; padding: 0; }
		img { outline: none; text-decoration: none; display: block;}
		br, strong br, b br, em br, i br { line-height:100%; }
		table, table td, table tr { border-collapse: collapse; }
		/* Body text color for the New Yahoo.  This example sets the font of Yahoo's Shortcuts to black. */
		.yshortcuts, .yshortcuts a, .yshortcuts a:link,.yshortcuts a:visited, .yshortcuts a:hover, .yshortcuts a span {
		color: black; text-decoration: none !important; border-bottom: none !important; background: none !important;
		}	
		div, span, a, p { -webkit-text-adjust:none; }
		#email-background-table a:hover { text-decoration:underline !important; }
	</style>
	
</head>
<body topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" style="margin-bottom:0; margin-left:0; margin-right:0; margin-top:0;">

<table id="email-background-table" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #F7F7F7; border-collapse:collapse;">
	<tbody>
		<tr>
			<td width="100%" height="20" bgcolor="#F7F7F7"></td>
		</tr>
		<tr>
			<td align="center">
			
				<table id="email-main-table" class="w640" width="640" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; border:1px solid #DDDDDD;">
					<tbody>
						<tr>
							<td width="100%" align="center" valign="top">
							
								<table id="email-header" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
									<tbody>
										<tr>
											<td valign="top" style="border-bottom:1px solid #DDDDDD;">
												<a href="http://allotransport.com" style="display:block;">
													<img src="http://<?PHP echo $_SERVER['HTTP_HOST']; ?>/images/email-header.jpg" width="100%" height="auto" border="0" align="top">
												</a>
											</td>
										</tr>
									</tbody>
								</table>
								
							</td>
						</tr>
						<tr>
							<td width="100%" valign="top" bgcolor="#FFFFFF">
							
								<table id="email-content" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
									<tbody>
										<tr>
											<td width="5%"></td>
											<td width="90%" valign="top">

												<table class="email-content-bloc" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
													<tbody>
														<tr><td width="100%" height="20" bgcolor="#FFFFFF"></td></tr>
														<tr>
															<td width="100%" valign="top">
															
																<!-- Begin content -->
																<?php echo $content; ?>
																<!-- End content -->
																
															</td>
														</tr>
														<tr><td width="100%" height="10"></td></tr>
													</tbody>
												</table>

											</td>
											<td width="5%"></td>
										</tr>
									</tbody>
								</table>
								
							</td>
						</tr>
						<tr><td width="100%" height="20" bgcolor="#ffffff"></td></tr>
						<tr>
							<td width="100%">
								<table width="100%" cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td width="5%"></td>
										<td width="90%" align="left">
											<p style="font-family:<?PHP echo $fontFamily; ?>; font-size:12px; color:#777777;">Ce courriel vous a été envoyé automatiquement par le système Allo Transport. NE RÉPONDEZ PAS à ce courriel, car aucun destinataire ne recevrait votre message.</p>
										</td>
										<td width="5%"></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr><td width="100%" height="20"></td></tr>
						<tr>
							<td width="100%">
							
								<table id="email-footer" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#054B91" style="background-color: #054B91; color: #FFFFFF;">
									<tbody>
										<tr>
											<td width="5%"></td>
											<td width="50%" height="25"></td>
											<td width="40%"></td>
											<td width="5%"></td>
										</tr>
										<tr>
											<td width="5%"></td>
											<td width="50%" valign="top" align="left">
												<p class="email-footer-content-left" style="font-family: <?PHP echo $fontFamily; ?>; font-size: 13px; line-height: 13px; color: #FFFFFF; margin-top: 0; margin-bottom: 0; white-space: normal;">Corporation du transport collectif comté Roberval inc.</p>
												<a href="https://www.facebook.com/pages/Allo-Transport-comté-Roberval/351392288276650" style="display:block; margin-top:30px; outline:none;" title="Allo Transport sur facebook">
													<img src="http://<?PHP echo $_SERVER['HTTP_HOST']; ?>/images/email-facebook-icon.jpg" style="border:none;" width="34" height="34" alt="facebook" title="Visitez-nous sur facebook" />
												</a>
											</td>
											
											<td width="40%" valign="top" align="right">
												<p class="email-footer-content-right" style="font-family: <?PHP echo $fontFamily; ?>; font-size: 12px; line-height: 16px; color: #FFFFFF; margin-top: 0; margin-bottom: 0; white-space: normal;">
													<span>825 boul. Sacré-Coeur</span><br>
													<span>Saint-Félicien, Québec</span><br>
													<span>G8K 1S7</span><br>
													<span><a href="mailto:info@allotransport.com" style="color: #A8D8A0; text-decoration:none;"><span style="color:#A8D8A0;">info@allotransport.com</span></a></span><br>
													<span><a href="http://allotransport.com" style="color: #A8D8A0; text-decoration:none;" title="Site Web"><span style="color:#A8D8A0;">allotransport.com</span></a></span>
												</p>
											</td>
											<td width="5%"></td>
										</tr>
										<tr>
											<td width="5%"></td>
											<td width="50%" height="20"></td>
											<td width="40%"></td>
											<td width="5%"></td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td width="100%" height="60"></td>
		</tr>
	</tbody>
</table>

</body>
</html>