// GET businesses browser
/*
app.get('/businesses', function(request, response, mysql){
	response.data.page = 'businesses';
	response.data.title = 'VBOX - Businesses';
	mysql('SELECT * FROM businesses', function(rows){
		response.data.businesses = rows;
		response.finish();
	});
});
*/


// GET classifieds 
app.get('/businesses', function(request, response, mysql){
	// Construct Meta & Resources
	response.data.page = 'businesses';
	response.data.title = 'VBOX - Businesses';
	response.data.scripts = [scripts.jquery, scripts.jelq, scripts.coffee, scripts.ect, scripts.selectize, scripts.google, scripts.maps, scripts.geo, scripts.imagesLoaded, scripts.masonry, scripts.page(response)];
	response.data.categories = new Categories();
	
	// Construct Input Data
	var query = request.query;
	response.data.inputs = {};
	response.data.inputs = merge(response.data.inputs, query);

	// LIMIT
	var limit_count = response.data.limit_count = 10;
	var page = query.page ? (query.page-1)*limit_count : 0 ;
	var LIMIT = 'LIMIT '+page+','+limit_count;
	
	// WHERE
	var WHERE = '';
	if(query.category || query.search_value){
		WHERE += 'WHERE ';
		if(query.search_value){
			var value = mysql.escape('%'+query.search_value+'%');
			WHERE += 'name LIKE ' + value;
		}
		
		if(query.category){
			if(query.search_value) WHERE += ' AND ';
			WHERE += 'category = ' + parseInt(query.category);
		}
	}
	
	// SELECT / ORDER
	var HAVING = '';
	console.log(query);
	if(query && query.latitude && query.longitude){
		var SELECT = ', (3959 * acos(cos(radians('+query.latitude+')) * cos(radians(latitude)) * cos( radians(longitude) - radians('+query.longitude+')) + sin(radians('+query.latitude+')) * sin(radians(latitude)))) AS distance FROM businesses ';
		var ORDER = 'ORDER BY distance';
		var HAVING = 'HAVING distance < 100';
	} else {
		var SELECT = 'FROM businesses';
		var ORDER = 'ORDER BY time_created DESC';
	}	

	var next = new Next(2, finish);
	var original_sql = 'SELECT * ' + SELECT +' '+ WHERE +' '+HAVING+' '+ ORDER +' '+ LIMIT;
	var count_sql = 'SELECT COUNT(*) FROM ( SELECT id ' + SELECT +' '+ WHERE +' '+HAVING + ') AS count';
	console.log('\n\noriginal_sql: ', original_sql);
	console.log('\n\ncount_sql: ', count_sql);
	
	mysql(count_sql, function(rows){
		console.log('TOTAL_ROWS:', rows[0]['COUNT(*)']);
		response.data.current_page = isset(query.page) ? parseInt(query.page) : 1 ;
		response.data.total_rows = rows[0]['COUNT(*)'];
		response.data.total_pages = Math.ceil(response.data.total_rows / limit_count);
	
		var showPageCount = 3;
		response.data.from_pages = response.data.current_page-showPageCount > 0  
			? response.data.current_page-showPageCount
			: 1 ;
		response.data.to_pages = response.data.current_page+showPageCount < response.data.total_pages 
			? response.data.current_page+showPageCount
			: response.data.total_pages ;
		
		next();
	});
	
	mysql(original_sql, function(rows){
		var rowNext = new Next(rows.length, function(){
			response.data.businesses = rows;
			next();
		});
		console.log('sayeed'+response.data.businesses);
		rows.forEach(function(row){
			row.category = getCategory(row.category);
			row.avatar = row.avatar 
				? '/uploads/avatars/original/'+row.avatar 
				: '/images/no-business-profile.png';
			rowNext();
			/*
			// get seller informations
			if(row.seller_type == 1){ // business seller
				mysql.businesses.get('id', row.seller, function(rows){
					row.seller = rows[0];
					row.seller_type = 'business';
					row.seller.avatar = row.seller.avatar 
						? '/uploads/avatars/original/'+row.seller.avatar 
						: '/images/no-business-profile.png';
					rowNext();
				});
			} else { // private seller
				mysql.accounts.get('id', row.seller, function(rows){
					row.seller = app.accounts.user(rows[0]);
					row.seller_type = 'private';
					rowNext();
				});
			}
			
			// get pictures
			mysql.ad_pictures.get('ad', row.id, function(pictures){
				row.pictures = pictures;
				rowNext();
			});*/
		});
	});
	
	function finish(){
		response.finish();
	}
});

