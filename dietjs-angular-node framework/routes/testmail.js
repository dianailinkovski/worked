app.get('/testmail', function(request, response, mysql){
		mail.send({
					email: 'deb17276@gmail.com',
					subject: 'Please verify your email',
					html: 'verify.html',
					activated: '12345678',
					name: 'Deb'
				});
		response.data.title = 'Welcome to VBOX'
		response.data.page = 'welcome';
		response.data.scripts = [scripts.maps, scripts.geo, scripts.validetta, scripts.page(response)];
		response.finish();
});