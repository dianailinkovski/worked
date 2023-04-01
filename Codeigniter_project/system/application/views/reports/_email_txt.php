<?php
$reportString = "TrackStreet Reports - $title\r\n\r\n$brandName - $headerDate\r\n\r\nToday's Sellers:\r\n";
$sectionCount = 0;
$incCols = array();
switch($report_type){
	case 'marketdetail':
	case 'merchantdetail';
		$incCols = array(0, 2, 6, 7, 8, 9);
		break;
	case 'violationdetails':
		$incCols = array(0, 1, 4, 7, 8, 9, 10);
		break;
	case 'violator':
		$incCols = array(0, 2, 4, 7, 8, 9, 10);
		break;
	//these show all columns
	case 'marketoverview':
	case 'merchantoverview':
	default:
		$incCols = 'all';
		break;
}

foreach($report as $key => $rep){
	if($sectionCount > 0) $reportString .= "\r\n\r\n";
	$sectionCount++;

	foreach($rep['table'] as $k => $table){
		$selector = ($k == 0) ? 'th' :'td';
		$rows = $this->mvexport->getContentRow($table);
		$rSize = count($rows);

		foreach($rows as $key_ => $tr){
			$td_html = $this->mvexport->getRowColumns($tr, $selector);
			$counter = 0;
			foreach($td_html as $index => $td){
				$txt = $td->innertext();
				if($k == 0){
					//skip certain cells for the email...
					if($incCols != 'all' && !in_array($index, $incCols)){
						$counter++;
						continue;
					}
					$txt = trim($txt);
					$reportString .= ($counter > 0) ? " / $txt": "$txt";
					if(($counter+1) == sizeof($td_html)) $reportString .= ":\r\n";
				}else{
					//table cells
					//skip certain cells for the email...
					if($incCols != 'all' && !in_array($index, $incCols)){
						$counter++;
						continue;
					}
					if($counter == 0 && $time_frame != '24'){
						//link to daily report
						if($anchor = $td->find('a', 0)){
							$aHref = $anchor->getAttribute('href');
							$txt = strip_tags($txt);
						}
					}elseif(($counter == 1 || $counter == 9) && $time_frame == '24'){
						//link to merchant details -- or --
						//last column shows product listing
						if($anchor = $td->find('a', 0)){
							$aHref = $anchor->getAttribute('href');
							$txt = strip_tags($txt);
						}
					}elseif($counter == 8 && $reportType == 'whois'){
						$txt = ($anchor = $td->find('a', 0)) ? 'Violation': 'Non Violation';
					}elseif($counter == 9 && $reportType == 'whois'){
						//last column shows product listing
						if($anchor = $td->find('a', 0)){
							$aHref = $anchor->getAttribute('href');
							$txt = strip_tags($txt);
						}
					}
					$txt = trim($txt);
					$reportString .= ($counter > 0) ? ' / '.$txt: $txt;
					if(($counter+1) == sizeof($td_html)) $reportString .= "\r\n";
				}
				$counter++;
			}
		}
	}
}

$reportString .= "\r\n© ".date('Y').' Sticky Business, LLC - www.juststicky.com';
echo wordwrap($reportString);
?>