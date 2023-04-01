<?PHP if ($newMessageCount > 0): ?>

<a id="message-modal-link" class="has-new-messages" href="<?php echo $this->controller->createUrl('/message/default/modal'); ?>" data-icon="1" data-toggle="modal" data-target="<?PHP echo ($this->dataTarget === null ? '#messages-modal' : $this->dataTarget); ?>" title="Vous avez <?php echo $newMessageCount; ?> nouveau<?PHP echo ($newMessageCount > 1) ? "x" : ""; ?> message<?PHP echo ($newMessageCount > 1) ? "s" : ""; ?>"><span class="message-number">(<?php echo $newMessageCount+$oldMessageCount; ?>)</span></a>

<?PHP elseif ($oldMessageCount > 0): ?>

<a href="<?php echo $this->controller->createUrl('/message/default/modal'); ?>" data-icon="1" data-toggle="modal" data-target="<?PHP echo ($this->dataTarget === null ? '#messages-modal' : $this->dataTarget); ?>"  title="Vous avez <?php echo $oldMessageCount; ?> message<?PHP echo ($oldMessageCount > 1) ? "s" : ""; ?>"><span class="message-number">(<?php echo $oldMessageCount; ?>)</span></a>

<?PHP else: ?>

<a href="<?php echo $this->controller->createUrl('/message/default/modal'); ?>" data-icon="0" data-toggle="modal" data-target="<?PHP echo ($this->dataTarget === null ? '#messages-modal' : $this->dataTarget); ?>"  title="Vous n'avez aucun message"><span class="message-number">(0)</span></a>

<?PHP endif; ?>


<?PHP if ($this->dataTarget === null): ?>
<div class="modal fade ajax-modal" id="messages-modal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>
<?php endif; ?>