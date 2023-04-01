<?php /* comment out for the time being
<script src="http://interact.juststicky.com/js/embed/client.js/2009/interact_<?= $interact_script_id ?>"
        id="interact_<?= $interact_script_id ?>"
        data-text="Discuss this with Sticky Interact"
        data-unique="<?= $interact_script_id ?>">
</script>
*/ ?>

<?php
for($i = 0, $n = count($javascript_files); $i < $n; $i++) {
	if (file_extension($javascript_files[$i]) === 'js') {
		$mod = '';
		if (file_exists($javascript_files[$i])) {
			if ($mtime = filemtime($javascript_files[$i])) {
				$mod = '?' . $mtime;
			}
		}
		echo script_tag($javascript_files[$i] . $mod);
	}
	else {
		require($javascript_files[$i]);
	}
}
?>

