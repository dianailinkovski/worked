<script>
///////////////////////////////Import CSV Data/////////////////////////////////////////
function showEditField(id)
{
  var html = '<select id="set_header_'+id+'" name="set_header_'+id+'">'+
                '<option value="">Select</option>'+
                '<option value="ignore">Ignore</option>'+
                '<option value="title">Item Name</option>'+
                '<option value="upc_code">UPC</option>'+
                '<option value="sku">SKU</option>'+
                '<option value="retail_price">Retail Price</option>'+
                '<option value="price_floor">MAP</option>'+
                '<option value="wholesale_price">Wholesale Price</option>'+
              '</select>';
      html += '<img src="/images/icons/16/71.png" alt="Save" title="Save" onclick="saveColName('+id+');" class="jsLink">&nbsp;'+
							'<img src="/images/icons/16/55.png" alt="Ignore" title="Ignore" onclick="deleteColField('+id+');" class="jsLink">';

	$('#csv_headers th').each(function(i, e){
    if(i == id){
			$(e).html(html);
			if(!$(e).hasClass('edit_col')) $(e).addClass('edit_col');
			$(e).removeClass('set_col').removeClass('del_col');
    }else{
      if($('#th_'+i).hasClass('set_col') && $('#th_'+i).hasClass('del_col')){
        var returnHtml = 'Column&nbsp;'+(i+1)+'<br>'+
													'<img src="/images/icons/16/2.png" alt="Set" title="Set" onclick="showEditField('+i+');" class="jsLink">&nbsp;'+
													'<img src="/images/icons/16/55.png" alt="Ignore" title="Ignore" onclick="deleteColField('+i+');" class="jsLink">';
        $('#th_'+i).html(returnHtml);
        $('#th_'+i).removeClass('edit_col').removeClass('set_col');
      }
    }
  });

	return;
}

function deleteColField(col_id)
{
  $('#th_'+col_id).html('Ignored<br><img src="/images/icons/16/2.png" alt="Save" onclick="showEditField('+col_id+');" class="jsLink">');
  $('#th_'+col_id).removeClass('set_col').removeClass('edit_col').addClass('del_col');
	$('#header_'+col_id).val('ignore');
	checkCompletion();
}

function saveColName(id)
{
	h = $('#set_header_'+id);
  if(h.val() != 'ignore'){
		$('#header_'+id).val(h.val());
		hv = $('#set_header_'+id+' option[value="'+h.val()+'"]').text();
    html = hv+'<br><img src="/images/icons/16/2.png" alt="Set" title="Set" onclick="showEditField('+id+');" class="jsLink">&nbsp;|&nbsp;<img src="/images/icons/16/55.png" alt="Ignore" title="Ignore" onclick="deleteColField('+id+');" class="jsLink">';
    $("#th_"+id).removeClass('edit_col').addClass('set_col');
    $("#th_"+id).html(html);
		checkCompletion();
  }else if(h.val() == 'ignore'){
    deleteColField(id);
  }
}

function checkCompletion()
{
	passed = true;

	// required fields that are needed for CSV import
	// var req = new Array('title', 'upc_code', 'retail_price', 'price_floor');
	var req = new Array('title', 'upc_code'); // list reduced by Christophe on 12/9/2015
	
	var vals = new Array();

	//check against total number & column duplication
	t = $('input[name="cols_count"]').val();
	//total values set
	ts = 0;
	fi = null;

	for(var i=0; i<t; i++){
		//make sure the column has been set...
		c = $('input[name="header_'+i+'"]').val();
		if(c == ''){
			//if any columns are empty, form will not pass
			passed = false;
			//note the first index not set for later use...
			if(!fi) fi = i;
		}
		//make sure the column isn't a dupe...
		if(vals.indexOf(c) !== -1){
			passed = false;
		}else if(c != '' && c != 'ignore'){
			vals.push(c);
			ts++;
		}else if(c == 'ignore'){
			ts++;
		}
	}

	// now check for the required product fields
	// @todo we should be checking field names - not length
	for (var r = 0; r < req.length; r++)
	{
		if (vals.indexOf(req[r]) === -1)
		{
			passed = false;
		}
	}

	s = $('input[id="csv_submit"]');
	if(passed){
		s.removeClass('hidden');
	}else if(!s.hasClass('hidden')){
		s.addClass('hidden');
	}

	if(!passed) showEditField(fi);

	return passed;
}

function validateSubmit()
{
  if(!checkCompletion()){
    alert("Please select at least one column to import.");
    return false;
  }else{
    return true;
  }
}
</script>