<?php
echo script_tag('js/jqwidgets/globalization/jquery.global.js');
echo script_tag('js/jqwidgets/globalization/jquery.glob.en-US.js');
echo script_tag('js/jqwidgets/jqxgrid.pager.js');
echo script_tag('js/jqwidgets/jqxgrid.selection.js');
echo script_tag('js/jqwidgets/jqxlistbox.js');
echo script_tag('js/jqwidgets/jqxdropdownlist.js');
//echo script_tag('js/jqwidgets/jqxgrid.columnsreorder.js');
?>
<script type="text/javascript">
var flag=true;
var timeout;
$(document).ready(function(){
	// load the grids
	<?php if ( ! empty($products_saved)): ?>
		var dialog = '<h1 align="center">Product Import Success<\/h1><div class="row_dat"><p><?php echo $products_saved; ?> products successfully imported.<\/p><p>Tracking has been enabled and you will receive market place data shortly.<\/p><\/div>';
		sv_alert(dialog);
	<?php endif; ?>

	CatGrid.init();

	$('.modalWindow').on('click', '.btn_cancel', function(){
		$('.modalWindow').dialog('close');
	});

	// add filter behavior
	$('#main').on('submit', '.catalog_filter', function(e){
		e.preventDefault();
	});

	// prevent annoying searchbox behaviour (POST on every keyup.).  Instead, do one search only.
	var delay = (function(){
		var timer = 0;
		return function(callback, ms){
			clearTimeout (timer);
			timer = setTimeout(callback, ms);
		};
	})();
	
	$('#main').on("keyup", '#searchString', function(e){
		var $that = $(this);
		delay(function(){
			if ( (e.which < 32 && e.which != 8) || (e.which >= 33 && e.which < 46) || (e.which >= 112 && e.which <= 123) ) {
				//ignore
			}else{
				CatGrid.filter($that.val());
			}
		},1000);
	});
	$('#main').on("keyup", '#compSearchString', function(e){
		var $that = $(this);
		delay(function(){
			if ( (e.which < 32 && e.which != 8) || (e.which >= 33 && e.which < 46) || (e.which >= 112 && e.which <= 123) ) {
				//ignore
			}else{
				CompGrid.filter($that.val());
			}
		},1000);
	});
	$('#main').on('keyup', '#priceSearchString', function(e){
		var $that = $(this);
		delay(function(){
			if ( (e.which < 32 && e.which != 8) || (e.which >= 33 && e.which < 46) || (e.which >= 112 && e.which <= 123) ) {
				//ignore
			}else{
				PriceGrid.filter($that.val());
			}
		},1000);
	});
	$('#main').on('keyup', '#lookupSearchString', function(e){
		var $that = $(this);
		delay(function(){
			if ( (e.which < 32 && e.which != 8) || (e.which >= 33 && e.which < 46) || (e.which >= 112 && e.which <= 123) ) {
				//ignore
			}else{
				//alert($that.val());
				LookupGrid.filter($that.val());
			}
		},1000);
	});

	$('.jqx-dropdownlist-state-normal').click(function(){
		var offset = $(this).offset();
		if(offset && offset.left) {
			$('#listBoxgridpagerlistbrand_catalog').css('left', offset.left)
		}
	});

	$('.catalog-del-prod').on('click', function(){
		var delindexes = $("#brand_catalog").jqxGrid('getselectedrowindexes');

		if(delindexes.length == 0){
			var dialog = '<div class="modalWindo dialog"><p>No item selected. Please first select Item(s) to delete.<\/p><\/div>';
			sv_alert(dialog);
			return false;
		}else{
			var ids = CatGrid.getSelectedIds();
			var data = {ids: ids};
			var url = base_url+"catalog/delete_products";
			var dialog = "Are you sure you want to permanently delete these product(s)?<br/>All historical data will become inaccessible and new pricing data will not be generated.";
			defaultConfirmDialog(url, data, dialog);
			return false;
		}
		return true;
	});

	// Competitor Analysis
	$('#main').on('click', '.competitor-add-prod', comp_add_popup);
	$('#main').on('click', '.competitor-del-prod', comp_delete);
	$('#main').on('click', '.competitor-ass-prod', comp_link_popup);
	$('#main').on('click', '.competitor-unass-prod', comp_unlink);

	// Autocompletion
	var upcsURL = base_url+"catalog/get_product_upcs/";
	var productNamesURL = base_url+"catalog/get_products_names/";

	var CompUPC = new SVProductAutoComplete(upcsURL, true, true);
	CompUPC.appendTo = '#compUpcAutoComplete';
	CompUPC.select = function( event, ui ) {
		$('input[name=competitor_product_title]').val(ui.item.title);
		$('input[name=competitor_product_id]').val(ui.item.id);
	};
	$(document).on('focus.autocomplete', '#competitor_upc', function(e){
		$(this).autocomplete(CompUPC.get());
		$(document).off('focus.autocomplete', "#competitor_upc");
	});

	var CompTitle = new SVProductAutoComplete(productNamesURL, true);
	CompTitle.appendTo = '#compTitleAutoComplete';
	CompTitle.select = function( event, ui ) {
		$('input[name=competitor_upc]').val(ui.item.upc);
		$('input[name=competitor_product_id]').val(ui.item.id);
	}
	$(document).on('focus.autocomplete', '#competitor_product_title', function(e){
		$(this).autocomplete(CompTitle.get());
		$(document).off('focus.autocomplete', '#competitor_product_title');
	});

	var AssocUPC = new SVProductAutoComplete(upcsURL, false, true);
	AssocUPC.appendTo = '#assocUpcAutoComplete';
	AssocUPC.select = function( event, ui ) {
		$('input[name=associated_product_title]').val(ui.item.title);
		$('input[name=associated_product_id]').val(ui.item.id);
	};
	$(document).on('focus.autocomplete', '#associated_upc', function(e){
		$(this).autocomplete(AssocUPC.get());
		$(document).off('focus.autocomplete', '#associated_upc');
	});

	var AssocTitle = new SVProductAutoComplete(productNamesURL);
	AssocTitle.appendTo = '#assocTitleAutoComplete';
	AssocTitle.select = function( event, ui ) {
		$('input[name=associated_upc]').val(ui.item.upc);
		$('input[name=associated_product_id]').val(ui.item.id);
	}
	$(document).on('focus.autocomplete', '#associated_product_title', function(e){
		$(this).autocomplete(AssocTitle.get());
		$(document).off('focus.autocomplete', '#associated_product_title');
	});

	// Promotional Pricing
	$('#main').on('click', '#addPromoPricing', promo_pricing_add);
	$('#main').on('click', '#deletePromoPricing', promo_pricing_delete);

	$('#main').on('change', '#pricingSwitch', function(e){
		load_promotional_pricing_tab($('#pricingSwitch').val());
		e.preventDefault();
	});

	$('#excel_export').off('click');
	$(document).on('click', '#excel_export', function(){
		window.location = base_url + 'catalog/export_catalog/excel';
	});
    
    $('#pdf_export').off('click');
	$(document).on('click', '#pdf_export', function(){
		window.location = base_url + 'catalog/export_catalog/pdf';
	});
        
        
    $("#addrowbutton").on('click', function () {
        var datarow = generatedata(1)[0];
        var commit = $("#jqxgrid").jqxGrid('addrow', null, datarow)
    });
});

