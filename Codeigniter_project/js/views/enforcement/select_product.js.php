<?php
echo script_tag('js/jqwidgets/globalization/jquery.global.js');
echo script_tag('js/jqwidgets/globalization/jquery.glob.en-US.js');
?>
<script type="text/javascript">

$(document).ready(function(){

	var schedUrl = '<?php echo base_url()?>enforcement/get_products_list?q=1';
	var schDatafields = [
		{ name: 'store_id', type: 'int' },
		{ name: 'brand_logo' },
		{ name: 'store_name' },
		{ name: 'action' }
	];
	var ProductsGrid = new SVGrid('#product_list', schedUrl);
	ProductsGrid.sCookieName = 'schs';
	ProductsGrid.setSource(schDatafields);

 	ProductsGrid.create = function(){
		//pinned columns
		var columns = [
			{ text: 'Name', dataField: 'store_name', width: 440, pinned: true, editable: false },
			//{ text: 'Logo', dataField: 'brand_logo', width: 150, editable: false },
			{ text: 'Settings', dataField: 'action', width: 627, editable: false }
		];

		var dataAdapter = new $.jqx.dataAdapter(ProductsGrid.source);
		$(ProductsGrid.sel).jqxGrid({
			source: dataAdapter,
			theme: ProductsGrid.theme,
			width: 1098,
			sortable: true,
			sorttogglestates: 1,
			pageable: true,
			autoheight: true,
			editable: true,
			columnsresize: false,
			columnsmenu: false,
			rowdetails: true,
			pagesizeoptions: ['5','10', '20', '30','50','100'],
			pagesize: (ProductsGrid.bpS ? ProductsGrid.bpS : 10),
			selectionmode: 'none',
			columns: columns,
			ready: ProductsGrid.ready
		});
	};

	ProductsGrid.init();

	$('.container').on("keyup", '#searchString', function(e){
		if (e.which != 13)
			ProductsGrid.filter($(this).val());
	});

	$('#srchform .resetButton').click(function(){
		$(this).prev('.search').val('');
		ProductsGrid.reload();
	});
});
</script>