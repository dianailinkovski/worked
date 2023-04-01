<script type="text/javascript">
var _permission_id = '<?= $permission_id?>';
var timeout;
    
function elaborate_keyword(){
	var keyword = $('#searchMerchantString').val();
	var merchOrRet = $('input[name=merchOrRet]:checked').val();
	var dateFrom = $('#date_from').val();
	if (dateFrom == 'Start') {
		dateFrom = '';
	}
	var dateTo = $('#date_to').val();
	if (dateTo == 'Stop') {
		dateTo = '';
	}
	var value = keyword + '|' + merchOrRet + '|' + dateFrom + '|' + dateTo;
	NoteGrid.filter(value);
}

$(document).ready(function(){
	NoteGrid.init();

	$('#searchMerchantString').on('keyup search', function(e){
		if (timeout != "") clearTimeout(timeout);
		timeout = setTimeout(function(){
			if ( e.which == 13 || (e.which < 32 && e.which != 8) || (e.which >= 33 && e.which < 46) || (e.which >= 112 && e.which <= 123) ) {
				//ignore
			}else{
				elaborate_keyword();
			}
		},500);
	});
	
	$( "input[name=merchOrRet]:radio" ).change(function() {
		elaborate_keyword();
	});
	
	$( "input[name=time_frame]:radio" ).change(function() {
		var minusDays = $(this).val();
		if (minusDays==24){ minusDays = 1; }
		var today = $.datepicker.formatDate('yy-mm-dd', new Date());
		//var dateFrom = $.datepicker.setDate($.datepicker.getDate('yy-mm-dd')-7);
		$('#date_to').datepicker('setDate', today);
		$('#date_from').datepicker('setDate', '-'+minusDays+'d');
		//alert ('time frame: '+$(this).val()+' today: '+today+' dateFrom: '+ dateFrom);
		elaborate_keyword();
	});
    
    $('#searchStaffNotesString').on('keyup search', function(e){
		if (e.which != 13) 
			get_staff_note_of_merchant(0);
	})
    
    $('#searchDiscussionString').on('keyup search', function(e){
		if (e.which != 13) 
			get_note_of_merchant(0);
	})
});

