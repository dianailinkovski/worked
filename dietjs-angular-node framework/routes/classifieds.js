// GET classifieds 
app.get('/classifieds', function(request, response, mysql){
	// Construct Meta & Resources
	response.data.page = 'classifieds';
	response.data.title = 'VBOX - Classifieds';
	response.data.scripts = [scripts.jquery, scripts.jelq, scripts.coffee, scripts.ect, scripts.selectize, scripts.google, scripts.maps, scripts.geo, scripts.imagesLoaded, scripts.masonry, scripts.page(response)];
	response.data.categories = new Categories();
	//console.log('Test:Sep:'+response.data.categories);
	// Construct Input Data
	var query = request.query;

	
	console.log(query);
		var i = 1;
		var customQuery = '';
		var check = false;
		var checkCondition = true;
		for(var x in query){
			if(x=='category' || i>1){
				var res = x.replace(/@/g,'')
				if(res=='category' || res=='trade_type'){
					
				}else{
					if(query[x]!=''){
						/*if(i==2){
							customQuery += "name = '" +res+"' AND value='"+query[x]+"'";
						}else{
							customQuery += " OR name = '" +res+"' AND value='"+query[x]+"'";
						}*/
						if(query[x]=='Price'){
						}else{
							if(query[x] == '0'){
								if(checkCondition){
									customQuery += "name = '" +res+"'";
									checkCondition = false;
								}else{
									customQuery += " OR name = '" +res+"'";
								}
								
							}else{
								if(checkCondition){
									customQuery += "name = '" +res+"' AND value='"+query[x]+"'";
									checkCondition = false;
								}else{
									customQuery += " OR name = '" +res+"' AND value='"+query[x]+"'";
								}
							}
							
						}
						
						check = true;
					}
				}
				i++;
			}
		}
		
		//console.log(customQuery);
		var newCustomQuery =  "AND id IN  (select ad from ad_fields where "+ customQuery+")";	
		
   
	response.data.inputs = {};
	response.data.inputs.trade_type = "1";
	response.data.inputs = merge(response.data.inputs, query);

	// LIMIT
	var limit_count = response.data.limit_count = 20;
	var page = query.page ? (query.page-1)*limit_count : 0 ;
	var LIMIT = 'LIMIT '+page+','+limit_count;
	
	// WHERE
	var WHERE = '';
	var checkWhere = false;
	var checkPrice = false;
	if(query.category || query.trade_type){
		WHERE += 'WHERE ';
        var firstAnd = 0;
		if(query.search_value){
			var value = mysql.escape("%"+query.search_value+"%");
            firstAnd = 1;
			WHERE += 'title LIKE ' + value + ' OR description LIKE ' + value;
			checkWhere = true;
		}
//		
		if(query.category){
			if(query.search_value) 
            if (firstAnd==1) WHERE += ' AND ';
			WHERE += 'category = ' + parseInt(query.category);
            firstAnd = 1;
            checkWhere = true;
		}
		if(query.Price){
			checkPrice = true;
			if(query.category || query.search_value) 
            if (firstAnd==1) WHERE += ' AND ';
			WHERE += ' price >= ' + (query.Price);
            firstAnd = 1;
            checkWhere = true;
		}
		if(query.PriceEndText){
			if(query.category || query.search_value) 
			if(checkPrice){
				if (firstAnd==1) WHERE += ' AND ';
				WHERE += ' price <= ' + (query.PriceEndText);
			}else{
				if (firstAnd==1) WHERE += ' AND ';
				WHERE += ' price <= ' + (query.PriceEndText);
			}
            //WHERE += ' AND ';
			//WHERE += ' price <= ' + (query.PriceEndText);
			/*if(firstAnd==1){
				WHERE += ' AND ';
				WHERE += ' price <= ' + (query.PriceEndText);
				
			}else{
				WHERE += ' price <= ' + (query.PriceEndText);
			}*/
            firstAnd = 1;
            checkWhere = true;
		}
		if(query.country_short){
			if(query.country_short && query.country_short!="") {
			    
			 if (firstAnd==1) WHERE += ' AND ';
			WHERE += "country_short = '" + (query.country_short)+"'";
            firstAnd = 1;
            checkWhere = true;
            }
		}
		if(query.trade_type){
			if(query.category || query.search_value) {
			    
			 if (firstAnd==1) WHERE += ' AND ';
			WHERE += 'trade_type = ' + parseInt(query.trade_type);
            firstAnd = 1;
            checkWhere = true;
            }
		}
		if(query.administrative_area_level_1_short){
			if(query.administrative_area_level_1_short && query.administrative_area_level_1_short!="") {
                if (firstAnd==1) WHERE += ' AND ';
			WHERE += "administrative_area_level_1_short = '" + (query.administrative_area_level_1_short)+"'";
            firstAnd = 1;
            checkWhere = true;
            }
		}
		if(query.administrative_area_level_1_long){
			if(query.administrative_area_level_1_long && query.administrative_area_level_1_long!="") {
			    
			 if (firstAnd==1) WHERE += ' AND ';
			WHERE += "administrative_area_level_1_long = '" + (query.administrative_area_level_1_long)+"'";
            firstAnd = 1;
            checkWhere = true;
            }
		}
		/*if(query.administrative_area_level_2_short){
			if(query.administrative_area_level_2_short && query.administrative_area_level_2_short!="") {
                if (firstAnd==1) WHERE += ' AND ';
			WHERE += "administrative_area_level_2_short = '" + (query.administrative_area_level_2_short)+"'";
            firstAnd = 1;
            checkWhere = true;
            }
		}*/
	}
	
	// SELECT / ORDER
	var HAVING = '';
	
	//if(customQuery!=''){
	if(check){
		WHERE += ' '+newCustomQuery;
	}
	//if(WHERE = 'WHERE '){
	if(checkWhere){
		console.log('ifffffffffffffffffff checkWhere');
	}else{
		console.log('elseeeeeeeeeeeeeeeee ifffffffffffffffffff checkWhere');
		WHERE = '';
	}
	
	
	console.log('STAR'+WHERE);
	if(query && query.latitude && query.longitude){
		var SELECT = ', (3959 * acos(cos(radians('+query.latitude+')) * cos(radians(latitude)) * cos( radians(longitude) - radians('+query.longitude+')) + sin(radians('+query.latitude+')) * sin(radians(latitude)))) AS distance FROM ads ';
		var ORDER = 'ORDER BY distance';
		//var HAVING = 'HAVING distance < 1000000';
		if(query.Distance == ''){
			var HAVING = '';
		}else{
			if(query.DistanceEndText){
				var HAVING = 'HAVING distance >= '+query.Distance+' AND distance <= '+query.DistanceEndText;
			}else{
				var HAVING = 'HAVING distance <= '+query.Distance;
			}
			
		}
		//var HAVING = 'HAVING distance < 10';
	} else {
		var SELECT = 'FROM ads';
		var ORDER = 'ORDER BY time_created DESC';
	}	

	var next = new Next(2, finish);
	//var original_sql = 'SELECT * ' + SELECT +' '+ WHERE ' ' +HAVING+' '+ ORDER +' '+ LIMIT ;
	var original_sql = 'SELECT * ' + SELECT +' '+ WHERE +' '+HAVING+' '+ ORDER +' ';
	//var original_sql = 'SELECT * ' + SELECT +' '+ WHERE ' ' +HAVING+' '+newCustomQuery+' '+ ORDER +' '+ LIMIT;
	var count_sql = 'SELECT COUNT(*) FROM ( SELECT id ' + SELECT +' '+ WHERE +' '+HAVING + ') AS count';
	
	console.log('\n\noriginal_sql: ', original_sql);
	console.log('\n\ncount_sql: ', count_sql);
	
	mysqlCountRows(request, response, mysql, count_sql, next);
	
	mysql(original_sql, function(rows){
		var rowNext = new Next(rows.length*2, function(){
			response.data.ads = rows;
			next();
		});
		rows.forEach(function(row){
			// get seller informations
			if(row.seller_type == 1){ // business seller
				var businessNext = new Next(2, function()
				{
					row.seller.online = row.agent.account.online;
					rowNext();
				});
				mysql.businesses.get('id', row.seller, function(rows)
				{
					//console.log(row.seller.avatar );
					row.seller_type = 'business';
					row.seller.avatar = row.seller.avatar ? '/uploads/avatars/original/'+row.seller.avatar : '/images/no-business-profile.png';
					//~ if(typeof rows[0].avatar != undefined)
					//~ {
						//~ //row.seller_type = '/uploads/avatars/original/'+row.seller.avatar;
						//~ row.seller_type = '/images/no-business-profile.png';
					//~ }else
					//~ {
						//~ row.seller_type = '/images/no-business-profile.png';
					//~ }
					businessNext();
				});
				
				// get employee
				mysql.business_employees.get('id',row.agent, function(employees){
					row.agent = employees[0];
					// get employee account
					mysql.accounts.get('id', row.agent.account, function(accounts){
						row.agent.account = app.accounts.user(accounts[0]);
						businessNext();
					});
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
	
	function finish(){
		response.finish();
	}
});

// GET classifieds/mine
app.get('/classifieds/mine', function(request, response, mysql){
    
console.log(">>>>>>>>>>>>>>>>>>>>>>>> GET ADS <<<<<<<<<<<<<<<<<<<<<<<<<<")
	var seller = response.head.account.business ? response.head.account.business.id : response.head.account.id ;
	var seller_type = response.head.account.business ? 1 : 0 ;
	response.data.ads = [];
	
	mysql('SELECT * FROM ads'
		 +' WHERE seller = '+mysql.escape(seller)
		 +' AND seller_type = '+mysql.escape(seller_type)
		 +' ORDER BY time_created DESC', 
	function(rows){
		var rowNext = new Next(rows.length*2, function(){
			response.data.ads = rows;
			finish();
		});
		rows.forEach(function(row){
			// get seller informations
			
			if(row.seller_type == 1){ // business seller
				mysql.businesses.get('id', row.seller, function(businesses){
					row.seller = businesses[0];
					row.seller_type = 'business';
					row.seller.avatar = row.seller.avatar 
						? '/uploads/avatars/original/'+row.seller.avatar 
						: '/images/no-business-profile.png';
					rowNext();
				});
			} else { // private seller
				mysql.accounts.get('id', row.seller, function(accounts){
					row.seller = app.accounts.user(accounts[0]);
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
	
	function finish(){
		response.data.page = 'classifieds_mine';
		response.data.title = 'My Classifieds - VBOX';
		response.data.scripts = [scripts.jquery, scripts.jelq, scripts.imagesLoaded, scripts.masonry, scripts.page(response)];
		response.finish();
	}
});

// GET /classifieds/:id
app.get(/^\/classifieds\/([^\/]+)\/?$/i, function(request, response, mysql){
	// get classifieds
    console.log("::::::::::::::::::::::::::::: Get ADS ::::::::::::::::::::::::")
	get_classified(request.params[1], request, response, mysql, function(){
		// set metadata
		response.data.title = response.data.ad.title + ' - VBOX';
		response.data.page = 'classified';
		response.data.scripts = [scripts.jquery, scripts.jelq, scripts.share, scripts.slider, scripts.google, scripts.masonry, scripts.page(response)];
		
		// get sub category
		response.data.ad.sub_category = response.data.ad.fields.filter(function(field, index){
			if(field.name == 'sub_category'){ 
				response.data.ad.fields.splice(index, 1); 
				return field.value; 
			}
		})[0];
		
		// get category
		response.data.ad.category = getCategory(response.data.ad.category);
		
		// get comments
		mysql.ad_comments.get('ad', response.data.ad.id, {
			order: 'time DESC'
		}, function(comments){
			// get comment authors
			var next = new Next(comments.length, function(){
				response.data.ad.comments = comments;
				finish();
			});
			comments.forEach(function(comment){
				mysql.accounts.get('id', comment.author, function(accounts){
					comment.author = app.accounts.user(accounts[0]);
					next();
				});
			});
		});
		
		function finish(){
			response.finish();
		}
		
		
	});
});

// GET /classifieds/:id/edit
app.get(/^\/classifieds\/([^\/]+)\/edit\/?$/i, function(request, response, mysql){	
	get_classified(request.params[1], request, response, mysql, function(){
		classified_editor(request, response, mysql);
	});
});

classified_editor = function(request, response, mysql, callback){
	
	// set metadata
	response.data.page = 'classifieds_edit';
	response.data.title = 'Edit - ' + response.data.ad.title + ' - VBOX';
	response.data.scripts = [scripts.jquery, scripts.jelq, scripts.selectize, scripts.dropzone, scripts.accounting, scripts.maps, scripts.geo, scripts.page(response)];
	response.data.currencies = JSON.parse(JSON.stringify(currencies));
	response.data.inputs = response.data.ad;
	response.data.inputs.trade_type = response.data.inputs.trade_type || 1;
	response.data.categories = new Categories();

	// if seller is a business
	if(response.head.account.business){
		// get confirmed employees
		mysql('SELECT * FROM accounts, business_employees'
			+ ' WHERE business_employees.business = "' + response.head.account.business.id 
			+ '" AND accounts.id = business_employees.account AND confirmed = 1', 
		function(rows){
			rows.forEach(function(row){ row = app.accounts.user(row) });
			response.head.account.business.employees = rows;
			if(!callback) response.finish(); else callback();
		});

	// if seller is private
	} else {
		response.data.inputs.email_address = response.head.account.email;
		response.data.inputs.phone_number = response.head.account.phone_number;
		response.data.inputs.contact_number = response.data.business 
			? response.data.business.contact_number 
			: response.head.account.contact_number ;
		if(!callback) response.finish(); else callback();
	}
			
	
	
}

// GET /classifieds/:id/edit
app.get(/^\/classifieds\/([^\/]+)\/pictures\/?$/i, function(request, response, mysql){
	if(request.params[1]){
		// get ad pictures
		mysql.ad_pictures.get('ad', request.params[1], function(pictures){
			response.success({pictures:pictures});
		});
	} else {
		response.error();
	}
});

// GET classifieds/place
app.get('/classifieds/place', function(request, response, mysql){
	if(response.head.account.id){
		response.data.page = 'classifieds_place';
		response.data.title = 'Place Classified';
		response.data.scripts = [scripts.jquery, scripts.jelq, scripts.selectize, scripts.dropzone, scripts.accounting, scripts.maps, scripts.geo, scripts.page(response)];
		response.data.currencies = JSON.parse(JSON.stringify(currencies));
		response.data.inputs = {};
		response.data.inputs.trade_type = 1;
		response.data.categories = new Categories();
		
		if(response.head.account.business){
			// get confirmed employees
			mysql('SELECT * FROM accounts, business_employees'
				+ ' WHERE business_employees.business = "' + response.head.account.business.id 
				+ '" AND accounts.id = business_employees.account AND confirmed = 1', 
			function(rows){
				rows.forEach(function(row){ row = app.accounts.user(row) });
				response.head.account.business.employees = rows;
				response.finish();
			});
		} else {
			response.data.inputs.email_address = response.head.account.email;
			response.data.inputs.phone_number = response.head.account.phone_number;
			response.data.inputs.contact_number = response.data.business 
				? response.data.business.contact_number 
				: response.head.account.contact_number ;
			response.finish();
		}
		
	} else {
		response.data.errors = request.errors;
		response.data.inputs = request.body || {};
		response.data.page = 'signup';
		response.data.scripts = [scripts.maps, scripts.geo, scripts.page(response)];
		response.finish();
	}
});

// POST /classifieds/place
app.post.simple('/classifieds/place', function(request, response){
	app.upload('/uploads/pictures/original', request, response, function(mysql){
		// Demands
		request.demand('title');
		request.demand('formatted_address');
		request.demand('category');

		// Passed
		if(request.passed){
			try {
				// Next & Ad
				var next = new Next(3, finish);
				var ad 	 = new Ad(request, response);
				
				// Save Fields
				if(ad.fields.length) mysql.ad_fields.saveMore(ad.fields, next); else next(); 
				
				// Save Photos
				if(ad.photos.length) mysql.ad_pictures.saveMore(ad.photos, next); else next();
				
				// Save Ad
                console.log("BODY = "+JSON.stringify(ad.body))
				mysql.ads.save(ad.body, next);
				
				// Finish Operation
				function finish(){ 
					response.success(); 
					mysql.end(); 
				}
			
			} catch (error) { 
				console.log('/classifieds/place .... Threw Exception: ', error);
				request.abort(); // Not Passed 	
			}
		} else {
			console.log('/classifieds/place .... Request failed');
			request.abort(); // Not Passed 		
		}
	});
}, true);

// POST /classifieds/edit
app.post('/classifieds/edit', function(request, response, mysql){
	// Demands
	request.demand('title');
	request.demand('formatted_address');
	request.demand('category');

	// Passed
	if(request.passed){
	
		// Next & Ad
		var next = new Next(3, finish);
		var ad 	 = new Ad(request, response);
		
		// Delete Old Fields
		mysql.ad_fields.delete('ad', ad.body.id, next);
		
		// Save Fields
		if(ad.fields.length) mysql.ad_fields.saveMore(ad.fields, next); else next();
		
		// Save Ad
		mysql.ads.save(ad.body, next);
		
		// Finish Operation
		function finish(){ 
			response.redirect('/classifieds/'+ad.body.id);
		}
	
	} else {
		response.data.ad = new Ad(request, response);
		extend_classified(response.data.ad.body, mysql, function(){
			classified_editor(request, response, mysql, function(){
				response.data.inputs = response.data.ad.body;
				response.data.errors = request.errors;
				response.finish();
			});
		});
	}
});

// POST /classifieds/addPicture - JSON
app.post.simple('/classifieds/adPicture', function(request, response){
	app.upload('/uploads/pictures/original', request, response, function(mysql){
		if(request.body.id){	
		
			console.log('APP UPLOAD SUCCEEDED');
			if(request.body.files){
				// construct photos
				var photos = [];
				if(request.body.files){
					for(index in request.body.files){
						var file = request.body.files[index];
						var source = app.public+'/uploads/pictures/original/';
						photos.push({
							ad: request.body.id,
							source: file.path.split(source)[1],
							type: file.type,
							size: file.size,
						});
					}
				}
				try {
					// save photos
					mysql.ad_pictures.saveMore(photos, function(){
						response.success();
						mysql.end();
					});
				} catch (error) {
					request.error('reason', error.message);
					request.abort();
					mysql.end();
				}
				
			
			// no photos were found	
			} else {
				request.error('photos', 'missing');
				request.abort();
				mysql.end();
			}
		
		// no ad is found
		} else {
			request.error('ad', 'missing');
			request.abort();
			mysql.end();
		}
	});
}, true);

// GET /classifieds/:id/delete
app.get('/classifieds/delete', function(request, response, mysql){
	var id = request.query.id;
	if(isset(id)){
		// check if session user has access to the file
		if(response.head.account.id){
			// get ad from mysql
			mysql.ads.get('id', id, function(ads){	
				// get the ad
				var ad = ads[0];
				if(ad){
					// who is the session seller?
					if(response.head.account.business){
						var seller = response.head.account.business.id;
					} else {
						var seller = response.head.account.id;
					}
					
					console.log(seller, 'VS', ad.seller);
					
					// check if the ad seller is the session seller
					if(seller = ad.seller){
						// asnyc handler
						var next = new Next(2, finish);
						
						// remove ad from the database
						mysql.ads.delete('id', id, next);
						
						// remove all associated files
						mysql.ad_pictures.get('ad', id, function(pictures){
							if(pictures && pictures.length){
								var picNext = new Next(pictures.length, function(){
									mysql.ad_pictures.delete('ad', id, next);
								});
								pictures.forEach(function(picture){
									var path = app.public + '/uploads/pictures';
									fs.unlink(path+'/original/'+picture.source, picNext);
								});
							} else {
								next();
							}
						});
						
						// remove all associated fields
						mysql.ad_fields.delete('ad', id, next);
						
						// finish
						function finish(){
							response.redirect('back');
						}
					
					// else not authorized
					} else {
						request.error('account', 'not authorized')
						response.error();
						mysql.end();
					}
				} else {
					request.error('ad', 'not found')
					response.error();
					mysql.end();
				}
			});
		} else {
			request.error('account', 'not found')
			response.error();
			mysql.end();
		}
	} else {
		request.error('id', 'missing')
		response.error();
		mysql.end();
	}
});

// POST /classifieds/removePicture
app.post('/classifieds/removePicture', function(request, response, mysql){
	var id = request.query.id;
	if(isset(id)){
		// check if session user has access to the file
		if(response.head.account.id){
			// get file from mysql
			mysql.ad_pictures.get('id', id, function(pictures){
				var picture = pictures[0];
				
				// get ad of the picture from mysql
				mysql.ads.get('id', picture.ad, function(ads){	
					// get the ad
					var ad = ads[0];
					
					// who is the session seller?
					if(response.head.account.business){
						var seller = response.head.account.business.id;
					} else {
						var seller = response.head.account.id;
					}
					
					console.log(seller, 'VS', ad.seller);
					
					// check if the ad seller is the session seller
					if(seller = ad.seller){
						// asnyc handler
						var next = new Next(2, finish);
						
						// remove file from the database
						mysql.ad_pictures.delete('id', picture.id, next);
						
						// remove file from file system
						fs.unlink(app.public+'/uploads/pictures/original/'+picture.source, next);
						
						// finish
						function finish(){
							response.success();
						}
						
					// else not authorized
					} else {
						request.error('account', 'not authorized')
						response.error();
						mysql.end();
					}
					
				});
			});
		} else {
			request.error('account', 'not found')
			response.error();
			mysql.end();
		}
	} else {
		request.error('id', 'missing')
		response.error();
		mysql.end();
	}
});

function extend_classified(ad, mysql, callback){
	var next = new Next(3, callback);
		
	// get ad pictures
	mysql.ad_pictures.get('ad', ad.id, function(pictures){
		ad.pictures = pictures;
		next();
	});
	
	// get ad fields
	mysql.ad_fields.get('ad', ad.id, function(fields){
		ad.fields = fields;
		next();
	});
	
	// get business seller info
	console.log('#ad.seller_type', ad.seller_type)
	if(ad.seller_type == 1){ 
		var nextSeller = new Next(2, next);

		// get business
		mysql.businesses.get('id', ad.seller, function(businesses){
			ad.business = businesses[0];
			ad.business.avatar = ad.business.avatar 
				? '/uploads/avatars/original/'+ad.business.avatar 
				: '/images/no-business-profile.png';
			nextSeller();
		});
		
		// get employee
		mysql.business_employees.get('id', ad.agent, function(employees){
			ad.agent = employees[0];
			console.log('#ad.agent', ad.agent);
			// get employee account
			mysql.accounts.get('id', ad.agent.account, function(accounts){
				ad.agent.account = app.accounts.user(accounts[0]);
				nextSeller();
			});
		});
		
	// get personal seller info
	} else {
		// get employee account
		mysql.accounts.get('id', ad.seller, function(accounts){
			ad.seller = app.accounts.user(accounts[0]);	
			console.log('#ad.seller', ad.seller);			
			next();
		});
		/*
		mysql.accounts.get('id', ad.agent, function(accounts){
				ad.agent.account = app.accounts.user(accounts[0]);
				console.log('#ad.agent', ad.agent);
				next();
		});
		*/
		
	}
}

function get_classified(id, request, response, mysql, callback){
	if(isset(id)){
		mysql.ads.get('id', id, function(ads){
			var ad = ads[0];
			if(ad){
				extend_classified(ad, mysql, function(){
					response.data.ad = ad;
					callback();
				});
			} else {
				app.notFoundHandler(request, response);
			}
		});
	} else {
		app.notFoundHandler(request, response);
	}
}

function Ad(request, response){
	// Construct Ad
	var ad = {
		// title & description
		title: request.body.title,
		slug: slug(request.body.title),
		description: request.body.description || null,
		
		// trade type
		trade_type: request.body.trade_type,
		
		// category
		category: request.body.category,

		// price & currency
		price: request.body.price,
		formatted_price: request.body.formatted_price,
		currency: request.body.currency.split('-')[0],
		currency_symbol: request.body.currency.split('-')[1],

		// location columns
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

		// privacy
		show_vbox: request.body.show_vbox ? 1 : 0,
		show_email: request.body.show_email ? 1 : 0,
		show_contact_form: request.body.show_contact_form ? 1 : 0,
		show_phone: request.body.show_phone ? 1 : 0,

		// time created
		time_created: new Date().getTime(),
		
	};

	if(request.body.id) {
		ad.id = request.body.id;
		// fields to delete
		// photos to delete
	} else {
		ad.id = uniqid();
	}
	
	
	// Seller Type & Agent
	ad.seller_type = response.head.account.business ? 1 : 0 ;
	ad.seller = ad.seller_type 
		? response.head.account.business.id : response.head.account.id ;
	ad.agent = ad.seller_type 
		? request.body.contact_details : response.head.account.id ; 
	
	// Fields
	var fields = [];
	for(index in request.body){
		if(index[0] == '@' && isset(request.body[index])){
			fields.push({
				ad		: ad.id,
				name	: index.substr(1),
				value	: request.body[index],
			});
		} 
	}

	// Photos
	var photos = [];
	if(request.body.files){
		for(index in request.body.files){
			var file = request.body.files[index];
			var source = app.public+'/uploads/pictures/original/';
			photos.push({
				ad: ad.id,
				source: file.path.split(source)[1],
				type: file.type,
				size: file.size,
			});
		}
	}
	return {
		body: ad,
		fields: fields,
		photos: photos,
	};
}
