<?php
ob_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Export to Excel</title>
<style type="text/css">
.test { font-family:sans-serif}
</style>
</head>
<body><?php
if( ! empty($graph_image_name)): ?>
	<img src="http://<?=$this->config->item('s3_bucket_name').'/stickyvision/graph_images/'.$store_id.'/'.$graph_image_name;?>" width="760" /></td>
<?php
endif;
foreach($report as $key => $rep) { ?>
<table cellspacing="0" cellpadding="5" border="0">
<?php if($key > 0){ ?>
	<tr><td>&nbsp;</td></tr>
<?php }?>
	<tr>
		<td><?php echo $rep['title'] ?></td>
	</tr>
</table>
<table cellspacing="0" cellpadding="0" border="0" style="font-family:Verdana, Arial, Helvetica, sans-serif;"><?php
foreach($rep['table'] as $k => $table) {
	$selector = ($k == 0) ? 'th' : 'td';
	$rowCount = 0;
	foreach($this->mvexport->getContentRow($table) as $key_ => $tr){
		echo '<tr>';
		$cellNum = 0;
		foreach($this->mvexport->getRowColumns($tr, $selector) as $td){
			$txt = $td->innertext();
			if($cellNum == 0 && $k !=0 && $time_frame != '24'){
				//link to daily report
				if($anchor = $td->find('a', 0)){
					$aHref = $anchor->getAttribute('href');
					$sText  = strip_tags($txt);
					$txt = '<a href="'.$aHref.'">'.$sText."</a>";
				}
			}elseif($cellNum == 1 && $k !=0 && $time_frame == '24'){
				//link to merchant details
				if($anchor = $td->find('a', 0)){
					$aHref = $anchor->getAttribute('href');
					$sText  = strip_tags($txt);
					$txt = '<a href="'.$aHref.'">'.$sText."</a>";
				}
			}elseif($cellNum == 9 && $k !=0 && $time_frame == '24'){
				//last column shows product listing
				if($anchor = $td->find('a', 0)){
					$aHref = $anchor->getAttribute('href');
					$sText  = strip_tags($txt);
					$txt = '<a href="'.$aHref.'">'.$sText."</a>";
				}
			}
			echo '<'.$selector.'>'.$txt.'</'.$selector.'>';
			$cellNum++;
		}
		echo "</tr>\n";
		$rowCount++;
	}
}
?>
</table>
<?php }
$this->mvexport->clear();?>
</body>
</html>

<?php
$output = ob_get_contents();
ob_end_clean();

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"".trim($fileName.' '.$report_title).".xls\"");
header('Content-Length: ' . strlen($output));
header("Pragma: no-cache");
header("Expires: 0");

echo $output;