if ($('#enforcement_grid').length){
	var noteUrl = '<?=site_url('enforcement/get_notifications') . '?q=2'?>';
	var NoteGrid = new SVGrid('#enforcement_grid', noteUrl);

	NoteGrid.sCookieName = 'ngs';
	var noteDatafields = [
		{ name: 'id', type: 'int' },
		{ name: 'merchant' },
		{ name: 'seller_id' },
		{ name: 'notes' },
		{ name: 'note_count' },
		{ name: 'active' },
		{ name: 'website_address' },
		{ name: 'name_to' },
		{ name: 'email_to' },
		{ name: 'type' },
		//{ name: 'last_violation', type: 'date' },
		{ name: 'violated_count', type: 'int' },
		{ name: 'violation_level', type: 'int' },
		{ name: 'repeat_violator' },
		{ name: 'last_violation', type: 'date' },
		{ name: 'history_url' },
		{ name: 'reset_url' },
		{ name: 'staff_note' },
		{ name: 'staff_note_history_url' },
        //{ name: 'marketplace' }
	];

	NoteGrid.setSource(noteDatafields);
	NoteGrid.create = function(){
	<?php if ( isset($this->data->note_enable) && $this->data->note_enable == 1 ) : // Show merchant discussions on MAP Enforcement Page?>
        if ( _permission_id == '0' || _permission_id == '1' || _permission_id == '2') { // therms member
            var columns = [
				{ text: 'Merchant', dataField: 'merchant', width: 150, pinned: true },
				{ text: 'Seller ID', dataField: 'seller_id', width: 100},
				{ text: 'Notes', dataField: 'notes', width: 50, cellsalign: 'center' },
		 		{ text: 'Active', dataField: 'active', width: 50, cellsalign: 'center'},
				{ text: 'Website', dataField: 'website_address', width: 120 },
	 			{ text: 'Contact Name', dataField: 'name_to', width: 115 },
	 			{ text: 'Contact Email', dataField: 'email_to', width: 120 },
	 			{ text: 'Warning Type', dataField: 'type', width: 110 },
	 			//{ text: 'Date', dataField: 'last_violation', width: 90, cellsformat: 'd' },
	 			{ text: 'Violations', dataField: 'violated_count', width: 75, cellsalign: 'center' },
	 			{ text: 'Notification Msg.', dataField: 'violation_level', width: 115, cellsalign: 'center' },
	 			{ text: 'Repeat Violator', dataField: 'repeat_violator', width: 115, cellsformat: 'd', cellsalign: 'center'  },
	 			{ text: 'Last Violation', dataField: 'last_violation', width: 100, cellsformat: 'd', cellsalign: 'center'   },
	 			{ text: 'Contact History', dataField: 'history_url', width: 115, cellsalign: 'center' },
                { text: 'Reset Violator Count', dataField: 'reset_url', width: 150, cellsalign: 'center' },
                { text: 'Staff Note', dataField: 'staff_note', width: 150 }
            ];
        } else {
            var columns = [
				{ text: 'Merchant', dataField: 'merchant', width: 150, pinned: true },
				{ text: 'Seller ID', dataField: 'seller_id', width: 100},
				{ text: 'Notes', dataField: 'notes', width: 50, cellsalign: 'center' },
		 		{ text: 'Active', dataField: 'active', width: 50, cellsalign: 'center'},
				{ text: 'Website', dataField: 'website_address', width: 170 },
	 			{ text: 'Contact Name', dataField: 'name_to', width: 155 },
	 			{ text: 'Contact Email', dataField: 'email_to', width: 180 },
	 			{ text: 'Warning Type', dataField: 'type', width: 110 },
	 			//{ text: 'Date', dataField: 'last_violation', width: 90, cellsformat: 'd' },
	 			{ text: 'Violations', dataField: 'violated_count', width: 75, cellsalign: 'center' },
	 			{ text: 'Notification Msg.', dataField: 'violation_level', width: 115, cellsalign: 'center' },
	 			{ text: 'Repeat Violator', dataField: 'repeat_violator', width: 115, cellsformat: 'd', cellsalign: 'center'  },
	 			{ text: 'Last Violation', dataField: 'last_violation', width: 100, cellsformat: 'd', cellsalign: 'center'   },
	 			{ text: 'Contact History', dataField: 'history_url', width: 115, cellsalign: 'center' },
                { text: 'Reset Violator Count', dataField: 'reset_url', width: 150, cellsalign: 'center' }
            ];
        }
	<?php else: ?>
        if ( _permission_id == '0' || _permission_id == '1' || _permission_id == '2') { // therms member
            var columns = [
	 			{ text: 'Merchant', dataField: 'merchant', width: 150, pinned: true },
				{ text: 'Seller ID', dataField: 'seller_id', width: 100},
	 			{ text: 'Active', dataField: 'active', width: 50, cellsalign: 'center'},
	 			{ text: 'Website', dataField: 'website_address', width: 140 },
	 			{ text: 'Contact Name', dataField: 'name_to', width: 120 },
	 			{ text: 'Contact Email', dataField: 'email_to', width: 120 },
	 			{ text: 'Warning Type', dataField: 'type', width: 100 },
	 			//{ text: 'Date', dataField: 'last_violation', width: 90, cellsformat: 'd' },
	 			{ text: 'Violations', dataField: 'violated_count', width: 80, cellsalign: 'center' },
	 			{ text: 'Notification Msg.', dataField: 'violation_level', width: 120, cellsalign: 'center' },
	 			{ text: 'Repeat Violator', dataField: 'repeat_violator', width: 120, cellsformat: 'd', cellsalign: 'center' },
	 			{ text: 'Last Violation', dataField: 'last_violation', width: 100, cellsformat: 'd', cellsalign: 'center' },
	 			{ text: 'Contact History', dataField: 'history_url', width: 115, cellsalign: 'center' },
                { text: 'Reset Violator Count', dataField: 'reset_url', width: 150, cellsalign: 'center' },
                { text: 'Staff Note', dataField: 'staff_note', width: 155 }
	 		];
        } else {
            var columns = [
	 			{ text: 'Merchant', dataField: 'merchant', width: 150, pinned: true },
				{ text: 'Seller ID', dataField: 'seller_id', width: 100},
	 			{ text: 'Active', dataField: 'active', width: 50, cellsalign: 'center'},
	 			{ text: 'Website', dataField: 'website_address', width: 180 },
	 			{ text: 'Contact Name', dataField: 'name_to', width: 175 },
	 			{ text: 'Contact Email', dataField: 'email_to', width: 180 },
	 			{ text: 'Warning Type', dataField: 'type', width: 100 },
	 			//{ text: 'Date', dataField: 'last_violation', width: 90, cellsformat: 'd' },
	 			{ text: 'Violations', dataField: 'violated_count', width: 80, cellsalign: 'center' },
	 			{ text: 'Notification Msg.', dataField: 'violation_level', width: 120, cellsalign: 'center' },
	 			{ text: 'Repeat Violator', dataField: 'repeat_violator', width: 120, cellsformat: 'd', cellsalign: 'center' },
	 			{ text: 'Last Violation', dataField: 'last_violation', width: 100, cellsformat: 'd', cellsalign: 'center' },
	 			{ text: 'Contact History', dataField: 'history_url', width: 115, cellsalign: 'center' },
                { text: 'Reset Violator Count', dataField: 'reset_url', width: 150, cellsalign: 'center' }
	 		];
        }
	<?php endif;?>

		var dataAdapter = new $.jqx.dataAdapter(NoteGrid.source);
		$(NoteGrid.sel).jqxGrid({
			source: dataAdapter,
			theme: NoteGrid.theme,
			width : 1677,
			sortable: true,
			sorttogglestates: 1,
			pageable: true,
			autoheight: true,
			editable: true,
			columnsresize: true,
			columnsmenu: false,
			pagesizeoptions: ['5','10', '20', '30','50','100','1000'],
			pagesize: (NoteGrid.bpS ? NoteGrid.bpS : 50),
			selectionmode: 'none',
			columns: columns,
			ready: NoteGrid.ready
		});
	};

	NoteGrid.getRow = function(rowindex){
		return $(NoteGrid.sel).jqxGrid('getrowdata', rowindex);
	}

	NoteGrid.updateContact = function(args){
		var row = NoteGrid.getRow(args.rowindex);
		var data = {
			merchant_name_id: row.id,
			column: args.datafield,
			value: args.value
		};
        
        if ( args.value == args.oldvalue ) return;
        
        var msg_id = '#enforcement_grid_msg';
        if ( args.datafield == 'staff_note' ) {
            if ( args.value == '' ) {
                sv(msg_id).set_error("Please input staff note.", 'slow');
                $(NoteGrid.sel).jqxGrid('setcellvalue', args.rowindex, args.datafield, args.oldvalue);
                return;
            }
            $.post(base_url+"enforcement/save_staff_note", data, function(response){
                if (response.status) {
                    sv(msg_id).set_success(response.html, 'slow');
                    
                    $(NoteGrid.sel).jqxGrid('setcellvalue', args.rowindex, 'staff_note', '<a link="'+base_url+'enforcement/staff_notes_preview/'+row.id+'" class="ajaxtooltip">'+args.value+'</a>');
                    ajaxtooltip_initialize();
                } else {
                    $(NoteGrid.sel).jqxGrid('setcellvalue', args.rowindex, args.datafield, args.oldvalue);
                    sv(msg_id).set_error(response.html, 'slow');
                }
            }, 'json');
        } else {
            $.post(base_url+"enforcement/update_contact", data, function(response){
                if (response.status) {
                    sv(msg_id).set_success(response.html, 'slow');
                    args.oldvalue = args.value;
                } else {
                    $(NoteGrid.sel).jqxGrid('setcellvalue', args.rowindex, args.datafield, args.oldvalue);
                    sv(msg_id).set_error(response.html, 'slow');
                }

                if (response.active)
                    $(NoteGrid.sel).jqxGrid('setcellvalue', args.rowindex, 'active', response.active);
            }, 'json');
        }
	}

	NoteGrid.isEditable = function(args){
        if (args.datafield === 'name_to' || args.datafield === 'email_to') {
            return true;
        } else if (args.datafield === 'staff_note') {
            if (args.value.length == 0) {
                if ( _permission_id == '0' || _permission_id == '2' ) {
                    return true;
                }
            }
        }
		return false;
	}

	NoteGrid.bindEvents = function(){
		NoteGrid.defaultEvents();

        $(NoteGrid.sel).on('cellbeginedit', function(e){
			if ( !NoteGrid.isEditable(e.args) )
				$(NoteGrid.sel).jqxGrid('endcelledit', e.args.rowindex, e.args.datafield, true);
		});

		$(NoteGrid.sel).on('cellendedit', function(e){
			NoteGrid.updateContact(e.args);
		});

		/* Commented this out --- since new system is in place
		$(NoteGrid.sel).on('cellclick', function(e){
			if (e.args.datafield === 'merchant'){
				var row = NoteGrid.getRow(e.args.rowindex);
				NoteGrid.notifyResource = row.id;
				violator_notification(row.id);
			}
			else if (e.args.datafield === 'active'){
				NoteGrid.updateContact(e.args);
			}
			else if (e.args.datafield === 'notes'){
				// Show Dialog of Notes
				var row = NoteGrid.getRow(e.args.rowindex);
				NoteGrid.notifyResource = row.id;
				note_of_merchant(row.id, row.merchant);
			}
		});
		*/

		$(NoteGrid.sel).on('cellclick', function(e){
            var row = NoteGrid.getRow(e.args.rowindex);
				
			if(e.args.datafield == 'active') {
				NoteGrid.updateContact(e.args);
			} else if (e.args.datafield === 'notes'){
				// Show Dialog of Notes
				NoteGrid.notifyResource = row.id;
				note_of_merchant(row.id, row.merchant);
			} else if (e.args.datafield === 'reset_url') {
                if ( row.violated_count > 0 ) {
                    reset_violation_count(row.id, e.args.rowindex);
                }
            } else if (e.args.datafield === 'staff_note') {
                if (e.args.value.length > 0) {
                    _staff_note_row_index = e.args.rowindex;
                    
                    NoteGrid.notifyResource = row.id;
                    staff_note_of_merchant(row.id, row.merchant);
                }
            } else if (e.args.datafield === 'history_url') {
                show_contact_history_dialog(row.id);
            }
		});

		$(NoteGrid.sel).on('bindingcomplete', function(e){
			ajaxtooltip_initialize();
		});

		$(NoteGrid.sel).on('pagechanged', function(e){
			ajaxtooltip_initialize();
		});
	};

	$('#srchform .resetButton').click(function(){
		$(this).prev('.search').val('');
		NoteGrid.reload();
	});
}