// Grid Objects
var columnsWidth = new Object;
columnsWidth.check = getWidth('check',40);
columnsWidth.upc_code = getWidth('upc_code',130);
columnsWidth.is_tracked = getWidth('is_tracked',80);
//columnsWidth.title = getWidth('title',633);
//columnsWidth.upc_code = getWidth('upc_code',125);
columnsWidth.sku = getWidth('sku',100);
columnsWidth.retail_price = getWidth('retail_price',77);
columnsWidth.price_floor = getWidth('price_floor',77);
columnsWidth.wholesale_price = getWidth('wholesale_price',80);

var columnCheckBox;
var centeredCell = function(row, column, value){
	return '<div style="text-align:center;margin-top:2px;">'+value+'<\/div>';
}
var numericCell = function(row, column, value){
	return '$'+(value*1).format(2);
}
var titleCell = function(row, column, value){
	return '<div style="overflow: hidden; text-overflow: ellipsis; padding-bottom: 2px; text-align: left; margin-right: 2px; margin-left: 4px; margin-top: 4px;" title="'+value+'">'+value+'</div>';
}

if ($('.catGrid').length){
	var catUrl = base_url + 'catalog/get_all_catalog_items?q=2&store_id=<?=$store_id?>';
	var CatGrid = new SVGrid('.catGrid', catUrl);
	<?php if ( ! empty($column_order)): ?>
		CatGrid.colOrder = <?=json_encode($column_order)?>;
	<?php endif; ?>
	var pinnedColumns = [
		{
			text: '', datafield: 'check', columntype: 'checkbox', width: 40, pinned: true, sortable: false, columnsreorder: false, resizable: false,
			renderer: CatGrid.renderer,
			rendered: CatGrid.rendered
		},
		{ text: 'UPC', dataField: 'upc_code', width: 130, pinned: true, resizable: false}
	];
	var catDatafields = [
		{ name: 'check', type: 'boolean' },
		{ name: 'upc_code' },
		{ name: 'sku' },
		{ name: 'title' },
		{ name: 'retail_price'},
		{ name: 'price_floor'},
		{ name: 'wholesale_price'},
		{ name: 'is_tracked' },
		{ name: 'is_archived' },
		{ name: 'id' },
		{ name: 'store_id' }
	];
	CatGrid.setSource(catDatafields);
	CatGrid.create = function(){
		//pinned columns
		columnsData = pinnedColumns.slice();
		for(var i=0; i<CatGrid.colOrder.length; i++){
			var textData = makeText(CatGrid.colOrder[i]);
			if(CatGrid.colOrder[i] == 'is_tracked'){
				columnsData.push({ text:textData, dataField: CatGrid.colOrder[i], width: columnsWidth[CatGrid.colOrder[i]], editable:false, cellsrenderer: centeredCell});
			}else if (CatGrid.colOrder[i] == 'sku' ){
				columnsData.push({ text:textData, dataField: CatGrid.colOrder[i], width: columnsWidth[CatGrid.colOrder[i]], cellsrenderer: titleCell});
			}else if (CatGrid.colOrder[i] == 'retail_price' || CatGrid.colOrder[i] == 'price_floor' || CatGrid.colOrder[i] == 'wholesale_price' ){
				//columnsData.push({ text:textData, dataField: CatGrid.colOrder[i], width: columnsWidth[CatGrid.colOrder[i]], cellsalign: 'right', cellsformat: 'c2', columntype: 'numberinput'});
				columnsData.push({ text:textData, dataField: CatGrid.colOrder[i], width: columnsWidth[CatGrid.colOrder[i]], cellsalign: 'right', cellsrenderer: numericCell, columntype: 'numberinput'});
			}else{
				columnsData.push({ text:textData, dataField: CatGrid.colOrder[i], width: columnsWidth[CatGrid.colOrder[i]]});
			}
		}
        var custom_pages =  ['5','10', '20', '30','50','100'];
        var dataAdapter = new $.jqx.dataAdapter(CatGrid.source);
        if(typeof(total_count) !='undefined' && total_count > 100) {
            custom_pages.push(total_count);
        }
        
		CatGrid.jqxGrid({
			source: dataAdapter,
			theme: CatGrid.theme,
			width : '100%',
			//autoheight: 'true',
			//height: '800px',
			sortable: true,
			sorttogglestates: 1,
			pageable: true,
			autoheight: true,
			editable: true,
			columnsresize: false,
			columnsreorder: true,
			columnsmenu: false,
			altrows: true,
			pagesizeoptions: custom_pages,
			pagesize: (CatGrid.bpS ? CatGrid.bpS : 10),
			selectionmode: 'none',
			columns: columnsData,
			ready: CatGrid.ready,
		});
		
	};

	CatGrid.bindEvents = function(){
		CatGrid.defaultEvents();
               
		$(CatGrid.sel).on('cellbeginedit', function (event){
			var args = event.args;
			var rowdata = $(CatGrid.sel).jqxGrid('getrowdata', args.rowindex);
			if(rowdata && rowdata.is_archived.toString() == '1'){
				CatGrid.jqxGrid('endcelledit', args.rowindex, args.datafield, true);
				return false;
			}
			return true;
		});

		$(CatGrid.sel).on('cellendedit', function (event){
			if (columnCheckBox){
				if (event.args.value){
					CatGrid.jqxGrid('selectrow', event.args.rowindex);
				}else {
					CatGrid.jqxGrid('unselectrow', event.args.rowindex);
				}
				var selectedRowsCount = $(CatGrid.sel).jqxGrid('getselectedrowindexes').length;
				var rowscount = $(CatGrid.sel).jqxGrid('getdatainformation').rowscount;
				CatGrid.updatingCheckState = true;
				if (selectedRowsCount == rowscount){
					$(columnCheckBox).jqxCheckBox('check')
				}
			}
		});

		
		/*$(CatGrid.sel).on("bindingcomplete", function (event) {
                    var $info = CatGrid.jqxGrid('getdatainformation');
                    if($info.rowscount > 100)
                    {
                        CatGrid.jqxGrid({pagesizeoptions: ['5','10', '20', '30','50','100', '' + $info.rowscount]});
                    }
                    else
                    {
                            CatGrid.jqxGrid({pagesizeoptions: ['5','10', '20', '30','50','100']});
                    }
                    CatGrid.jqxGrid('updatepagerdetails');
	}); */
		$(CatGrid.sel).on("cellclick", function(event){
			var column = event.args.column;
			var rowindex = event.args.rowindex;
            var flag = true;
			if(column.datafield =='is_tracked'){
				var rdata = CatGrid.jqxGrid('getrowdata', rowindex);
				if(rdata.is_tracked.indexOf('Skip') == -1){
					if(rdata.is_tracked.indexOf('Not Tracked') == -1){
						CatGrid.jqxGrid('setcellvalue', rowindex, "is_tracked", '<img alt="Not Tracked" style="cursor:pointer;margin-top: 2px" src="'+base_url+'/images/icons/dot-yellow.png" title="Click for Track" />');
						product_tracking(rdata.id, 'untracking', 0);
					}else{
						CatGrid.jqxGrid('setcellvalue', rowindex, "is_tracked", '<img alt="Tracked" style="cursor:pointer;margin-top: 2px" src="'+base_url+'/images/icons/checkmark.png" title="Click for Not Track" />');
						product_tracking(rdata.id, 'tracking', 0);
					}
				}
			}
		});

		$(CatGrid.sel).on('cellvaluechanged', function(){
			var row = $(CatGrid.sel).jqxGrid('getrowdata', args.rowindex);
			var data = {
				upc: row.upc_code,
                sku: row.sku,
				column: args.datafield,
				value: args.newvalue,
				old_value: args.oldvalue
			};
			if (data.column !== 'is_tracked' && data.column !== 'check') // is tracked is updated using product_tracking() above
				if(flag) {
                    $.post(base_url+"catalog/update_catalog_cell", data, function(response){
                        if(typeof response.error !='undefined' && response.error == 1) {
                            flag = false;
                            $(CatGrid.sel).jqxGrid('setcellvalue',args.rowindex,data.column,data.old_value);
                            var dialog = '<p>Product with same UPC and SKU is already exists.<\/p>';
                                sv_alert(dialog);
                                setTimeout(function() {
                                        $(this).dialog('close');
                                },
                                3000
                            );
                        }
                    }, 'json');
                }
		});

		$(CatGrid.sel).on('columnreordered', function(event){
			if (CatGrid.automaticallySetColumnIndex) {
				CatGrid.automaticallySetColumnIndex = false;
				return;
			}
			var sCols = {};

			var cols = CatGrid.jqxGrid('columns').records;
			for (var i = 0; i < cols.length; i++){
				if ( ! cols[i].pinned){
					var colName = cols[i].datafield;
					sCols[colName] = CatGrid.jqxGrid('getcolumnindex', colName);
				}
			}

			//update sort in db
			var args = event.args;
			var data = {cols: sCols};
			var columnReorderedCB = function(response){
				if ( !response.status ){
					CatGrid.automaticallySetColumnIndex = true;
					CatGrid.jqxGrid('setcolumnindex', args.datafield, args.oldindex);
				}
			};
			$.post(base_url+"catalog/update_column_sort/", data, columnReorderedCB, 'json');
		});
	};
}
var CompGrid;
function competitor_analysis_grid() {
	if ($('#competitor_grid').length){
		var compUrl = '<?=base_url() . 'catalog/get_all_competitor_items?q=2&store_id=' . $store_id;?>';
		var compDatafields = [
			{ name: 'check', type: 'boolean' },
			{ name: 'upc_code' },
			{ name: 'title' },
			{ name: 'owner' },
			{ name: 'associated_product' },
			{ name: 'associated_product_id' }
		];
		CompGrid = new SVGrid('#competitor_grid', compUrl);
		CompGrid.sCookieName = 'cps';
		CompGrid.setSource(compDatafields);
		CompGrid.create = function(){
			var columns = [
				{
					text: '', datafield: 'check', columntype: 'checkbox', width: 40, pinned: true, sortable: false,
					renderer: CompGrid.renderer,
					rendered: CompGrid.rendered
				},
				{ text: 'UPC Code', dataField: 'upc_code',width: columnsWidth.upc_code, pinned:true },
				{ text: 'Title', dataField: 'title', width: 498 },
				{ text: 'Brand Owner', dataField: 'owner', width: 200 },
				{ text: 'Associated Product', dataField: 'associated_product', width: 406 }
			];

			var compDataAdapter = new $.jqx.dataAdapter(CompGrid.source);
			CompGrid.jqxGrid({
				source: compDataAdapter,
				theme: CompGrid.theme,
				width : 1198,
				sortable: true,
				columnsmenu: false,
				sorttogglestates: 3,
				pageable: true,
				autoheight: true,
				editable: false,
				columnsresize: false,
				pagesizeoptions: ['5','10', '20', '30','50','100'],
				pagesize: (CompGrid.pbS ? CompGrid.pbS : 10),
				selectionmode: 'none',
				columns: columns,
				ready: CompGrid.ready
			});
		};
		CompGrid.bindEvents = function(){
			$(CompGrid.sel).on('rowclick', function (event){
				var args = event.args;
				var row = args.rowindex;
				rowindexes = $('#competitor_grid').jqxGrid('getselectedrowindexes');

				if(rowindexes.indexOf(row) != -1){
					CompGrid.jqxGrid('unselectrow', row);
					CompGrid.jqxGrid('setcellvalue', row, "check", 0);
				}else{
					CompGrid.jqxGrid('selectrow', row);
					CompGrid.jqxGrid('setcellvalue', row, "check", 1);
				}
			});
			// select or unselect rows when the checkbox is checked or unchecked.
			$(CompGrid.sel).on('cellendedit', function (event){
				if (event.args.value){
					CompGrid.jqxGrid('selectrow', event.args.rowindex);
				}else {
					CompGrid.jqxGrid('unselectrow', event.args.rowindex);
				}
				if (columnCheckBox){
					var selectedRowsCount = CompGrid.jqxGrid('getselectedrowindexes').length;
					var rowscount = CompGrid.jqxGrid('getdatainformation').rowscount;
					updatingCompCheckState = true;
					if (selectedRowsCount == rowscount){
						$(columnCheckBox).jqxCheckBox('check')
					}else $(columnCheckBox).jqxCheckBox('indeterminate');
					updatingCompCheckState = false;
				}
			});
		};
	}
}
var PriceGrid;
function promotional_pricing_grid(type) {
	type = type ? type : 'price_floor';
	var columnName = 'MAP';
	switch (type){
		case 'retail_price':
			columnName = 'Retail';
			break;
		case 'wholesale_price':
			columnName = 'Wholsale';
			break;
	}
	if ($('#promo_pricing').length){
		var ppUrl = base_url + 'catalog/get_all_promotional_pricing/' + type ;
		var ppDatafields = [
			{ name: 'check', type: 'boolean' },
			{ name: 'product_id', type: 'int' },
			{ name: 'title' },
			{ name: 'start_date', type: 'date' },
			{ name: 'end_date', type: 'date' },
			{ name: type, type: 'float' }
		];
		PriceGrid = new SVGrid('#promo_pricing', ppUrl);
		PriceGrid.type = type;
		PriceGrid.sCookieName = 'pps';
		PriceGrid.setSource(ppDatafields);
		PriceGrid.create = function(){
			//pinned columns
			var columns = [
				{
					text: '',
					datafield: 'check',
					columntype: 'checkbox',
					width: 40,
					pinned: true,
					sortable: false,
					renderer: PriceGrid.renderer,
					rendered: PriceGrid.rendered
				},
				{ text: 'Title', dataField: 'title', pinned: true },
				{ text: 'Start Date', dataField: 'start_date', width: 150, cellsformat: 'd', columntype: 'datetimeinput', pinned: true },
				{ text: 'End Date', dataField: 'end_date', width: 150, cellsformat: 'd', pinned: true },
				{ text: columnName, dataField: PriceGrid.type, width: columnsWidth[PriceGrid.type], pinned: true, cellsalign: 'right', cellsformat: 'c2', columntype: 'numberinput' }
			];

			var dataAdapter = new $.jqx.dataAdapter(PriceGrid.source);
			PriceGrid.jqxGrid({
				source: dataAdapter,
				theme: PriceGrid.theme,
				width: 1198,
				sortable: true,
				sorttogglestates: 1,
				pageable: true,
				autoheight: true,
				editable: true,
				columnsresize: true,
				columnsmenu: false,
				pagesizeoptions: ['5','10', '20', '30','50','100'],
				pagesize: (PriceGrid.bpS ? PriceGrid.bpS : 10),
				selectionmode: 'none',
				columns: columns,
				ready: PriceGrid.ready
			});
		};
		PriceGrid.bindEvents = function(){
			$(PriceGrid.sel).on('cellbeginedit', function (event){
				var args = event.args;
				var rowdata = $(PriceGrid.sel).jqxGrid('getrowdata', args.rowindex);
				if(rowdata && args.datafield === 'title'){
					$(this).jqxGrid('endcelledit', args.rowindex, args.datafield, true);
					event.preventDefault();
				}
			});

			$(PriceGrid.sel).on('cellendedit', function(event){
				if (columnCheckBox){
					if (event.args.value){
						PriceGrid.jqxGrid('selectrow', event.args.rowindex);
					}else {
						PriceGrid.jqxGrid('unselectrow', event.args.rowindex);
					}
					var selectedRowsCount = PriceGrid.jqxGrid('getselectedrowindexes').length;
					var rowscount = PriceGrid.jqxGrid('getdatainformation').rowscount;
					updatingCheckState = true;
					if (selectedRowsCount == rowscount){
						$(columnCheckBox).jqxCheckBox('check')
					}
				}
			});

			$(PriceGrid.sel).on('cellvaluechanged', function(e){
				var args = e.args;
				var index = args.rowindex;
				var row = $(PriceGrid.sel).jqxGrid('getrowdata', index);
				var data = {
					id: row.uid,
					product_id: row.product_id,
					pricing_type: PriceGrid.type,
					start_date: row.start_date,
					end_date: row.end_date,
					column: args.datafield,
					value: row[PriceGrid.type]
				};
				if (data.column !== 'title'){ // title is not editable on pricing page
					$.post(base_url+"catalog/update_promotional_pricing", data, function(response){
						if ( ! response.status){
							PriceGrid.jqxGrid('setcellvalue', index, response.column, '');
						}
					}, 'json');
				}
			});
		};
	}
}

