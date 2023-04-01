<div class="preview-merchant-notes">
    <h2>Staff Notes<span><?= $notes_count?> comment<?= ($notes_count>1?'s':'')?></span></h2>
			
<?php foreach ( $staff_notes as $entry ) : ?>
	<div class="note-box">
        <b><?= date('m/d/Y h:i A', strtotime($entry->date))?></b>, by <b><?= $entry->user_name?></b>
		<br/>
		<?= nl2br($entry->entry) ?>
	</div>
<?php endforeach; ?>
            
<?php if ( $notes_count > 5 ) : ?>
	<p><span><i>(more...)</i></span></p>
<?php endif; ?>
</div>