// GET businesses profile
//app.post(/^\/accounts\/([^\/]+)\/?$/i, function(request, response, mysql){
app.get(/^\/([a-zA-Z0-9]+)\/?$/i, function(request, response, mysql){
if (request.params[1]){
console.log(request.params[1])
	mysql.businesses.get('address', request.params[1], function(rows){

if (rows.length>0){

		response.data.business = rows[0];
	console.log(rows[0]);
	console.log("----------------------------------------");
if (rows.length>0){	
		response.data.business.avatar = response.data.business.avatar 
			? '/uploads/avatars/original/'+response.data.business.avatar 
			: '/images/no-business-profile.png';
			}
else{
var row = {}
row['avatar'] =  '/images/no-business-profile.png';
row['category']=2

response.data.business = row;
}
		response.data.followerCount =0;
		response.data.ads = [];
		response.data.stores = [];
		response.data.employees = [];
		response.data.subpage = request.query.show || 'wall';
		response.data.business.category = getCategory(response.data.business.category)
		
		var business_id = response.data.business.id;
		
		// check connection	
		response.data.loginUser = false;
		var follower = response.head.account.business 
			? response.head.account.business.id 
			: response.head.account.id;
			if (response.head.account.id){
					//console.log('------------------->'+JSON.stringify(response.head.account))
				response.data.loginUser = true;
			}
		// check connection
		if(response.head.account && response.head.account.business && business_id == response.head.account.business.id){
			response.data.connection = 'you';
			afterConnection();
		} else {
			var sql = 'SELECT * FROM follows'
				+ ' WHERE follower = ' + mysql.escape(follower) 
				+ ' AND following = ' + mysql.escape(business_id);
			mysql(sql, function(rows){
				if(rows && rows.length){
					response.data.connection = 'following';
				} else {
					response.data.connection = 'stranger';
				
}
				  var sqlCount = 'SELECT * FROM follows'
                                + ' WHERE following = ' + mysql.escape(business_id);
                        mysql(sqlCount, function(rowsCount){
				var likedislike = "Select * from likedislike where "
				response.data.followerCount = rowsCount.length;

				afterConnection();

			});


			});
		}
		
		function afterConnection(){
			if(response.data.subpage == 'wall') {
				// LIMIT
				var limit_count = response.data.limit_count = 10;
				var page = request.query.page ? (request.query.page-1)*limit_count : 0 ;
				var LIMIT = page+','+limit_count;
				var count_sql = 'SELECT COUNT(*) FROM posts WHERE owner = ' + mysql.escape(business_id);
				var nextPost = new Next(2, finish);
				mysqlCountRows(request, response, mysql, count_sql, nextPost);
				
				mysql.posts.get('owner', business_id, {
					order: 'time DESC',
					limit: LIMIT
				}, function(rows){
					response.data.posts = rows;
					nextPost();
				});
				
			} else if(response.data.subpage == 'ads'){
				var next = new Next(1, finish);
				
				// get ads
				mysql('SELECT * FROM ads'
					 +' WHERE seller = '+mysql.escape(business_id)
					 +' AND seller_type = 1'
					 +' ORDER BY time_created DESC', 
				function(rows){
					var rowNext = new Next(rows.length*2, function(){
						response.data.ads = rows;
						next();
					});
					rows.forEach(function(row){
						// get seller informations
						if(row.seller_type == 1){ // business seller
							mysql.businesses.get('id', row.seller, function(rows){
								row.seller = rows[0];
								row.seller_type = 'business';
								row.seller.avatar = row.seller.avatar 
									? '/uploads/avatars/original/'+row.seller.avatar 
									: '/images/no-business-profile.png';
								rowNext();
							});
						} else { // private seller
							mysql.accounts.get('id', row.seller, function(rows){
								row.seller = app.accounts.user(rows[0]);
								row.seller_type = 'private';
								rowNext();
							});
						}
						
						// get pictures
						mysql.ad_pictures.get('ad', row.id, function(pictures){
							row.pictures = pictures;
							rowNext();
						});
					});
				});
				
			} else if(response.data.subpage == 'stores') {
				
				
				if(request.query.store){
					var next = new Next(2, finish);
					
					// get selected store
					mysql.stores.get('id', request.query.store, function(rows){
						response.data.store = rows[0];
						next();
					});
					
					// get confirmed employees
					mysql('SELECT * FROM accounts, business_employees'
						+ ' WHERE business_employees.business = ' + mysql.escape(business_id) 
						+ ' AND business_employees.store = ' + mysql.escape(request.query.store)
						+ ' AND accounts.id = business_employees.account AND confirmed = 1', 
					function(rows){
						rows.forEach(function(row){ row = app.accounts.user(row) });
						response.data.employees = rows;
						response.data.subpage = 'store';
						next();
					});
				} else {
					// get stores
					mysql.stores.get('business', business_id, function(stores){
						response.data.stores = stores;
						finish();
					});
				}
			}else{
				var limit_count = response.data.limit_count = 10;
				var page = request.query.page ? (request.query.page-1)*limit_count : 0 ;
				var LIMIT = page+','+limit_count;
				var count_sql = 'SELECT COUNT(*) FROM posts WHERE owner = ' + mysql.escape(business_id);
				var nextPost = new Next(2, finish);
				mysqlCountRows(request, response, mysql, count_sql, nextPost);
				
				mysql.posts.get('owner', business_id, {
					order: 'time DESC',
					limit: LIMIT
				}, function(rows){
					response.data.posts = rows;
					nextPost();
				});
			}
		}


}else{

	response.data.page = 'notFound';
	response.finish();

}

	});
	
	function finish(){
		response.data.title = response.data.business.name + ' - ' + response.data.subpage + ' - VBOX';
		response.data.page = 'business';
		if(response.data.subpage == 'stores'){
			
			response.data.scripts = [scripts.jquery, scripts.jelq, scripts.coffee, scripts.ect, scripts.google, scripts.page(response)];
		} else if (response.data.subpage == 'ads') {
			response.data.scripts = [scripts.jquery, scripts.jelq, scripts.masonry, scripts.imagesLoaded, scripts.page(response)];
		} else {
			response.data.scripts = [scripts.jquery, scripts.jelq];	
		}
		
		response.finish();
	}
}
else{
	response.data.page = 'notFound';
	response.finish();


}



}, true);

