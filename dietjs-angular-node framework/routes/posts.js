// POST /posts/save


app.post.simple('/posts/savepic', function(request, response, mysql){

	request.demand('message'); 


	app.upload('/uploads/pictures/original', request, response, function(mysql){
console.log("sayeed--==================>",request.body.message);
		if(request.body.files && request.body.files.avatar && request.body.files.avatar.size){
			var source = app.public+'/uploads/pictures/original/';

			var avatar = request.body.files.avatar.path.split(source)[1];
			
			console.log({id: response.head.account.id, avatar: avatar});
			
			var next = new Next(2, finish);
			
		
				
			 next();
			var  msg=request.body.message
			mysql.posts.save({owner: response.head.account.id, image_name: avatar,author:response.head.account.id,message:msg,time:new Date().getTime()}, next);
			
			function finish(){
				response.redirect('back');
			}
			
		} else {
			request.error('photo', 'error');
			request.abort();
		}
	});


	response.onAbort = function(){

			response.data.errors = request.errors;
			response.data.inputs = request.body;
			response.redirect('back');
		}

}, true);


app.post('/posts/save', function(request, response, mysql){

	request.demand('message'); 

if( response.head.account.id){
		var owner = response.head.account.id;
		

		
		if(response.head.account.business && request.body.owner == 'business'){
			owner = response.head.account.business.id;
		}

	
		var msg=request.body.message;	
		var url;
		if(isset(request.body.url))	
			url=request.body.url;
		else
			url='';

	
		var sqlInsert = "INSERT INTO posts (`author`, `owner`, `message`, `time`, `url`) VALUES ("+response.head.account.id+","+owner+",'"+msg+"',"+new Date().getTime()+",'"+url+"')";
		console.log('File SQL',sqlInsert);
		mysql(sqlInsert);
		response.redirect('back');
		
		
		} else {

		response.redirect('back');
		}


	});

app.post("/hitLikedislike",function(request,response,mysql){
console.log("-------------------------- HIT LIKE DISLIKE -----------------------");
    var totalLike = 0;
      var totalDislike = 0;
	var alreadyLike = true;
	var existId = {};
        if (response.head.account.id){
        var sql = "Select * from likedislike where cateType='"+request.body.referType+"' and cateId="+request.body.referId;
        mysql(sql,function(data){
        if (data.length>0){
                for (var i = 0;i<data.length;i++){
                if (data[i].likeDislike==-1){
                ++totalDislike;
                }
                else{

                ++totalLike;
                }
		if (data[i].userId==response.head.account.id){
		existId = data[i];
		alreadyLike = false;
		}
                }
        }

	
	
	if (!alreadyLike){
	if (request.query.hitLike ==1){
	++totalLike;
    --totalDislike;
	}
	else{
	--totalLike;
	++totalDislike;
		}
    var sqlupdate = "Update likedislike SET likeDislike = "+request.body.hitLike+" where id = "+existId.id;

	mysql(sqlupdate);


	}
	else{

        if (request.query.hitLike ==1){
	++totalLike;
	}
	else{
	++totalDislike;
		}
var sqlupdate = "INSERT INTO likedislike (`cateType`,`cateId`,`userId`,`likeDislike`) VALUES ('"+request.body.referType+"',"+request.body.referId+","+response.head.account.id+","+request.body.hitLike+")";
   mysql(sqlupdate);
	}

        result = {
        totalLike:totalLike,
        totalDislike:totalDislike,
        postId:request.body.referId
        }
        response.data.totalLike = (result);
response.end(JSON.stringify(result));
});
}
else{
response.error();
mysql.end();
}

})



app.get("/totallikedislike",function(request,response,mysql){
console.log("==== GET TOTAL LIKE AND DISLIKE ====")
console.log("POST ID _ "+request.query.postId)
	var totalLike = 0;
	var totalDislike = 0;
	if (true){
	var sql = "Select * from likedislike where cateType='POST' and cateId="+request.query.postId;
	mysql(sql,function(data){
	if (data.length>0){
		for (var i = 0;i<data.length;i++){
		if (data[i].likeDislike==-1){
		++totalDislike;
		}
		else{

		++totalLike;
		}

		}
}
	result = {
	totalLike:totalLike,
	totalDislike:totalDislike,
	postId:request.query.postId
	}
	response.data.totalLike = (result);
    response.end(JSON.stringify(result));
	})	
	}
	else{
	response.error();
	mysql.end();
	
	}



})


app.get('/posts/follow', function(request, response, mysql){
	request.demand('id');
	
	if(request.passed){
		var follower = response.head.account.business 
			? response.head.account.business.id 
			: response.head.account.id;
			
		// check if there is connection
		var sql = 'SELECT * FROM follows'
			+ ' WHERE follower = ' + mysql.escape(follower) 
			+ ' AND following = ' + mysql.escape(request.query.id);
		mysql(sql, function(rows){
			if(rows && rows.length){
				mysql.follows.delete('id', rows[0].id, function(){
					response.redirect('back');
				});
			} else {
if (follower!=null){
				mysql.follows.save({
					follower: follower,
					following: request.query.id,
					following_type: request.query.type,
					follower_type: response.head.account.business ? 'business' : 'personal'
				}, function(){
					response.redirect('back');
				});
		}	}
		});
		
		
		
		
	} else {
		response.error();
		mysql.end();
	}
});

// GET /posts/delete
app.get('/posts/delete', function(request, response, mysql){
	var id = request.query.id;
	if(isset(id)){
		// check if session user has access to the file
		if(response.head.account.id){
			// get posts from mysql
			mysql.posts.get('id', id, function(posts){	
				// get the post
				var post = posts[0];
				if(post){
					var owner = response.head.account.business || response.head.account.id;
					// check if the ad seller is the session seller
					if(owner = post.owner){
						// remove ad from the database
						mysql.posts.delete('id', id, function(){
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
