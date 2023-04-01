<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true" title="Fermer la fenêtre">&times;</button>
	<h4 class="modal-title" id="myModalLabel">Centre de messages d'Allo Transport</h4>
</div>

<form role="form" id="frm-messages">

	<div class="modal-body">
		<!-- Modal content -->
		
		<?PHP if (empty($messages)): ?>
		
		<p>Vous n'avez aucun message.</p>
		
		<?PHP else: ?>
		
			<?PHP foreach ($messages as $message): ?>
		
		<div class="alert alert-info alert-dismissable">
			<div class="alert-header clearfix">
				<p class="message-date"><?php echo Helper::formatDate($message->message->datetime, "reg"); ?></p>
				<?PHP if ($message->seen == 0): ?>
				<span class="message-new-tag icon-star" title="Nouveau message"></span>
				<?PHP endif; ?>
			</div>
			<button id="dismiss-button-<?php echo $message->message_id; ?>" type="button" class="close" data-dismiss="alert" aria-hidden="true" title="Ne plus voir ce message">&times;</button>
			<div class="section-bloc"><?php echo $message->message->message; ?></div>
		</div>
		
			<?PHP endforeach; ?>
		
		<?PHP endif; ?>
		
		<!-- /Modal content -->
	</div>
	
	<div class="modal-footer">
		<button type="button" class="btn btn-link" data-dismiss="modal" title="Fermer la fenêtre">Fermer</button>
	</div>

</form>

<?php Yii::app()->clientScript->registerScript('dismiss-buttons', "
	var msgCount = $('#frm-messages .alert-dismissable').length;
	
	$('[id^=\"dismiss-button\"]').click(function(){
		var id = $(this).attr('id');
		id = id.substr(id.lastIndexOf('-')+1);
		$.post('".$this->createUrl('/message/default/delete')."', {id:id});
		
		msgCount = msgCount-1;
		if (msgCount > 0){
			$('#message-modal-link').attr('title', 'Vous avez '+msgCount+' message(s)').html('<span class=\"message-number\">('+msgCount+')</span>');
		} else {
			$('#message-modal-link').attr('data-icon', '0').attr('title', 'Vous n’avez aucun message').html('<span class=\"message-number\">(0)</span>');
		}
	});
	
	$('#message-modal-link').removeClass('has-new-messages').attr('title', 'Vous avez '+msgCount+' message(s)');
", CClientScript::POS_READY); ?>