// GET businesses/create
app.get('/businesses/create', function(request, response, mysql){
	if(response.head.account.id){
		response.data.page = 'create_business';
		response.data.title = 'VBOX - Create a Business';	
		response.data.scripts = [scripts.maps, scripts.geo, scripts.selectize, scripts.page(response)];
		response.data.categories = new Categories();
		response.finish();
	} else {
		app.login(request, response);
	}
});

// POST businesses/create
app.post.simple(/^\/businesses\/create\/?$/i, function(request, response){
	// Parse Form
	var form = new formidable.IncomingForm();
	form.keepExtensions = true;
	form.uploadDir = app.public + "/uploads/avatars/original";
	form.parse(request, createConnection);
	
	function createConnection(err, fields, files){
		request.abort = function(){
			
			for(index in files){
				console.log('\n#FIES -> unlink', files[index].path);
				fs.unlinkSync(files[index].path);
			}
		}
		app.db(function(mysql){
			app.headers(request, response, mysql, function(){
				if(response.head.account.id){
					request.body = merge(fields, files);
					afterParse();
				} else {
					request.abort();
					app.login(request, response);
				}
			});
		});
	}
	
	function afterParse(){
		request.demand('name');
		request.demand('address');
		//request.demand('location');
		request.demand('contact_number');
		
		if(!request.body.avatar || !request.body.avatar.size){ 
			request.error('avatar', 'It\'s empty'); 
		}
		
		if(!request.errors.address){
			mysql.businesses.get('address', request.body.address, function(rows){
				if(rows && rows.length){
					request.error('address', 'already in use');
				} 
				afterDemands();
			});
		} else {
			afterDemands();
		}
		
		function afterDemands(){
			if(request.passed){
				try {
					var next = new Next(2, finish);
					var business = {
						id: uniqid(),
						name: request.body.name,
						address: request.body.address,
						contact_number: request.body.contact_number,
						avatar: request.body.avatar.path.split(app.public+'/uploads/avatars/original/')[1],
						route_short: request.body.route_short || null,
						route_long: request.body.route_long || null,
						postal_code_short: request.body.postal_code_short || null,
						postal_code_long: request.body.postal_code_long || null,
						neighborhood_short: request.body.neighborhood_short || null,
						neighborhood_long: request.body.neighborhood_long || null,
						street_number_short: request.body.neighborhood_short || null,
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
						time_created: new Date().getTime(),
						description: request.body.description,
						contact_email: request.body.contact_email || null,
						website: request.body.website || null,
						category: request.body.category || null,
					};
					var employee = {
						account: response.head.account.id,
						business: business.id,
						role: 0,
						contact_email: response.head.account.email,
						time_created: new Date().getTime(),
						confirmed: 1
					};
					
					mysql.businesses.save(business, next);
					mysql.business_employees.save(employee, next);
					function finish(){ response.redirect('/'+business.address); }
					
				} catch (error) {	
					console.log(error);
					//console.error(error);
					response.end('something went wrong', error.message);
					request.abort();
				}
			} else {	
				console.log('response finish with fail');
				response.data.errors = request.errors;
				response.data.page = 'create_business';
				response.data.title = 'VBOX - Create a Business';
				response.data.inputs = request.body;
				response.data.scripts = [scripts.maps, scripts.geo, scripts.page(response)];
				request.abort();	
				response.finish();
			}
		}
	}
});

