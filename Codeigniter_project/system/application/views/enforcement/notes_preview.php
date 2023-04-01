<div class="preview-merchant-notes">
    <h2>Notes<span><?= $merchant_notes_count?> comment<?= ($merchant_notes_count>1?'s':'')?></span></h2>
			
<?php foreach ( $merchant_notes as $entry ) : ?>
	<div class="note-box">
        <b><?= date('m/d/Y h:i A', strtotime($entry->date)) ?></b>, by <b><?= $entry->reporter_name ?></b> on <b><?= $entry->company ?></b>
		<br/>
		<b><?= $entry->type_of_entry ?></b>: <?= nl2br($entry->entry) ?>
	</div>
<?php endforeach; ?>
            
<?php if ( $merchant_notes_count > 2 ) : ?>
	<p><span><i>(more...)</i></span></p>
<?php endif; ?>
</div>