/*
 * Discussion of Merchant
 */
function format_note_input() {
	$("#merchant_notes_dialog #searchDiscussionString").val("");
	$('#merchant_notes_dialog #entry_note').val("");

	$('#merchant_note_error').removeClass("error").hide();
	
	$('#merchant_notes_dialog #entry_note').removeAttr("required");
}

function get_note_of_merchant(page) {
	var merchant_name_id = $("#merchant_notes_dialog #merchant_name_id").val();
	var url = base_url + 'enforcement/get_merchant_notes/' + merchant_name_id;
	
	var params = {
			page: page,
			keyword: $("#merchant_notes_dialog #searchDiscussionString").val()
	}

	$('#merchant_notes_loading').fadeIn();
	$.post(url, params, function(response) {
        if (response) {
			$('#merchant_notes_loading').fadeOut();
			$("#merchant_notes_list").fadeOut(200, function() {
				$("#merchant_notes_list").html(response).fadeIn();
			})
		}
	})
}

function note_of_merchant(merchant_name_id, merchant_name) {
    $("#merchant_notes_dialog #note_merchant_name").html(merchant_name);
	$("#merchant_notes_dialog #merchant_name_id").val(merchant_name_id);
	$("#merchant_notes_list").html('');

	format_note_input();

	$('#merchant_notes_loading').show();
		
	var url = base_url + 'enforcement/get_merchant_notes/' + merchant_name_id;
	$.post(url, function(response) {
        if (response) {
			$('#merchant_notes_loading').fadeOut();
			$("#merchant_notes_list").html(response).fadeIn();
			
			showDialog('#merchant_notes_dialog', 900, 740, {
				buttons: {
					'Cancel': function(){
						$(this).dialog('close');
					},
					'Save': function(){
						save_note_of_merchant(merchant_name_id);
					}
				}
			});
		}
	})
}

