<script>
$(document).ready(function(){
	$('#add_more').click(function(){
		pr = $('.product_info_row');
		pc = pr.length;
		html_template = '<div class="product_info_row">'+
											'<hr>'+
											'<img class="jsLink" id="remove_'+pc+'" src="/images/icons/24/69.png" alt="Remove Product" onclick="removeProduct(this);">'+
								      '<div class="inputBlockContainer add_pro">'+
								        '<label for="title_'+pc+'">Product Title:</label>'+
								        '<input type="text" name="title[]" id="title_'+pc+'" value="" class="validate[required]">'+
								      '</div>'+
								      '<div class="inputBlockContainer add_pro">'+
								        '<label for="upc_code_'+pc+'">UPC Code:</label>'+
								        '<input type="text" name="upc_code[]" id="upc_code_'+pc+'" value="" class="validate[required,custom[integer]]">'+
								      '</div>'+
								      '<div class="inputBlockContainer add_pro">'+
								        '<label for="sku_'+pc+'">SKU:</label>'+
								        '<input type="text" name="sku[]" id="sku_'+pc+'" value="">'+
								      '</div>'+
								      '<div class="inputBlockContainer add_pro">'+
								        '<label for="retail_price_'+pc+'">Retail Price:</label>'+
								        '<input type="text" name="retail_price[]" id="retail_price_'+pc+'" value="" class="validate[required]">'+
								      '</div>'+
								      '<div class="inputBlockContainer add_pro">'+
								        '<label for="wholesale_price_'+pc+'">Wholesale Price:</label>'+
								        '<input type="text" name="wholesale_price[]" id="wholesale_price_'+pc+'" value="">'+
								      '</div>'+
								      '<div class="inputBlockContainer add_pro">'+
								        '<label for="price_floor_'+pc+'">MAP:</label>'+
								        '<input type="text" name="price_floor[]" id="price_floor_'+pc+'" value="">'+
								      '</div>'+
										'</div>';

	  $(html_template).insertBefore('#btns');
	});
});

function removeProduct(i){
	return $(i).parent('.product_info_row').remove();
}

// some live character trimming, and delegated for inserted html, too.
$("#form_manual_add").on('change', 'input[type="text"]', function()
{
	var value = $(this).val();
	value = value.replace(/[\$\,]*/g,'');
	value = value.replace(/\s{2,}/g,' ');
	value = $.trim(value);
	$(this).val( value );
	//if($(this).attr("name") == 'title[]' ) //else if($(this).attr("name") == 'upc_code[]' ) 
	//console.log('|'+$( this ).val()+'|');
});



</script>