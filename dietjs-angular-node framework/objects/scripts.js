scripts = {
	jquery		: '/scripts/modules/jquery.js',
	jelq		: '/scripts/modules/jelq.js',
	dropzone	: '/scripts/modules/dropzone.js',
	chosen		: '/chosen/chosen.jquery.min.js',
	selectize	: '/selectize/js/selectize.min.js',
	masonry		: '/scripts/modules/masonry.js',
	imagesLoaded: '/scripts/modules/imagesLoaded.js',
	validetta	: '/scripts/modules/validetta.js',
	ect			: '/scripts/modules/ect.js',
	coffee		: '/scripts/modules/coffee.js',
	share		: '/scripts/modules/rrssb.min.js',
	google		: 'https://maps.googleapis.com/maps/api/js?v=3.exp',
	maps		: 'http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places',
	geo			: '/scripts/modules/jquery.geocomplete.js',
	currency	: '/scripts/modules/currency.js',
	accounting	: '/scripts/modules/accounting.js',
	slider		: '/royalslider/jquery.royalslider.min.js',
	page		: function(response){
		return '/scripts/pages/'+response.data.page+'.js';
	}
};