function save_note_of_merchant(merchant_name_id) {
	// validate the form
	var $form = $('#merchant_notes_dialog #frm_note_of_merchant');
	var type_of_entry = $('#merchant_notes_dialog #type_of_entry');
	var entry_note = $('#merchant_notes_dialog #entry_note');

	var errors = [];
	if (empty(entry_note.val())) {
		errors.push('Please write comment.');
	}

	if (errors.length > 0) {
		entry_note.attr("required", "required");
		
		var error = '<p>' + errors.join('</p><p>') + '</p>';
		sv('#merchant_note_error').set_error(error, 'slow');

		return false;
	}

	// everything is valid, post to schedule/violator_notification
	var data = {
			type_of_entry: type_of_entry.val(),
			entry_note: entry_note.val()
	}
	var url = base_url + 'enforcement/save_note_of_merchant/' + merchant_name_id;
	$.post(url, data, function(response) {
		if (response == 'success') {
			format_note_input();
			
			get_note_of_merchant(0);
		} else {
			sv_alert('Failed write comment.');
		}
	});

	return false;
}

function reset_violation_count(merchant_name_id, rowindex) {
    var url = base_url + 'enforcement/reset_violation_count/' + merchant_name_id;
	$.post(url, {}, function(response) {
		if (response == 'success') {
            $(NoteGrid.sel).jqxGrid('setcellvalue', rowindex, 'violated_count', '');
            $(NoteGrid.sel).jqxGrid('setcellvalue', rowindex, 'reset_url', '');
		} else {
			sv('#enforcement_grid_msg').set_error("Failed reset validation count", 'slow');
		}
	});
}

