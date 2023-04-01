$(document).ready(function() {
	
	//****************************************************************************
	// Applique une animation de deplacement entre les liens pointant sur une ancre et leur ancre (href='#ancre').
	$('a[data-animate=true]').click(function()
	{
		var linkPathName = this.pathname;
		
		if (linkPathName == "")
		{
			this.pathname = location.pathname;	// S'assure que les liens pointant vers les ancres (entrees via le CMS) contiennent le chemin complet de la page.
		}
		
		if (location.hostname == this.hostname)
		{
			var $target = $(this.hash);
			$target = $target.length && $target || $('[id=' + this.hash.slice(1) +']');
			if ($target.length)
			{
				var targetOffset = $target.offset().top;
				$('html,body').animate({scrollTop: targetOffset-20}, 500);
				return false;
			}
		}
	});
	
	
	// Gère l'affichage du bouton de retour vers le haut de la page.
	$(window).scroll(function()
	{
		var wScrollTop = $(window).scrollTop();

		if (wScrollTop > 500)
		{
			$('#btn-btt').show(200);
		}
		else if (wScrollTop <= 500)
		{
			$('#btn-btt').hide(200);
		}
	});
	$(window).scroll();
	
	
	//	Gere la valeur par defaut (placeholder) des éléments input.placeholder.
	$('input.placeholder').each(function()
	{
		$(this).val($(this).attr('title'));
		
		$(this).click(function()
		{
			if ($(this).val() == $(this).attr('title'))
			{
				$(this).val('');
				$(this).removeClass('placeholder');
			}
		});
		
		$(this).blur(function()
		{
			if ($(this).val() == '')
			{
				$(this).val($(this).attr('title'));
				$(this).addClass('placeholder');
				
			}
		});
	});
	
});