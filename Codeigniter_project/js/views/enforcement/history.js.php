<script type="text/javascript">
var _contact_merchant_name_id = 0;
function show_contact_history_dialog(merchant_name_id) {
    _contact_merchant_name_id = merchant_name_id;
    showDialog('#contact_history_dialog', 1066, 740, {
        buttons: {
            'Cancel': function(){
                $(this).dialog('close');
            }
        }
    });
    
    init_contact_history_grid();
}
    
    
$(document).ready(function(){
	$('#searchContactHistoryString').on('keyup search', function(e){
		if (e.which != 13) {
            if ( ContactHistoryGrid ) {
                ContactHistoryGrid.filter($(this).val());
            }
        }
	})
});

var ContactHistoryGrid = false;
function init_contact_history_grid() {
    var noteUrl = '<?=site_url("enforcement/get_history")?>/' + _contact_merchant_name_id + '?q=2';
    if ( ContactHistoryGrid === false ) {
        ContactHistoryGrid = new SVGrid('#contact_history_grid', noteUrl);
        ContactHistoryGrid.sCookieName = 'chs';
        var noteDatafields = [
            { name: 'name_to' },
            { name: 'email_to' },
            { name: 'name_from' },
            { name: 'email_from' },
            { name: 'email_level' },
            { name: 'email_repeat' },
            { name: 'date', type: 'date' }
        ];
        ContactHistoryGrid.setSource(noteDatafields);
        ContactHistoryGrid.create = function(){
            var columns = [
                { text: 'Seller Name', dataField: 'name_to', width: 120, pinned: true, cellsalign: 'left' },
                { text: 'Emailed To', dataField: 'email_to', width: 200, pinned: true, cellsalign: 'left' },
                { text: 'From Name', dataField: 'name_from', width: 120, pinned: true, cellsalign: 'left' },
                { text: 'Emailed From', dataField: 'email_from', width: 200, pinned: true, cellsalign: 'left' },
                { text: 'Warning Letter Type', dataField: 'email_level', width: 150, cellsalign: 'left' },
                { text: 'Repeat Number', dataField: 'email_repeat', width: 150, cellsalign: 'left' },
                { text: 'Date Sent', dataField: 'date', width: 100, cellsformat: 'd', cellsalign: 'left' },
            ];

            var dataAdapter = new $.jqx.dataAdapter(ContactHistoryGrid.source);
            $(ContactHistoryGrid.sel).jqxGrid({
                source: dataAdapter,
                theme: ContactHistoryGrid.theme,
                width : 1042,
                sortable: true,
                sorttogglestates: 1,
                pageable: true,
                autoheight: true,
                editable: false,
                columnsresize: true,
                columnsmenu: false,
                pagesizeoptions: ['5','10', '20', '30','50','100'],
                pagesize: (ContactHistoryGrid.chs ? ContactHistoryGrid.chs : 10),
                selectionmode: 'none',
                columns: columns,
                ready: ContactHistoryGrid.ready
            });
        };
        
        ContactHistoryGrid.init();
    } else {
        ContactHistoryGrid.setUrl(noteUrl);
		ContactHistoryGrid.refresh();
    }
}

</script>