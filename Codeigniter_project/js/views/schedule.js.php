<?php
echo script_tag('js/jqwidgets/globalization/jquery.global.js');
echo script_tag('js/jqwidgets/globalization/jquery.glob.en-US.js');
?>
<script type="text/javascript">

var rowDetailTmp = '<form id="inline_schedule_edit" name="inline_schedule_edit"><div class="schd_error error hidden">Please complete all form fields</div><div class="inputBlockContainer"><div class="inputContainer"><label>Report Name:</label><input type="text" name="report_name" value=""><input type="hidden" name="report_id" value=""></div><div class="inputContainer"><label>Report Type:</label><select name="controller"><option value="reports">Price Trend Report</option><option value="violations">Violation Report</option><option value="overview">Pricing Overview</option><option value="violationoverview">Violation Overview</option></select><input type="hidden" name="contoller_function" value=""></div></div><div class="inputBlockContainer sch_prods"><div class="inputContainer"><label>Specific Product</label><input type="radio" name="report_products" value="productpricing"></div><div class="inputContainer"><label>Specific Group</label><input type="radio" name="report_products" value="group_report"></div></div><div class="inputBlockContainer"><label class="report_products_lbl">Product Name:</label><input type="text" name="report_products_val" class="report_products_val" value=""><input type="hidden" name="report_products_vals" value=""></div><div class="inputBlockContainer"><div class="inputContainer"><label>Report Schedule:</label><input type="text" name="report_datetime" class="report_datetime" size="12" value=""> <?php echo renderHourDropDown("hh")?> : <?php echo renderMinuteDropDown("mm")?> : <select name="ampm"><option value="am">am</option><option value="pm">pm</option></select></div><div class="inputContainer"><label>Recurring:</label><select name="report_recursive_frequency" onchange="markRecursive(this.value)"><option value="0">None</option><option value="1">Every Day</option><option value="7">Every Week</option><option value="31">Every Month</option><option value="365">Every Year</option></select></div></div><div class="inputBlockContainer"><label>Emails:</label><input type="text" name="email_addresses" value=""><div class="email_container"></div></div><div class="button redButton"><div class="buttonCornerL"></div><input type="button" class="save_schedule" name="save_schedule" value="Save Changes"></div><div class="button redButton"><div class="buttonCornerL"></div><input type="button" class="cancel_schedule jsLink" name="" value="Cancel/Close"></form>';