/*
 * Staff Note of Merchant
 */
var _staff_note_row_index = 0;
function format_staff_note_input() {
	$("#searchStaffNotesString").val("");
	$('#merchant_staff_notes_dialog #note_id').val("0");
    $('#merchant_staff_notes_dialog #entry_note').val("");

	$('#merchant_staff_note_error').removeClass("error").hide();
	
	$('#merchant_staff_notes_dialog #entry_note').removeAttr("required");
}

function get_staff_note_of_merchant(page) {
	var merchant_name_id = $("#merchant_staff_notes_dialog #merchant_name_id").val();
	var url = base_url + 'enforcement/get_merchant_staff_notes/' + merchant_name_id;
	
	var params = {
			page: page,
			keyword: $("#searchStaffNotesString").val()
	}

	$('#merchant_staff_notes_loading').fadeIn();
	$.post(url, params, function(response) {
        if (response) {
			$('#merchant_staff_notes_loading').fadeOut();
			$("#merchant_staff_notes_list").fadeOut(200, function() {
				$("#merchant_staff_notes_list").html(response).fadeIn();
                if ( page == 0 ) {
                    init_last_staff_note();
                }
			})
		}
	})
}

function init_last_staff_note() {
    var merchant_name_id = $("#merchant_staff_notes_dialog #merchant_name_id").val();
    var comment = $("#merchant_staff_notes_list .merchant_notes .merchant_note:first-child p.entry").html();
    
    if ( comment != undefined ) {
        $(NoteGrid.sel).jqxGrid('setcellvalue', _staff_note_row_index, 'staff_note', '<a link="'+base_url+'enforcement/staff_notes_preview/'+merchant_name_id+'" class="ajaxtooltip">'+comment+'</a>');
    } else {
        $(NoteGrid.sel).jqxGrid('setcellvalue', _staff_note_row_index, 'staff_note', '');
    }
    
    ajaxtooltip_initialize();
}

function staff_note_of_merchant(merchant_name_id, merchant_name) {
    $("#merchant_staff_notes_dialog #note_merchant_name").html(merchant_name);
	$("#merchant_staff_notes_dialog #merchant_name_id").val(merchant_name_id);
	$("#merchant_staff_notes_list").html('');

	format_staff_note_input();

	$('#merchant_staff_notes_loading').show();
		
	var url = base_url + 'enforcement/get_merchant_staff_notes/' + merchant_name_id;
	$.post(url, function(response) {
        if (response) {
			$('#merchant_staff_notes_loading').fadeOut();
			$("#merchant_staff_notes_list").html(response).fadeIn();
			init_last_staff_note();
            if ( _permission_id == '0' || _permission_id == '2' ) { // owner, admin
                showDialog('#merchant_staff_notes_dialog', 900, 750, {
                    buttons: {
                        'Cancel': function(){
                            $(this).dialog('close');
                        },
                        'Save': function(){
                            save_staff_note_of_merchant(merchant_name_id);
                        }
                    }
                });
            } else {
                showDialog('#merchant_staff_notes_dialog', 900, 740, {
                    buttons: {
                        'Cancel': function(){
                            $(this).dialog('close');
                        }
                    }
                });
            }
		}
	})
}

