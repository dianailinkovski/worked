<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td>
			<?=$failures?> of <?=$attempts?> enforcement emails failed to be delivered.
			The details of the failures are described below.
		</td>
	</tr>
	<tr>
		<td>

			<table cellspacing="0" cellpadding="0" border="0" width="100%" style="padding-top: 50px;">
				<tr>
					<th align="left">Failures</th>
					<th align="left">Reason for Failure</th>
				</tr>
				<?php if ( ! empty($failed)):
						for ($i = 0, $n = count($failed); $i < $n; $i++): ?>
				<tr>
					<td>
						<?php
						$notification = $failed[$i];
						if ( ! empty($notification['name_to']))
							echo $notification['name_to'] . ' &lt;' . $notification['email_to'] . '&gt;';
						else
							echo $notification['email_to'];
						?>
					</td>
					<td>
						<?php
						$exception = $exceptions[$i];
						echo empty($exception) ? 'Unknown Error' : $exception;
						?>
					</td>
				</tr>
					<?php
						endfor;
					endif;
				?>
			</table>

		</td>
	</tr>
</table>