$(function() {
	$('#main').on('change', '#showLookupArchived', function(){
		var show = $(this).attr('checked') ? 1 : 0;
		LookupGrid.setUrl(base_url + 'catalog/get_all_lookup_items?q=2&is_archived='+show);
		LookupGrid.refresh();
		LookupGrid.setUrl(catUrl);
	});
})

var LookupGrid;
function product_lookup_grid(){
	if ($('#lookup_grid').length){
		var catUrl = base_url + 'catalog/get_all_lookup_items?q=2';
		var lookupDatafields = [
			{ name: 'check', type: 'boolean' },
			{ name: 'upc_code' },
			{ name: 'sku' },
			{ name: 'title' },
			{ name: 'url' }
		];
		LookupGrid = new SVGrid('#lookup_grid', catUrl);
		LookupGrid.baseUrl = catUrl;
		LookupGrid.sCookieName = 'lgs';
		LookupGrid.setSource(lookupDatafields);
		LookupGrid.create = function(){
			var columns = [
				{
					text: '', datafield: 'check', columntype: 'checkbox', width: 40, pinned: true, sortable: false,
					renderer: LookupGrid.renderer,
					rendered: LookupGrid.rendered
				},
				{ text: 'UPC Code', dataField: 'upc_code',width: columnsWidth.upc_code, pinned:true },
				{ text: 'SKU', dataField: 'sku', width: columnsWidth.sku},//, width: 380 
				{ text: 'Title', dataField: 'title', width: 380 },//
				{ text: 'URL', dataField: 'url' }//, width: 500
			];

			var lookupDataAdapter = new $.jqx.dataAdapter(LookupGrid.source);
			LookupGrid.jqxGrid({
				source: lookupDataAdapter,
				theme: LookupGrid.theme,
				width : 1198,
				sortable: true,
				columnsmenu: false,
				sorttogglestates: 3,
				pageable: true,
				autoheight: true,
				editable: true,
				columnsresize: true,
				pagesizeoptions: ['5','10', '20', '30','50','100'],
				pagesize: (LookupGrid.pbS ? LookupGrid.pbS : 10),
				selectionmode: 'singlecell',
				columns: columns,
				ready: LookupGrid.ready
			});
		};

		LookupGrid.updateCell = function(args){
			var row = LookupGrid.jqxGrid('getrowdata', args.rowindex);
			var data = {
				upc: row.upc_code,
				column: args.datafield,
				value: args.value,
				old_value: args.oldvalue,
				//marketplace_id: $('.retailerLookup').val()
			};
			var updateCellCB = function(response){
				if ( ! response.status){
					//LookupGrid.jqxGrid('setcellvalue', args.rowindex, args.datafield, args.oldvalue);
					LookupGrid.jqxGrid('setcellvalue', args.rowindex, args.datafield, args.value);
					//if (args.value !== 'http://')
						//LookupGrid.jqxGrid('showvalidationpopup', args.rowindex, args.datafield, 'Must be a valid URL.');
				}
			}
            if (data.column === 'url'){
                if (data.value !== data.old_value/* && data.marketplace_id > 0*/)
					$.post(base_url+"catalog/update_product_lookup", data, updateCellCB, 'json');
			}
		}

		LookupGrid.bindEvents = function(){
			LookupGrid.defaultEvents();

			$(LookupGrid.sel).on('cellbeginedit', function (e){
				var args = e.args;
				var marketplace_id = $('.retailerLookup').val();
				if(args.datafield !== 'url' || marketplace_id <= 0){
					$(this).jqxGrid('endcelledit', args.rowindex, args.datafield, true);
					e.preventDefault();
				}
				else if(args.value.length == 0){
					$(this).jqxGrid('setcellvalue', args.rowindex, args.datafield, 'http://');
				}
			});

			$(LookupGrid.sel).on('cellendedit', function (e){
				var args = e.args;

				if (args.datafield === 'url') {
					LookupGrid.updateCell(args);
				}
				else if (columnCheckBox){
					if (e.args.value){
						LookupGrid.jqxGrid('selectrow', e.args.rowindex);
					}else {
						LookupGrid.jqxGrid('unselectrow', e.args.rowindex);
					}
					var selectedRowsCount = LookupGrid.jqxGrid('getselectedrowindexes').length;
					var rowscount = LookupGrid.jqxGrid('getdatainformation').rowscount;
					LookupGrid.updatingCheckState = true;
					if (selectedRowsCount == rowscount){
						$(columnCheckBox).jqxCheckBox('check');
					}
				}
			});

			$(document).on('change', '.retailerLookup', function(e){
				var marketplaceId = $(this).val();
				LookupGrid.setUrl(LookupGrid.baseUrl + '&marketplace_id=' + marketplaceId);
				LookupGrid.refresh();
			});
		};
	}
}

