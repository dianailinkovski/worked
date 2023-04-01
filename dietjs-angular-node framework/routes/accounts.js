app.accounts({
	table		: 'accounts',
	username	: 'email',
	password	: 'password',
	select		: '*',
	change_password: {
		old_password: 'old_password',
		new_password: 'new_password',
		new_password_again: 'new_password_again'
	}
});

app.accounts.user = function(user, full){
	if(user){
		
		//console.log(user);
		// CONSTRUCT full name from first and last name
		user.full_name = user.first_name + ' ' + user.last_name;
		user.id=user.id;
		
		// GET avatar from db or gravatar
		var avatar = user.avatar;
		user.avatarName = user.avatar;
		user.avatar = function(size, type){
			var size = isset(size) ? size : 50;
			if(isset(avatar)){
				
				return '/uploads/profiles/original/' + avatar;
			} else if (size <= 50) {
				return '/images/no-profile-small.png';
			} else if (size > 50) {
				return '/images/no-profile.png';
			}
		}
		
		// DELETE sensitive information
		if(!isset(full)){
			delete user.password;
			delete user.session;
		}
		return user;
	} else {
		return false;
	}
}
/*
app.get("/chat",function(request,response,mysql){

console.log("--------------------- chating pages-------------------");
    var user_id = request.query.userId;
	if(user_id){ //== response.head.acc-idount.id){
console.log("This is correct page = " +user_id)
       response.data.page = 'chat';
	response.data.title = 'VBOX - Contact Us';

	response.finish();

	} else{
		response.redirect("/");		
		mysql.end();
	    
	}

}) */
app.post(/^\/accounts\/([^\/]+)\/?$/i, function(request, response, mysql){
	console.log('resulttttttt==============================>>>>>>>>>>>>>>>>>>>>>>>>>>>');
	console.log(mysql);
	console.log('resulttttttt==============================>>>>>>>>>>>>>>>>>>>>>>>>>>>');
				
	var user_id = request.params[1];
	console.log('response.head.account.avatarName=',user_id);
	if(isset(user_id)){
		var next = new Next(1, finish);
		
		response.data.subpage = request.query.show ? request.query.show : 'wall' ;
		
		if(user_id == response.head.account.id){
			response.data.user = response.head.account;
			response.data.connection = 'you';
			afterUser();
		} else {
			mysql.accounts.get('id', user_id, function(rows){
				response.data.user = rows[0] ? app.accounts.user(rows[0]) : false ;
				
				var follower = response.head.account.business 
					? response.head.account.business.id 
					: response.head.account.id;
					
				// check connection
				var sql = 'SELECT * FROM follows'
					+ ' WHERE follower = ' + mysql.escape(follower) 
					+ ' AND following = ' + mysql.escape(response.data.user.id);
				mysql(sql, function(rows){
					if(rows && rows.length){
						response.data.connection = 'following';
					} else {
						response.data.connection = 'stranger';
					}
					afterUser();
				});
			});
		}
		function afterUser(){
			if(response.data.subpage == 'ads'){
				
				response.data.ads = [];
				
				mysql('SELECT * FROM ads WHERE seller = '+mysql.escape(user_id)+' ORDER BY time_created DESC', function(rows){
					var rowNext = new Next(rows.length*2, next);
					rows.forEach(function(row){
						// get seller informations
						if(row.seller_type == 1){ // business seller
							mysql.businesses.get('id', row.seller, function(rows){
								row.seller = rows[0];
								row.seller_type = 'business';
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
					response.data.ads = rows;
				});
			} else if (response.data.subpage == 'wall') {
				// LIMIT
				var limit_count = response.data.limit_count = 10;
				var page = request.query.page ? (request.query.page-1)*limit_count : 0 ;
				var LIMIT = page+','+limit_count;
				var count_sql = 'SELECT COUNT(*) FROM posts WHERE owner = ' + mysql.escape(response.data.user.id);
				var nextPost = new Next(2, next);
				mysqlCountRows(request, response, mysql, count_sql, nextPost);
				
				mysql.posts.get('owner', response.data.user.id, {
					order: 'time DESC',
					limit: LIMIT
				}, function(rows){


					response.data.posts = rows;

					nextPost();
				});
			} else {
				app.notFoundHandler(request, response);
			}
		}
		
		
		function finish(){
			if(response.data.user){
				response.data.page = 'profile';
				response.data.title = response.data.user.full_name + '- VBOX';
				response.data.scripts = [scripts.jquery, scripts.jelq, scripts.imagesLoaded, scripts.masonry, scripts.page(response), scripts.dropzone,];
				response.finish();
			} else {
				app.notFoundHandler(request, response);
			}
		}
	} else {
		app.notFoundHandler(request, response);
	} 
});

app.login = function(request, response){
	var query = isset(request.url.query) ? '?' + request.url.query : '';
	response.data.redirect_url = request.url.pathname + query;
	response.data.page = 'login';
	response.data.scripts = [scripts.page(response)];
	response.finish();
} 

app.accounts.extend = function(request, response, mysql, callback){
	response.data = {};
	response.data.account = response.head.account;
	
	var next = new Next(1, function(){ callback(); });
	// get businesses
	mysql('SELECT * FROM business_employees, businesses WHERE business_employees.account = ' + mysql.escape(response.head.account.id) + ' AND businesses.id = business_employees.business AND business_employees.role = 0 ', function(rows){
		rows.forEach(function(business){
			business.avatar = isset(business.avatar)
				? '/uploads/avatars/original/'+business.avatar 
				: '/images/no-business-profile.png';
			if(business.session == request.cookies.business){
				business.selected = true;
				response.data.account.business = business;
			}
		});
		response.data.account.businesses = rows;
		next();
	})
	// get notificiations
	// get ads
}

app.accounts.on('login', function(request, response, mysql){
	if(request.errors.password || request.errors.email){
		response.data.page = 'login';
		response.data.errors = request.errors;
		response.data.scripts = [scripts.page(response)];
		response.finish();
	} else {
		console.log("user Login ------------------------------------>");
		var url = request.body.redirect_url || 'home';
		if(response.head.account) {
			url = request.body.redirect_url || response.head.account.id;			
		}
		response.redirect(url);		
		mysql.end();
	}
});

app.accounts.on('create', function(request, response, mysql){
	request.demand('first_name');
	request.demand('last_name');
	request.demand('contact_number');
	request.demand('email');
	request.demand('password');
	request.demand('gender');
	
	console.log(request.body);
	mysql.accounts.get('email', request.body.email, function(rows){
		if(rows && rows.length) request.error('email', 'already exists');
		onDemand();
	});
	
	function onDemand(){
		if(request.passed){
			var next = new Next(2, finish);
			var account_id = uniqid();
			
			if(isset(request.body.invite_code)){
				var activated = 1;
				mysql('UPDATE business_employees SET confirmed = 1, account = "'+account_id+'" WHERE confirmed = ' + mysql.escape(request.body.invite_code), next);
			} else {
				//var activated = uniqid();
				var activation_key = uniqid();
				var activated = 0;
				mail.send({
					email: request.body.email,
					subject: 'Please verify your email',
					html: 'verify.html',
					activated: activation_key,
					name: request.body.first_name
				});
				next();
			}
			
			mysql.accounts.save({
				id: account_id,
				first_name: request.body.first_name,
				last_name: request.body.last_name,
				email: request.body.email,
				contact_number: request.body.contact_number,
				password: sha1(request.body.password),
				gender: request.body.gender,
				activated: activated,
				country_short: request.body.country_short || null,
				country_long: request.body.country_long || null,
				locality_short: request.body.locality_short || null,
				locality_long: request.body.locality_long || null,
				administrative_area_level_1_short: request.body.administrative_area_level_1_short || null,
				administrative_area_level_1_long: request.body.administrative_area_level_1_long || null,
				administrative_area_level_2_short: request.body.administrative_area_level_2_short || null,
				administrative_area_level_2_long: request.body.administrative_area_level_2_long || null,
				formatted_address: request.body.formatted_address || null,
			}, next);
			
			function finish(){
				if(!activated){
					response.redirect('/accounts/confirm');
					mysql.end();
				} else {
					var auth = app.accounts.auth.setup(request.body.email, 
						request.body.password, mysql);
						 
					auth.success = function(user){
						Auth.login(response, user.session);
						response.redirect('/');
						mysql.end();
					};
					auth.failed	= function(){
						response.redirect('/accounts/login?login_failed');
						mysql.end();
					};
					auth.run();
				}
				
			}
			
		} else {
			response.data.errors = request.errors;
			response.data.inputs = request.body;
			response.data.page = 'signup';
			response.data.scripts = [scripts.maps, scripts.geo, scripts.page(response)];
			response.finish();
		}
	}
});

app.accounts.on('recover_notify', function(request, response, mysql){
	console.log('#recover_notify');
	if(!request.errors.recovery_email){
		response.data.title = 'VBOX - Recovery Email Sent';
		response.data.page = 'recover_notify';
		response.finish();
	} else {
		response.data.errors = request.errors;
		response.data.page = 'forgotPassword';
		response.finish();
	}
});

app.accounts.on('recover', function(request, response, mysql){
	response.data.reset_code = request.params[1];
	if(!objectLength(request.errors)){
		response.data.title = 'VBOX - New Password';
		response.data.page = 'recover';
		response.data.success = true;
		response.finish();
	} else {
		response.data.errors = request.errors;
		response.data.page = 'recover';
		response.finish();
	}
});

app.accounts.on('change_password', function(request, response, mysql){
	if(!objectLength(request.errors)){
		response.redirect('/accounts/settings/password?success=true');
	} else {
		response.data.errors = request.errors;
		response.data.inputs = request.body;
		response.data.subpage = 'password';
		settingsPage(request, response, mysql);
	}
});

app.get('/accounts/login', function(request, response, mysql){
	response.data.title = 'VBOX - Login';
	response.data.page = 'login';
	response.finish();
});

app.get('/accounts/signup', function(request, response, mysql){
	response.data.title = 'VBOX - Sign Up';
	response.data.page = 'signup';
	response.data.scripts = [scripts.jquery, scripts.jelq, scripts.maps, scripts.geo, scripts.page(response)];
	response.finish();
});

app.get(/\/accounts\/settings\/([^\/]+)\/?/i, settingsPage);
function settingsPage(request, response, mysql){
	response.data.title = 'VBOX - Settings';
	response.data.page = 'account_settings';
	response.data.subpage =  response.data.subpage || request.params[1] || 'details';
	response.data.scripts = [scripts.jquery, scripts.jelq, scripts.maps, scripts.geo, scripts.page(response)];
	if(!response.data.inputs) response.data.inputs = response.head.account;
	response.finish();
}

app.post('/accounts/save', function(request, response, mysql){
	request.demand('first_name');
	request.demand('last_name');
	request.demand('contact_number');
	request.demand('gender');
	
	console.log(request.body);
	
	if(request.passed){
		mysql.accounts.save({
			id: response.head.account.id,
			first_name: request.body.first_name,
			last_name: request.body.last_name,
			contact_number: request.body.contact_number,
			gender: request.body.gender,
			
			country_short: request.body.country_short || null,
			country_long: request.body.country_long || null,
			locality_short: request.body.locality_short || null,
			locality_long: request.body.locality_long || null,
			administrative_area_level_1_short: request.body.administrative_area_level_1_short || null,
			administrative_area_level_1_long: request.body.administrative_area_level_1_long || null,
			administrative_area_level_2_short: request.body.administrative_area_level_2_short || null,
			administrative_area_level_2_long: request.body.administrative_area_level_2_long || null,
			formatted_address: request.body.formatted_address || null,
		}, function(){
			response.redirect('/accounts/'+response.head.account.id);
		});
		
	} else {
		response.data.errors = request.errors;
		response.data.inputs = request.body;
		response.data.subpage = 'details';
		settingsPage(request, response, mysql);
	}
});

app.get.simple('/accounts/savePicture', function(request, response){
	response.redirect('/accounts/settings/picture');
});

app.get('/accounts/removePicture', function(request, response, mysql){
	if(response.head.account.id){

		// delete old avatar
		console.log('response.head.account.avatarName=',response.head.account.avatarName)
		if(response.head.account.avatarName){
			var source = app.public+'/uploads/profiles';
			var next = new Next(2, function(){
				response.redirect('back');
			})
			fs.unlink(source+'/original/'+response.head.account.avatarName, next);
			mysql.accounts.save({id: response.head.account.id, avatar: null }, next)
		} else {
			response.redirect('back');
		}
	} else {
		response.redirect('back');
	}
});

app.post.simple('/accounts/savePicture', function(request, response){
	app.upload('/uploads/profiles/original', request, response, function(mysql){
		console.log('request.body.files', request.body.files)
		if(request.body.files && request.body.files.picture && request.body.files.picture.size){
			var source = app.public+'/uploads/profiles/original/';

			var avatar = request.body.files.picture.path.split(source)[1];
			
			console.log({id: response.head.account.id, avatar: avatar});
			
			var next = new Next(2, finish);
			
			// delete old avatar
			console.log('response.head.account.avatarName=',response.head.account.avatarName)
			if(response.head.account.avatarName){
				var source = app.public+'/uploads/profiles';
				console.log('REMOVE FILE', source+'/original/'+response.head.account.avatarName)
				fs.unlink(source+'/original/'+response.head.account.avatarName, next);
			} else {
				next();
			}
			
			// save new avatar
			mysql.accounts.save({id: response.head.account.id, avatar: avatar}, next);
			
			function finish(){
				response.redirect('/accounts/settings/picture?success=true');
			}
			
		} else {
			request.error('photo', 'error');
			request.abort();
		}
	});
	
	response.onAbort = function(){

		response.data.errors = request.errors;
		response.data.inputs = request.body;
		response.data.subpage = 'picture';
		settingsPage(request, response, mysql);
	}
}, true);

app.get('/accounts/confirm', function(request, response, mysql){
	response.data.title = 'VBOX - Please verify your email';
	response.data.page = 'confirm';
	response.finish();
});

app.get('/accounts/forgotPassword', function(request, response, mysql){
	response.data.title = 'VBOX - Forgot Password';
	response.data.page = 'forgotPassword';
	response.finish();
});

app.get(/^\/accounts\/([^\/]+)\/?$/i, function(request, response, mysql){
	
	var user_id = request.params[1];
	console.log('user id=',user_id);
	if(isset(user_id)){
		var next = new Next(1, finish);
		
		response.data.subpage = request.query.show ? request.query.show : 'wall' ;
		
		if(user_id == response.head.account.id){
			response.data.user = response.head.account;
			response.data.connection = 'you';
			afterUser();
		} else {
			mysql.accounts.get('id', user_id, function(rows){
				response.data.user = rows[0] ? app.accounts.user(rows[0]) : false ;
				
				var follower = response.head.account.business 
					? response.head.account.business.id 
					: response.head.account.id;
					
				// check connection
				var sql = 'SELECT * FROM follows'
					+ ' WHERE follower = ' + mysql.escape(follower) 
					+ ' AND following = ' + mysql.escape(response.data.user.id)+" AND following_type='personal'";
				mysql(sql, function(rows){
					if(rows && rows.length){
						response.data.connection = 'following';
					} else {
						response.data.connection = 'stranger';
					}
					

					                   	var sqlCount = 'SELECT * FROM follows'
					+ ' WHERE  following = ' + mysql.escape(response.data.user.id)+"  AND following_type='personal'";
				mysql(sqlCount, function(rowsCount){
			        response.data.followerCount = rowsCount.length;

					afterUser();

				});

				});
			});
		}
		function afterUser(){
			if(response.data.subpage == 'ads'){
				
				response.data.ads = [];
				
				mysql('SELECT * FROM ads WHERE seller = '+mysql.escape(user_id)+' ORDER BY time_created DESC', function(rows){
					var rowNext = new Next(rows.length*2, next);
					rows.forEach(function(row){
						// get seller informations
						if(row.seller_type == 1){ // business seller
							mysql.businesses.get('id', row.seller, function(rows){
								row.seller = rows[0];
								row.seller_type = 'business';
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
					response.data.ads = rows;
				});
			} else if (response.data.subpage == 'wall') {
				// LIMIT
				var limit_count = response.data.limit_count = 10;
				var page = request.query.page ? (request.query.page-1)*limit_count : 0 ;
				var LIMIT = page+','+limit_count;
				var count_sql = 'SELECT COUNT(*) FROM posts WHERE owner = ' + mysql.escape(response.data.user.id);
				var nextPost = new Next(2, next);
				mysqlCountRows(request, response, mysql, count_sql, nextPost);
				
				mysql.posts.get('owner', response.data.user.id, {
					order: 'time DESC',
					limit: LIMIT
				}, function(rows){
					response.data.posts = rows;
					console.log('==============================================>>>>>>>>>>>>>>>>>');
					console.log(response.data.posts);
					console.log(rows);
					console.log('==============================================>>>>>>>>>>>>>>>>>');
					nextPost();
				});
				console.log('==============================================');
				console.log(limit_count);
				console.log(mysql.posts);
				console.log('==============================================');
			} else {
				app.notFoundHandler(request, response);
			}
		}
		
		
		function finish(){
			if(response.data.user){
				response.data.page = 'profile';
				response.data.title = response.data.user.full_name + '- VBOX';
				response.data.scripts = [scripts.jquery, scripts.jelq,scripts.dropzone, scripts.imagesLoaded, scripts.masonry, scripts.page(response)];
				response.finish();
			} else {
				app.notFoundHandler(request, response);
			}
		}
	} else {
		app.notFoundHandler(request, response);
	} 
});
