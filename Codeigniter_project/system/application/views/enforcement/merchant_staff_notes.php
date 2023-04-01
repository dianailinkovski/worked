<div class="merchant_notes <?= $permission_id == 1 ? "merchant_notes_extends_list":""?>">	    
    
    <?php if ( $notes_count > 0 ) :?>
        <?php foreach ($notes as $note ) : ?>
		        <div class="merchant_note clear" id="staff_note_<?= $note['id']; ?>">
                <div class="merchant_note_action">
                    <input type="checkbox" value="<?= $note['id']; ?>" class="staff_note_select" />
                </div>
                <div class="merchant_note_body">
                    <b><?= date('m/d/Y h:i A', strtotime($note['date']))?></b>, by <b><?= $note['user_first_name']; ?> <?= $note['user_last_name']; ?></b>
                    <p class="entry"><?= str_replace("\n", "<br>", $note['entry'])?></p>
                </div>
                <div class="note_action">
                    <a href="javascript:delete_staff_note_of_merchant(<?= $merchant->id?>, <?= $note['id']?>)">
                        <img src="<?= frontImageUrl()?>icons/16/69.png" />
                        Delete
                    </a>
                    &nbsp;|&nbsp;
                    <a href="javascript:edit_staff_note_of_merchant(<?= $merchant->id?>, <?= $note['id']?>)">
                        <img src="<?= frontImageUrl()?>icons/16/60.png" />
                        Edit
                    </a>
                    &nbsp;
                </div>
            </div>
	      <?php endforeach;?>
    <?php else: ?>
        <p>
	          There aren't any notes currently available to show.
	      </p>
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
		<a href="javascript:get_staff_note_of_merchant(<?= $i-1?>)" tilte="Go to <?= $i?>"><?= $i?></a>
		<?php endif;?>
	<?php endfor;?>
	</div>
	<div style="clear:borth"></div>
</div>
<?php 
}
?>