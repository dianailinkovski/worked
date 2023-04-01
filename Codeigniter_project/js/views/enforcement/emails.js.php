<script type="text/javascript">

$(document).ready(function () {

	// bug causing code; it resets every tinyMCE every time you click on a tab - Christophe 11/10/2015
	/*
	$('.tabNav a').on('click', function() {
		var href = $(this).attr('href');
		var tab = href.replace('#', '');
		var id = $(this).parents('.tabs').find('#'+tab+' .wysiwyg').attr('id');
		if ($(this).parents('.tabs').find('#'+tab+' .wysiwyg').not('tinyMceLoaded')) {
			$(this).parents('.tabs').find('#'+tab+' .wysiwyg').addClass('tinyMceLoaded');
			initTinyMCE(id);
		}
	});
	*/
	
});

// change on 11/10/2015
if ($('#wysiwyg1').length >= 1)
{
    initTinyMCE('wysiwyg1');
}

if ($('#wysiwyg2').length >= 1)
{
    initTinyMCE('wysiwyg2');
}

function initTinyMCE(id){
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
		height: 670
	});
}
</script>