function defaultConfirmDialog(url, data, dialog, callback){
	sv_confirm(dialog, function(){
		document.body.style.cursor = 'wait';
		$.post(url, data, function(response){
			$('.modalWindow').dialog('close');
			document.body.style.cursor = '';
			var dialog = ''+response.html+'';
			sv_alert(dialog);
			if (callback && typeof(callback) === 'function')
				callback();
			return false;
		}, 'json');
		return false;
	});
}
var circle;
function saveNewProduct(form){
	if($.trim(form.product_title.value) == "") {
                $("#error_product_title").html('Please enter the product title.').show();
		return false;
	} else {
		$('#error_product_title').html('');
	}
	
        if($.trim(form.product_upc.value) == "") {
		$("#error_product_upc").html('Please enter the product UPC code.').show();
		return false;
	} else {
		$('#error_product_upc').html('');
	}
        
        if($.trim(form.product_sku.value) == "") {
		$("#error_product_sku").html('Please enter the product SKU.').show();
		return false;
	} else {
		$('#error_product_sku').html('');
	}
        
        if($.trim(form.product_retail.value) == "") {
		$("#error_product_retail").html('Please enter the product retail price.').show();
		return false;
	} 
        else if(!IsNumeric(form.product_retail.value)) {
            $("#error_product_retail").html('Please enter correct product retail price.').show();
            return false;
        }
        else {
		$('#error_product_retail').html('');
	}
        
        if($.trim(form.product_wholesale.value) == "") {
		$("#error_product_wholesale").html('Please enter the product wholesale price.').show();
		return false;
	} 
        else if(!IsNumeric(form.product_wholesale.value)) {
            $("#error_product_wholesale").html('Please enter correct product wholesale price.').show();
            return false;
        }
        else {
		$('#error_product_wholesale').html('');
	}
        
        if($.trim(form.product_map.value) == "") {
		$("#error_product_map").html('Please enter the product map price.').show();
		return false;
	} 
        else if(!IsNumeric(form.product_map.value)) {
            $("#error_product_map").html('Please enter correct product map price.').show();
            return false;
        }
        else {
		$('#error_product_map').html('');
	}
        
	$.post(base_url+'catalog/saveNewProduct', $(form).serialize(), function(response){
		if(response.success == 1) {
			$('.modalWindow').dialog('close');
			var datainformation = $(CatGrid.sel).jqxGrid('getdatainformation');
			var paginginformation = datainformation.paginginformation;
			if(response.new_record == 1) {
				CatGrid.jqxGrid('addrow', response.id, response.data);
			} else {
				CatGrid.jqxGrid('updaterow', response.id, response.data);
			}
			var dialog = '<p>Product saved successfully<\/p>';
			sv_alert(dialog);
			setTimeout(function() {
				$(this).dialog('close');
			},
			3000
		);
		}else{
			$('span#add_new_product_error').text(response.error);
		}
	}, 'json');
}//endSaveNewProduct
function resetSearchFilter(){
	LookupGrid.refresh();
	$('input[name=searchString]').val('');
}
function resetCompFilter(){
	$('#compSearchString').val('');
	CompGrid.filter();
}
function resetPriceFilter(){
	$('#priceSearchString').val('');
	PriceGrid.filter();
}
function resetLookupFilter(){
	$('#lookupSearchString').val('');
	LookupGrid.refresh();
}
function getWidth(name,defaultValue){
	var cook = getCookie(name);
	if(cook !=null){
		return parseInt(cook);
	}else{
		return parseInt(defaultValue);
	}
}

