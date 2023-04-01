app.upload = function(uploadDir, request, response, callback){
	
	
	console.log('app.upload()..');
	// Parse Form
	var form = new formidable.IncomingForm();
	
	form.keepExtensions = true;
	form.uploadDir = app.public + uploadDir;
	form.type = 'multipart';
	console.log('..app.upload()..@x2..');
	console.log("------------------------->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>"+JSON.stringify(form));
	
	form.on('file', function(name, file) { });
	form.on('error', function(err) { console.log("error============================="+err); });
	form.on('aborted', function() { console.log('Aborted'); });

	form.parse(request, function(err, fields, files){
		
		
		
		
		console.log('....app.parse()');
		request.abort = function(mysql){
			console.log('response finish with fail');
			response.data.errors = request.errors || {};
			response.data.errors['upload'] = err;
			for(index in files){
				console.log('\n#FIES -> unlink', files[index].path);
				fs.unlinkSync(files[index].path);
			}
			if(!response.onAbort){
				response.error();
				if(mysql) mysql.end();
			} else {
				response.onAbort();
			}
		}
		app.db(function(mysql){
			console.log('....app.db()');
			app.headers(request, response, mysql, function(){
				console.log('.... .... app.headers()');
				if(response.head.account.id){
					console.log('.... .... app.headers -> response.head.account.id == ', response.head.account.id);
					request.body = merge(request.body, fields);
					console.log('.... .... app.headers -> fields == ');
					request.body.files = objectLength(files) ? files : false ;
					console.log('.... .... app.headers -> files == ');
					app.accounts.extend(request, response, mysql, function(){
						callback(mysql)
					});
				} else {
					console.log('....app.headers -> abort');
					request.abort(mysql);
				}
			});
		});
	});
 
}
