<script type="text/javascript">
$(document).ready(function(){
	NoteGrid.init();
});

if ( $('#email_settings_grid').length ){ 
	var noteUrl = '<?=site_url('enforcement/get_email_settings')?>';
	var NoteGrid = new SVGrid('#email_settings_grid', noteUrl);

	NoteGrid.sCookieName = 'ngs';
	var noteDatafields = [
		{ name: 'id', type: 'int' },
		{ name: 'level' },
		{ name: 'name' },
		{ name: 'subject' },
		{ name: 'notify_after_days' },
		{ name: 'no_of_days_to_repeat' },
		{ name: 'template' },
	];

	NoteGrid.setSource(noteDatafields);
	NoteGrid.create = function(){
		var columns = [
			{ text: 'No.', dataField: 'level', width: 70, pinned: true },
			{ text: 'Name', dataField: 'name', width: 200, pinned: true },
	 		{ text: 'Subject', dataField: 'subject', width: 400},
			{ text: 'Notification Delay', dataField: 'notify_after_days', width: 150, cellsalign: 'center' },
 			{ text: '# Times to Send', dataField: 'no_of_days_to_repeat', width: 150, cellsalign: 'center' },
			{ text: 'Template', dataField: 'template', width: 90, cellsalign: 'center' }
		];
		
		var dataAdapter = new $.jqx.dataAdapter(NoteGrid.source);
		$(NoteGrid.sel).jqxGrid({
			source: dataAdapter,
			theme: NoteGrid.theme,
			width : '100%',
			sortable: true,
			sorttogglestates: 1,
			pageable: true,
			autoheight: true,
			editable: true,
			columnsresize: true,
			columnsmenu: false,
			pagesizeoptions: ['10'],
			pagesize: (NoteGrid.bpS ? NoteGrid.bpS : 10),
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
			id: row.id,
			column: args.datafield,
			value: args.value
		};
		$.post(base_url+"enforcement/update_email_setting", data, function(response){
			if (response.notify_after_days)
				$(NoteGrid.sel).jqxGrid('setcellvalue', args.rowindex, 'notify_after_days', response.notify_after_days);
			else if (no_of_days_to_repeat)
				$(NoteGrid.sel).jqxGrid('setcellvalue', args.rowindex, 'no_of_days_to_repeat', response.no_of_days_to_repeat);
		}, 'json');
	}

	NoteGrid.isEditable = function(datafield){
		return (datafield === 'subject' || datafield === 'notify_after_days' || datafield === 'no_of_days_to_repeat')
	}

	NoteGrid.bindEvents = function(){
		NoteGrid.defaultEvents();

		$(NoteGrid.sel).on('cellbeginedit', function(e){
			if ( ! NoteGrid.isEditable(e.args.datafield))
				$(NoteGrid.sel).jqxGrid('endcelledit', e.args.rowindex, e.args.datafield, true);
		});

		$(NoteGrid.sel).on('cellendedit', function(e){
			NoteGrid.updateContact(e.args);
		});		
	};
}
</script>