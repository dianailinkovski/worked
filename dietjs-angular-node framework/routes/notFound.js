app.notFound(function(request, response){
	response.data.page = 'notFound';
	response.finish();
});