// GET businesses/use
app.get(/^\/businesses\/use\/([^\/]+)$/i, function(request, response, mysql){
	if(request.params[1]){
		if(response.head.account.id){
			console.log(request.params[1]);
			if(request.params[1] != 'none'){
				var query = 
						'SELECT'
					+	' business_employees.id AS id,'
					+	' businesses.address AS business '
					+	'FROM business_employees, businesses'
					+	' WHERE business_employees.account = ' + mysql.escape(response.head.account.id) 
					+ 	' AND business_employees.business = ' + mysql.escape(request.params[1])
					+	' AND businesses.id = business_employees.business'
				console.log(query);
				mysql(query, function(rows){
					if(rows && rows.length){
						var next = new Next(1, finish);
						var session = uniqid();
						mysql.business_employees.save({ id: rows[0].id, session: session }, next);
						response.cookies.set('business', session, { httpOnly: true });
						
						function finish(){
							response.redirect('back');
						}
						
					} else {
						response.end('not authorized');
					}
				});
			} else {
				response.cookies.delete('business');
				response.redirect('back');
			}
		} else {
			app.login(request, response);
		}
	} else {
		request.notFoundHandler();
	}
});

// GET businesses/settings
app.get(/\/businesses\/settings\/([^\/]+)\/?/i, settingsPage);
function settingsPage(request, response, mysql){
	if(response.head.account.business){
		response.data.title = 'VBOX - Business Settings';
		response.data.page = 'business_settings';
		response.data.subpage =  response.data.subpage || request.params[1] || 'details';
		response.data.scripts = [scripts.jquery, scripts.jelq, scripts.selectize, scripts.maps, scripts.geo, scripts.page(response)];
		if(response.data.subpage == 'details'){
			response.data.categories = new Categories();
		}
		if(!response.data.inputs) response.data.inputs = response.head.account.business;
		response.finish();
	} else {
		var subpage = request.params[1] || 'details';
		response.redirect('/accounts/settings/'+subpage);
	}
}


