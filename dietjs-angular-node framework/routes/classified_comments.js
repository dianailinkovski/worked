app.post('/classifieds/comment', function(request, response, mysql){
	request.demand('message');
	request.demand('ad');
	if(request.passed && response.head.account.id){
		mysql.ad_comments.save({
			author	: response.head.account.id,
			message	: request.body.message,
			ad		: request.body.ad,
			time	: new Date().getTime(),
		}, function(){
			response.redirect('/classifieds/'+request.body.ad+'?comment=success');
		})
	} else {
		response.redirect('/classifieds/'+request.body.ad+'?comment=failed');
	}
});

// GET /classifieds/comment/delete
app.get('/classifieds/comment/delete', function(request, response, mysql){
	var id = request.query.id;
	if(isset(id)){
		// check if session user has access to the file
		if(response.head.account.id){
			// get comment from mysql
			mysql.ad_comments.get('id', id, function(comments){	
				// get the comment
				var comment = comments[0];
				if(comment){
					// check if the ad seller is the session seller
					if(response.head.account.id = comment.author){
						// remove ad from the database
						mysql.ad_comments.delete('id', id, function(){
							response.redirect('back');
						});
					
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