function alphaNumeric(str) {
    var letters = /^[a-zA-Z0-9]+$/;
    return letters.test(str);
}

function IsNumeric(sText){
    var ValidChars = "0123456789.";
    var IsNumber = true;
    var Char;
    for (i = 0; i < sText.length && IsNumber == true; i++) {
        Char = sText.charAt(i);
        if (ValidChars.indexOf(Char) == -1) {
            IsNumber = false;
        }
    }
    var dots = sText.split('.');
    if(dots.length > 2) {
        IsNumber = false;
    }
    return IsNumber;
}

/********************************************************************
 *                      Product List                                *
 *                                                                  *
 *            catalog management functions                          *
 *                                                                  *
 ********************************************************************/

function bulkActionsMethod(methodValue){
	if(methodValue == 'group_add') {
		group_add_popup();
	}	else if(methodValue == 'group_product_add') {
		group_product_add();
	}	else if(methodValue == '2')	{
		trackItems(1);
	}	else if(methodValue == '3')	{
		unTrackItems(1);
	}	else if(methodValue == '4')	{
		archiveItems(1);
	}	else if(methodValue == '5')	{
		archiveItems(0);
	}
}
function trackItems(show){
	var ids = CatGrid.getSelectedIds();
	if(ids.length == 0){
		var dialog = '<p>No item selected. Please select item(s) first to track.<\/p>';
		sv_alert(dialog);
		return false;
	}

	var data = {ids: ids};
	product_tracking(data.ids, 'tracking');

	return;
}//end trackItems
function unTrackItems(show){
	var ids = CatGrid.getSelectedIds();
	if(ids.length == 0){
		var dialog = '<p>No item selected. Please select item(s) to stop tracking.<\/p>';
		sv_alert(dialog);
		return false;
	}

	var data = {ids: ids};
	product_tracking(data.ids, 'untracking');

	return;
}//unTrackItems
function archiveItems(type){
	var ids = CatGrid.getSelectedIds();
	if(ids.length == 0){
		var dialog = '<p>No item selected. Please select item(s) first to '+(type == 1 ? 'archive' : 'un-archive')+'.<\/p>';
		sv_alert(dialog);
		return false;
	}

	var data = {ids: ids};
	product_archiving(data.ids, type);

	return;
}//archiveItems
function product_archiving(ids, type){
	document.body.style.cursor = 'wait';
	if(type == 1) {
		sv_confirm("Are you sure you want to archive these product(s)?<br/>Archived products will retain historical data but will no longer report any new pricing data.", function(){
			triggerArchiveSave(ids, type);
			return false;
		});
	}else{
		triggerArchiveSave(ids, type);
	}
	return false;
}//product_archiving
function triggerArchiveSave(ids, type) {
	var data = {ids: ids, action: type};
	var triggerArchiveSaveCB = function(response) {
		CatGrid.refresh();
		document.body.style.cursor = '';
		$('.modalWindow').dialog('close');
		var dialog = ''+response.html+'';
		sv_alert(dialog);
		return false;
	};
	$.post(base_url+"catalog/product_archiving", data, triggerArchiveSaveCB, 'json');
}//end triggerArchiveSave

