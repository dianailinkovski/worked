<script type="text/javascript">
$(function() {
	$("#edit_store").click(function() {
		var errors = [];
		
		if ($("#ID1_email").val() == '') {
			errors.push("Please enter an amazon email address.");
		}
		
		if ($("#ID1_emailpassword").val() == '') {
			errors.push("Please enter an amazon password.");
		}
		
		//if ( $("#proxy_address").val() == '' && ( $("#proxy_user").val() != '' || $("#proxy_password").val() != '' ) ) {
		//	errors.push("Please enter an proxy address.");
		//}
		//if ($("#proxy_user").val() == '' && $("#proxy_password").val() != '') {
		//	errors.push("Please enter an proxy user.");
		//}

		if (errors.length > 0) {
			var error = '<p>' + errors.join('</p><p>') + '</p>';
			$("#amazon-error").html(error);

			return false;
		}
		
		$("#amazon-error").html("");
		return true;
	});
	
	
    // Clone the form fields
	$('#btnAdd').click(function () {
        var num     = $('.clonedInput').length, // how many "duplicatable" input fields we currently have

        newNum  = new Number(num + 1),      // the numeric ID of the new input field being added
        newElem = $('#entry' + num).clone().attr('id', 'entry' + newNum).show(); // create the new element via clone(), and manipulate it's ID using newNum value
			
        // manipulate the name/id values of the input inside the new element
		
		    // H2 - section
        newElem.find('.heading-reference').attr('id', 'ID' + newNum + '_reference').attr('name', 'ID' + newNum + '_reference').html('Login #' + newNum);
		
        // Email - text
        newElem.find('.label_email').attr('for', 'ID' + newNum + '_email');
        newElem.find('.input_email').attr('id', 'ID' + newNum + '_email').attr('name', 'ID' + newNum + '_email').val('');
 
        // Password - text
        newElem.find('.label_password').attr('for', 'ID' + newNum + '_password');
        newElem.find('.input_password').attr('id', 'ID' + newNum + '_password').attr('name', 'ID' + newNum + '_password').val('');
		
        // Marketplace - select
        newElem.find('.label_marketplace').attr('for', 'ID' + newNum + '_marketplace');
        newElem.find('.input_marketplace').attr('id', 'ID' + newNum + '_marketplace').attr('name', 'ID' + newNum + '_marketplace').val('');
        newElem.find('.input_marketplace').find('option').removeAttr("selected");
		
        // Error message
		    newElem.find('.error').remove();
 
		    // insert the new element after the last "duplicatable" input field
        $('#entry' + num).after(newElem);
        $('#ID' + newNum + '_title').focus();
 
        // enable the "remove" button
        $('#btnDel').attr('disabled', false);
 
        // right now you can only add 5 sections. change '5' below to the max number of times the form can be duplicated
        if (newNum == 5)
        {
            $('#btnAdd').attr('disabled', true).prop('value', "You've reached the limit");
        }
    });
	
 
    $('#btnDel').click(function () {
        
        // confirmation
        if (confirm("Are you sure you wish to remove this section? This cannot be undone."))
        {
            var num = $('.clonedInput').length;
            
            // how many "duplicatable" input fields we currently have
            $('#entry' + num).slideUp('fast', function () {
                $(this).remove();

                // if only one element remains, disable the "remove" button
                if (num -1 === 1)
                $('#btnDel').attr('disabled', true);

                // enable the "add" button
                $('#btnAdd').attr('disabled', false).prop('value', "add section");
            });
        }
        
        return false;
    });
 

 
 

})
</script>