function save_staff_note_of_merchant(merchant_name_id) {
	// validate the form
	var $form = $('#merchant_staff_notes_dialog #frm_note_of_merchant');
	var entry_note = $('#merchant_staff_notes_dialog #entry_note');

	var errors = [];
	if (empty(entry_note.val())) {
		errors.push('Please write comment.');
	}

	if (errors.length > 0) {
		entry_note.attr("required", "required");
		
		var error = '<p>' + errors.join('</p><p>') + '</p>';
		sv('#merchant_staff_note_error').set_error(error, 'slow');

		return false;
	}

	// everything is valid, post to schedule/violator_notification
	var data = {
            merchant_name_id : merchant_name_id,
            column : 'staff_note',
			value: entry_note.val()
	}
	var url = base_url + 'enforcement/save_staff_note';
	$.post(url, data, function(response) {
		if (response.status) {
            format_staff_note_input();
			
            get_staff_note_of_merchant(0);
		} else {
			sv_alert('Failed write comment.');
		}
	}, 'json');

	return false;
}

function delete_staff_note_of_merchant(merchant_name_id, note_id) {
    sv_confirm("Are you sure delete selected item?", function() {
        var url = base_url + 'enforcement/delete_staff_note';
        $.post(url, {ids:[note_id]}, function(response) {
            $('.confirm-dialoog').dialog('close');
            if (response.status) {
                format_staff_note_input();

                get_staff_note_of_merchant(0);
            } else {
                sv_alert('Failed delete comment.');
            }
        }, 'json');
    });
}

function edit_staff_note_of_merchant(merchant_name_id, note_id) {
    $("#merchant_staff_notes_edit_dialog #entry_note").val( $("#staff_note_"+note_id+" p.entry").html().replace("<br>", "\n") );
        
    showDialog('#merchant_staff_notes_edit_dialog', 900, 250, {
        buttons: {
            'Cancel': function(){
                $(this).dialog('close');
            },
            'Save': function(){
                var edit_dialog = $(this);
                
                var entry_note = $('#merchant_staff_notes_edit_dialog #entry_note');
                var errors = [];
                if (empty(entry_note.val())) {
                    errors.push('Please write comment.');
                }

                if (errors.length > 0) {
                    entry_note.attr("required", "required");

                    var error = '<p>' + errors.join('</p><p>') + '</p>';
                    sv('#merchant_staff_note_edit_error').set_error(error, 'slow');

                    return false;
                }
                
                var data = {
                        merchant_name_id : merchant_name_id,
                        column : 'staff_note',
                        value: entry_note.val(),
                        note_id: note_id
                }
                var url = base_url + 'enforcement/save_staff_note';
                $.post(url, data, function(response) {
                    if (response.status) {
                        $("#staff_note_"+note_id+" p.entry").html(data.value.replace("\n", "<br>"));
                        
                        sv('#merchant_staff_note_edit_error').set_success(response.html, 'slow');
                        
                        setTimeout(function() {edit_dialog.dialog('close');}, 500);
                    } else {
                        sv_alert('Failed write comment.');
                    }
                }, 'json');
            }
        }
    }); 
}

function staff_note_action($action) {
    if ( $action.val() == 'all' ) {
        $(".staff_note_select").attr("checked", "checked");
    } else if ( $action.val() == 'unall' ) {
        $(".staff_note_select").removeAttr("checked");
    } else if ( $action.val() == 'delete' ) {
        var ids = [];
        $(".staff_note_select:checked").each(function(index){
           ids.push($(this).val()); 
        });
        
        $action.val('');
        if ( ids.length == 0 ) {
            sv_alert("Please select items for delete.");
        } else {
            sv_confirm("Are you sure delete selected item?", function() {
                var url = base_url + 'enforcement/delete_staff_note';
                $.post(url, {ids:ids}, function(response) {
                    $('.confirm-dialoog').dialog('close');
                    
                    if (response.status) {
                        format_staff_note_input();

                        get_staff_note_of_merchant(0);
                    } else {
                        sv_alert('Failed delete comment.');
                    }
                }, 'json');
            });
            
        }
    }
}
</script>