// POST businesses/save
app.post('/businesses/save', function(request, response, mysql){
	request.demand('name');
	request.demand('formatted_address');
	request.demand('contact_number');
	
	console.log(request.body);
	
	if(request.passed){
		var business = {
			id: response.head.account.business.id,
			name: request.body.name,
			contact_number: request.body.contact_number,
			route_short: request.body.route_short || null,
			route_long: request.body.route_long || null,
			postal_code_short: request.body.postal_code_short || null,
			postal_code_long: request.body.postal_code_long || null,
			neighborhood_short: request.body.neighborhood_short || null,
			neighborhood_long: request.body.neighborhood_long || null,
			street_number_short: request.body.neighborhood_short || null,
			street_number_long: request.body.street_number_long || null,
			country_short: request.body.country_short || null,
			country_long: request.body.country_long || null,
			locality_short: request.body.locality_short || null,
			locality_long: request.body.locality_long || null,
			latitude: request.body.latitude || null,
			longitude: request.body.longitude || null,
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
			description: request.body.description,
			contact_email: request.body.contact_email || null,
			website: request.body.website || null,
			category: request.body.category || null,
		};
		mysql.businesses.save(business, function(){
			response.redirect('/businesses/settings/details?success=true');
		});
		
	} else {
		response.data.errors = request.errors;
		response.data.inputs = request.body;
		response.data.subpage = 'details';
		settingsPage(request, response, mysql);
	}
});

// GET businesses/removePicture
app.get('/businesses/removePicture', function(request, response, mysql){
	if(response.head.account.id && response.head.account.business){

		if(response.head.account.business.avatar){
			var source = app.public+'/uploads/avatars';
			var next = new Next(2, function(){
				response.redirect('back');
			})
			fs.unlink(source+'/original/'+response.head.account.business.avatar, next);
			mysql.businesses.save({id: response.head.account.business.id, avatar: null }, next)
		} else {
			response.redirect('back');
		}
	} else {
		response.redirect('back');
	}
});

// POST businesses/savePicture 
app.post.simple('/businesses/savePicture', function(request, response){
	app.upload('/uploads/avatars/original', request, response, function(mysql){
		console.log('request.body.files', request.body.files)
		if(request.body.files && request.body.files.picture && request.body.files.picture.size){
			var source = app.public+'/uploads/avatars/original/';

			var avatar = request.body.files.picture.path.split(source)[1];
			
			console.log({id: response.head.account.id, avatar: avatar});
			
			var next = new Next(2, finish);
			
			// delete old avatar
			console.log('response.head.account.avatarName=',response.head.account.avatarName)
			if(response.head.account.avatarName){
				var source = app.public+'/uploads/avatars';
				console.log('REMOVE FILE', source+'/original/'+response.head.account.avatarName)
				fs.unlink(source+'/original/'+response.head.account.avatarName, next);
			} else {
				next();
			}
			
			// save new avatar
			mysql.businesses.save({id: response.head.account.business.id, avatar: avatar}, next);
			
			function finish(){
				response.redirect('/businesses/settings/logo?success=true');
			}
			
		} else {
			request.error('photo', 'error');
			request.abort();
		}
	});
	
	response.onAbort = function(){

		response.data.errors = request.errors;
		response.data.inputs = request.body;
		response.data.subpage = 'logo';
		settingsPage(request, response, mysql);
	}
}, true);

// GET businesses/savePicture
app.get.simple('/businesses/savePicture', function(request, response){
	response.redirect('/accounts/settings/picture');
});