$(document).ready(function(){

	var schedUrl = '<?php echo base_url()?>schedule/schedule_reports_list?g=rando';
	var schDatafields = [
		{ name: 'check', type: 'boolean' },
		{ name: 'id', type: 'int' },
		{ name: 'report_id', type: 'int' },
		{ name: 'report_name' },
		{ name: 'display_type' },
		{ name: 'controller' },
		{ name: 'controller_function' },
		{ name: 'report_products_val' },
		{ name: 'report_datetime', type: 'date' },
		{ name: 'report_where' },
		{ name: 'report_recursive_frequency' },
		{ name: 'email_addresses' }
	];
	var SchedGrid = new SVGrid('#schedule_list', schedUrl);
	SchedGrid.sCookieName = 'schs';
	SchedGrid.setSource(schDatafields);

	//this handles the expanded rows
	var initrowdetails = function (index, parentElement, gridElement, datarecord) {
		var tabsdiv = null;
    tabsdiv = $($(parentElement).children()[0]);

    var f = $(tabsdiv).children('form');
    f.attr('id', 'inline_schedule_edit_'+index);
    f.attr('name', 'inline_schedule_edit_'+index);
    var c = $(f).children('.cancel_schedule');
    c.attr('name', index);

		if (tabsdiv != null){
			//set all the fields
			var name = $(tabsdiv).find('input[name="report_name"]');
			name.val(datarecord.report_name);
			var rptid = $(tabsdiv).find('input[name="report_id"]');
			rptid.val(datarecord.report_id);
			var cont = $(tabsdiv).find('select[name="controller"]');
			cont.val(datarecord.controller);
			var cont_func = $(tabsdiv).find('input[name="controller_function"]');
			cont_func.val(datarecord.controller_function);

			var report_products_type, report_products_val, report_products_vals;
			var where = $.parseJSON(datarecord.report_where);
			switch(datarecord.controller){
				case 'reports':
				case 'violations':
					if(where.groupId != undefined){
						report_products_type = 'group_report';
						report_products_vals = where.groupId[0];
					}else{
						report_products_type = 'productpricing';
						report_products_vals = where.productIds[0];
					}
					break;
				case 'overview':
				case 'violationoverview':
					if(!$(tabsdiv).find('.sch_prods').hasClass('hidden')) $(tabsdiv).find('.sch_prods').addClass('hidden');
					break;
			}

			$(tabsdiv).find('input[value="'+report_products_type+'"]').attr('checked', true);
			var prod_val = $(tabsdiv).find('input[name="report_products_val"]');
			prod_val.val(datarecord.report_products_val);
			var prod_vals = $(tabsdiv).find('input[name="report_products_vals"]');
			prod_vals.val(report_products_vals);

			var d = new Date(datarecord.report_datetime);
			var m = ('0'+d.getMonth());
			var dy = ('0'+d.getDate());
			var y = d.getFullYear();
			var h, ampm;
			if(d.getHours() > 12){
				h = ('0'+(d.getHours()-12));
				ampm = 'pm';
			}else{
				h = ('0'+d.getHours());
				ampm = 'am';
			}
			var mn = '0'+d.getMinutes();

			var datetime = $(tabsdiv).find('input[name="report_datetime"]');
			datetime.val(m.slice(-2)+'-'+dy.slice(-2)+'-'+y);
			var hh = $(tabsdiv).find('select[name="hh"]');
			hh.val(h.slice(-2));
			var mm = $(tabsdiv).find('select[name="mm"]');
			mm.val(mn.slice(-2));
			var ap = $(tabsdiv).find('select[name="ampm"]');
			ap.val(ampm);

			var rec_freq = $(tabsdiv).find('select[name="report_recursive_frequency"]');
			rec_freq.val(datarecord.report_recursive_frequency);

			var emails = datarecord.email_addresses.split(',');
			for(var i=0; i<emails.length; i++){
				c = '<div class="reportEmails" id="rptE_'+i+'"><input type="hidden" name="email_addresses[]" value="'+emails[i]+'">'+emails[i]+'<span class="jsLink" onclick="xRptEmail(this);"><img src="/images/icons/16/69.png" alt="Remove" class="imgIcon"></span></div>';
				$(tabsdiv).find('.email_container').append(c);
			}
		}
	}

	SchedGrid.create = function(){
		//pinned columns
		var columns = [
			{
				text: '',
				datafield: 'check',
				columntype: 'checkbox',
				width: 40,
				pinned: true,
				sortable: false,
				renderer: SchedGrid.renderer,
				rendered: SchedGrid.rendered
			},
			{ text: 'Title', dataField: 'report_name', pinned: true, editable: false },
			{ text: 'Report Type', dataField: 'display_type', width: 150, pinned: true, editable: false },
			{ text: 'Start Date', dataField: 'report_datetime', width: 150, cellsformat: 'd', columntype: 'datetimeinput', editable: false }
		];

		var dataAdapter = new $.jqx.dataAdapter(SchedGrid.source);
		$(SchedGrid.sel).jqxGrid({
			source: dataAdapter,
			theme: SchedGrid.theme,
			width: 1098,
			sortable: true,
			sorttogglestates: 1,
			pageable: true,
			autoheight: true,
			editable: true,
			columnsresize: false,
			columnsmenu: false,
			rowdetails: true,
			selectionmode: 'multiplerows',
			initrowdetails: initrowdetails,
			rowdetailstemplate: { rowdetails: '<div class="rowHolder">'+rowDetailTmp+'</div>', rowdetailsheight: 250 },
			pagesizeoptions: ['5','10', '20', '30','50','100'],
			pagesize: (SchedGrid.bpS ? SchedGrid.bpS : 10),
			selectionmode: 'none',
			columns: columns,
			ready: SchedGrid.ready
		});
	};

	SchedGrid.bindEvents = function(){
		$(SchedGrid.sel).on('cellendedit', function(event){
			if (columnCheckBox){
				if (event.args.value){
					$(SchedGrid.sel).jqxGrid('selectrow', event.args.rowindex);
				}else {
					$(SchedGrid.sel).jqxGrid('unselectrow', event.args.rowindex);
				}
				var selectedRowsCount = $(SchedGrid.sel).jqxGrid('getselectedrowindexes').length;
				var rowscount = $(SchedGrid.sel).jqxGrid('getdatainformation').rowscount;
				updatingCheckState = true;
				if (selectedRowsCount == rowscount){
					$(columnCheckBox).jqxCheckBox('check')
				}
			}
		});
	};

	SchedGrid.init();

	// add filter behavior
	$('.container').on('submit', '.schedule_filter', function(e){
		e.preventDefault();
	});

	$('.container').on("keyup", '#searchString', function(e){
		if (e.which != 13)
			SchedGrid.filter($(this).val());
	});

	$("#add_schedule, #cancel_schedule").on('click', function (){
		$('.scheduleActionArea').toggleClass("hidden");
		return false;
	});

	$('input[name="report_products"]:radio').on('change', function(){
		type = $(this).val();
		f = $(this).parents('form:first');
		search_field_type(type, f.attr('id'));
	});

	//initialize the field search - this is altered with search_field_type
	$('input[name="report_products_val"]').autocomplete({
		source: function( request, response ) {
			$.ajax({
							url: base_url+"schedule/get_products_names/"+request.term,
							dataType: "json",
							data: {},
							success: function(items) {
								if(!items || items.length == 0) {
									return false;
								}
								response($.map( items, function( item ) {
									return {
													value: item.title,
													id: item.id
													}
								}));
							}
						});
		},
		minLength: 1,
		deferRequestBy: 0,
		select: function( event, ui ) {
			$(this).val(ui.item.value);
			$(this).next('input[name="report_products_vals"]').val(ui.item.id);
		}
	});

	$('.report_datetime').datepicker({
		dateFormat: 'mm-dd-yy',
		minDate:'mm-dd-yy'
	});

	$('.save_schedule').on('click', function()
	{
		//get the proper form (could be many)
		f = $(this).parent('form');

		if (checkSchFields(f.attr('id')))
	  {
			data = $(f).serializeArray();
			
			$.post(
						base_url+"schedule/save_report",
						data,
						function(data, status){
							if(f.attr('id') == 'schedule_edit') clearSchFields(f.attr('id'));

							if (alert('Report has been successfully added.'))
							{
								location.reload();
							}
							
							//SchedGrid.reload();
						},
						'json'
		  );
		}
		else
		{
			alert('Please correct form fields and make sure they are all filled in with the appropriate values.')
		}
	});

	//misc form actions
	$('#controller').change(function(){
		v = $(this).val();
		y = v.indexOf("overview");
		h = $('.sch_prods').hasClass('hidden');

		if(y != -1 && !h){
			$(".sch_prods").addClass('hidden');
		}else if(y == -1 && h){
			$(".sch_prods").removeClass('hidden');
		}
	});

	$(".cancel_schedule").on('click', function (){
		//trigger row close...
		i = $(this).attr('name');
		$('#schedule_list').jqxGrid('hiderowdetails', i);
	});

	$('.resetButton').click(function(){
		$(this).val('');
		SchedGrid.reload();

	});

	$('#bulkActions').change(function(){
		v = $(this).val();
		if(v == '1'){
			var ids = SchedGrid.getSelectedIds();

			if(ids.length == 0){
				$('select[name="bulkActions"]').val('1');
				var dialog = '<p><b>No report selected</b><br />Please select desired report(s) to delete.<\/p>';
				sv_alert(dialog);
				return false;
			}else{
				var data = {ids: ids};
				$.post(
					base_url+"schedule/delete_reports",
					data,
					function(data, status){
						SchedGrid.reload();
                        $('#bulkActions').val('');
					},
					'json'
				);
			}
		}
	});
});