function loadCatalog(){
	CatGrid.setUrl(catUrl);
	CatGrid.refresh();
}

/********************************************************************
 *                       Product Groups                             *
 *                                                                  *
 *            catalog group management functions                    *
 *                                                                  *
 ********************************************************************/

function group_add_popup(){
	var ids = CatGrid.getSelectedIds();
	if(ids.length == 0){
		var dialog = '<p>No item selected. Please select item(s) first to create group.<\/p>';
		sv_alert(dialog);
		return false;
	}

	$('input[name=group_ids]').val(ids);
	showDialog('#addGroupPopup', 'auto', 'auto', {
		buttons: {
			'Cancel': function(){
				$(this).dialog('close');
			},
			'Save': group_add
		}
	});

	return;
}

function group_add(){
	var ids = $('input[name=group_ids]').val();
	if (empty(ids)){
		sv("#createGroupMessage").set_error("<p>No item selected. Please select item(s) first to create group.<\/p>", 'slow');
		return false;
	}

	var name = $('input[name=group_name]').val();
	if (empty(name)){
		sv("#createGroupMessage").set_error("<p>Invalid group name.<\/p>", 'slow');
		return false;
	}

	var form = $('#newGroupForm');
	if (empty(form)){
		sv("#createGroupMessage").set_error("<p>An unknown error occurred. Please try refreshing the page.<\/p>", 'slow');
		return false;
	}

	// everything is good, we can create the group
	document.body.style.cursor = 'wait';
	var data = form.serialize();
	var groupAddCB = function(response){
		if(response.div_class === 'error'){
			sv("#createGroupMessage").set_error("<p>"+response.html+"<\/p>", 'slow');
		}else{
			$('input[name=group_ids]').val('');
			$('input[name=group_name]').val('');

			sv("#createGroupMessage").set_success("<p>"+response.html+"<\/p>", 'slow');
			ProductGroups.reload();
            GroupProducts.clear();
            CatGrid.clear();
			$('.modalWindow').dialog('close');
			document.body.style.cursor = '';
		}
	}
	$.post(base_url+"catalog/create_group/", data, groupAddCB, 'json');
}

