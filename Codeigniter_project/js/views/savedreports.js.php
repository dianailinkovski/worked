<?php
echo script_tag('js/jqwidgets/globalization/jquery.global.js');
echo script_tag('js/jqwidgets/globalization/jquery.glob.en-US.js');
?>
<script type="text/javascript">
$(document).ready(function(){
	$(".reportName").on('click', function (){
		var r = $(this).parents('tr:first').attr('id');
		$('#'+r+' .reportName').toggleClass("hidden");
		$('#'+r+' .reportDetails').toggleClass("hidden");
	});

	$('.report_datetime').datepicker({
		dateFormat: 'mm-dd-yy',
		minDate:'mm-dd-yy',
	});

	$('.save_report').on('click', function(){
		//get the proper form (could be many)
		var f = $(this).parents('form:first').attr('id');
		var p = checkRptFields(f);
		if(p.passed){
			if(!$('#'+f+' .rpt_error').hasClass('hidden')) $('#'+f+' .rpt_error').addClass('hidden');
			var data = $('#' + f).serialize();
			var saveReportCB = function(response){
				if($('#'+f+' .sch_prods').hasClass('hidden'))
					$('#'+f+' .sch_prods').removeClass('hidden');
			}
			$.post(base_url+"savedreports/save_report", data, saveReportCB, 'json');
		}else{
			$('#'+f+' .rpt_error').html(p.message);
			if($('#'+f+' .rpt_error').hasClass('hidden')) $('#'+f+' .rpt_error').removeClass('hidden');
		}
	});

	//misc form actions
	$(".cancel_savedreports").on('click', function (){
		//trigger row close...
		var r = $(this).parents('tr:first').attr('id');
		$('#'+r+' .reportName').toggleClass("hidden");
		$('#'+r+' .reportDetails').toggleClass("hidden");
	});

	$('select[name="report_recursive_frequency"]').on('change', function(){
		if($(this).val()=='0'){
			$(this).next('input[name="report_recursive_frequency"]').val('0');
		}else{
			$(this).next('input[name="report_recursive_frequency"]').val($(this).val());
		}
	});
});

function checkRptFields(f){
	var valid = { passed: true, message : '' }
	var n = $('#'+f+' input[name="report_name"]').val();
	if(n == undefined || n == ''){
		valid.passed = false;
		valid.message += 'Report Name is required';
	}

	if($('#'+f+' input[name="report_recursive_frequency"]').val() > 0){
		var e = $('#'+f+' input[name="email_addresses[]"]').map(function(){return $(this).val();}).get();
		if(e == undefined || e.length == 0){
			valid.passed = false;
			valid.message += (valid.message.length > 0)?'<br>':'';
			valid.message += 'Email addresses are required for recurring reports';
		}
	}
	return valid;
}
</script>