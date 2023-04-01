// GET businesses/employees
app.get(/^\/businesses\/employees\/?$/i, function(request, response, mysql){
	when.business(request, response, mysql, function(){
		var next = new Next(3, finish);
		var business_id = mysql.escape(response.head.account.business.id);
		
		// get stores
		mysql.stores.get('business', response.head.account.business.id, function(stores){
			var selectStores = [];
			stores.forEach(function(store){
				selectStores.push([store.id, store.name]);
			});
			response.data.stores = selectStores;
			next();
		});
		
		// get confirmed employees
		mysql('SELECT * FROM accounts, business_employees'
			+ ' WHERE business_employees.business = ' + business_id 
			+ ' AND accounts.id = business_employees.account AND confirmed = 1', 
		function(rows){
			rows.forEach(function(row){ row = app.accounts.user(row) });
			response.head.account.business.employees = rows;
			next();
		});
		
		// get pending employees
		mysql('SELECT * FROM business_employees'
			+ ' WHERE business_employees.business = ' + business_id 
			+ ' AND confirmed != 1', 
		function(rows){
			response.head.account.business.pending_employees = rows;
			next();
		});
		
		function finish(){
			response.data.page = 'business_employees';
			response.data.title = 'VBOX - Business Employess';
			response.data.scripts = [scripts.jquery, scripts.jelq, scripts.selectize, scripts.page(response)];
			response.finish();
		}
	});
});

// POST businesses/employees/save
app.post('/businesses/employees/save', function(request, response, mysql){
	when.business(request, response, mysql, function(){
		var employee = {
			id: request.body.id,
			contact_number: request.body.contact_number,
			contact_email: request.body.contact_email,
			role: request.body.role,
			store: request.body.store || null
		}
		console.log(employee);
		mysql.business_employees.save(employee, function(row){
			response.redirect('back');
			mysql.end();
		});
	});
});

// GET businesses/employees/invite
app.get('/businesses/employees/invite', function(request, response, mysql){
	when.business(request, response, mysql, function(){
		response.data.page = 'business_invite_employee';
		response.data.title = 'VBOX - Invite Employee';	
		response.finish();
	});
});

// POST businesses/employees/invite
app.post('/businesses/employees/invite', function(request, response, mysql){
	when.business(request, response, mysql, function(){
		var confirmation_code = uniqid() + 'aD-' + sha1(uniqid()) + sha1(request.body.email);
					console.log("-----------------------sending--------------- email--------");
			console.log(request.body.email);
		mysql.accounts.get('email', request.body.email, function(rows){
			var account = rows[0] || {};
			var name = account.first_name ? account.first_name + ' ' + account.last_name : false ;
			console.log("-----------------------sending--------------- email--------");
			console.log(request.body.email);
			
			mail.send({
				email		: request.body.email,
				subject		: response.head.account.full_name + ' invited you to ' + response.head.account.business.name + ' at VBOX',
				html		: 'employee_invite.html',
				business	: response.head.account.business,
				code		: confirmation_code,
				name		: name,
				inviter		: response.head.account.full_name
			});
			
			mysql.business_employees.save({
				contact_email	: request.body.email,
				confirmed		: confirmation_code,
				business		: response.head.account.business.id,
				account			: account.id,
				role			: 1,
				time_created	: new Date().getTime()
			}, onFinish);
			
			function onFinish(){	
				response.redirect('/businesses/employees');
				mysql.end();
			}
		});
	});
});

// GET businesses/employees/delete
app.get('/businesses/employees/delete', function(request, response, mysql){
	when.business(request, response, mysql, function(){
		mysql.business_employees.delete('id', request.query.id, function(){
			response.redirect('/businesses/employees');
			mysql.end();
		});
	});
});

// GET businesses/employees/invite/answer
app.get('/businesses/employees/invite/answer', function(request, response, mysql){
	if(isset(request.query.code)){
		mysql.business_employees.get('confirmed', request.query.code, function(employees){
			var employee = employees[0];
			if(employee){
				mysql.businesses.get('id', employee.business, function(rows){
					response.data.business = rows[0];
					response.data.page = 'business_employee_invite_answer';
					response.data.answer = request.query.answer;
					if(employee.account){
						if(request.query.answer == 'accept'){
							mysql.business_employees.save({
								id	 : employee.id,
								confirmed : 1
							}, function(){
								response.data.title = 'Confirmation Code Accepted - VBOX';
								response.finish();
							});
						} else {
							mysql.business_employees.delete('confirmed', request.query.code, function(){
								response.data.title = 'Confirmation Code Declined - VBOX';
								response.finish();
							});
						}
					} else if (request.query.answer == 'accept') {
						response.data.inputs = { 
							invite_code: request.query.code,
							email: request.query.email,
							email_verified: true
						};
						response.data.title = 'Sign up for VBOX';
						response.data.page = 'signup';
						response.data.scripts = [scripts.maps, scripts.geo, scripts.page(response)];
						response.finish();
					} else {
						mysql.business_employees.delete('confirmed', request.query.code, function(){
							response.data.title = 'Confirmation Code Declined - VBOX';
							response.finish();
						});
					}
				});
			} else {
				response.data.title = 'Invalid Invitation Code';
				response.data.page = 'business_employee_invite_answer_fail';
				response.finish();
			}
		});
	} else {
		response.data.title = 'Invalid Invitation Code';
		response.data.page = 'business_employee_invite_answer_fail';
		response.finish();
	}
});

// GET businesses/employees/invite/revoke
app.get('/businesses/employees/invite/revoke', function(request, response, mysql){
	when.business(request, response, mysql, function(){
		response.data.page = 'business_invite_employee';
		mysql.business_employees.delete('id', request.query.id, function(){
			response.redirect('/businesses/employees');
			mysql.end();
		});
	});
});