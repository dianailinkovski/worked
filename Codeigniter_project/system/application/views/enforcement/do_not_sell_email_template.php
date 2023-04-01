<div id="modal-content">

    <?php if ($this->success_msg != ''): ?>
        <div class="alert alert-success" role="alert">
        	<?php echo $this->success_msg; ?>
        </div>  
    <?php endif; ?>
    
    <?php if ($this->error_msg != ''): ?>
    	<div class="alert alert-danger" role="alert">
    		<?php echo $this->error_msg; ?>
    	</div>
    <?php endif; ?> 
    
    <?php if (validation_errors() != ''): ?>    
        <div class="alert alert-danger" role="alert">
            <?php echo validation_errors(); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($form_error_msgs)): ?>
        <div class="alert alert-danger" role="alert">
        	<?php foreach ($form_error_msgs as $error_msg): ?>
                <p><?php echo $error_msg; ?></p>
        	<?php endforeach; ?>
        </div>    
    <?php endif; ?>
    
    <h3>
        Edit DNS Report Email Template
    </h3>
    
    <div id="template-form-area">
    
        <p id="dynamic-tokens">
            Dynamic Tokens: 
            <span class="dynamic-token-inline">{first_name}</span> 
            <span class="dynamic-token-inline">{last_name}</span> 
            <span class="dynamic-token-inline">{date}</span> 
            <span class="dynamic-token-inline">{report_table}</span>
        </p>
    
        <form autocomplete="off" action="/enforcement/do_not_sell_email_template" method="post">
            <div id="template-body-preview">
                <textarea id="wysiwyg" name="email_body"><?php echo set_value('email_body', $email_body);?></textarea>
            </div>
            <p style="margin-top: 20px;">                
                <input class="btn btn-success" type="submit" value="Save Changes" />
            </p>	
        </form>        
    </div>
    
</div>   

<script type="text/javascript">

function initTinyMCE(id)
{
	tinyMCE.init({
		// General options
		mode : "exact",
		elements : id,
		theme : "advanced",
		plugins : "autolink,lists,spellchecker,pagebreak,style,table,advhr,advimage,advlink,iespell,inlinepopups,insertdatetime,preview,searchreplace,print,contextmenu,paste,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect,|,styleprops,spellchecker,|,visualchars,nonbreaking,blockquote",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,advhr,|,fullscreen",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		width: 975,
		height: 500
	});
}

$(document).ready(function() {

    initTinyMCE('wysiwyg');
    
});    

</script>