function group_delete(){
	var group = ProductGroups.selectedItem;
	var groupID = group ? group.value : null;

	if (+groupID <= 0) {
		var dialog = '<p>Please first select a group.<\/p>';
		sv_alert(dialog);
		return false;
	}

	// everything is valid, we can delete the group
	var groupDeletePopupCB = function(){
		document.body.style.cursor = 'wait';

		var groupDeleteCB = function(response){
			ProductGroups.reload();
            GroupProducts.reload();
			document.body.style.cursor = '';
			$('.modalWindow').dialog('close');
		};
		$.post(base_url+'catalog/delete_group/'+groupID, groupID, groupDeleteCB, 'json');
	};
	sv_confirm('Are you sure you want to delete this Group?', groupDeletePopupCB);
}

function group_product_add(){
	var ids = CatGrid.getSelectedIds();
	if(ids == '') {
		var dialog = '<p>No items selected. Please first select item(s) to add.<\/p>';
		sv_alert(dialog);
		return false;
	}
	var group = ProductGroups.selectedItem;
	var groupID = group ? group.value : 0;
	if (+groupID <= 0) {
		var dialog = '<p>Please first select a group.<\/p>';
		sv_alert(dialog);
		return false;
	}

	// everything is valid, we can add the products to the group
	var data = "ids="+ids;
	var url = base_url+"catalog/edit_group/"+groupID;
	var dialog = "Are you sure you want to add the selected products to the group?";
	var groupProductAddCB = function(){
		$.post(url, data, function() {
            ProductGroups.reload();
            $(ProductGroups.sel).on('bindingComplete', function (event) {
                ProductGroups.selectItem(groupID);
            });
            CatGrid.clear();
        }, 'json');
		$('.modalWindow').dialog('close');
	}
	sv_confirm(dialog, groupProductAddCB);
}
function group_product_delete(item){
	var groupID = item.originalItem.group_id;
	var productID = item.originalItem.product_id;

	if(empty(productID)) {
		var dialog = '<p>An error occurred. The product could not be removed from the group.<\/p>';
		sv_alert(dialog);
		return false;
	}
	if (empty(groupID)) {
		var dialog = '<p>Invalid group. Please first select a group.<\/p>';
		sv_alert(dialog);
		return false;
	}
    // everything is valid, we can delete the group
	var groupProductDeletePopupCB = function(){
		document.body.style.cursor = 'wait';

		var groupProductDeleteCB = function(response){
			GroupProducts.reload();
			document.body.style.cursor = '';
			$('.modalWindow').dialog('close');
		};
        var data = {ids: productID, groupID: groupID};
		$.post(base_url+'catalog/delete_group_products/'+groupID, data, groupProductDeleteCB, 'json');
	};
    sv_confirm('Are you sure you want to delete this Group Product?', groupProductDeletePopupCB);
	
}

function addProductPopup(type){
	var at = '';
	if(type=='compAdd'){
		at = 'addCompPopup';
		h = 'auto';
	}else{
		at = 'addProdPopup';
		h = 225;
	}
        $('#'+at).find("input[type='text']").val('');
        $('#'+at).find(".error").html('');
	$('#'+at).dialog({
		modal: true
	});
}


