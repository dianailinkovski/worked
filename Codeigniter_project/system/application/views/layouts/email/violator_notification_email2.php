<?php
//$as_html = TRUE;
//$company = 'Company Co.';
//$name_to = 'John Doe';
//$title = 'MAP Violation';
//$email_to = 'John@doe.com';
//$phone = '530-582-1021';
//$evidence = <<< EVIDENCE
//<table>
//	<tr>
//		<th>Date</th>
//		<th>Product</th>
//		<th>Wholesale</th>
//		<th>Retail</th>
//		<th>MAP</th>
//		<th>Price</th>
//		<th>Violation</th>
//		<th>Screenshot</th>
//	</tr>
//	<tr>
//		<td>09/26/2012</td>
//		<td>Product 1</td>
//		<td>$50.00</td>
//		<td>$60.00</td>
//		<td>$55.00</td>
//		<td>$40.00</td>
//		<td>Low</td>
//		<th>http://stickyvision.juststicky.com/screenshot1</th>
//	</tr>
//</table>
//EVIDENCE;
$greeting = ! empty($name_to) ? 'To ' . $name_to : 'To whom it may concern at ' . $merchant;
$name_to = ! empty($name_to) ? $name_to : $merchant;
if ($as_html):
?>
<table id="letter" width="650" border="0" cellspacing="0" cellpadding="0" style="margin: 50px;">
	<tr id="header" style="font-weight: bold;">
		<td id="title" align="center">
			<?php echo $company ?><br /><br />

			<?php echo date('m-d-Y') ?><br /><br />
		</td>
	</tr>
	<tr style="font-weight: bold">
		<td>
			<?php echo $name_to ?><br />
			<?php echo $title ?><br />
			<?php echo $email_to ?><br /><br />
			<span style="margin-left: 50px; font-style: italic;">Re:	&emsp;Continued Violation of  <?php echo $company ?> MAP Policy</span><br /><br />
		</td>
	</tr>
	<tr id="body1">
		<td>
			<?php echo $greeting ?>,<br /><br />

				&emsp;&emsp;You are receiving this letter because you continue to violate the Minimum Advertised Pricing Policy (the "<u>MAP Policy</u>") of <?php echo $company ?> (the "<u>Company</u>").  Evidence of this continued violation is included as <u>Attachment 1</u> to this letter.<br /><br />
		</td>
	</tr>
	<tr id="warning" style="font-weight: bold;">
		<td align="center">
			THIS LETTER WILL SERVE AS YOUR FINAL WARNING<br /><br />
			CEASE ALL VIOLATIONS IMMEDIATELY<br /><br />
		</td>
	</tr>
	<tr id="body2">
		<td>
				&emsp;&emsp;Your ability to purchase products from the Company continues to be suspended.  To avoid final action by the Company, call us immediately, and no later than 24 hours from your receipt of this letter, at the following number: <?php echo $phone ?>.<br /><br />

				&emsp;&emsp;The Company has not waived (and is not waiving) any of its rights or remedies arising in connection with the foregoing, and the Company hereby expressly reserves and preserves all such rights and remedies.  If you do not comply with the conditions set forth above, you will be deemed to be engaging in activity with the <i>intent to harm</i> the Company, and the Company will pursue whatever legal actions may be necessary to protect its rights under applicable law, which potentially include initiating legal proceedings against you to cease these activities, including a suit for damages for violation of the MAP Policy.<br /><br />

				&emsp;&emsp;Losses to the Company resulting from one violation of the MAP Policy can be significant, and can negatively affect the entire market and our brand value.  Damages suffered by the Company and resulting from your activities will be your responsibility.<br /><br /><br /><br />
		</td>
	</tr>
	<tr id="salutation">
		<td style="padding-left: 375px">
			Sincerely,<br /><br />

			<span style="font-weight: bold;"><?php echo $company ?></span>
		</td>
	</tr>
</table>
<table width="650" border="0" cellspacing="0" cellpadding="0" style="margin: 50px;">
	<tr id="evidence">
		<td align="center">
			<?php echo $evidence ?>
		</td>
	</tr>
</table>

<?php
else: ?>

<?php echo $company ?>

<?php echo date('m-d-Y') ?>


<?php echo $name_to ?>
<?php echo $title ?>
<?php echo $email_to ?>

	Re:		Continued Violation of  <?php echo $company ?> MAP Policy

<?php echo $greeting ?>,

	You are receiving this letter because you continue to violate the Minimum Advertised Pricing
Policy (the "MAP Policy") of <?php echo $company ?> (the "Company").  Evidence of this continued violation is
included as Attachment 1 to this letter.

THIS LETTER WILL SERVE AS YOUR FINAL WARNING

CEASE ALL VIOLATIONS IMMEDIATELY

	Your ability to purchase products from the Company continues to be suspended.  To avoid final
action by the Company, call us immediately, and no later than 24 hours from your receipt of this letter,
at the following number: <?php echo $phone ?>.

	The Company has not waived (and is not waiving) any of its rights or remedies arising in
connection with the foregoing, and the Company hereby expressly reserves and preserves all such rights
and remedies.  If you do not comply with the conditions set forth above, you will be deemed to be
engaging in activity with the intent to harm the Company, and the Company will pursue whatever legal
actions may be necessary to protect its rights under applicable law, which potentially include initiating
legal proceedings against you to cease these activities, including a suit for damages for violation of the
MAP Policy.

	Losses to the Company resulting from one violation of the MAP Policy can be significant, and
can negatively affect the entire market and our brand value. Damages suffered by the Company and
resulting from your activities will be your responsibility



														Sincerely,

														<?php echo $company ?>

<?php
endif;
