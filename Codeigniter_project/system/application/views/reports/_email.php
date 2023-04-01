<?php

$tableCellStyle = 'style="border:1px solid #E7E6E5;height:20px;padding:2px 2px 2px 5px;text-align:left;vertical-align:top"';
$solidLineStyle = 'style="background-color:#81b72a;height:1px;line-height:1px;overflow:hidden;"';

$incCols = array();

switch($report_type)
{
    case 'dns_list':
        $incCols = array(0, 1, 2, 3, 4, 5);
    case 'marketdetail':
        
    case 'merchantdetail';
        $incCols = array(0, 2, 6, 7, 8, 9);
        break;
    case 'violationdetails':
        $incCols = array(0, 4, 7, 8, 9, 10);
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

?>
<body>

<div style="color:#666;font-family:Arial,Helvetica,sans-serif;min-height:400px;">

	<table border="0" cellpadding="0" cellspacing="0" width="580">
    <tr>
    	<td width="515" align="left">
				<img src="<?=$this->config->item('public_base_url').'images/trackstreet_logo_white_293w.png';?>" alt="Logo" />
				<?php if(isset($merchant_logo) && !empty($merchant_logo)):?> <img src="<?=$merchant_logo;?>" alt="<?=$brandName;?>" /><?php endif;?>
			</td>
    	<td width="65" align="right">
				<span style="font-size:23px;font-family:'Times New Roman', Times, serif;color:#6a696e;">REPORTING</span><br />
				<span style="font-size:9px;color:#6a696e;font-family:Arial, Helvetica, sans-serif;font-weight:bold;"><?=$headerDate;?></span>
			</td>
		</tr>
		<tr>
			<td colspan="2" <?=$solidLineStyle;?>>&nbsp;</td>
		</tr>
		<tr>
			<td height="10" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2" style="color:#fff;padding:5px;font-size:12px;font-weight:bold;" bgcolor="#00a0d1"><?=$title;?></td>
		</tr>
		<?php if($graph_image_name): ?>
		    <tr>
            <td colspan="2"><img src="<?=$this->config->item('s3_cname').'/stickyvision/graph_images/'.$store_id.'/'.$graph_image_name;?>" width="760" /></td>
		    </tr>
    <?php endif;?>
	</table>
	
	<?php
	
	$sectionCount = 0;
	
	foreach ($report as $key => $rep):
		$exMargin = ($sectionCount > 0) ? 'margin-top:10px;' : '';
		$sectionCount++;
    ?>

    <?php if (empty($rep['table'])): ?>
        <p>
            No results are present in this report.
        </p>
    <?php else: ?>
    
        <table width="580" cellspacing="0" cellpadding="3" border="1" style="border-collapse:collapse;background:none repeat scroll 0 0 rgb(255, 255, 255);font-family:Arial,Helvetica,sans-serif;font-size:11px;<?=$exMargin;?>">
            <?php
            
            foreach($rep['table'] as $k => $table)
            {
            	$selector = ($k == 0) ? 'th' :'td';
            	
            	$rows = $this->mvexport->getContentRow($table);
            	
            	$rSize = count($rows);
            
            	foreach($rows as $key_ => $tr)
              {
            		if($k == 0)
                {
            			$bgColor = 'style="background:#E7E7E8;color:#000;"';
            		}
            		else
                {
            			$bgColor = ($key_ % 2 == 0) ? '' : 'style="background:#E7E7E8"';
            		}
            
            		echo '<tr '.$bgColor.'>';
            		
            		$td_html = $this->mvexport->getRowColumns($tr, $selector);
            
            		$tr_str = '';
            		$counter = 0;
            		
            		foreach($td_html as $index => $td)
                {
            			$txt = $td->innertext();
            			
            			if ($k == 0)
                  {
            				// use this to skip certain cells for the email...
            				if ($incCols != 'all' && !in_array($index, $incCols))
                    {
            					$counter++;
            					continue;
            				}
            				
            				$tr_str .= '<th '.$tableCellStyle.'>'.$txt.'</th>';
            			}
            			else
                  {
            				// table cells
            				// use this to skip certain cells for the email...
            				if ($incCols != 'all' && !in_array($index, $incCols))
            				{
            					$counter++;
            					continue;
            				}
            
            				if ($counter == 0 && $time_frame != '24')
                    {
            					// link to daily report
            					if ($anchor = $td->find('a', 0))
                      {
            						$aHref = $anchor->getAttribute('href');
            						$sText  = strip_tags($txt);
            						$txt = '<a href="'.$aHref.'">'.$sText."</a>";
            					}
            				}
            				elseif ($counter == 1 && $time_frame == '24')
                    {
            					// link to merchant details
            					if ($anchor = $td->find('a', 0))
                      {
            						$aHref = $anchor->getAttribute('href');
            						$sText  = strip_tags($txt);
            						$txt = '<a href="'.$aHref.'">'.$sText."</a>";
            					}
            				}
            				elseif ($counter == 9 && $time_frame == '24')
                    {
            					//last column shows product listing
            					if ($anchor = $td->find('a', 0))
                      {
            						$aHref = $anchor->getAttribute('href');
            						$sText  = strip_tags($txt);
            						$txt = '<a href="'.$aHref.'">'.$sText."</a>";
            					}
            				}
            				
            				$tr_str .= '<td '.$tableCellStyle.'>'.$txt.'</td>';
            			}
            			
            			$counter++;
            		}
            		
            		echo $tr_str."</tr>";
            		
            	}
            }
            
            ?>
        </table>
      <?php endif; ?>      
	<?php endforeach; ?>
	
	<table border="0" cellpadding="0" cellspacing="0" width="580">
		<tr>
			<td height="10">&nbsp;</td>
		</tr>
		<tr>
			<td width="580" <?=$solidLineStyle;?>>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>
			    <?='&copy;'.date('Y').' TrackStreet - <a href="http://www.trackstreet.com">www.trackstreet.com</a>'; ?>
			</td>
		</tr>
	</table>
	
</div>
</body>