/********************************************************************
 *                       Competitor Analysis                        *
 *                                                                  *
 *       Catalog competitor product management functions            *
 *                                                                  *
 ********************************************************************/

function comp_add_popup(e){
	showDialog('#addCompPopup', 'auto', 'auto', {
		buttons: {
			'Cancel': function(){
				$(this).dialog('close')
			},
			'Save': comp_add
		}
	});
}
function comp_add(){
	var prodId = $('form[name=addCompProdFrm] input[name=competitor_product_id]').val();
	var compAddCB = function(response){
		if(response.success == 1){
			$('.modalWindow').dialog('close');
			$('span#add_comp_product_error').text('');
			$('input[name=competitor_product_title]').val('');
			$('input[name=competitor_upc]').val('');
			var datainformation = $('#competitor_grid').jqxGrid('getdatainformation');
			var paginginformation = datainformation.paginginformation;
			$('#competitor_grid').jqxGrid('addrow', response.id, response.data);
			var dialog = '<p>Competitor Product Successfully Added<\/p>';
			sv_alert(dialog);
		}else{
			$('span#add_comp_product_error').text(response.message);
		}
	};
	$.post(base_url+'catalog/add_competition/'+prodId, compAddCB, 'json');
}
function comp_delete(e){
	var ids = CompGrid.getSelectedIds();
	if(ids.length <= 0){
		var dialog = '<p>Please select a competitor product to delete.<\/p>';
		sv_alert(dialog);
		return false;
	}

	var url = base_url+"catalog/remove_competition/";
	var dialog = "Are you sure you want to remove these competing product(s)?";
	var compDeleteCB = function(data){
		CompGrid.refresh();
		CompGrid.clear();
		$('.modalWindow').dialog('close');
		document.body.style.cursor = '';
	}
	sv_confirm(dialog, function(){
		document.body.style.cursor = 'wait';
		$.post(url, {ids: ids}, compDeleteCB, 'json');
	});

	return false;
}
function comp_link_popup(e){
	var ids = CompGrid.getSelectedIds();
	if(ids.length > 1){
		var dialog = '<p>Please select only one competitor product to associate.<\/p>';
		sv_alert(dialog);
		return false;
	}else if(ids.length == 0){
		var dialog = '<p>Please select a competitor product to associate.<\/p>';
		sv_alert(dialog);
		return false;
	}else{
		showDialog('#assCompPopup', 'auto', 'auto', {
			buttons: {
				'Cancel': function(){
					$(this).dialog('close');
				},
				'Save': comp_link
			}
		});
	}

	return;
}
function comp_link(){
	var ids = CompGrid.getSelectedIds();
	if(ids.length > 1){
		var dialog = '<p>Please select only one competitor product to associate.<\/p>';
		sv_alert(dialog);
		return false;
	}else if(ids.length == 0){
		var dialog = '<p>Please select a competitor product to associate.<\/p>';
		sv_alert(dialog);
		return false;
	}else{
		var compId = ids[0];
		var prodId = $('form[name=assCompProdFrm] input[name=associated_product_id]').val();

		if(prodId && compId){
			var data = {
				competitor_prod_id: compId,
				product_id: prodId
			};

			document.body.style.cursor = 'wait';
			var compLinkCB = function(response){
				CompGrid.clear();
				CompGrid.refresh();
				$('.modalWindow').dialog('close');
				$('input[name=associated_product_title]').val('');
				$('input[name=associated_upc]').val('');
				$('input[name=associated_product_id]').val('');
				document.body.style.cursor = '';
				var dialog = '<p>'+response.message+'<\/p>';
				sv_alert(dialog);
			}
			$.post(base_url+"catalog/associate/", data, compLinkCB, 'json');
		}
	}
}
function comp_unlink(e){
	var rows = CompGrid.getSelectedRows();
	if(rows.length <= 0){
		var dialog = '<p>Please select a competitor product to un-associate.<\/p>';
		sv_alert(dialog);
		return false;
	}else{
		var data = [];
		for (var row in rows)
			data.push({apid: rows[row].associated_product_id, cid: rows[row].uid});

		var compUnlinkCB = function(response){
			CompGrid.clear();
			CompGrid.refresh();
			sv_alert(response.message);
		};

		$.post(base_url+"catalog/unassociate/", {ids: data}, compUnlinkCB, 'json');
	}
	return;
}

function savePriceDates(form){
	var rowindexes = $("#brand_catalog").jqxGrid('getselectedrowindexes');

	if(rowindexes.length == 1){
		curRow = $(CatGrid.sel).jqxGrid('getrowdata', rowindexes[0]);

		t = $('select[name=price_type]').val();
		v = $('input[name=price_value]').val();
		s = new Date($('input[name=price_start]').val());
		e = new Date($('input[name=price_end]').val());
		var data = { type: t, value: v, id: curRow.uid, start: $('input[name=price_start]').val(), end: $('input[name=price_end]').val() };

		if(v == ''){
			$('#add_price_error').html('Please enter a price value.');
			return false;
		}else if(e.getTime()>=s.getTime()){
			$('#add_price_error').html('');
			$.post(base_url+'catalog/savePriceDates', data, function(response){
				if(response.success == 1){
					$('.modalWindow').dialog('close');
					var dialog = '<p>Pricing saved successfully.<\/p>';
					sv_alert(dialog);
					setTimeout(function() {
						$(this).dialog('close');
					}, 3000);
				}
			}, 'json')
			.error(function(){
				$('#add_price_error').html(data.message);
				return false;
			});
		}else{
			$('#add_price_error').html('End date must be after start date.');
			return false;
		}
	}else{
		$('#add_price_error').html('Please select one row for pricing dates.');
		return false;
	}
}//savePriceDates
</script>