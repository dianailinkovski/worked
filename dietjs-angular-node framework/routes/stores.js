app.get('/businesses/stores', function(request, response, mysql){
	when.business(request, response, mysql, function(){
		mysql.stores.get('business', response.head.account.business.id, {
			order: 'time_created DESC'
		}, function(stores){
			response.data.stores = stores;
			response.data.title = response.head.account.business.name + ' - Stores';
			response.data.page = 'stores';
			response.data.scripts = [scripts.jquery, scripts.jelq, scripts.maps, scripts.geo, scripts.page(response)];
			response.finish();
		});
	});
});

app.post('/businesses/stores', function(request, response, mysql){
	when.business(request, response, mysql, function(){
		request.demand('name');
		request.demand('formatted_address');
		console.log('/businesses/stores/save', request.body);
		if(request.passed){
			var id = request.body.id || uniqid();
			var store = {
				id: id,
				name: request.body.name,
				business: response.head.account.business.id,
				route_short: request.body.route_short || null,
				route_long: request.body.route_long || null,
				postal_code_short: request.body.postal_code_short || null,
				postal_code_long: request.body.postal_code_long || null,
				neighborhood_short: request.body.neighborhood_short || null,
				neighborhood_long: request.body.neighborhood_long || null,
				street_number_short: request.body.street_number_short || null,
				street_number_long: request.body.street_number_long || null,
				country_short: request.body.country_short || null,
				country_long: request.body.country_long || null,
				locality_short: request.body.locality_short || null,
				locality_long: request.body.locality_long || null,
				administrative_area_level_1_short: 
					request.body.administrative_area_level_1_short || null,
				administrative_area_level_1_long: 
					request.body.administrative_area_level_1_long || null,
				administrative_area_level_2_short: 
					request.body.administrative_area_level_2_short || null,
				administrative_area_level_2_long: 
					request.body.administrative_area_level_2_long || null,
				formatted_address: 
					request.body.formatted_address || null,
				latitude: request.body.latitude || null,
				longitude: request.body.longitude || null,
			};
			if(!request.body.id){
				store.time_created = new Date().getTime();
			}
			mysql.stores.save(store, function(){
				response.redirect('back');
			});
		} else {
			response.data.errors = request.errors;
			response.data.inputs = request.body;
			
			mysql.stores.get('business', response.head.account.business.id, function(stores){
				response.data.stores = stores;
				response.data.title = response.head.account.business.name + ' - Stores';
				response.data.page = 'stores';
				response.data.scripts = [scripts.jquery, scripts.jelq, scripts.maps, scripts.geo, scripts.page(response)];
				response.finish();
			});
		}
	});
});

app.get('/businesses/stores/delete', function(request, response, mysql){
	when.business(request, response, mysql, function(){
		if(request.query.id){
			mysql.stores.delete('id', request.query.id, function(){
				response.redirect('back');
			});
		} else {
			response.redirect('/business/stores/?error=no_id');
		}
	});		
});

when = {};
when.business = function(request, response, db, callback){
	if(response.head.account.id){
		if(response.head.account.business){
			callback();
		} else {
			response.redirect('/?error=no_business');
			mysql.end();
		}
	} else {
		app.login(request, response);
	}
}

