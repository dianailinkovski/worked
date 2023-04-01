<script type="text/javascript">
$(document).ready(function(){
	NoteGrid.init();

	$('#searchString').on('keyup search', function(e){
		if (e.which != 13)
			NoteGrid.filter($(this).val());
	})
});

if ($('#staff_notes_grid').length){
	var noteUrl = '<?=site_url("enforcement/get_staff_notes/$merchant_name_id") . '?q=2'?>';
	var NoteGrid = new SVGrid('#staff_notes_grid', noteUrl);
	NoteGrid.sCookieName = 'ngs';
	var noteDatafields = [
		{ name: 'id' },
		{ name: 'entry' },
        { name: 'user_name' },
        { name: 'user_email' },
		{ name: 'date', type: 'date' }
	];
	NoteGrid.setSource(noteDatafields);
	NoteGrid.create = function(){
		var columns = [
			{ text: 'Note', dataField: 'entry', width: 600, pinned: true, cellsalign: 'left' },
			{ text: 'User Name', dataField: 'user_name', width: 140, pinned: true, cellsalign: 'left' },
			{ text: 'User Email', dataField: 'user_email', width: 200, pinned: true, cellsalign: 'left' },
			{ text: 'Date Sent', dataField: 'date', width: 100, cellsformat: 'd', cellsalign: 'left' },
		];

		var dataAdapter = new $.jqx.dataAdapter(NoteGrid.source);
		$(NoteGrid.sel).jqxGrid({
			source: dataAdapter,
			theme: NoteGrid.theme,
			width : 1042,
			sortable: true,
			sorttogglestates: 1,
			pageable: true,
			autoheight: true,
			editable: false,
			columnsresize: true,
			columnsmenu: false,
			pagesizeoptions: ['5','10', '20', '30','50','100'],
			pagesize: (NoteGrid.ngs ? NoteGrid.ngs : 10),
			selectionmode: 'none',
			columns: columns,
			ready: NoteGrid.ready
		});
	};
}

</script>