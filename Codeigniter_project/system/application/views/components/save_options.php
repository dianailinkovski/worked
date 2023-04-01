<!-- start save_options -->
<div class="buttonArea clear">
    <div class="resource" id="save_report">
        <img src="<?= frontImageUrl(); ?>icons/24/23.png" alt="Save" title="Save" width="24" height="24" />
    </div>
    <div id="sendEmail" class="resource">
        <img alt="email" src="<?= frontImageUrl(); ?>icons/24/54.png" width="24" height="24" title="Send Email" />
    </div>
    <div id="excel_export" class="resource">
        <img alt="excel" src="<?= frontImageUrl(); ?>icons/24/128.png" width="24" height="24" title="Export to Excel" />
    </div>
    <div id="pdf_export" class="resource">
        <img alt="pdf" src="<?= frontImageUrl(); ?>icons/pdf.png" width="24" height="24" title="Export to PDF"/>
    </div>
    <?php if (!empty($show_notify_resource)): ?>
        <!-- What does this do? (Christophe - 8/24/2015) -->
        <!--  
        <div id="violator_notification" class="resource">
            <img alt="notify" src="<?= frontImageUrl(); ?>/icons/24/10.png" width="24" height="24" title="Show Notify"/>
        </div>
        -->
    <?php endif; ?>
</div>
<div id="sendEmailDialog" title="Send Email" class="modalWindow dialog">
    <form id="sendEmailForm" name="send_email_form" class="clear">
        <div id="sendEmailMessage"></div>
        <div id="report_email_success_message"></div>
        <p>Email will be sent to the selected email addresses.</p>
        <input type="text" name="email_addresses" id="email_addresses" value="">
		    <div class="error"></div>
        <div class="email_container"></div>
    </form>
</div>
<form name="exportForm" id="exportForm" method="post">
    <input type="hidden" id="report_id" name="report_id" value="<?= !empty($report_id) ? $report_id : ''; ?>" />
    <input type="hidden" id="report_name" name="report_name" value="<?= !empty($report_name) ? $report_name : 'Violations Report'; ?>" />
    <input type="hidden" id="controller" name="controller" value="<?= $controller ?>" />
    <input type="hidden" id="controller_function" name="controller_function" value="<?= !empty($report_where['by_which']) ? $report_where['by_which'] : $method ?>" />
    <input type="hidden" id="export_content" name="export_content" value="0" />
    <input type="hidden" id="file_name" name="file_name" value="<?= isset($file_name) ? $file_name : ''; ?>" />
    <input type="hidden" id="report_where" name="report_where" value="<?= (isset($report_where) && is_array($report_where) ? htmlentities(json_encode($report_where), ENT_QUOTES) : '') ?>" />
    <input type="hidden" name="graph_data" id="graph_data" value="<?= isset($graphDataType) ? $graphDataType : ''; ?>" />
</form>
<!-- end save_options -->
