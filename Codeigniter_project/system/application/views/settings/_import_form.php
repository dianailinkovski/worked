	<div class="tabs bigTabs clear"><!-- needs tabs class -->
		<?=$this->load->view('components/overview_header', array('report_name' => 'Upload Products'), TRUE)?>

  <div class="content_dashboard">
    <form action="<?php echo base_url();?>data_management/saveCsvData/" method="post" id="formID" onsubmit="return validateSubmit('saved_colsNames');">
	    <input type="hidden" name="hasHeader" id="hasHeader" value="<?=$hasHeader;?>">
      <div class="heading_section  clearfix">
        <div style="padding-top:5px; float:right;">
          <span class="fl pad6">
            <a class="button clearfix" id="saveImportData" href="javascript:void(0);">
              <span class="lft_area"></span>
              <span class="rpt_content">Import Items Now</span>
              <span class="rgt_area"></span>
            </a>
          </span>
        </div>
      </div>
      <div class="content_accordian">
        <div id="content_1" class="disp_content_white">
          <?php if (isset($error)) { ?>
            <div class="error" style="margin-bottom:10px;"><?php echo $error; ?></div>
          <?php } ?>

          <p>Tell us how your data matches up to our system.</p>
          <div>
            <input type="hidden" value="" name="json_array" id="json_array" />
            <input type="hidden" name="cols_count" id="cols_count" value="<?php if (isset($headerColumns)) echo count($headerColumns); ?>" />
            <input type="hidden" name="rows_count" id="rows_count" value="<?php if (isset($dataArray)) echo count($dataArray); ?>" />
            <input type="hidden" name="filename" id="filename" value="<?php echo $filename; ?>" />
            <div id="saved_colsNames"></div>
            <table width="100%" id="csvImportTable" border="0" cellspacing="0" cellpadding="5" class="rpt_area">
              <thead>
                <tr>
                  <th id="th_0" class="editSelect">
                    <!--<div>name this column</div>-->
                    <select id="0" name="0" onclick="changeBg(0);" onchange="javascript:saveColName(0);">
                      <option value="">Select</option>
                      <option value="ignore">Ignore</option>
                      <option value="sku">SKU</option>
                      <option value="title">Item Name</option>
                      <option value="upc_code">UPC</option>
                      <option value="retail_price">Retail Price</option>
                      <option value="price_floor">MAP</option>
                      <option value="wholesale_price">Wholesale Price</option>
                    </select>
                    <br />
                    <a href="javascript:void(0);" onclick="saveColName(0);"><img src="<?php echo base_url(); ?>images/71.png" alt="Save" /></a>&nbsp;<a href="javascript:void(0);" onclick="deleteColField(0);"><img src="<?php echo base_url(); ?>images/55.png" alt="Ignore" /></a>
                  </th>
                  <?php
 if (isset($headerColumns)) { for ($i = 1; $i < count($headerColumns); $i++) { ?>
                      <th id="th_<?php echo $i; ?>"><p>unnamed column<br /><a href="javascript:void(0);" onclick="showEditField(<?php echo $i; ?>);"><img src="<?php echo base_url(); ?>images/2.png" alt="Match" /></a>&nbsp;<a href="javascript:void(0);" onclick="deleteColField(<?php echo $i; ?>);"><img src="<?php echo base_url(); ?>images/55.png" alt="Ignore" /></a></p></th>
                  <?php
 } } ?>
                </tr>
              </thead>
              <tbody>
                <?php
 for ($i = 0; $i < count($dataArray); $i++) { if ($i > CSV_RECORDS_PER_PAGE) break; if (isset($data_row_error)) { for ($k = 0; $k < count($data_row_error); $k++) { echo "<style>#rows_reports_" . $data_row_error[$k] . " {background: none repeat scroll 0 0 #F2F2F2 !important;}</style>"; } } ?>
                  <tr class="row_reports" id="rows_reports_<?php echo $i; ?>">
                    <?php
 for ($j = 0; $j < count($dataArray[$i]); $j++) { if ($hasHeader && $i == 0) { ?>
                    <input type="hidden" name="header_<?php echo $i . '_' . $j; ?>" id="header_<?php echo $i . '_' . $j; ?>" value="<?php echo htmlentities(remove_non_ascii($dataArray[$i][$j])); ?>" />
                    <td id="<?php echo $i . '_' . $j; ?>"><strong><?php echo htmlentities(remove_non_ascii($dataArray[$i][$j])); ?></strong></td>
                  <?php } else { ?>
                    <td class="edit" id="<?php echo $i . '_' . $j; ?>"><?php echo htmlentities(remove_non_ascii($dataArray[$i][$j])); ?></td>
                    <?php
 } } ?>
                </tr>

            <?php } ?>
              </tbody>
            </table>
<?php if (count($dataArray) > CSV_RECORDS_PER_PAGE) { ?>
            <div id="show_more_rows" align="center">
              <a class="clearfix" style="width:120px; display:block;" onclick="javascript:show_more();" href="javascript:void(0);">
                <span class="lft_area"></span>
                <span class="rpt_content"><< Show More >></span>
                <span class="rgt_area"></span>
              </a>
            </div>
<?php } ?>
          </div><!-- end entry... -->
        </div>
      </div>
    </form>
  </div>
</div>