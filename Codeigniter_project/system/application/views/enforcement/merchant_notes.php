<div class="merchant_notes">	
<!-- Merchant Note List -->
<?php if ( $notes_count > 0 ) :?>
	<?php foreach ($notes as $note ) : ?>
		<div class="merchant_note">
			<b><?= date('m/d/Y h:i A', strtotime($note->date))?></b>, by <b><?= $note->reporter_name?></b> on <b><?= $note->company?></b>
			
			<p><b><?= $note->type_of_entry?>:</b> <?= nl2br($note->entry)?></p>
		</div>
	<?php endforeach;?>
<?php else: ?>
	Empty discussion.
<?php endif;?>
</div>

<?php 
if ( $notes_count > 0 ) {
	$page_endrow = ($page+1) * $page_rows;
	if ( $page_endrow > $notes_count ) $page_endrow;
	$page_total_count = ceil($notes_count / $page_rows);
	
?>
<div class="note_pagination">
	<span><?= $page * $page_rows + 1?>-<?= ($page+1) * $page_rows > $notes_count ? $notes_count : ($page+1) * $page_rows ?> of <?= $notes_count?></span>
	<div class="pages">
		Got to:
	<?php for ( $i = 1; $i <= $page_total_count; $i ++ ):?>
		&nbsp;
		<?php if ( $i - 1 == $page ):?>
		<a href="#" class="actived"><?= $i?></a>
		<?php else:?>
		<a href="javascript:get_note_of_merchant(<?= $i-1?>)" tilte="Go to <?= $i?>"><?= $i?></a>
		<?php endif;?>
	<?php endfor;?>
	</div>
	<div style="clear:borth"></div>
</div>
<?php 
}
?>