function search_field_type(type, f){
	l = v = '';
	if(type == 'group_report'){
		l = 'Group Name:';
		cf = "get_groups_names/";
	} else if(type == 'productpricing'){
		l = 'Product Name:';
		cf = "get_products_names/";
	}

	$('#'+f+' .sch_prods .report_products_lbl').html(l);
	$('#'+f+' input[name="report_products_val"]').val(v);
	$('#'+f+' input[name="report_products_val"]').autocomplete({
		source: function( request, response ) {
			$.ajax({
							url: base_url+"schedule/"+cf+request.term,
							dataType: "json",
							data: {},
							success: function(items) {
								if(!items || items.length == 0) {
									return false;
								}
								response($.map( items, function( item ) {
									return {
													value: item.title,
													id: item.id
													}
								}));
							}
						});
		},
		minLength: 1,
		deferRequestBy: 0,
		select: function( event, ui ) {
			$(this).val(ui.item.value);
			$(this).next('input[name="report_products_vals"]').val(ui.item.id);
		}
	});
}//end search_field_type

function checkSchFields(f){
	passed = true;

	n = $('#'+f+' input[name="report_name"]').val();
	t = $('#'+f+' select[name="controller"]').val();
	pt = $('#'+f+' input[name="report_products"]:radio:checked').val();
	pv = $('#'+f+' input[name="report_products_vals"]').val();
	dt = $('#'+f+' input[name="report_datetime"]').val();
	hh = $('#'+f+' select[name="hh"]').val();
	mm = $('#'+f+' select[name="mm"]').val();
	ap = $('#'+f+' select[name="ampm"]').val();
	r = $('#'+f+' select[name="report_recursive_frequency"]').val();
	//e = $('#'+f+' input[name="email_addresses[]"]').map(function(){return $(this).val();}).get();

	console.log(n+" \n"+t+" \n"+pt+" \n"+pv+" \n"+dt+" \n"+hh+"\n"+mm+"\n"+ap+" \n"+r);

	if (
			n == undefined || 
			t == undefined || 
			pt == undefined || 
			pv == undefined || 
			dt == undefined || 
			hh == undefined || 
			mm == undefined || 
			ap == undefined || 
			r == undefined 
			// (e == undefined || e.length == 0) 
	)
  {
		passed = false;
		$('#'+f+' .schd_error').removeClass('hidden');
	}else{
		$('#'+f+' .schd_error').addClass('hidden');
	}

	return passed;
}

function clearSchFields(f){
	$('#'+f).find('input[type!="button"]').val('');
	$('#'+f+' .controller').val('reports');
	$('#'+f+' .hh').val('01');
	$('#'+f+' .mm').val('00');
	$('#'+f+' .ampm').val('am');
	$('#'+f+' .report_recursive_frequency').val('0');
	$('#'+f+' .email_container').html('');
	if($('#'+f+' .sch_prods').hasClass('hidden')) $('#'+f+' .sch_prods').removeClass('hidden');
}
</script>