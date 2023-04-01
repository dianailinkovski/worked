<table id="email-background-table" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #EEEEEE; border-collapse:collapse; font-size:16px;">
	<tbody>
		<tr>
			<td align="center">
			
				<table id="email-main-table" class="w640" width="640" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
					<tbody>
						<tr>
							<td width="100%" valign="top">
							
								<table id="email-header" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
									<tbody>
										<tr>
											<td width="100%" height="20" bgcolor="#EEEEEE"></td>
										</tr>
										<tr>
											<td valign="top">
												<a href="<?PHP echo $this->createAbsoluteUrl('/site/index'); ?>" style="display:block;">
													<img src="http://<?PHP echo $_SERVER['HTTP_HOST']; ?>/images/email-header.jpg" width="100%" height="auto" alt="" border="0" align="top" style="border-radius:6px 6px 0px 0px; -moz-border-radius: 6px 6px 0px 0px; -webkit-border-radius:6px 6px 0px 0px;">
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
											
										<?php
										foreach ($feeds as $feed):
										
											if ((isset($feed['content']) && $feed['content'] != '') || isset($feed['items']) && count($feed['items']) > 0):
											?>
	
												<table class="email-content-bloc" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
													<tbody>
														<tr><td width="100%" height="20" bgcolor="#FFFFFF"></td></tr>
														
													<?PHP if (isset($feed['title'])):		//////////// Feed Title. ?>
																												
														<tr>
															<td width="100%" valign="top">
																<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
																	<tbody>
																		<tr>
																			<td valign="bottom" align="left" width="75%">
																				<div class="email-content-bloc-title" style="line-height:1em; font-family: Georgia, serif; font-size:2em; color:#2F424E;"><?php echo CHtml::encode($feed['title']); ?></div>
																			</td>
																			<td valign="bottom" align="right" width="25%">
																				<?php if ($feed['link'] != ''):	//////////// Link to more article from that feed. ?>
																				<div class="email-content-bloc-more" style="font-family: Arial, Helvetica, sans-serif; font-size:0.8em; text-align:right;">
																					<a href="<?php echo $feed['link']; ?>" style="color:#00B1B8; text-decoration:none;" title="<?PHP echo Yii::t('newsletterModule.common', 'Voir plus'); ?>">[+]</a>
																				</div>
																				<?php endif; ?>
																			</td>
																		</tr>
																	</tbody>
																</table>
															</td>
														</tr>
														
													<?PHP endif; ?>
														
													<?php if (isset($feed['content'])):	//////////// User static content. ?>
															
														<tr>
															<td width="100%" valign="top">
																<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
																	<tbody>
																		<tr>
																			<td valign="top" align="left">
																				<div class="email-content-bloc-content" style="line-height:1.4em; font-family: Arial, Helvetica, sans-serif; font-size:0.9em; color:#666666;">
																					<?php echo $feed['content']; ?>
																				</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
															</td>
														</tr>
														<tr><td width="100%" height="10"></td></tr>
														
													<?php
													else:
														
														foreach ($feed['items'] as $item):		//////////// Feed articles.
														
															$articleDescription = $item->get_description();
															$articleImage = "";
															if (!empty($articleDescription) and strpos($articleDescription, "<img") > 0):
																$posStartImgSrc = strpos($articleDescription, "src=")+5;
																$posEndImgSrc = strpos($articleDescription, '"', $posStartImgSrc+6);
																$articleImage = substr($articleDescription, $posStartImgSrc, $posEndImgSrc-$posStartImgSrc);
																
																$posStartDescription = strpos($articleDescription, "<div>", $posEndImgSrc)+5;
																$posEndDescription = strpos($articleDescription, "</div>", $posStartDescription);
																$articleDescription = substr($articleDescription, $posStartDescription, $posEndDescription-$posStartDescription);
															endif;
															?>
														<tr>
															<td width="100%" valign="top">
															
																<table class="email-content-article" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
																	<tbody>
																		<tr><td width="100%" height="20"></td></tr>
																		<tr>
																			<td>
																				<div class="email-content-article-title" style="font-family: Arial, Helvetica, sans-serif; font-size:1.6em; line-height:1.2em;">
																					<a href="<?php echo $item->get_permalink(); ?>" style="color:#00B1B8; text-decoration:none;"><span style="color:#00B1B8;"><?php echo $item->get_title(); ?></span></a>
																				</div>
																			</td>
																		</tr>
																		<tr><td width="100%" height="8"></td></tr>
																		<tr>
																			<td>
																				<div class="email-content-article-date" style="font-family: Arial, Helvetica, sans-serif; font-size:0.8em; color:#666666;"><?php echo Yii::t('newsletterModule.common', 'Posté le'); ?> <?php echo Helper::formatDate($item->get_date('Y-m-d H:i:s'), 'reg'); ?></div>
																			</td>
																		</tr>
																		<tr><td width="100%" height="20"></td></tr>
																		<tr>
																			<td>
																				
																				<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
																					<tbody>
																						<tr>
																							<?PHP if ($articleImage != ""):	/////////// Article Image. ?>
																							
																							<td align="left" valign="top">
																								<div class="email-content-article-image">
																									<a href="<?php echo $item->get_permalink(); ?>"><img class="w250" width="250" src="<?PHP echo $articleImage; ?>" alt="<?php echo $item->get_title(); ?>" border="0"></a>
																								</div>
																							</td>
																							<td class="w20" width="20"></td>
																							
																							<?php endif; ?>
																							
																							<td align="left" valign="top">
																								<?php 
																								if (!empty($articleDescription)):	///////// Article Abstract.
																								?>
																								<div class="email-content-article-abstract">
																									<p style="margin-top:0; margin-bottom:1em; line-height:1.4em; font-family: Arial, Helvetica, sans-serif; font-size:0.9em; color:#666666;"><?php echo $articleDescription; ?></p>
																								</div>
																								<div class="email-content-article-permalink" style="font-family: Arial, Helvetica, sans-serif; font-size:0.9em;">
																									<a href="<?php echo $item->get_permalink(); ?>" style="display:inline-block; background-color:#00B1B8; padding:0.4em 0.8em; color:#ffffff; text-decoration:none;"><span style="color:#ffffff;"><?PHP echo Yii::t('newsletterModule.common', 'Lire la suite'); ?></span></a>
																								</div>
																								<?php endif; ?>
																							</td>
																						</tr>
																					</tbody>
																				</table>
																				
																			</td>
																		</tr>
																		<tr><td width="100%" height="25" style="border-bottom:#CCCCCC 1px dotted;"></td></tr>
																	</tbody>
																</table>
																

															</td>
														</tr>
														<tr><td width="100%" height="10"></td></tr>
															<?php
															endforeach;
														endif;
														?>
														
													</tbody>
												</table>
												
												<?PHP
												endif;
												
											endforeach;
											?>
												
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
							
								<table id="email-footer" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#585550" style="background-color: #585550; color: #FFFFFF;">
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
												<p class="email-footer-content-left" style="font-family: Arial, Helvetica, sans-serif; font-size: 0.8em; line-height: 1.4em; color: #FFFFFF; margin-top: 0; margin-bottom: 0; white-space: normal;"><?PHP echo htmlentities(Yii::t('newsletterModule.common', 'Vous recevez ceci parce que vous êtes abonné à l’infolette'), ENT_NOQUOTES, 'UTF-8'); ?> <a href="<?PHP echo $this->createAbsoluteUrl('/newsletter/default/unsuscribe', array('token'=>'{{{token}}}')); ?>" style="color: #6AD2FF; text-decoration:none;"><span style="color:#00B1B8;"><?PHP echo htmlentities(Yii::t('newsletterModule.common', 'Je veux me désabonner'), ENT_NOQUOTES, 'UTF-8'); ?></span></a></p>
											</td>
											
											<td width="40%" valign="top" align="right" style="text-align:right;">
												<p class="email-footer-content-right" style="font-family: Arial, Helvetica, sans-serif; font-size: 0.7em; line-height: 1.4em; color: #FFFFFF; margin-top: 0; margin-bottom: 0; white-space: normal;">
													<span><?PHP echo htmlentities(Yii::t('newsletterModule.common', 'Siège social'), ENT_NOQUOTES, 'UTF-8'); ?></span><br>
													<span>111 adresse,</span><br>
													<span>Saint-Félicien, Qu&eacute;bec</span><br>
													<span>1A1 A1A</span><br>
													<span><a href="mailto:info@exemple.com" style="color: #6AD2FF; text-decoration:none;"><span style="color:#00B1B8;">info@exemple.com</span></a></span><br>
													<span><a href="<?PHP echo $this->createAbsoluteUrl('/site/index'); ?>" style="color: #6AD2FF; text-decoration:none;"><span style="color:#00B1B8;">www.exemple.com</span></a></span>
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
						<tr>
							<td width="100%" height="60"></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>